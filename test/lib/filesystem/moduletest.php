<?php
namespace stack\fileSystem;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class ModuleTest extends StackOSTest {

    /**
     * Save a file with a module and see if its still there after reading
     */
    public function testContainsModule() {
        $system = $this->getFileSystem();

        // write the document
        $file = new \stack\filesystem\File(\stack\Root::ROOT_PATH_USERS . '/foo', \stack\Root::ROOT_UNAME);
        $user = new \stack\module\User('foo', \stack\Root::ROOT_UNAME, \stack\Root::ROOT_PATH_HOME . '/foo');
        $user->changePassword('asd');
        $file->setModule($system->createModule('stack.user', $user));
        $system->writeFile($file);

        $user = $system->readFile(\stack\Root::ROOT_PATH_USERS . '/foo')->getModule();
        $this->assertTrue($user instanceof \stack\module\User);
    }

    /**
     * Save a plain module and see if it still has its data after saving
     */
    public function testData() {
        $fileSystem = $this->getFileSystem();
        // create the document
        $file = new \stack\filesystem\File(\stack\Root::ROOT_USER_PATH_HOME . '/plain', \stack\Root::ROOT_UNAME);
        // create module, set data to it  and place it in document
        $plain = $fileSystem->createModule('stack.plain', new \stack\module\Plain(null));
        $plain->setData((object)array('foo'=>'bar'));
        $file->setModule($plain);
        // write document
        $fileSystem->writeFile($file);
        //read document
        $doc = $fileSystem->readFile(\stack\Root::ROOT_USER_PATH_HOME . '/plain');
        $this->assertTrue($doc->getModule() instanceof \stack\module\Plain);
        $this->assertEquals('bar', $file->getModule()->getData()->foo);
    }

    /**
     * Register a module that has already been registered.
     * @expectedException stack\filesystem\Exception_ModuleConflict
     */
    public function testModuleConflict() {
        $system = $this->getFileSystem();
        $system->registerModule('stack.user', function($data) {

        });
    }
}
