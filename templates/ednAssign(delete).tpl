{**
 * @file plugins/pubIds/edn/templates/ednAssign.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Assign EDN to an object option.
 *}

{assign var=pubObjectType value=$pubIdPlugin->getPubObjectType($pubObject)}
{assign var=enableObjectEdn value=$pubIdPlugin->getSetting($currentContext->getId(), "enable`$pubObjectType`Edn")}
{if $enableObjectEdn}
	{fbvFormArea id="pubIdEDNFormArea" class="border" title="plugins.pubIds.edn.editor.edn"}
	{if $pubObject->getStoredPubId($pubIdPlugin->getPubIdType())}
		{fbvFormSection}
			<p class="pkp_help">{translate key="plugins.pubIds.edn.editor.assignEdn.assigned" pubId=$pubObject->getStoredPubId($pubIdPlugin->getPubIdType())}</p>
		{/fbvFormSection}
	{else}
		{assign var=pubId value=$pubIdPlugin->getPubId($pubObject)}
		{if !$canBeAssigned}
			{fbvFormSection}
				{if !$pubId}
					<p class="pkp_help">{translate key="plugins.pubIds.edn.editor.assignEdn.emptySuffix"}</p>
				{else}
					<p class="pkp_help">{translate key="plugins.pubIds.edn.editor.assignEdn.pattern" pubId=$pubId}</p>
				{/if}
			{/fbvFormSection}
		{else}
			{assign var=templatePath value=$pubIdPlugin->getTemplateResource('ednAssignCheckBox.tpl')}
			{include file=$templatePath pubId=$pubId pubObjectType=$pubObjectType}
		{/if}
	{/if}
	{/fbvFormArea}
{/if}
