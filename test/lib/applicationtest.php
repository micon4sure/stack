<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
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