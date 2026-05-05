<?php
/**
 * @file plugins/generic/deleteProductionSubmissions/DeleteProductionSubmissionsPlugin.php
 *
 * Copyright (c) 2014-2022 Simon Fraser University
 * Copyright (c) 2003-2022 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class DeleteProductionSubmissionsPlugin
 * @brief Plugin to delete submissions in the production stage.
 */

namespace APP\plugins\generic\deleteProductionSubmissions;

use APP\core\Application;
use APP\template\TemplateManager;
use PKP\linkAction\LinkAction;
use PKP\linkAction\request\AjaxModal;
use PKP\plugins\GenericPlugin;
use APP\plugins\generic\deleteProductionSubmissions\settings\DeleteProductionSubmissionsSettingsForm;

class DeleteProductionSubmissionsPlugin extends GenericPlugin
{
    /**
     * @copydoc Plugin::register()
     */
    public function register($category, $path, $mainContextId = null)
    {
        $success = parent::register($category, $path, $mainContextId);
        if ($success && $this->getEnabled()) {
            // No hooks needed for now
        }
        return $success;
    }

    /**
     * @copydoc Plugin::getDisplayName()
     */
    public function getDisplayName()
    {
        return __('plugins.generic.deleteProductionSubmissions.displayName');
    }

    /**
     * @copydoc Plugin::getDescription()
     */
    public function getDescription()
    {
        return __('plugins.generic.deleteProductionSubmissions.description');
    }

    /**
     * @copydoc Plugin::getActions()
     */
    public function getActions($request, $actionArgs)
    {
        $actions = parent::getActions($request, $actionArgs);
        if (!$this->getEnabled()) {
            return $actions;
        }

        $router = $request->getRouter();
        $actions[] = new LinkAction(
            'deletion',
            new AjaxModal(
                $router->url($request, null, null, 'manage', null, array('verb' => 'deletion', 'plugin' => $this->getName(), 'category' => 'generic')),
                $this->getDisplayName()
            ),
            __('plugins.generic.deleteProductionSubmissions.deletion'),
            'delete'
        );

        return $actions;
    }

    /**
     * @copydoc Plugin::manage()
     */
    public function manage($args, $request)
    {
        $context = $request->getContext();
        $verb = $request->getUserVar('verb');

        switch ($verb) {
            case 'deletion':
                $form = new DeleteProductionSubmissionsSettingsForm($this);
                if ($request->getUserVar('save')) {
                    $form->readInputData();
                    if ($form->validate()) {
                        $form->execute();
                        return new \PKP\core\JSONMessage(true);
                    }
                }
                $form->initData();
                return new \PKP\core\JSONMessage(true, $form->fetch($request));
        }
        return parent::manage($args, $request);
    }
}

if (!PKP_STRICT_MODE) {
    class_alias('\APP\plugins\generic\deleteProductionSubmissions\DeleteProductionSubmissionsPlugin', '\DeleteProductionSubmissionsPlugin');
}
