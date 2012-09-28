<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class RunTest extends \stack\StackOSTest {
    /**
     * Test adduser and deluser modules
     */
    public function testUser() {
        // save a new user
        $uname = 'foo';
        $pass = 'bar';
        $path = \stack\Root::ROOT_PATH_RUN . '/adduser';

        $this->context->pushSecurity(new \stack\security\PriviledgedSecurity());
        $this->context->getShell()->execute($path, $uname, $pass);

        // saved user's uname must match original uname
        $this->assertEquals(
            $uname,
            $this->context->getShell()->readFile(\stack\Root::ROOT_PATH_USERS . "/$uname")->getModule()->getUname()
        );

        $path = \stack\Root::ROOT_PATH_RUN . '/deluser';
        $this->context->getShell()->execute($path, $uname);

        try {
            $this->context->getShell()->readFile(\stack\Root::ROOT_PATH_USERS . "/$uname");
            $this->fail();
        } catch(\stack\filesystem\Exception_FileNotFound $e) {
            // pass
        }
    }

    /**
     * test addgroup and delgroup modules
     */
    public function testGroup() {
        // save a new user
        $gname = 'foo';
        $path = \stack\Root::ROOT_PATH_RUN . '/addgroup';

        $this->context->pushSecurity(new \stack\security\PriviledgedSecurity());
        $this->context->getShell()->execute($path, $gname);

        // saved user's uname must match original uname
        $this->assertEquals(
            $gname,
            $this->context->getShell()->readFile(\stack\Root::ROOT_PATH_USERS . "/$gname")->getModule()->getGname()
        );

        $path = \stack\Root::ROOT_PATH_RUN . '/deluser';
        $this->context->getShell()->execute($path, $gname);

        try {
            $this->context->getShell()->readFile(\stack\Root::ROOT_PATH_USERS . "/$gname");
            $this->fail();
        } catch(\stack\filesystem\Exception_FileNotFound $e) {
            // pass
        }
    }
}