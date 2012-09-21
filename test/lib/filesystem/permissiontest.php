<?php
namespace stack\filesystem;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */
use stack\Security_Priviledge;

class PermissionTests extends StackOSTest {

    /**
     * Test read* write* and deleteFile
     */
    public function testReadWriteDelete() {
        $fs = $this->getFileSystem();
        $this->context->pushSecurity(new \stack\security\PriviledgedSecurity());

        // write the file
        $file = $fs->createFile('/foo', \stack\Root::ROOT_UNAME);
        $file->setOwner('test');
        $fs->writeFile($file);

        // assert that the written document matches the read
        $this->assertEquals(
            $file->getOwner(),
            $fs->readFile('/foo')->getOwner()
        );
        $this->assertEquals(
            $file->getPath(),
            $fs->readFile('/foo')->getPath()
        );

        // delete file and assert that it's gone
        $fs->deleteFile($file);
        try {
            $fs->readFile('/foo');
            $this->fail();
        } catch(\stack\filesystem\Exception_FileNotFound $e) {
            // pass
        }
    }
}