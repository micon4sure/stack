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

class FileSystemTest extends StackOSTest {

    /**
     * Test if the initial files are being written
     */
    public function testInitialFiles() {
        $fs = $this->getFileSystem();

        // assert that initial files exist
        $this->assertTrue($fs->readFile(\stack\Root::ROOT_PATH) instanceof \stack\filesystem\File);
        $this->assertEquals(
            \stack\Root::ROOT_UNAME,
            $fs->readFile(\stack\Root::ROOT_PATH)->getOwner()
        );

        $this->assertTrue($fs->readFile(\stack\Root::ROOT_PATH_USERS) instanceof \stack\filesystem\File);
        $this->assertTrue($fs->readFile(\stack\Root::ROOT_PATH_USERS_ROOT) instanceof \stack\filesystem\File);
        $this->assertTrue($fs->readFile(\stack\Root::ROOT_PATH_GROUPS) instanceof \stack\filesystem\File);
        $this->assertTrue($fs->readFile(\stack\Root::ROOT_USER_PATH_HOME) instanceof \stack\filesystem\File);
    }

    /**
     * Test read* write* and deleteFile
     */
    public function testReadWriteDelete() {
        $fs = $this->getFileSystem();

        // write the document
        $document = new \stack\filesystem\File('/foo', \stack\Root::ROOT_UNAME);
        $fs->writeFile($document);

        // assert that the written document matches the read
        $this->assertEquals($document->getOwner(), $fs->readFile('/foo')->getOwner());
        $this->assertEquals($document->getPath(), $fs->readFile('/foo')->getPath());

        // delete file and assert that it's gone
        $fs->deleteFile($fs->readFile('/foo'));
        try {
            $fs->readFile('/foo');
            $this->fail();
        } catch(\stack\filesystem\Exception_FileNotFound $e) {
            // pass
        }
    }

     public function testCreateModule() {
        $fs = $this->getFileSystem();

        // test fs's ability to create valid modules
        // - user
        $user = new \stack\module\User('foo', '/foo');
        $module = $fs->createModule(\stack\module\User::NAME, (object)array('uname' => 'foo', 'home' => '/foo/bar', 'password' => 'foo'));
        $this->assertTrue($module instanceof \stack\module\User);
        // - group
        $module = $fs->createModule(\stack\module\Group::NAME, (object)array('gname' => 'qux'));
        $this->assertTrue($module instanceof \stack\module\Group);
    }

    public function testSaveThrice() {
        $fs = $this->getFileSystem();
        $file = new \stack\filesystem\File('/foo', \stack\Root::ROOT_UNAME);
        $fs->writeFile($file);
        $fs->writeFile($file);
        $fs->writeFile($file);
    }
}