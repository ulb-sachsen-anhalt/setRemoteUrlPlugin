<?php

/**
 *
 * Copyright (c) 2022 Universitäts- und Landesbibliothek Sachsen-Anhalt
 * Distributed under the GNU GPL v2. For full terms see the file LICENCE.
 *
 */

import('lib.pkp.classes.plugins.GenericPlugin');

class SetRemoteUrlPlugin extends GenericPlugin {

    public function register($category, $path, $mainContextId = NULL) {
        $success = parent::register($category, $path);
        if ($success && $this->getEnabled()) {
                HookRegistry::register('TemplateManager::display', array($this, 'setRemoteUrl'));
            }
        return $success;
        }
        
    public function setRemoteUrl($hookName, $args) {
        $application = Application::get(); 
        $className = $application->getQualifiedDAOName('ArticleGalleyDAO');
        if ($className) {                                                                                
            $fileDao = DAORegistry::getDAO('ArticleGalleyDAO');
            $tablename = "publication_galleys";                                
         } else {                                                    
            $fileDao = DAORegistry::getDAO('PublicationFormatDAO');
            $tablename = "publication_formats";
        }     
        
        $template =& $args[1];
        $request = Application::get()->getRequest();
        if ($template != 'frontend/pages/indexSite.tpl') return false;
        $remote_url=null;
        $publication_id=null;
        $token=null;
        $queryarray = $request->getQueryArray();
        if(!array_key_exists('token', $queryarray)) return false;
        foreach($queryarray as $key => $value) {
            error_log($key."=".$value);
            if ($key == 'publication_id') $publication_id=$value;
            if ($key == 'remote_url') $remote_url=$value;
            if ($key == 'token') $token=$value;    
            }
        if($token==null) {
            error_log('[ERROR] token missing');
            $dispatcher = $request->getDispatcher();
            $dispatcher->handle404();
            }
        if($token!=$this->getSetting(42, 'token')) {
            error_log('[ERROR] token given, but not correct. Check Plugin settings');
            $dispatcher = $request->getDispatcher();
            $dispatcher->handle404();
            }
        // ok, everything matches
        // we'll write new remote_url    
        $results = $fileDao->retrieve(
            "update $tablename set remote_url=? where publication_id=?",
            [$remote_url, $publication_id]
            );
        foreach ($results as $g) {}
        return true;
        }

    public function getActions($request, $actionArgs) {
        $actions = parent::getActions($request, $actionArgs);
        if (!$this->getEnabled()) {
            return $actions;
            }
    
        $router = $request->getRouter();
        import('lib.pkp.classes.linkAction.request.AjaxModal');
        $linkAction = new LinkAction(
            'settings',
            new AjaxModal(
                $router->url(
                    $request,
                    null,
                    null,
                    'manage',
                    null,
                    array(
                        'verb' => 'settings',
                        'plugin' => $this->getName(),
                        'category' => 'generic'
                    )
                ),
                $this->getDisplayName()
            ),
            __('manager.plugins.settings'),
            null
        );
        array_unshift($actions, $linkAction);
        return $actions;
        }        

    public function manage($args, $request) {
        switch ($request->getUserVar('verb')) {
            case 'settings':
                $this->import('SetRemoteUrlSettingsForm');
                $form = new SetRemoteUrlSettingsForm($this);
                if (!$request->getUserVar('save')) {
                    $form->initData();
                        return new JSONMessage(true, $form->fetch($request));
                    }
                $form->readInputData();
                if ($form->validate()) {
                    $form->execute();
                    return new JSONMessage(true);
                    }
        }
        return parent::manage($args, $request);
    }

    public function getDisplayName() {
        return __('plugins.generic.remote_url.name');
    }

    public function getDescription() {
        return __('plugins.generic.remote_url.description');
    }

    public function isSitePlugin() {
        return !Application::get()->getRequest()->getContext();
    }
}
?>