<?php
namespace stack\module\run;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class AddUser extends \stack\module\BaseModule {

    const NAME = \stack\Root_Modules::MODULE_ADDUSER;

    protected function export($data) {
        return $data;
    }

    public function run(\stack\Context $context, $uname, $password) {
        $user = new \stack\module\User($uname);
        $user->changePassword($password);
        $file = new \stack\filesystem\File(\stack\Root::ROOT_PATH_USERS . '/' . $user->getUname(), \stack\Root::ROOT_UNAME);
        $file->setModule($user);
        $context->getShell()->writeFile($file);

        $home = $file = new \stack\filesystem\File($user->getHome(), $user->getUname());
        $context->getShell()->writeFile($home);
    }
}