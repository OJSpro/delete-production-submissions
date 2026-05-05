{**
 * plugins/generic/deleteProductionSubmissions/templates/settingsForm.tpl
 *
 * Copyright (c) 2014-2022 Simon Fraser University
 * Copyright (c) 2003-2022 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Deletion tool settings form.
 *}
<script>
	$(function() {ldelim}
	$('#deleteProductionSubmissionsSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
	{rdelim});

	function toggleMode() {ldelim}
	var mode = $('input[name="deletionMode"]:checked').val();
	$('#selectSubmissionsArea').toggle(mode === 'select');
	$('#timeThresholdArea').toggle(mode === 'time');
	{rdelim}

	function toggleTarget() {ldelim}
	var target = $('input[name="targetSet"]:checked').val();
	$('.target-list').hide();
	$('#list-' + target).show();
	{rdelim}

	function scanSubmissions() {ldelim}
	$('#confirmationArea').show();
	$('#scanButton').hide();
	{rdelim}
</script>

<div id="deleteProductionSubmissionsContainer">
	<form class="pkp_form" id="deleteProductionSubmissionsSettingsForm" method="post"
		action="{url router=$smarty.const.ROUTE_COMPONENT op="manage" category="generic" plugin=$pluginName verb="deletion" save=true}">
		{csrf}

		<div class="pkp_notification"
			style="background: #fff3f3; border: 1px solid #ebccd1; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
			<h4 style="color: #a94442; margin-top: 0;">
				{translate key="plugins.generic.deleteProductionSubmissions.deletion.warning.title"}</h4>
			<p>{translate key="plugins.generic.deleteProductionSubmissions.deletion.description"}</p>
			<ul style="color: #a94442;">
				<li>{translate key="plugins.generic.deleteProductionSubmissions.deletion.warning.item.one"}</li>
				<li>{translate key="plugins.generic.deleteProductionSubmissions.deletion.warning.item.two"}</li>
			</ul>
		</div>

		{fbvFormArea id="deletionOptions"}
		{fbvFormSection title="plugins.generic.deleteProductionSubmissions.label.targetSet" list=true}
		{fbvElement type="radio" name="targetSet" id="targetSetProduction" value="production" checked=true label="plugins.generic.deleteProductionSubmissions.target.production" onclick="toggleTarget()"}
		{fbvElement type="radio" name="targetSet" id="targetSetIncomplete" value="incomplete" label="plugins.generic.deleteProductionSubmissions.target.incomplete" onclick="toggleTarget()"}
		{fbvElement type="radio" name="targetSet" id="targetSetDeclined" value="declined" label="plugins.generic.deleteProductionSubmissions.target.declined" onclick="toggleTarget()"}
		{/fbvFormSection}

		{fbvFormSection title="plugins.generic.deleteProductionSubmissions.label.selectMode" list=true}
		{fbvElement type="radio" name="deletionMode" id="deletionModeTime" value="time" checked=true label="plugins.generic.deleteProductionSubmissions.mode.time" onclick="toggleMode()"}
		{fbvElement type="radio" name="deletionMode" id="deletionModeSelect" value="select" label="plugins.generic.deleteProductionSubmissions.mode.select" onclick="toggleMode()"}
		{fbvElement type="radio" name="deletionMode" id="deletionModeAll" value="all" label="plugins.generic.deleteProductionSubmissions.mode.all" onclick="toggleMode()"}
		{/fbvFormSection}

		<div id="timeThresholdArea">
			{fbvFormSection title="plugins.generic.deleteProductionSubmissions.thresholdLabel"}
			{fbvElement type="text" name="deletionThreshold" id="deletionThreshold" value=$defaultThreshold size=$FBV_SECTION_SMALL}
			{/fbvFormSection}
		</div>

		<div id="selectSubmissionsArea"
			style="display:none; max-height: 250px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; margin-bottom: 10px;">
			<div id="list-production" class="target-list">
				<h4>{translate key="plugins.generic.deleteProductionSubmissions.target.production"}</h4>
				{if $productionSubmissions|@count > 0}
					{foreach from=$productionSubmissions item=submission}
						<div style="margin-bottom: 5px;">
							<input type="checkbox" name="selectedSubmissionIds[]" value="{$submission->getId()}">
							[{$submission->getId()}] {$submission->getLocalizedTitle()|escape}
						</div>
					{/foreach}
				{else}
					<p>{translate key="plugins.generic.deleteProductionSubmissions.noSubmissionsFound"}</p>
				{/if}
			</div>
			<div id="list-incomplete" class="target-list" style="display:none;">
				<h4>{translate key="plugins.generic.deleteProductionSubmissions.target.incomplete"}</h4>
				{if $incompleteSubmissions|@count > 0}
					{foreach from=$incompleteSubmissions item=submission}
						<div style="margin-bottom: 5px;">
							<input type="checkbox" name="selectedSubmissionIds[]" value="{$submission->getId()}">
							[{$submission->getId()}] {$submission->getLocalizedTitle()|escape}
						</div>
					{/foreach}
				{else}
					<p>{translate key="plugins.generic.deleteProductionSubmissions.noSubmissionsFound"}</p>
				{/if}
			</div>
			<div id="list-declined" class="target-list" style="display:none;">
				<h4>{translate key="plugins.generic.deleteProductionSubmissions.target.declined"}</h4>
				{if $declinedSubmissions|@count > 0}
					{foreach from=$declinedSubmissions item=submission}
						<div style="margin-bottom: 5px;">
							<input type="checkbox" name="selectedSubmissionIds[]" value="{$submission->getId()}">
							[{$submission->getId()}] {$submission->getLocalizedTitle()|escape}
						</div>
					{/foreach}
				{else}
					<p>{translate key="plugins.generic.deleteProductionSubmissions.noSubmissionsFound"}</p>
				{/if}
			</div>
		</div>

		<div id="previewArea" style="margin-top: 20px;">
			<button type="button" id="scanButton" class="pkp_button"
				onclick="scanSubmissions()">{translate key="plugins.generic.deleteProductionSubmissions.preview"}</button>
		</div>

		<div id="confirmationArea"
			style="display:none; margin-top: 20px; padding: 15px; background: #fcf8e3; border: 1px solid #faebcc; border-radius: 4px;">
			<p><strong>{translate key="common.confirm"}</strong></p>
			{fbvFormSection list=true}
			{fbvElement type="checkbox" name="confirmDeletion" id="confirmDeletion" label="plugins.generic.deleteProductionSubmissions.finalConfirm"}
			{/fbvFormSection}
			<br>
			{fbvFormButtons submitText="common.delete" hideCancel=true}
		</div>
		{/fbvFormArea}
	</form>
</div>