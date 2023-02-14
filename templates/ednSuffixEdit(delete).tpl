{**edn
 * @file plugins/pubIds/edn/templates/ednSuffixEdit.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Edit custom EDN suffix for an object (issue, submission, galley)
 *}

 {assign var=pubObjectType value=$pubIdPlugin->getPubObjectType($pubObject)}
 {assign var=enableObjectEdn value=$pubIdPlugin->getSetting($currentContext->getId(), "enable`$pubObjectType`Edn")}
 {if $enableObjectEdn}
     {assign var=storedPubId value=$pubObject->getStoredPubId($pubIdPlugin->getPubIdType())}
     {fbvFormArea id="pubIdEDNFormArea" class="border" title="plugins.pubIds.edn.editor.edn"}
         {assign var=formArea value=true}
         {if $pubIdPlugin->getSetting($currentContext->getId(), 'ednSuffix') == 'customId' || $storedPubId}
             {if empty($storedPubId)} {* edit custom suffix *}
                 {fbvFormSection}
                     <p class="pkp_help">{translate key="plugins.pubIds.edn.manager.settings.ednSuffix.description"}</p>
                     {fbvElement type="text" label="plugins.pubIds.edn.manager.settings.ednSuffix" id="ednSuffix" value=$ednSuffix size=$fbvStyles.size.MEDIUM}
                 {/fbvFormSection}
                 {if $canBeAssigned}
                     {assign var=templatePath value=$pubIdPlugin->getTemplateResource('ednAssignCheckBox.tpl')}
                     {include file=$templatePath pubId="" pubObjectType=$pubObjectType}
                 {else}
                     <p class="pkp_help">{translate key="plugins.pubIds.edn.editor.customSuffixMissing"}</p>
                 {/if}
             {else} {* stored pub id and clear option *}
             {fbvFormSection}
                 <p>
                     {$storedPubId|escape}<br />
                     {capture assign=translatedObjectType}{translate key="plugins.pubIds.edn.editor.ednObjectType"|cat:$pubObjectType}{/capture}
                     {capture assign=assignedMessage}{translate key="plugins.pubIds.edn.editor.assigned" pubObjectType=$translatedObjectType}{/capture}
                     <p class="pkp_help">{$assignedMessage}</p>
                     {include file="linkAction/linkAction.tpl" action=$clearPubIdLinkActionEdn contextId="publicIdentifiersForm"}
                 </p>
                 {/fbvFormSection}
             {/if}
         {else} {* pub id preview *}
             <p>{$pubIdPlugin->getPubId($pubObject)|escape}</p>
             {if $canBeAssigned}
                 <p class="pkp_help">{translate key="plugins.pubIds.edn.editor.canBeAssigned"}</p>
                 {assign var=templatePath value=$pubIdPlugin->getTemplateResource('ednAssignCheckBox.tpl')}
                 {include file=$templatePath pubId="" pubObjectType=$pubObjectType}
             {else}
                 <p class="pkp_help">{translate key="plugins.pubIds.edn.editor.patternNotResolved"}</p>
             {/if}
         {/if}
     {/fbvFormArea}
 {/if}
 {* issue pub object *}
 {if $pubObjectType == 'Issue'}
     {assign var=enablePublicationEdn value=$pubIdPlugin->getSetting($currentContext->getId(), "enablePublicationEdn")}
     {assign var=enableRepresentationEdn value=$pubIdPlugin->getSetting($currentContext->getId(), "enableRepresentationEdn")}
     {if $enablePublicationEdn || $enableRepresentationEdn}
         {if !$formArea}
             {assign var="formAreaTitle" value="plugins.pubIds.edn.editor.edn"}
         {else}
             {assign var="formAreaTitle" value=""}
         {/if}
         {fbvFormArea id="pubIdEDNFormArea" class="border" title=$formAreaTitle}
             {fbvFormSection list="true" description="plugins.pubIds.edn.editor.clearIssueObjectsEdn.description"}
                 {include file="linkAction/linkAction.tpl" action=$clearIssueObjectsPubIdsLinkActionEdn contextId="publicIdentifiersForm"}
             {/fbvFormSection}
         {/fbvFormArea}
     {/if}
 {/if}