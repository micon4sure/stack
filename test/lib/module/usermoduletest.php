<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class UserTest extends StackOSTest {
    /**
     * Test basic functionality
     */
    public function testUser() {
        $home = \stack\Root::ROOT_PATH_HOME . '/foo';
        $user = new \stack\module\User('foo', $home);
        $this->assertEquals('foo', $user->getUname());
        $this->assertEquals($home, $user->getHome());
    }

    /**
     * Test if a user is still uber after saving
     */
    public function testUber() {
        $fs = $this->getFileSystem();
        $this->context->pushSecurity(new \stack\security\PriviledgedSecurity());

        // check for plain set and get
        $home = \stack\Root::ROOT_PATH_HOME . '/foo';
        $user = new \stack\module\User('foo', $home);
        $user->changePassword('test');
        $user->setUber(true);

        // save in a document, read again, check for uber
        $this->assertEquals(true, $user->isUber());
        $file = new \stack\filesystem\File('/bar', $user->getUname());
        $file->setModule($user);
        $fs->writeFile($file);

        $file = $this->getFileSystem()->readFile('/bar');
        $this->assertTrue($file->getModule()->isUber());
    }

    /**
     * Test authentication of users
     */
    public function testAuth() {
        $user = new \stack\module\User('foo');
        $user->changePassword('bar');
        $this->assertTrue($user->auth(sha1('bar')));
    }
}