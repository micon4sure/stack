<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class MigrationInit002 implements \lean\Migration {
    /**
     * Create the database and write system files
     * @throws \Exception
     */
    public function up() {
        $context = \lean\Registry::instance()->get('stack.context');
        $shell = $context->getShell();

        // create file for login module
        $context->pushSecurity(new \stack\security\PriviledgedSecurity());
        $path = '/system/web/login';
        $file = new \stack\filesystem\File($path, \stack\Root::ROOT_UNAME);
        $file->setModule(new \stack\web\module\Login());
        $file->addPermission(new \stack\security\Permission_All(Security_Priviledge::READ));
        $file->addPermission(new \stack\security\Permission_All(Security_Priviledge::EXECUTE));
        $shell->writeFile($file);

        $context->pullSecurity();
    }

    /**
     * Remove file
     */
    public function down() {
        $context = \lean\Registry::instance()->get('stack.context');
        $shell = $context->getShell();
        $context->pushSecurity(new \stack\security\PriviledgedSecurity());
        /**
         * @var Context $context;
         */
        try {
            $file = $shell->readFile('/system/web/login');
            $shell->deleteFile($file);
        } catch(\stack\fileSystem\Exception_FileNotFound $e) {
            // pass
            // might happen if a unit test crashes
        }
        $context->pullSecurity();
    }
}

return new MigrationInit002();