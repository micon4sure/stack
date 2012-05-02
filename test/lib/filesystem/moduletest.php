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
        $document = new \stack\filesystem\Document($manager, \stack\filesystem\ROOT_USER_PATH_USERS . '/foo', \stack\filesystem\ROOT_UNAME);
        $module = new \stack\filesystem\module\UserModule('foo', \stack\filesystem\ROOT_UNAME, \stack\filesystem\ROOT_PATH_HOME . '/foo');
        $document->setModule($manager->createModule('stack.user', $module));
        $manager->writeDocument($document);

        $module = $manager->readDocument(\stack\filesystem\ROOT_USER_PATH_USERS . '/foo')->getModule();
        $this->assertTrue($module instanceof \stack\filesystem\module\UserModule);
    }

    public function testData() {
        $manager = $this->getManager();
        // create the document
        $document = new \stack\filesystem\Document($manager, \stack\filesystem\ROOT_USER_PATH_HOME . '/plain', \stack\filesystem\ROOT_UNAME);
        // create module, set data to it  and place it in document
        $module = $manager->createModule('stack.plain', new \stack\filesystem\module\PlainModule(null));
        $module->setData((object)array('foo'=>'bar'));
        $document->setModule($module);
        // write document
        $manager->writeDocument($document);
        //read document
        $doc = $manager->readDocument(\stack\filesystem\ROOT_USER_PATH_HOME . '/plain');
        $this->assertTrue($doc->getModule() instanceof \stack\filesystem\module\PlainModule);
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
