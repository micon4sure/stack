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


class RunTest extends \stack\StackOSTest {
    public function testUser() {
        // save a new user
        $uname = 'foo';
        $pass = 'bar';
        $path = \stack\Root::ROOT_PATH_SYSTEM_RUN . '/adduser';

        $this->context->pushSecurity(new \stack\security\PriviledgedSecurity());
        $this->context->getShell()->execute($this->context, $path, $uname, $pass);

        // saved user's uname must match original uname
        $this->assertEquals(
            $uname,
            $this->context->getShell()->readFile(\stack\Root::ROOT_PATH_USERS . "/$uname")->getModule()->getUname()
        );

        $path = \stack\Root::ROOT_PATH_SYSTEM_RUN . '/deluser';
        $this->context->getShell()->execute($this->context, $path, $uname);

        try {
            $this->context->getShell()->readFile(\stack\Root::ROOT_PATH_USERS . "/$uname");
            $this->fail();
        } catch(\stack\filesystem\Exception_FileNotFound $e) {
            // pass
        }
    }

    public function testGroup() {
        // save a new user
        $gname = 'foo';
        $path = \stack\Root::ROOT_PATH_SYSTEM_RUN . '/addgroup';

        $this->context->pushSecurity(new \stack\security\PriviledgedSecurity());
        $this->context->getShell()->execute($this->context, $path, $gname);

        // saved user's uname must match original uname
        $this->assertEquals(
            $gname,
            $this->context->getShell()->readFile(\stack\Root::ROOT_PATH_USERS . "/$gname")->getModule()->getGname()
        );

        $path = \stack\Root::ROOT_PATH_SYSTEM_RUN . '/deluser';
        $this->context->getShell()->execute($this->context, $path, $gname);

        try {
            $this->context->getShell()->readFile(\stack\Root::ROOT_PATH_USERS . "/$gname");
            $this->fail();
        } catch(\stack\filesystem\Exception_FileNotFound $e) {
            // pass
        }
    }
}