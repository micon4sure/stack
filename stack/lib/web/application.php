<?php
namespace stack\web;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class Application extends \stack\Application {

    /**
     * @var Request
     */
    private $request;

    /**
     * @var \lean\Session
     */
    private $session;

    /**
     * @var \stack\module\User
     */
    private $user;

    /**
     * Override parent constructor to start session
     *
     * @param \stack\Context $context
     */
    public function __construct(\stack\Context $context) {
        parent::__construct($context);
        $this->session = new \lean\Session('stack.web.application');
        $context->getEnvironment()->setDefaultSettings($this->getDefaultSettings());
        $this->request = new Request();
    }

    /**
     * Register web bundle
     */
    public function init() {
        parent::init();
        $this->registerBundle(new \stack\Bundle_Web($this));
    }

    /**
     * Run the web application.
     * If target is '/login', try to log in the user.
     * Elsewise, get the user from the filesystem and execute the module inside
     *
     * @param $target
     */
    public function run() {
        // push user security if user is logged in, unpriviledged otherwise
        if($this->getContext()->getUser()) {
            // push user security
            $this->getContext()->pushSecurity(new \stack\security\DefaultSecurity($this->getContext()->getUser()));
        }
        else {
            $this->getContext()->pushSecurity(new \stack\security\AnonymousSecurity());
        }

        // dispatch request, catch output in buffer and print if debug is on
        try {
            ob_start();
            $response = $this->dispatchStackRequest();
            $ob = ob_get_clean();
            $response->send();

            $this->getContext()->pullSecurity();
        } catch(\Exception $e) {
            $this->getContext()->pullSecurity();
            $ob = ob_get_clean();

            Response_Exception::fromException($e)->send();
        }

        if($this->getEnvironment()->isDebug() && strlen($ob)) {
            echo '<h1>DEBUG</h1>';
            echo $ob;
        }
    }

    /**
     * Create the actual response by module
     *
     * @return Response|Response_HTTP404
     * @throws \stack\filesystem\Exception_FileNotFound
     * @throws \stack\Exception
     * @throws \Exception
     */
    protected function dispatchStackRequest() {
        // try to read requested file
        try {
            if(\lean\Text::left($this->request->getPath(), \stack\module\web\StaticFiles::LOCATION) == \stack\module\web\StaticFiles::LOCATION) {
                // static file has been requested.
                $file = $this->getShell()->readFile(\stack\module\web\StaticFiles::LOCATION);
            } else {
                // dynamic stack file
                $file = $this->getShell()->readFile($this->request->getPath());
            }
        }
        catch(\stack\filesystem\Exception_FileNotFound $e) {
            if($this->getEnvironment()->get('debug')) {
                throw $e;
            } else {
                return new Response_HTTP404("File at " . $this->request->getPath() . " not found");
            }
        }

        // run requested file
        try {
            //TODO plain json if file has no module
            // initialize module
            $module = $file->getModule();
            if(!$module instanceof \stack\module\BaseModule_Abstract) {
                $module = new \stack\module\web\DirectoryModule();
            }
            $module->init($this);
            if($module instanceof \stack\module\web\BaseModule) {
                $module->initRequest($this->request);
            }

            // run module
            $response = $module->run($this->getContext(), $this->request);
            if(!$response instanceof Response) {
                throw new \stack\Exception("Malformed response from module '" . get_class($module) . "'");
            }
            return $response;
        }
        catch(\Exception $e) {
            if($this->getEnvironment()->get('debug')) {
                throw $e;
            } else {
                return new \stack\web\Response(500, 'Internal Server Error.');
            }
        }
    }

    /**
     * Save user to session
     *
     * @param \stack\module\User $user
     */
    protected function setUser(\stack\module\User $user) {
        $this->session->user = $user;
    }

    /**
     * Retrieve user from session
     *
     * @return \stack\module\User
     */
    public function getUser() {
        return $this->session->user;
    }

    /**
     * Get default settings
     *
     * @return array default settings
     */
    protected function getDefaultSettings() {
        $settings = array();
        // environment
        $settings['stack.environment.name'] = 'development';
        $settings['stack.environment.file'] = STACK_APPLICATION_ROOT . '/config/environment.ini';
        $settings['stack.web.directory'] = STACK_ROOT . '/www';
        $settings['stack.application.web.directory'] = STACK_APPLICATION_ROOT . '/www';
        // templates
        $settings['stack.template.directory'] = STACK_APPLICATION_ROOT . '/template';
        $settings['stack.template.document.directory'] = STACK_APPLICATION_ROOT . '/template/document';
        $settings['stack.template.layout.directory'] = STACK_APPLICATION_ROOT . '/template/layout';
        $settings['stack.template.view.directory'] = STACK_APPLICATION_ROOT . '/template/view';
        $settings['stack.template.partial.directory'] = STACK_APPLICATION_ROOT . '/template/partial';
        return $settings;
    }

    /**
     * Get an application setting
     *
     * @param string $setting
     * @return mixed setting value
     * @throws Exception
     */
    public function getSetting($setting) {
        return $this->getEnvironment()->get($setting);
    }
}