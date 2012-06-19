<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class ShellTest extends \stack\filesystem\StackOSTest {

    public function setUp() {
        parent::setUp();
        $this->context->pushSecurity(new \stack\security\PriviledgedSecurity());
    }

    /**
     * Test if a user can log in
     */
    public function testLogin() {
        // save a new user
        $uName = 'foo';
        $uPass = 'bar';
        $path = Root::ROOT_PATH_SYSTEM_RUN . '/adduser';

        $this->context->pushSecurity(new \stack\security\PriviledgedSecurity());
        $this->context->getShell()->execute($path, $uName, $uPass);

        // saved user's uname must match original uname
        $this->assertEquals(
            $uName,
            $this->context->getShell()->readFile(Root::ROOT_PATH_USERS . "/$uName")->getModule()->getUname()
        );

        // assert that user can log in
        $this->assertTrue($this->context->getShell()->login($uName, sha1($uPass)));
    }

    /**
     * @expectedException \stack\Exception_UserNotFound
     *
     */
    public function testUserNotFound() {
        $this->context->getShell()->login('asd', 'asd');
    }

    /**
     * Test that a database can be nuked and initialized
     */
    public function testNukeAndInit() {
        $this->context->getShell()->nuke();
        $this->context->getShell()->init();
    }

    public function testChangeDir() {
        $this->context->pushSecurity(new \stack\security\PriviledgedSecurity());
        $app = $this->application;
        $app->addUser('foo', 'bar');
        $this->context->getShell()->login('foo', sha1('bar'));

        $shell = $this->context->getShell();
        $shell->cd(Root::ROOT_PATH_USERS_ROOT);
        $this->assertEquals(
            Root::ROOT_PATH_USERS_ROOT,
            $shell->getCurrentWorkingFile()->getPath()
        );
    }
}
