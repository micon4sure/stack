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

class GroupTest extends StackOSTest {
    public function testNamechange() {
        $gname = 'foo';
        $path = '/bar';
        // check for plain set and get
        $group = new module\Group($gname);
        // save in a document, read again, check for correct gname
        $this->assertEquals($gname, $group->getGname());
        $document = new File($this->getManager(), $path, $group->getGname());
        $document->setModule($group);
        $this->assertEquals(
            $gname,
            $document->getModule()->getGname()
        );
        $document->save();

        // reread document, check gname
        $document = $this->getManager()->readFile($path);
        $this->assertEquals(
            $gname,
            $document->getModule()->getGname()
        );

        // again, with new uname
        $new = 'qux';
        $document = $this->getManager()->readFile($path);
        $document->getModule()->setGname($new);
        $document->save();
        // reread document, check new
        $document = $this->getManager()->readFile($path);
        $this->assertEquals(
            $new,
            $document->getModule()->getGname()
        );
    }
}