<?php
namespace sotest\kernel;
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

class FileTest extends \StackOSTest {

    /**
     * Test if the initial files are being written
     */
    public function testInitialFiles() {
        $kernel = $this->getKernel();

        // assert that initial files exist
        $this->assertTrue($kernel->readDocument(\stackos\ROOT_PATH) instanceof \stackos\Document);
        $this->assertTrue($kernel->readDocument(\stackos\ROOT_PATH_USERS) instanceof \stackos\Document);
        $this->assertTrue($kernel->readDocument(\stackos\ROOT_PATH_GROUPS) instanceof \stackos\Document);
        $this->assertTrue($kernel->readDocument(\stackos\ROOT_PATH_HOME) instanceof \stackos\Document);
    }

    /**
     * Test read* write* and deleteDocument
     */
    public function testReadWriteDelete() {
        $kernel = $this->getKernel();

        // write the document
        $document = new \stackos\Document($kernel, '/foo', \stackos\ROOT_UNAME);
        $kernel->writeDocument($document);

        // assert that the written document matches the read
        $this->assertEquals($document->getOwner(), $kernel->readDocument('/foo')->getOwner());
        $this->assertEquals($document->getPath(), $kernel->readDocument('/foo')->getPath());

        // delete file and assert that it's gone
        $kernel->deleteDocument($kernel->readDocument('/foo'));
        try {
            $kernel->readDocument('/foo');
            $this->fail();
        } catch(\stackos\Exception_DocumentNotFound $e) {
            // pass
        }
    }

    /**
     * Test Document's save method
     */
    public function testSave() {
        $kernel = $this->getKernel();
        $document = new \stackos\Document($kernel, '/foo', \stackos\ROOT_UNAME);
        $document->save();

        // assert that the written document matches the read
        $this->assertEquals($document->getOwner(), $kernel->readDocument('/foo')->getOwner());
        $this->assertEquals($document->getPath(), $kernel->readDocument('/foo')->getPath());
    }
}