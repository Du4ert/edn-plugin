<?php

/**
 * @file plugins/pubIds/edn/EDNPubIdPlugin.inc.php
 *
 * Copyright (c) 2014-2021 Simon Fraser University
 * Copyright (c) 2003-2021 John Willinsky
 * Distributed under the GNU GPL v3. For full terms see the file docs/COPYING.
 *
 * @class EDNPubIdPlugin
 * @ingroup plugins_pubIds_edn
 *
 * @brief edn plugin class
 */
namespace APP\plugins\pubIds\edn;

use APP\core\Application;
use APP\facades\Repo;
use APP\plugins\PubIdPlugin;
use PKP\plugins\Hook;

class EDNPubIdPlugin extends PubIdPlugin {

	/**
	 * @copydoc Plugin::register()
	 */
	public function register($category, $path, $mainContextId = null) {
		$success = parent::register($category, $path, $mainContextId);
		if ($success && $this->getEnabled($mainContextId)) {
			Hook::add('CitationStyleLanguage::citation', [$this, 'getCitationData']);
			Hook::add('Publication::getProperties::summaryProperties', [$this, 'modifyObjectProperties']);
			Hook::add('Publication::getProperties::fullProperties', [$this, 'modifyObjectProperties']);
			Hook::add('Publication::validate', [$this, 'validatePublicationEdn']);
			Hook::add('Publication::getProperties::values', [$this, 'modifyObjectPropertyValues']);
			Hook::add('Form::config::before', [$this, 'addPublicationFormFields']);
			Hook::add('Form::config::before', [$this, 'addPublishFormNotice']);
			Hook::add('TemplateManager::display', [$this, 'loadEdnFieldComponent']);
		}
		return $success;
	}

	//
	// Implement template methods from Plugin.
	//
	/**
	 * @copydoc Plugin::getDisplayName()
	 */
	function getDisplayName() {
		return __('plugins.pubIds.edn.displayName');
	}

	/**
	 * @copydoc Plugin::getDescription()
	 */
	function getDescription() {
		return __('plugins.pubIds.edn.description');
	}

	/**
	 * @copydoc PKPPubIdPlugin::instantiateSettingsForm()
	 */
	function instantiateSettingsForm($contextId) {
		return false;
	}

		/**
	 * @copydoc PKPPubIdPlugin::getFormFieldNames()
	 */
	function getFormFieldNames() {
		return array('ednSuffix');
	}

		/**
	 * @copydoc PKPPubIdPlugin::getPrefixFieldName()
	 */
	function getPrefixFieldName() {
		return false;
	}
	

		/**
	 * @copydoc PKPPubIdPlugin::getLinkActions()
	 */
	function getLinkActions($pubObject) {
		$linkActions = array();

		return $linkActions;
	}

