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
     * Override parent constructor to start session
     *
     * @param \stack\Context $context
     */
    public function __construct(\stack\Context $context) {
        parent::__construct($context);
        $this->session = new \lean\Session('stack.web.application');
    }

    /**
     * Run the web application.
     * If target is '/login', try to log in the user.
     * Elsewise, get the user from the filesystem and execute the module inside
     *
     * @param $target
     * @param $request
     */
    public function run($target, $request) {
        $this->request = new Request($request);
        // intercept path /login to dispatch login event
        if($target == '/login') {
            $response = $this->login();
        } else {
            // run stack
            $this->renderInterface();
        }

        $response->send();
    }

    /**
     * Try to log the user in.
     * Send 401 if user/password combo wrong or user does not exist entirely.
     *
     * @return Response_JSON
     */
    protected function login() {
        $this->getContext()->pushSecurity(new \stack\security\PriviledgedSecurity());
        try {
            $loggedIn = $this->getShell()->login($this->request->uName, $this->request->uPass);
            $this->getContext()->pullSecurity();
            if($loggedIn) {
                $this->session->user = $this->request->uName;
                return new Response_HTTP302($this->getShell()->getCurrentUser()->getHome());
            }
        } catch(\stack\Exception_UserNotFound $e) {
            $this->getContext()->pullSecurity();
        }

        return new Response_JSON(['message' => 'Access denied.'], 401);
    }
}