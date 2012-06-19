<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class ContextTest extends StackOSTest {
    /**
     * Test that a context will always deliver the same instance for env, shell and fs
     */
    public function testContext() {
        // get the preconfigured context from StackOSTest
        $context = $this->context;
        // assert that context das not make up new instanced as it goes.
        $this->assertTrue($context->getEnvironment() === $context->getEnvironment());
        $this->assertTrue($context->getShell() === $context->getShell());
        $this->assertTrue($context->getFileSystem() === $context->getFileSystem());
    }

    /**
     * Test read* write* and deleteFile
     */
    public function testReadWriteDelete() {
        $context = $this->context;
        $context->pushSecurity(new \stack\security\PriviledgedSecurity());

        // write the file
        $file = new \stack\fileSystem\File('/foo', \stack\Root::ROOT_UNAME);
        $file->setOwner('test');
        $context->getShell()->writeFile($file);

        // assert that the written document matches the read
        $this->assertEquals(
            $file->getOwner(),
            $context->getShell()->readFile('/foo')->getOwner()
        );
        $this->assertEquals(
            $file->getPath(),
            $context->getShell()->readFile('/foo')->getPath()
        );

        // delete file and assert that it's gone
        $context->getShell()->deleteFile($file);
        try {
            $context->getShell()->readFile('/foo');
            $this->fail();
        } catch(\stack\filesystem\Exception_FileNotFound $e) {
            // pass
        }
    }
}