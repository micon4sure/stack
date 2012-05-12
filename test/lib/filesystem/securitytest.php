<?php
namespace stack\fileSystem;
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

use stack\Security_Priviledge;

class SecurityTest extends StackOSTest {
    public function testPriviledged() {
        $security = new \stack\security\PriviledgedSecurity();
        $check = $security->checkFilePermission(new SecurityTest_Mock_File(), \stack\security_Priviledge::READ);
        $this->assertTrue($check);
    }

    public function testUnpriviledged() {
        $security = new \stack\security\UnpriviledgedSecurity();
        $check = $security->checkFilePermission(new SecurityTest_Mock_File(), \stack\security_Priviledge::READ);
        $this->assertFalse($check);
    }

    /**
     * Test if $uber has access to $file owned by $user
     */
    public function testCheckUber() {
        // create arbitrary user, uber user and file
        $uname = 'user';
        $path = \stack\Root::ROOT_PATH_HOME . '/' . $uname;
        $user = new \stack\module\User($uname, $path);
        $uber = new \stack\module\User('uber', $path);
        $uber->setUber(true);
        $file = new File($path, $uname);

        // check for permission to be true due to user being uber
        $security = new \stack\security\DefaultSecurity($uber);
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::READ));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::WRITE));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::EXECUTE));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::DELETE));
    }

    /**
     * Test if $owner has access to file with empty groups and empty permissions
     */
    public function testCheckOwner() {
        // create arbitrary user and file
        $uname = 'user';
        $path = \stack\Root::ROOT_PATH_HOME . '/' . $uname;
        $user = new \stack\module\User($uname, $path);
        $document = new File($path, $uname);
        //$document->addPermission(new Permi)

        // check for permission to be true due to user being uber (except execute)
        $security = new \stack\security\DefaultSecurity($user);
        $this->assertTrue($security->checkFilePermission($document, Security_Priviledge::READ));
        $this->assertTrue($security->checkFilePermission($document, Security_Priviledge::WRITE));
        $this->assertFalse($security->checkFilePermission($document, Security_Priviledge::EXECUTE));
        $this->assertTrue($security->checkFilePermission($document, Security_Priviledge::DELETE));
    }

    public function testGroupPermission() {
        // create arbitrary user and document
        $uname = 'user';
        $gname = 'group';
        $path = \stack\Root::ROOT_PATH_GROUPS . '/' . $gname;
        $group = new \stack\module\Group($gname);
        // ROOT_UNAME is file owner: prevent owner permission conflicts
        $file = new File($path, \stack\Root::ROOT_UNAME);
        $user = new \stack\module\User($uname, $path);
        $user->addToGroup($group);

        $security = new \stack\security\DefaultSecurity($user);
        // assert that user can do nothing on the file
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::READ));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::WRITE));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::EXECUTE));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::DELETE));

        // assert that user can now read
        $file->addPermission(new \stack\security\Permission_Group($gname, Security_Priviledge::READ));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::READ));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::WRITE));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::EXECUTE));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::DELETE));

        // assert that user can now read and write
        $file->addPermission(new \stack\security\Permission_Group($gname, Security_Priviledge::WRITE));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::READ));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::WRITE));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::EXECUTE));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::DELETE));

        // assert that user can now read, write and execute
        $file->addPermission(new \stack\security\Permission_Group($gname, Security_Priviledge::EXECUTE));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::READ));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::WRITE));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::EXECUTE));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::DELETE));

        // assert that user has all priviledges
        $file->addPermission(new \stack\security\Permission_Group($gname, Security_Priviledge::DELETE));
        $this->checkAllPriviledges($security, $file);
    }

    public function testUserPermission() {
        // create arbitrary user and document
        $uname = 'user';
        $path = \stack\Root::ROOT_PATH_USERS . '/' . $uname;
        $user = new \stack\module\User($uname, $path);
        // ROOT_UNAME is file owner: prevent owner permission conflicts
        $file = new File($path, \stack\Root::ROOT_UNAME);

        $security = new \stack\security\DefaultSecurity($user);
        // assert that user can do nothing on the file
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::READ));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::WRITE));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::EXECUTE));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::DELETE));

        // assert that user can now read
        $file->addPermission(new \stack\security\Permission_User($uname, Security_Priviledge::READ));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::READ));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::WRITE));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::EXECUTE));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::DELETE));

        // assert that user can now read and write
        $file->addPermission(new \stack\security\Permission_User($uname, Security_Priviledge::WRITE));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::READ));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::WRITE));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::EXECUTE));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::DELETE));

        // assert that user can now read, write and execute
        $file->addPermission(new \stack\security\Permission_User($uname, Security_Priviledge::EXECUTE));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::READ));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::WRITE));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::EXECUTE));
        $this->assertFalse($security->checkFilePermission($file, Security_Priviledge::DELETE));

        // assert that user has all permissions
        $file->addPermission(new \stack\security\Permission_User($uname, Security_Priviledge::DELETE));
        $this->checkAllPriviledges($security, $file);
    }
    public function checkAllPriviledges($security, $file) {
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::READ));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::WRITE));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::EXECUTE));
        $this->assertTrue($security->checkFilePermission($file, Security_Priviledge::DELETE));
    }
}

class SecurityTest_Mock_User extends \stack\module\User {
    public function __construct() {
        // pass
    }
}
class SecurityTest_Mock_File extends \stack\filesystem\File {
    public function __construct() {
        // pass
    }
}
