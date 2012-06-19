<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class GroupTest extends StackOSTest {
    /**
     * Test that the name of a group can be changed
     */
    public function testNamechange() {
        $fs = $this->getFileSystem();
        $this->context->pushSecurity(new \stack\security\PriviledgedSecurity());

        $gname = 'foo';
        $path = '/bar';
        // check for plain set and get
        $group = new module\Group($gname);
        // save in a document, read again, check for correct gname
        $this->assertEquals($gname, $group->getGname());
        $file = new \stack\filesystem\File($path, $group->getGname());
        $file->setModule($group);
        $this->assertEquals(
            $gname,
            $file->getModule()->getGname()
        );
        $fs->writeFile($file);

        // reread document, check gname
        $file = $this->getFileSystem()->readFile($path);
        $this->assertEquals(
            $gname,
            $file->getModule()->getGname()
        );

        // again, with new uname
        $new = 'qux';
        $file = $this->getFileSystem()->readFile($path);
        $file->getModule()->setGname($new);
        $fs->writeFile($file);
        // reread document, check new
        $file = $this->getFileSystem()->readFile($path);
        $this->assertEquals(
            $new,
            $file->getModule()->getGname()
        );
    }
}