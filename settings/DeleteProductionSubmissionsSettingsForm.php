<?php
/**
 * @file plugins/generic/deleteProductionSubmissions/settings/DeleteProductionSubmissionsSettingsForm.php
 *
 * Copyright (c) 2014-2022 Simon Fraser University
 * Copyright (c) 2003-2022 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class DeleteProductionSubmissionsSettingsForm
 * @brief Form for managers to delete production submissions.
 */

namespace APP\plugins\generic\deleteProductionSubmissions\settings;

use APP\facades\Repo;
use APP\template\TemplateManager;
use PKP\form\Form;
use PKP\submission\PKPSubmission;

class DeleteProductionSubmissionsSettingsForm extends Form
{
    /** @var \plugins\generic\deleteProductionSubmissions\DeleteProductionSubmissionsPlugin */
    public $plugin;

    /**
     * Constructor
     * @param $plugin \plugins\generic\deleteProductionSubmissions\DeleteProductionSubmissionsPlugin
     */
    public function __construct($plugin)
    {
        parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));
        $this->plugin = $plugin;

        $this->addCheck(new \PKP\form\validation\FormValidatorPost($this));
        $this->addCheck(new \PKP\form\validation\FormValidatorCSRF($this));
    }

    /**
     * @copydoc Form::initData()
     */
    public function initData()
    {
        $this->_data = [
            'targetSet' => 'production', // 'production' or 'incomplete'
            'deletionMode' => 'time',
            'deletionThreshold' => 90,
            'selectedSubmissionIds' => [],
        ];
    }

    /**
     * @copydoc Form::readInputData()
     */
    public function readInputData()
    {
        $this->readUserVars(['targetSet', 'deletionMode', 'deletionThreshold', 'selectedSubmissionIds', 'confirmDeletion']);
    }

    /**
     * @copydoc Form::fetch()
     */
    public function fetch($request, $template = null, $display = false)
    {
        $templateMgr = TemplateManager::getManager($request);
        $context = $request->getContext();

        $templateMgr->assign([
            'pluginName' => $this->plugin->getName(),
            'productionSubmissions' => $this->_getProductionSubmissions($context->getId()),
            'incompleteSubmissions' => $this->_getIncompleteSubmissions($context->getId()),
            'declinedSubmissions' => $this->_getDeclinedSubmissions($context->getId()),
            'defaultThreshold' => 90,
        ]);

        return parent::fetch($request, $template, $display);
    }

    /**
     * @copydoc Form::execute()
     */
    public function execute(...$functionArgs)
    {
        parent::execute(...$functionArgs);

        $request = \Application::get()->getRequest();
        $context = $request->getContext();
        
        if (!$this->getData('confirmDeletion')) {
            return;
        }

        $submissions = [];
        $mode = $this->getData('deletionMode');
        $targetSet = $this->getData('targetSet');

        switch ($mode) {
            case 'all':
                if ($targetSet === 'production') {
                    $submissions = $this->_getProductionSubmissions($context->getId());
                } elseif ($targetSet === 'incomplete') {
                    $submissions = $this->_getIncompleteSubmissions($context->getId());
                } else {
                    $submissions = $this->_getDeclinedSubmissions($context->getId());
                }
                break;
            case 'select':
                $ids = $this->getData('selectedSubmissionIds');
                if (!empty($ids)) {
                    foreach ($ids as $id) {
                        $submission = Repo::submission()->get((int)$id);
                        if ($submission && $submission->getData('contextId') == $context->getId()) {
                            // Validate based on target set
                            if ($targetSet === 'production' && 
                                $submission->getData('stageId') == WORKFLOW_STAGE_ID_PRODUCTION && 
                                $submission->getData('status') == PKPSubmission::STATUS_QUEUED) {
                                $submissions[] = $submission;
                            } elseif ($targetSet === 'incomplete' && $submission->getData('submissionProgress') > 0) {
                                $submissions[] = $submission;
                            } elseif ($targetSet === 'declined' && $submission->getData('status') == PKPSubmission::STATUS_DECLINED) {
                                $submissions[] = $submission;
                            }
                        }
                    }
                }
                break;
            case 'time':
                $threshold = (int)$this->getData('deletionThreshold');
                if ($targetSet === 'production') {
                    $submissions = $this->_getProductionSubmissions($context->getId(), $threshold);
                } elseif ($targetSet === 'incomplete') {
                    $submissions = $this->_getIncompleteSubmissions($context->getId(), $threshold);
                } else {
                    $submissions = $this->_getDeclinedSubmissions($context->getId(), $threshold);
                }
                break;
        }

        foreach ($submissions as $submission) {
            Repo::submission()->delete($submission);
        }
    }

    /**
     * Get submissions in production stage that are active (queued)
     */
    protected function _getProductionSubmissions($contextId, $daysInactive = null)
    {
        $collector = Repo::submission()->getCollector()
            ->filterByContextIds([$contextId])
            ->filterByStageIds([WORKFLOW_STAGE_ID_PRODUCTION])
            ->filterByStatus([PKPSubmission::STATUS_QUEUED]);

        if ($daysInactive !== null) {
            $collector->filterByDaysInactive($daysInactive);
        }

        return $collector->getMany()->toArray();
    }

    /**
     * Get incomplete submissions
     */
    protected function _getIncompleteSubmissions($contextId, $daysInactive = null)
    {
        $collector = Repo::submission()->getCollector()
            ->filterByContextIds([$contextId])
            ->filterByIncomplete(true);

        if ($daysInactive !== null) {
            $collector->filterByDaysInactive($daysInactive);
        }

        return $collector->getMany()->toArray();
    }

    /**
     * Get declined submissions
     */
    protected function _getDeclinedSubmissions($contextId, $daysInactive = null)
    {
        $collector = Repo::submission()->getCollector()
            ->filterByContextIds([$contextId])
            ->filterByStatus([PKPSubmission::STATUS_DECLINED]);

        if ($daysInactive !== null) {
            $collector->filterByDaysInactive($daysInactive);
        }

        return $collector->getMany()->toArray();
    }
}
