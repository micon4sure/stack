<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class Web002 implements \lean\Migration {
    /**
     * Create the database and write system files
     * @throws \Exception
     */
    public function up() {
        /**
         * @var \stack\Context $context
         */
        $context = \lean\Registry::instance()->get('stack.context');
        $shell = $context->getShell();

        // LOGIN
        $context->pushSecurity(new \stack\security\PriviledgedSecurity());
        $file = $shell->readFile(\stack\Root::ROOT_PATH);
        $file->setModule(new \stack\module\web\Login());
        $file->addPermission(new \stack\security\Permission_All(\stack\Security_Priviledge::READ));
        $file->addPermission(new \stack\security\Permission_All(\stack\Security_Priviledge::EXECUTE));
        $shell->writeFile($file);

        // STATIC FILES
        $context->pushSecurity(new \stack\security\PriviledgedSecurity());
        $path = '/static';
        $file = new \stack\filesystem\File($path, \stack\Root::ROOT_UNAME);
        $file->setModule(new \stack\module\web\StaticFiles());
        $file->addPermission(new \stack\security\Permission_All(\stack\Security_Priviledge::READ));
        $file->addPermission(new \stack\security\Permission_All(\stack\Security_Priviledge::EXECUTE));
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
            $file = $shell->readFile(\stack\Root::ROOT_PATH);
            $shell->deleteFile($file);
        } catch(\stack\fileSystem\Exception_FileNotFound $e) {
            // pass
            // might happen if a unit test crashes
        }
        $context->pullSecurity();
    }
}

return new Web002();