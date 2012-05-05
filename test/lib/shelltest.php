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

class ShellTestAccess  extends \stack\filesystem\StackOSTest {
    /**
     * @expectedException \stack\filesystem\Exception_NeedAccess
     */
    public function testNeedAccess() {
        Shell::instance();
    }

}

class ShellTest extends \stack\filesystem\StackOSTest {

    public function setUp() {
        parent::setUp();
        // init default shell with priviledged security
        $fs = new Filesystem($this->getManager());
        $fs->pushSecurity(new \stack\security\PriviledgedSecurity());
        Shell::instance($fs);
    }

    public function testLogin() {
        return;
        // save a new user
        $user = new \stack\module\User('test');
        $user->setPassword('foo');
        Shell::instance()->execute(Root::ROOT_PATH_SYSTEM . '/adduser', $user);

        $this->assertTrue(Shell::instance()->login('kos', 'foo'));
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
        Shell::instance()->login('asd', 'asd');
    }
}