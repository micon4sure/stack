<?php
namespace test\security;
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


class UnpriviledgedStrategyTests extends \StackOSTest {

    public function testCheckUserCreatePermission() {
        $strategy = new \stackos\kernel\security\UnprivilegedStrategy();
        $this->assertFalse($strategy->checkUserCreatePermission(self::getRootUser()));
    }

    public function testCheckUserDeletePermission() {
        $strategy = new \stackos\kernel\security\UnprivilegedStrategy();
        $this->assertFalse($strategy->checkUserDeletePermission(self::getRootUser()));
    }

    public function testCheckDocumentUserPermission() {
        $strategy = new \stackos\kernel\security\UnprivilegedStrategy();
        $document = new \stackos\Document(self::$kernel);

        // make sure noname has all permissions
        $this->assertFalse($strategy->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::READ));
        $this->assertFalse($strategy->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::WRITE));
        $this->assertFalse($strategy->checkDocumentPermission(self::getNoname(), $document, \stackos\kernel\security\Priviledge::EXECUTE));
    }
}
