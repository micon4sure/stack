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
        // init default shell with priviledged security
        $this->context->getFilesystem()->pushSecurity(new \stack\security\PriviledgedSecurity());
    }

    public function testLogin() {
        // save a new user
        $uname = 'foo';
        $pass = 'bar';
        $path = Root::ROOT_PATH_SYSTEM_RUN . '/adduser';

        $this->context->getShell()->pushSecurity(new \stack\security\PriviledgedSecurity());
        $this->context->getShell()->execute($this->context, $path, $uname, $pass);

        // saved user's uname must match original uname
        $this->assertEquals(
            $uname,
            $this->context->getShell()->readFile(Root::ROOT_PATH_USERS . "/$uname")->getModule()->getUname()
        );

        // assert that user can login
        $this->assertTrue($this->context->getShell()->login($uname, $pass));
    }

    /*public function testChangeDir() {
        // go unpriviledged
        $fs = new Filesystem($this->getManager());
        $fs->pushSecurity(new \stack\security\PriviledgedSecurity());
        $shell = Shell::instance($fs);

        $shell->cd(Root::ROOT_USER_PATH_USERS_ROOT);
        return;
        $this->assertEquals(
            Root::ROOT_USER_PATH_USERS_ROOT,
            $shell->getPath()
        );
    }*/

    /**
     * @expectedException \stack\Exception_UserNotFound
     *
     */
    public function testUserNotFound() {
        $this->context->getShell()->login('asd', 'asd');
    }

    public function testNukeAndInit() {
        $this->context->getShell()->nuke();
        $this->context->getShell()->init();
    }

    public function testIsInCWF() {
        $user = new \stack\module\User('test');
        $shell = $this->context->getShell();
        return;
        #$shell->execute();
        $shell->login($user, 'foo');
        $shell->cd('/foo');
        $this->assertTrue($shell->isInCWF('/foo', '/foo/bar'));
        $this->assertFalse($shell->isInCWF('/foo/bar', '/foo'));
    }
}