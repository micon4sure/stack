<?php
namespace stack;
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