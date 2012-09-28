<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class ShellTest extends StackOSTest {

    public function setUp() {
        parent::setUp();
        $this->context->pushSecurity(new \stack\security\PriviledgedSecurity());
    }

    /**
     * Test read* write* and deleteFile
     */
    public function testReadWriteDelete() {
        $shell = $this->context->getShell();

        // write the file
        $file = new \stack\filesystem\File('/foo', \stack\Root::ROOT_UNAME);
        $shell->writeFile($file);

        // assert that the written document matches the read
        $this->assertEquals($file->getOwner(), $shell->readFile('/foo')->getOwner());
        $this->assertEquals($file->getPath(), $shell->readFile('/foo')->getPath());

        // delete file and assert that it's gone
        $shell->deleteFile($shell->readFile('/foo'));
        try {
            $shell->readFile('/foo');
            $this->fail();
        } catch(\stack\filesystem\Exception_FileNotFound $e) {
            // pass
        }
    }

    /**
     * Test that module creation works as expected
     */
    public function testCreateModule() {
        $shell = $this->context->getShell();

        // test shells ability to create valid modules
        // - user
        $user = new \stack\module\User('foo', '/foo');
        $module = $shell->createModule(\stack\module\User::NAME, (object)array('uName' => 'foo', 'home' => '/foo/bar', 'uPass' => 'foo'));
        $this->assertTrue($module instanceof \stack\module\User);
        // - group
        $module = $shell->createModule(\stack\module\Group::NAME, (object)array('gname' => 'qux'));
        $this->assertTrue($module instanceof \stack\module\Group);
    }

}
