<?php
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
        $document = new \stackos\Document($manager, \stackos\ROOT_PATH_USERS . '/foo', \stackos\ROOT_UNAME);
        $document->setModule($manager->createModule('stackos.user', null));
        $manager->writeDocument($document);

        $module = $manager->readDocument(\stackos\ROOT_PATH_USERS . '/foo')->getModule();
        $this->assertTrue($module instanceof \stackos\module\UserModule);
    }

    public function testData() {
        #\lean\util\Dump::flat('==========');
        $manager = $this->getManager();
        // create the document
        $document = new \stackos\Document($manager, \stackos\ROOT_PATH_USERS . '/foo', \stackos\ROOT_UNAME);
        // create module, set data to it  and place it in document
        $module = $manager->createModule('stackos.user', null);
        $module->setData((object)array('foo'=>'bar'));
        $document->setModule($module);
        // write document
        $manager->writeDocument($document);
        //read document
        $doc = $manager->readDocument(\stackos\ROOT_PATH_USERS . '/foo');
        $this->assertTrue($doc->getModule() instanceof \stackos\module\UserModule);
        $this->assertEquals('bar', $document->getModule()->getData()->foo);
    }

    /**
     * @expectedException stackos\Exception_ModuleConflict
     */
    public function testModuleConflict() {
        $manager = $this->getManager();
        $manager->registerModuleFactory('stackos.user', function($data) {
            return new \stackos\module\UserModule($data);
        });
    }
}
