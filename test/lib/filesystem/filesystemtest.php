<?php
namespace stack\filesystem;
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

use \stack\filesystem\ROOT_UNAME;

class FileSystemTest extends StackOSTest {
    /**
     * Test read* write* and deleteFile
     */
    public function testReadWriteDelete() {
        $system = new \stack\Filesystem($this->getManager());

        // write the document
        $document = $system->createFile('/foo', ROOT_UNAME);
        $document->setOwner('test');
        $document->save();

        // assert that the written document matches the read
        $this->assertEquals(
            $document->getOwner(),
            $system->readFile('/foo')->getOwner()
        );
        $this->assertEquals(
            $document->getPath(),
            $system->readFile('/foo')->getPath()
        );

        // delete file and assert that it's gone
        $document->delete();
        try {
            $system->readFile('/foo');
            $this->fail();
        } catch(\stack\filesystem\Exception_FileNotFound $e) {
            // pass
        }
    }
}
