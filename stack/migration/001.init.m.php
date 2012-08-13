<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class MigrationInit001 implements \lean\Migration {
    /**
     * Create the database and write system files
     * @throws \Exception
     */
    public function up() {
        $context = \lean\Registry::instance()->get('stack.context');
        $shell = $context->getShell();
        $shell->init();

        // create initial files
        $files = array(
            \stack\Root::ROOT_PATH,
            \stack\Root::ROOT_PATH_SYSTEM,
            \stack\Root::ROOT_PATH_SYSTEM_RUN,
            \stack\Root::ROOT_PATH_USERS,
            \stack\Root::ROOT_PATH_GROUPS,
            \stack\Root::ROOT_USER_PATH_HOME,
        );
        $context->pushSecurity(new \stack\security\PriviledgedSecurity());
        try {
            foreach ($files as $path) {
                $file = new \stack\filesystem\File($path, \stack\Root::ROOT_UNAME);
                $shell->writeFile($file);
            }

            // create root user file + module
            $file = new \stack\filesystem\File(\stack\Root::ROOT_PATH_USERS_ROOT, \stack\Root::ROOT_UNAME);
            $user = new \stack\module\User(\stack\Root::ROOT_UNAME, \stack\Root::ROOT_USER_PATH_HOME);
            $password = sha1(uniqid());
            $user->changePassword($password);
            $file->setModule($user);
            $shell->writeFile($file);

            file_put_contents(STACK_ROOT . '/rootpw', "This is the automatically created root password: $password\nIt is to be changed ASAP, then this file is to be deleted.");

            // create system run files
            $modules = array(
                'adduser' => new \stack\module\run\AddUser($shell),
                'deluser' => new \stack\module\run\DelUser($shell),
                'addgroup' => new \stack\module\run\AddGroup($shell),
                'delgroup' => new \stack\module\run\DelGroup($shell),
            );
            foreach($modules as $name => $module) {
                $path = Root::ROOT_PATH_SYSTEM_RUN . "/$name";
                $file = new \stack\filesystem\File($path, \stack\Root::ROOT_UNAME);
                $file->setModule($module);
                $shell->writeFile($file);
            }
        } catch(\Exception $e) {
            $context->pullSecurity();
            throw $e;
        }
        $context->pullSecurity();
    }

    /**
     * Nuke database
     */
    public function down() {
        $context = \lean\Registry::instance()->get('stack.context');
        $context->pushSecurity(new \stack\security\PriviledgedSecurity());
        $shell = $context->getShell();
        try {
            $shell->nuke();
        } catch(\stack\fileSystem\Exception_FileNotFound $e) {
            // pass
            // might happen if a unit test crashes
        }
    }
}

return new MigrationInit001();