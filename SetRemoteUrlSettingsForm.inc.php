<?php

/**
 *
 * Copyright (c) 2021 Universitäts- und Landesbibliothek Sachsen-Anhalt
 * Distributed under the GNU GPL v2. For full terms see the file LICENCE.
 *
 */

import('lib.pkp.classes.form.Form');

class SetRemoteUrlSettingsForm extends Form {

    private $contextId=42;
    public $plugin;

    public function __construct($plugin) {
    	parent::__construct($plugin->getTemplateResource('settings.tpl'));
        $this->plugin = $plugin;
		$this->addCheck(new FormValidatorPost($this));
		$this->addCheck(new FormValidatorCSRF($this));
        }

    public function initData() {
		$this->setData('token', $this->plugin->getSetting($this->contextId, 'token'));
		parent::initData();
		}

    public function readInputData() {
	    $this->readUserVars(['token']);
	     parent::readInputData();
	    } 

	public function fetch($request, $template = null, $display = false) {
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->assign('pluginName', $this->plugin->getName());
        return parent::fetch($request, $template, $display);
	    }

	public function execute(...$functionArgs) {
        $this->plugin->updateSetting($this->contextId, 'token', $this->getData('token'));
        import('classes.notification.NotificationManager');
        $notificationMgr = new NotificationManager();
        $notificationMgr->createTrivialNotification(
    	    Application::get()->getRequest()->getUser()->getId(),
		    NOTIFICATION_TYPE_SUCCESS,
		    ['contents' => __('common.changesSaved')]
			);
		return parent::execute();
	    }
}
?>