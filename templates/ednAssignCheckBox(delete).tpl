{**edn
 * @file plugins/pubIds/edn/templates/ednAssignCheckBox.tpl
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * Displayed only if the EDN can be assigned.
 * Assign EDN form check box included in ednSuffixEdit.tpl and ednAssign.tpl.
 *}

{capture assign=translatedObjectType}{translate key="plugins.pubIds.edn.editor.ednObjectType"|cat:$pubObjectType}{/capture}
{capture assign=assignCheckboxLabel}{translate key="plugins.pubIds.edn.editor.assignEdn" pubId=$pubId pubObjectType=$translatedObjectType}{/capture}
{fbvFormSection list=true}
	{fbvElement type="checkbox" id="assignEdn" checked="true" value="1" label=$assignCheckboxLabel translate=false disabled=$disabled}
{/fbvFormSection}
