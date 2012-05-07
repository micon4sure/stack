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

class UserTest extends StackOSTest {
    public function testUser() {
        $home = \stack\Root::ROOT_PATH_HOME . '/foo';
        $user = new \stack\module\User('foo', $home);
        $this->assertEquals('foo', $user->getUname());
        $this->assertEquals($home, $user->getHome());
    }

    public function testUber() {
        $system = new \stack\Filesystem($this->getFileManager());
        $system->pushSecurity(new \stack\security\PriviledgedSecurity());

        // check for plain set and get
        $home = \stack\Root::ROOT_PATH_HOME . '/foo';
        $user = new \stack\module\User('foo', $home);
        $user->setPassword('test');
        $user->setUber(true);

        // save in a document, read again, check for uber
        $this->assertEquals(true, $user->isUber());
        $file = new \stack\filesystem\File('/bar', $user->getUname());
        $file->setModule($user);
        $system->writeFile($file);

        $file = $this->getFileManager()->readFile('/bar');
        \lean\util\Dump::deep($file->getModule());
        $this->assertTrue($file->getModule()->isUber());
    }

    public function testAuth() {
        $user = new \stack\module\User('foo');
        $user->changePassword('bar');
        $this->assertTrue($user->auth('bar'));
    }
}