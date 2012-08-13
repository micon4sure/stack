<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class ShellTest extends \stack\filesystem\StackOSTest {

    public function setUp() {
        parent::setUp();
        $this->context->pushSecurity(new \stack\security\PriviledgedSecurity());
    }

    /**
     * Test read* write* and deleteFile
     */
    public function testReadWriteDelete() {
        $shell = $this->context->getShell();

        // write the document
        $document = new \stack\filesystem\File('/foo', \stack\Root::ROOT_UNAME);
        $shell->writeFile($document);

        // assert that the written document matches the read
        $this->assertEquals($document->getOwner(), $shell->readFile('/foo')->getOwner());
        $this->assertEquals($document->getPath(), $shell->readFile('/foo')->getPath());

        // delete file and assert that it's gone
        $shell->deleteFile($shell->readFile('/foo'));
        try {
            $shell->readFile('/foo');
            $this->fail();
        } catch(\stack\filesystem\Exception_FileNotFound $e) {
            // pass
        }
    }
}
