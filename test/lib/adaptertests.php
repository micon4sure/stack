<?php
namespace test;
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

class AdapterTests extends \StackOSTest {
    public function testUserAdaption() {
        $kernel = self::$kernel;
        $permission = \stackos\security\Permission_User::create(self::getNoname('testuser')->getUname(), \stackos\security\Priviledge::WRITE);
        $adapter = new \stackos\Kernel_Adapter($kernel);

        $this->assertEquals(
            $permission,
            $adapter->toPermission($adapter->fromPermission($permission))
        );

        $this->assertEquals(
            array($permission),
            $adapter->toPermissions($adapter->fromPermissions(array($permission)))
        );
    }
    public function testGroupAdaption() {
        $permission = \stackos\security\Permission_Group::create('testgroup', 'unknown');
        $kernel = self::$kernel;
        $permission = \stackos\security\Permission_Group::create('testgroup', \stackos\security\Priviledge::WRITE);
        $adapter = new \stackos\Kernel_Adapter($kernel);

        $this->assertEquals(
            $permission,
            $adapter->toPermission($adapter->fromPermission($permission))
        );

        $this->assertEquals(
            array($permission),
            $adapter->toPermissions($adapter->fromPermissions(array($permission)))
        );
    }

    public function testUnknownPermission() {
        $kernel = self::$kernel;
        $permission = AdapterTest_Mock_Permission::create('testgroup', self::getNoname()->getUname());
        $adapter = new \stackos\Kernel_Adapter($kernel);
        try {
            $adapter->toPermission($adapter->fromPermission($permission));
            $this->fail('Expecting Exception_UnknownEntityType');
        }
        catch(\stackos\Exception_UnknownEntityType $e) {
            // pass
        }
    }
}

class AdapterTest_Mock_Permission extends \stackos\security\Permission {
    public static function create($entity, $holder) {
        return new self($entity, $holder, 'unknown');
    }
}