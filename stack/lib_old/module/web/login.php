<?php
namespace stack\module\web;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class Login extends LayeredModule implements \stack\web\Requestable {

    const NAME = 'stack.web.login';

    /**
     * @var \lean\Template $view
     */
    private $view;


    public function dispatchWebRequest(\stack\web\Request $request) {
        $context = $this->getApplication()->getContext();

        if($user = $context->getUser()) {
            // already logged in
            return new \stack\web\Response_HTML("Already logged in as '{$user->getUname()}'");
        }

        // initial view
        if(!$request->isXHR()) {
            $this->getDocument()->addCSSheet($this->getAssetWebPath('/login.css'));

            $this->getDocument()->addScript($this->getAssetWebPath('/login.js'));
            $this->getDocument()->addScript($this->getApplication()->getShell()->createModule(Core::NAME, null)->getAssetWebPath('/jquery/jquery-1.7.2.min.js'));
            $this->getDocument()->addScript($this->getApplication()->getShell()->createModule(Core::NAME, null)->getAssetWebPath('/sha1.js'));

            // initial request, show form
            $view = $this->view = $this->createView('login.php');
            $view->set('rootpw', file_get_contents(APPLICATION_ROOT_PATH . '/rootpw'));
            return $this->display();
        }

        // form has been sent via ajax.
        $context->pushSecurity(new \stack\security\PriviledgedSecurity());
        try {
            // read user and create response
            $userFile = $context->getShell()->readFile(\stack\Root::ROOT_PATH_USERS . '/' . $request->post('uName'));
            $user = $userFile->getModule();
            /**
             * @var \stack\module\User $user
             */
            // if user is actually auth'd, save it in session
            $loggedIn = $user->auth($request->post('uPass'));
            if($loggedIn) {
                $context->setUser($request->post('uName'));
                $response = new \stack\web\Response_JSON(['authorized' => true, 'home' => $user->getHome()]);
            }
            else {
                $response = new \stack\web\Response_JSON(['authorized' => false]);
            }
        }
        catch(\stack\filesystem\Exception_FileNotFound $e) {
            $response = new \stack\web\Response_JSON(['authorized' => false]);
        }
        $context->pullSecurity();

        return $response;
    }

    /**
     * @return \lean\Template
     */
    protected function getView() {
        return $this->view;
    }

    /**
     * Overwritten to pull file from stack template directory rather than from application one
     *
     * @param $template
     *
     * @return \lean\Template
     */
    protected function createView($template) {
        return \stack\Template::createTemplate(\stack\Template::STACK_TEMPLATE_DIRECTORY_VIEW, '/' . static::NAME . '/' . $template);
    }

    /**
     * Get a complete absolute filesystem path
     *
     * @param $asset
     *
     * @return mixed
     */
    public function getAssetPath($asset) {
        return STACK_ROOT_PATH . '/stack/www/' . static::NAME . $asset;
    }
    /**
     * Get a relative (web) path
     *
     * @param $path
     *
     * @return mixed
     */
    public function getAssetWebPath($asset) {
        return '/static/' . self::NAME . $asset;
    }
}