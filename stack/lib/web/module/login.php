<?php
namespace stack\web\module;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class Login extends \stack\module\BaseModule {

    const NAME = 'stack.web.login';

    public function run(\stack\Context $context, \stack\web\Request $request) {
        if(!$request->isXHR()) {
            // initial request, show form
            $template = new \lean\Template(STACK_ROOT . '/stack/template/login.php');
            $template->rootpw = '71e7e7cd158bbf8342a4cc6aee2f3503c1c57d8c';
            $markup = $template->render();
            $response = new \stack\web\Response_HTML($markup);
        } else {
            // form has been sent via ajax.
            $context->pushSecurity(new \stack\security\PriviledgedSecurity());
            try {
                // read user and create response
                $user = $context->getShell()->readFile(\stack\Root::ROOT_PATH_USERS . '/' . $request->post('uName'));
                /**
                 * @var \stack\module\User $user
                 */
                $loggedIn = $user->auth($request->post('uPass'));
                $response = new \stack\web\Response_JSON(['authorized' => $loggedIn]);

                // if user is actually auth'd, save it in session
                if($loggedIn) {
                    //FIXME LOGIN!
                }
            }
            catch(\stack\fileSystem\Exception_FileNotFound $e) {
                $response = new \stack\web\Response_JSON(['authorized' => false]);
            }
            $context->pullSecurity();
        }

        return $response;
    }
}