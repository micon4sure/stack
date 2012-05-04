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

use \stack\filesystem\ROOT_USER_PATH_USERS_ROOT;

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
        $fs = new Filesystem($this->getManager());
        $fs->pushSecurity(new \stack\security\PriviledgedSecurity());
        $shell = Shell::instance($fs);
    }

    public function loginTest() {
        $shell = Shell::instance();
        $shell->login(Root::ROOT_UNAME, 'foo');
    }

    public function testChangeDir() {
        return;
        $shell->cd(\stack\Filesystem_Paths::ROOT_USER_PATH_USERS_ROOT);
        $this->assertEquals(
            \stack\Filesystem_Paths::ROOT_USER_PATH_USERS_ROOT,
            $shell->getPath()
        );
    }
}