	/**
	//
	// Implement template methods from PubIdPlugin.
	//
	/**
	 * @copydoc PKPPubIdPlugin::constructPubId()
	 */
	function constructPubId($pubIdPrefix='', $pubIdSuffix, $contextId) {
		// return $pubIdSuffix;
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdType()
	 */
	function getPubIdType() {
		return 'edn';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdDisplayType()
	 */
	function getPubIdDisplayType() {
		return 'EDN';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdFullName()
	 */
	function getPubIdFullName() {
		return 'Elibrary Document Number';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getResolvingURL()
	 */
	function getResolvingURL($contextId, $pubId) {
		return 'https://www.elibrary.ru/'.$pubId;
	}

	function addJavaScript($name, $script, $args = []) {

		$args = array_merge(
			[
				'priority' => STYLE_SEQUENCE_NORMAL,
				'contexts' => ['backend'],
				'inline'   => false,
			],
			$args
		);

		$args['contexts'] = (array) $args['contexts'];
		foreach($args['contexts'] as $context) {
			$this->_javaScripts[$context][$args['priority']][$name] = [
				'script' => $script,
				'inline' => $args['inline'],
			];
		}
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdMetadataFile()
	 */
	function getPubIdMetadataFile() {
		return $this->getTemplateResource('ednSuffixEdit.tpl');
	}

	/**
	 * @copydoc PKPPubIdPlugin::getPubIdAssignFile()
	 */
	function getPubIdAssignFile() {
		return $this->getTemplateResource('ednAssign.tpl');
	}


	/**
	 * @copydoc PKPPubIdPlugin::getAssignFormFieldName()
	 */
	function getAssignFormFieldName() {
		return 'assignEdn';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getSuffixFieldName()
	 */
	function getSuffixFieldName() {
		return 'ednSuffix';
	}

	/**
	 * @copydoc PKPPubIdPlugin::getSuffixPatternsFieldNames()
	 */
	function getSuffixPatternsFieldNames() {
		return  false;
	}

	/**
	 * @copydoc PKPPubIdPlugin::getDAOFieldNames()
	 */
	function getDAOFieldNames() {
		return array('pub-id::edn');
	}

	/**
	 * @copydoc PKPPubIdPlugin::isObjectTypeEnabled()
	 */
	function isObjectTypeEnabled($pubObjectType, $contextId) {
		return (boolean) $this->getSetting($contextId, "enable" . $pubObjectType . "edn");
	}

	/**
	 * @copydoc PKPPubIdPlugin::isObjectTypeEnabled()
	 */
	function getNotUniqueErrorMsg() {
		return __('plugins.pubIds.edn.editor.ednCustomIdentifierNotUnique');
	}

	/**
	 * @copydoc PKPPubIdPlugin::validatePubId()
	 */
	function validatePubId($pubId) {
		return preg_match('/^[A-Za-z]{6}$/', $pubId);
	}

	/*
	 * Public methods
	 */
	/**
	 * Add edn to citation data used by the CitationStyleLanguage plugin
	 *
	 * @see CitationStyleLanguagePlugin::getCitation()
	 * @param $hookname string
	 * @param $args array
	 * @return false
	 */
	public function getCitationData($hookname, $args) {
		$citationData = $args[0];
		$article = $args[2];
		$issue = $args[3];
		$journal = $args[4];

		if ($issue && $issue->getPublished()) {
			$pubId = $article->getStoredPubId($this->getPubIdType());
		} else {
			$pubId = $this->getPubId($article);
		}

		if (!$pubId) {
			return;
		}

		$citationData->edn = $pubId;
	}


	/*
	 * Private methods
	 */
	
	/**
	 * Validate a publication's edn against the plugin's settings
	 *
	 * @param $hookName string
	 * @param $args array
	 */
	public function validatePublicationEdn($hookName, $args) {
		$errors = & $args[0];
        $object = $args[1];
        $props = & $args[2];


		if (empty($props['pub-id::edn'])) {
			return;
		}

		if (is_null($object)) {
			$submission = Repo::submission()->get($props['submissionId']);
		} else {
			$publication = Repo::publication()->get($props['id']);
			$submission = Repo::submission()->get($publication->getData('submissionId'));
		}

		$contextId = $submission->getData('contextId');

		$ednErrors = [];
		if (!$this->validatePubId($props['pub-id::edn'])) {
			$ednErrors[] = __('plugins.pubIds.edn.editor.ednCustomIdentifierDontMatchPreg');
		}
		
		if (!$this->checkDuplicate($props['pub-id::edn'], 'Publication', $submission->getId(), $contextId)) {
			$ednErrors[] = $this->getNotUniqueErrorMsg();
		}
		if (!empty($ednErrors)) {
			$errors['pub-id::edn'] = $ednErrors;
		}
	}

	/**
	 * Add edn to submission, issue or galley properties
	 *
	 * @param $hookName string <Object>::getProperties::summaryProperties or
	 *  <Object>::getProperties::fullProperties
	 * @param $args array [
	 * 		@option $props array Existing properties
	 * 		@option $object Submission|Issue|Galley
	 * 		@option $args array Request args
	 * ]
	 *
	 * @return array
	 */
	public function modifyObjectProperties($hookName, $args) {
		$props =& $args[0];

		$props[] = 'pub-id::edn';
	}

	/**
	 * Add edn submission, issue or galley values
	 *
	 * @param $hookName string <Object>::getProperties::values
	 * @param $args array [
	 * 		@option $values array Key/value store of property values
	 * 		@option $object Submission|Issue|Galley
	 * 		@option $props array Requested properties
	 * 		@option $args array Request args
	 * ]
	 *
	 * @return array
	 */
	public function modifyObjectPropertyValues($hookName, $args) {
	}

	/**
	 * Show edn during final publish step
	 *
	 * @param $hookName string Form::config::before
	 * @param $form FormComponent The form object
	 */
	public function addPublishFormNotice($hookName, $form) {
	}

		/**
	 * Add edn fields to the publication identifiers form
	 *
	 * @param $hookName string Form::config::before
	 * @param $form FormComponent The form object
	 */
	public function addPublicationFormFields($hookName, $form) {

		if ($form->id !== 'publicationIdentifiers') {
			return;
		}

		// Add a text field to enter the edn if no pattern exists
			$form->addField(new \PKP\components\forms\FieldText('pub-id::edn', [
				'label' => __('plugins.pubIds.edn.editor.edn'),
				'description' => __('plugins.pubIds.edn.editor.edn.description'),
				'value' => $form->publication->getData('pub-id::edn'),
			]));
	}

     /*
     /* @param string $hookName
     /* @param array $args
     */
    public function loadEdnFieldComponent($hookName, $args)
    {
        $templateMgr = $args[0];
        $template = $args[1];

        if ($template !== 'workflow/workflow.tpl') {
            return;
        }

        $templateMgr->addJavaScript(
            'purl-field-component',
            Application::get()->getRequest()->getBaseUrl() . '/' . $this->getPluginPath() . '/js/EDNCorrection.js',
            [
                'contexts' => 'backend',
                'priority' => STYLE_SEQUENCE_LAST,
            ]
        );

        $templateMgr->addStyleSheet(
            'purl-field-component',
            '
				.pkpFormField--purl__input {
					display: inline-block;
				}

				.pkpFormField--purl__button {
					margin-left: 0.25rem;
					height: 2.5rem; // Match input height
				}
			',
            [
                'contexts' => 'backend',
                'inline' => true,
                'priority' => STYLE_SEQUENCE_LAST,
            ]
        );
    }
	
}