<?php
namespace stack\test;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

use stack\File;
use stack\Module_Default;

class FileTest extends StackTest {
    /**
     * Make sure getPath returns the id of the document
     */
    public function testPath() {
        $doc = $this->createTestDocument();
        $path = $doc->_id;
        $file = new File($doc, new Module_Default(new \stdClass()));

        $this->assertEquals($path, $file->getPath());
    }
}