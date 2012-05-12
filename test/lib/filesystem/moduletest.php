<?php
namespace stack\fileSystem;
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

class ModuleTest extends StackOSTest {
    public function testContainsModule() {
        $system = $this->getFileSystem();

        // write the document
        $file = new \stack\filesystem\File(\stack\Root::ROOT_PATH_USERS . '/foo', \stack\Root::ROOT_UNAME);
        $user = new \stack\module\User('foo', \stack\Root::ROOT_UNAME, \stack\Root::ROOT_PATH_HOME . '/foo');
        $user->changePassword('asd');
        $file->setModule($system->createModule('stack.user', $user));
        $system->writeFile($file);

        $user = $system->readFile(\stack\Root::ROOT_PATH_USERS . '/foo')->getModule();
        $this->assertTrue($user instanceof \stack\module\User);
    }

    public function testData() {
        $fileSystem = $this->getFileSystem();
        // create the document
        $file = new \stack\filesystem\File(\stack\Root::ROOT_USER_PATH_HOME . '/plain', \stack\Root::ROOT_UNAME);
        // create module, set data to it  and place it in document
        $plain = $fileSystem->createModule('stack.plain', new \stack\module\Plain(null));
        $plain->setData((object)array('foo'=>'bar'));
        $file->setModule($plain);
        // write document
        $fileSystem->writeFile($file);
        //read document
        $doc = $fileSystem->readFile(\stack\Root::ROOT_USER_PATH_HOME . '/plain');
        $this->assertTrue($doc->getModule() instanceof \stack\module\Plain);
        $this->assertEquals('bar', $file->getModule()->getData()->foo);
    }

    /**
     * Register a module that has already been registered.
     * @expectedException stack\filesystem\Exception_ModuleConflict
     */
    public function testModuleConflict() {
        $system = $this->getFileSystem();
        $system->registerModule('stack.user', function($data) {

        });
    }
}
