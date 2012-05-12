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

class GroupTest extends StackOSTest {
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