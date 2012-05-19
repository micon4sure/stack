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
        $uname = 'foo';
        $pass = 'bar';
        $path = Root::ROOT_PATH_SYSTEM_RUN . '/adduser';

        $this->context->pushSecurity(new \stack\security\PriviledgedSecurity());
        $this->context->getShell()->execute($path, $uname, $pass);

        // saved user's uname must match original uname
        $this->assertEquals(
            $uname,
            $this->context->getShell()->readFile(Root::ROOT_PATH_USERS . "/$uname")->getModule()->getUname()
        );

        // assert that user can log in
        $this->assertTrue($this->context->getShell()->login($uname, $pass));
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
        $this->context->getShell()->login('foo', 'bar');

        $shell = $this->context->getShell();
        $shell->cd(Root::ROOT_PATH_USERS_ROOT);
        $this->assertEquals(
            Root::ROOT_PATH_USERS_ROOT,
            $shell->getCurrentWorkingFile()->getPath()
        );
    }
}
