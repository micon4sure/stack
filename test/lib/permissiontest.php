<?php
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
namespace stackos;
class PermissionTests extends \StackOSTest {
    /** Test if $uber has access to $file owned by $user
     */
    public function testCheckUber() {
        // create arbitrary user, uber user and document
        $uname = 'user';
        $path = ROOT_PATH_HOME . '/' . $uname;
        $user = new \stackos\module\UserModule($uname, $path);
        $uber = new \stackos\module\UserModule('uber', $path);
        $uber->setUber(true);
        $document = new Document($this->getManager(), $path, $uname);

        // check for permission to be true due to user being uber
        $security = new \stackos\security\DefaultSecurity();
        $this->assertTrue($security->checkDocumentPermission($uber, $document, Security_Priviledge::READ));
        $this->assertTrue($security->checkDocumentPermission($uber, $document, Security_Priviledge::WRITE));
        $this->assertTrue($security->checkDocumentPermission($uber, $document, Security_Priviledge::EXECUTE));
        $this->assertTrue($security->checkDocumentPermission($uber, $document, Security_Priviledge::DELETE));
    }

    /** Test if $owner has access to file with empty groups and empty permissions
     */
    public function testCheckOwner() {
        // create arbitrary user and document
        $uname = 'user';
        $path = ROOT_PATH_HOME . '/' . $uname;
        $user = new \stackos\module\UserModule($uname, $path);
        $document = new Document($this->getManager(), $path, $uname);
        //$document->addPermission(new Permi)

        // check for permission to be true due to user being uber (except execute)
        $security = new \stackos\security\DefaultSecurity();
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::READ));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::WRITE));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::EXECUTE));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::DELETE));
    }

    public function testGroupPermission() {
        // create arbitrary user and document
        $uname = 'user';
        $gname = 'group';
        $path = ROOT_USER_PATH_GROUPS . '/' . $gname;
        $group = new \stackos\module\GroupModule($gname);
        // ROOT_UNAME is document owner: prevent owner permission conflicts
        $document = new Document($this->getManager(), $path, ROOT_UNAME);
        $user = new \stackos\module\UserModule($uname, $path);
        $user->addToGroup($group);

        $security = new \stackos\security\DefaultSecurity();
        // assert that user can do nothing on the document
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::READ));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::WRITE));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::EXECUTE));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::DELETE));

        // assert that user can now read
        $document->addPermission(new \stackos\security\Permission_Group($gname, Security_Priviledge::READ));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::READ));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::WRITE));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::EXECUTE));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::DELETE));

        // assert that user can now read and write
        $document->addPermission(new \stackos\security\Permission_Group($gname, Security_Priviledge::WRITE));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::READ));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::WRITE));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::EXECUTE));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::DELETE));

        // assert that user can now read, write and execute
        $document->addPermission(new \stackos\security\Permission_Group($gname, Security_Priviledge::EXECUTE));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::READ));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::WRITE));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::EXECUTE));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::DELETE));

        // assert that user has all priviledges
        $document->addPermission(new \stackos\security\Permission_Group($gname, Security_Priviledge::DELETE));
        $this->checkAllPriviledges($security, $user, $document);

        // save document, retry for all priviledges
        \lean\util\Dump::all($document->getPermissions());
        $document->save();
        $savedDocument = $this->getManager()->readDocument($path);
        $this->assertEquals(
            $document->getPermissions(),
            $savedDocument->getPermissions());

        \lean\util\Dump::all($savedDocument->getPermissions());
        $this->checkAllPriviledges($security, $user, $savedDocument);
    }
    public function checkAllPriviledges($security, $user, $document) {
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::READ));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::WRITE));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::EXECUTE));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::DELETE));
    }

    public function testUserPermission() {
        // create arbitrary user and document
        $uname = 'user';
        $path = ROOT_USER_PATH_USERS . '/' . $uname;
        $user = new \stackos\module\UserModule($uname, $path);
        // ROOT_UNAME is document owner: prevent owner permission conflicts
        $document = new Document($this->getManager(), $path, ROOT_UNAME);

        $security = new \stackos\security\DefaultSecurity();
        // assert that user can do nothing on the document
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::READ));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::WRITE));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::EXECUTE));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::DELETE));

        // assert that user can now read
        $document->addPermission(new \stackos\security\Permission_User($uname, Security_Priviledge::READ));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::READ));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::WRITE));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::EXECUTE));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::DELETE));

        // assert that user can now read and write
        $document->addPermission(new \stackos\security\Permission_User($uname, Security_Priviledge::WRITE));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::READ));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::WRITE));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::EXECUTE));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::DELETE));

        // assert that user can now read, write and execute
        $document->addPermission(new \stackos\security\Permission_User($uname, Security_Priviledge::EXECUTE));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::READ));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::WRITE));
        $this->assertTrue($security->checkDocumentPermission($user, $document, Security_Priviledge::EXECUTE));
        $this->assertFalse($security->checkDocumentPermission($user, $document, Security_Priviledge::DELETE));

        // assert that user has all permissions
        $document->addPermission(new \stackos\security\Permission_User($uname, Security_Priviledge::DELETE));
        $this->checkAllPriviledges($security, $user, $document);

        // save document, retry for all permissions
        $document->save();
        $document = $this->getManager()->readDocument($path);
        $this->checkAllPriviledges($security, $user, $document);
    }

    public function testPopEmptyContext() {
        try {
            #$this->fail('Expecting Exception_MissingContext');
        }
        catch (\stackos\Exception_MissingSecurityStrategy $e) {
            // pass
        }
    }
}