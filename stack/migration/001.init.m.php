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
            Root::ROOT_PATH_SYSTEM,
            Root::ROOT_PATH_RUN,
            Root::ROOT_PATH_USERS,
            Root::ROOT_PATH_GROUPS,
            Root::ROOT_PATH_HOME,
            Root::ROOT_PATH_HOME . '/' . Root::ROOT_UNAME
        );
        $context->pushSecurity(new \stack\security\PriviledgedSecurity());

        $file = new \stack\filesystem\File(Root::ROOT_PATH, Root::ROOT_UNAME);
        $shell->writeFile($file);
        $file->addPermission(new \stack\security\Permission_All(Security_Priviledge::READ));

        try {
            foreach ($files as $path) {
                $file = new \stack\filesystem\File($path, Root::ROOT_UNAME);
                $shell->writeFile($file);
            }

            // create root user file + module
            $file = new \stack\filesystem\File(Root::ROOT_PATH_USERS_ROOT, Root::ROOT_UNAME);
            $user = new \stack\module\User(Root::ROOT_UNAME, Root::ROOT_PATH_HOME . '/' . Root::ROOT_UNAME);
            $password = sha1(uniqid());
            $user->changePassword($password);
            $file->setModule($user);
            $shell->writeFile($file);

            file_put_contents(STACK_APPLICATION_ROOT . '/rootpw', $password);

            // create system run files
            $modules = array(
                'adduser' => new \stack\module\run\AddUser($shell),
                'deluser' => new \stack\module\run\DelUser($shell),
                'addgroup' => new \stack\module\run\AddGroup($shell),
                'delgroup' => new \stack\module\run\DelGroup($shell),
            );
            foreach($modules as $name => $module) {
                $path = Root::ROOT_PATH_RUN . "/$name";
                $file = new \stack\filesystem\File($path, Root::ROOT_UNAME);
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
        } catch(\stack\filesystem\Exception_FileNotFound $e) {
            // pass
            // might happen if a unit test crashes
        }
    }
}

return new MigrationInit001();