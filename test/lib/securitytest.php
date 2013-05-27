<?php
namespace stack\test;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

use lean\util\Dump;
use stack\Cabinet;
use stack\Exception;
use stack\module\Group;
use stack\ModuleFactory;
use stack\Security;
use stack\Security_Exception;
use stack\Security_Permission;
use stack\Security_Priviledge;
use stack\module\User;

class SecurityTest extends StackTest {
    /**
     * @return \stack\Cabinet
     */
    protected function createCabinet() {
        return new Cabinet($this->client, new ModuleFactory());
    }
    protected function createRootFile() {
        $this->createCabinet()->createFile('/', User::create('root'));
    }

    /**
     * @return \stack\Security
     */
    protected function createSecurity() {
        $cabinet = new Cabinet($this->client, new ModuleFactory());
        return new Security($cabinet);
    }

    protected function createDummyUser() {
        return User::create('dummy');
    }

    public function testCreateFileWithNonExistentParent() {
        $security = $this->createSecurity();
        try {
            // this should fail, parent does not exist
            $security->createFile('/foo', $this->createDummyUser());
            $this->fail('Expected exception');
        } catch(Exception $e) {}
    }

    public function testCreateFileWithMissingPermissionOnParent() {
        $security = $this->createSecurity();
        $this->createRootFile();

        try {
            // this should fail, missing permissions on parent file
            $security->createFile('/foo', $this->createDummyUser());
            $this->fail('Expected exception');
        } catch(Exception $e) {}
    }

    public function testPermissionAllOnParent() {
        $cabinet = $this->createCabinet();
        $security = $this->createSecurity();
        $this->createRootFile();

        // grant write permission to parent file
        $root = $this->createCabinet()->fetchFile('/');
        $root->addPermission(Security_Priviledge::WRITE, Security_Permission::CONTEXT_ALL);
        $cabinet->storeFile($root);

        try {
            // should not fail, permission given to parent file
            $security->createFile('/foo', $this->createDummyUser());
        } catch(Security_Exception $e) {
            $this->fail('Should not fail');
        }
    }

    public function testPermissionUserOnParent() {
        $cabinet = $this->createCabinet();
        $security = $this->createSecurity();
        $user = $this->createDummyUser();
        $security->setUser($user);
        $this->createRootFile();

        // grant write permission to parent file
        $root = $this->createCabinet()->fetchFile('/');
        $root->addPermission(Security_Priviledge::WRITE, Security_Permission::CONTEXT_USER, $user->getUname());
        $cabinet->storeFile($root);

        try {
            // should not fail, permission given to parent file
            $security->createFile('/foo', $user);
        } catch(Security_Exception $e) {
            $this->fail('Should not fail');
        }
    }

    public function testPermissionGroupOnParent() {
        $cabinet = $this->createCabinet();
        $user = $this->createDummyUser();
        $security = $this->createSecurity();
        $security->setUser($user);
        $this->createRootFile();

        try {
            // this should fail, missing permissions on parent file
            $security->createFile('/foo', $user);
            $this->fail('Expected exception');
        } catch(Exception $e) {}

        // add user to group two
        $user->addToGroup('two');
        // grant write permission to parent file, for wrong group
        $root = $this->createCabinet()->fetchFile('/');
        $root->addPermission(Security_Priviledge::WRITE, Security_Permission::CONTEXT_GROUP, 'one');
        $cabinet->storeFile($root);

        try {
            // this should fail, missing permissions on parent file
            $security->createFile('/foo', $user);
            $this->fail('Expected exception');
        } catch(Exception $e) {}

        // grant write permission to parent file, for correct group
        $root->addPermission(Security_Priviledge::WRITE, Security_Permission::CONTEXT_GROUP, 'two');
        $cabinet->storeFile($root);
        try {
            // permissions given, file should be creatable
            $security->createFile('/foo', $user);
        } catch(Exception $e) {
            $this->fail('Should not fail anymore, explicit permissions given to group two, which user is in');
        }
    }
}