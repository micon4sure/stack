<?php
namespace stack\filesystem;
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
        $manager = $this->getManager();

        // write the document
        $document = new \stack\filesystem\File($manager, \stack\Root::ROOT_PATH_USERS . '/foo', \stack\Root::ROOT_UNAME);
        $module = new \stack\module\User('foo', \stack\Root::ROOT_UNAME, \stack\Root::ROOT_PATH_HOME . '/foo');
        $document->setModule($manager->createModule('stack.user', $module));
        $manager->writeFile($document);

        $module = $manager->readFile(\stack\Root::ROOT_PATH_USERS . '/foo')->getModule();
        $this->assertTrue($module instanceof \stack\module\User);
    }

    public function testData() {
        $manager = $this->getManager();
        // create the document
        $document = new \stack\filesystem\File($manager, \stack\Root::ROOT_USER_PATH_HOME . '/plain', \stack\Root::ROOT_UNAME);
        // create module, set data to it  and place it in document
        $module = $manager->createModule('stack.plain', new \stack\module\Plain(null));
        $module->setData((object)array('foo'=>'bar'));
        $document->setModule($module);
        // write document
        $manager->writeFile($document);
        //read document
        $doc = $manager->readFile(\stack\Root::ROOT_USER_PATH_HOME . '/plain');
        $this->assertTrue($doc->getModule() instanceof \stack\module\Plain);
        $this->assertEquals('bar', $document->getModule()->getData()->foo);
    }

    /**
     * Register a module that has already been registered.
     * @expectedException stack\filesystem\Exception_ModuleConflict
     */
    public function testModuleConflict() {
        $manager = $this->getManager();
        $manager->registerModuleFactory('stack.user', function($data) {

        });
    }
}
