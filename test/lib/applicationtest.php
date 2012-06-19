<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class ApplicationTest extends StackOSTest {
    /**
     * Test that a user can be created and deleted via applications conenience methods
     */
    public function testUser() {
        // push a priviledged security to be used in the application
        $this->context->pushSecurity(new \stack\security\PriviledgedSecurity());

        // add a new user
        $this->application->addUser('test', 'foo');

        // read the user and assert its type
        $user = $this->application->getUser('test');
        $this->assertTrue($user instanceof \stack\module\User);

        // delete the user
        $this->application->delUser('test');

        // expect exception
        try {
            $this->application->getUser('test');
            $this->fail();
        } catch(Exception_UserNotFound $e) {
            // pass
        }
    }

    /**
     * Test that a group can be created and deleted via applications conenience methods
     */
    public function testGroup() {
        // push a priviledged security to be used in the application
        $this->context->pushSecurity(new \stack\security\PriviledgedSecurity());

        // push test user as founder for test group
        $application = $this->application;
        $application->addUser('user', 'foo');

        // add a new group
        $application = $this->application;
        $application->addGroup('test', 'user');

        // read the group and assert its type
        $group = $application->getGroup('test');
        $this->assertTrue($group instanceof \stack\module\Group);

        // delete the group
        $application->delGroup('test');

        // expect exception
        try {
            $application->getGroup('test');
            $this->fail();
        } catch(Exception_GroupNotFound $e) {
            // pass
        }
    }
}