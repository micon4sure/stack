<?php
namespace stack\fileSystem;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
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

    /**
     * Test that module creation works as expected
     */
     public function testCreateModule() {
        $fs = $this->getFileSystem();

        // test fs's ability to create valid modules
        // - user
        $user = new \stack\module\User('foo', '/foo');
        $module = $fs->createModule(\stack\module\User::NAME, (object)array('uName' => 'foo', 'home' => '/foo/bar', 'uPass' => 'foo'));
        $this->assertTrue($module instanceof \stack\module\User);
        // - group
        $module = $fs->createModule(\stack\module\Group::NAME, (object)array('gname' => 'qux'));
        $this->assertTrue($module instanceof \stack\module\Group);
    }

    /**
     * Test saving a file thrice
     */
    public function testSaveThrice() {
        $fs = $this->getFileSystem();
        $file = new \stack\filesystem\File('/foo', \stack\Root::ROOT_UNAME);
        $fs->writeFile($file);
        $fs->writeFile($file);
        $fs->writeFile($file);
    }
}