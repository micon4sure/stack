<?php
namespace stackos;
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

class DocumentManagerTest extends StackOSTest {

    /**
     * Test if the initial files are being written
     */
    public function testInitialFiles() {
        $manager = $this->getManager();

        // assert that initial files exist
        $this->assertTrue($manager->readDocument(\stackos\ROOT_PATH) instanceof \stackos\Document);
        $this->assertEquals(\stackos\ROOT_UNAME, $manager->readDocument(\stackos\ROOT_PATH)->getOwner());

        $this->assertTrue($manager->readDocument(\stackos\ROOT_USER_PATH_USERS) instanceof \stackos\Document);
        $this->assertTrue($manager->readDocument(\stackos\ROOT_USER_PATH_USERS_ROOT) instanceof \stackos\Document);
        $this->assertTrue($manager->readDocument(\stackos\ROOT_USER_PATH_GROUPS) instanceof \stackos\Document);
        $this->assertTrue($manager->readDocument(\stackos\ROOT_USER_PATH_HOME) instanceof \stackos\Document);
    }

    /**
     * Test read* write* and deleteDocument
     */
    public function testReadWriteDelete() {
        $manager = $this->getManager();

        // write the document
        $document = new \stackos\Document($manager, '/foo', \stackos\ROOT_UNAME);
        $manager->writeDocument($document);

        // assert that the written document matches the read
        $this->assertEquals($document->getOwner(), $manager->readDocument('/foo')->getOwner());
        $this->assertEquals($document->getPath(), $manager->readDocument('/foo')->getPath());

        // delete file and assert that it's gone
        $manager->deleteDocument($manager->readDocument('/foo'));
        try {
            $manager->readDocument('/foo');
            $this->fail();
        } catch(\stackos\Exception_DocumentNotFound $e) {
            // pass
        }
    }

    /**
     * Test Document's save method
     */
    public function testSave() {
        $manager = $this->getManager();
        $document = new \stackos\Document($manager, '/foo', \stackos\ROOT_UNAME);
        $document->save();

        // assert that the written document matches the read
        $this->assertEquals($document->getOwner(), $manager->readDocument('/foo')->getOwner());
        $this->assertEquals($document->getPath(), $manager->readDocument('/foo')->getPath());
    }

    public function testCreateModule() {
        $manager = $this->getManager();

        // test manager's ability to create valid modules
        // - user
        $user = new \stackos\module\UserModule('foo', '/foo');
        $module = $manager->createModule(\stackos\module\UserModule::NAME, (object)array('uname' => 'foo', 'home' => '/foo/bar'));
        $this->assertTrue($module instanceof \stackos\module\UserModule);
        // - group
        $module = $manager->createModule(\stackos\module\GroupModule::NAME, (object)array('gname' => 'qux'));
        $this->assertTrue($module instanceof \stackos\module\GroupModule);
    }
}