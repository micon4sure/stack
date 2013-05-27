<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

use lean\util\Dump;
use stack\module\User;

class Security implements FileAccess {

    /**
     * @var Cabinet
     */
    private $cabinet;

    /**
     * @var User
     */
    private $user;

    /**
     * @param Cabinet $cabinet
     */
    public function __construct(Cabinet $cabinet) {
        $this->cabinet = $cabinet;
    }

    /**
     * @param module\User $user
     */
    public function setUser(User $user) {
        $this->user = $user;
    }

    /**
     * @param $path
     *
     * @throws Security_Exception
     * @return File
     */
    public function createFile($path, User $owner) {
        // check if user has priviledge to write on parent directory
        $parentPath = dirname($path);
        if(!$this->cabinet->fileExists($parentPath)) {
            throw new Exception("Can not create file at path '$path', parent file at '$parentPath' does not exist.");
        }
        $parent = $this->cabinet->fetchFile($parentPath);
        if(!$this->checkPermissions($parent, Security_Priviledge::WRITE)) {
            throw new Security_Exception("Not allowed to write file to path '$parentPath'");
        }

        // ok, go
        $this->cabinet->createFile($path, $owner);
    }

    /**
     * Save a file
     *
     * @throws Security_Exception
     * @param File $file
     */
    public function storeFile(File $file) {
        if(!$this->checkPermissions($file, Security_Priviledge::WRITE)) {
            throw new Security_Exception("Not allowed to write file '{$file->getPath()}'");
        }

        $this->cabinet->storeFile($file);
    }

    /**
     * Read a file
     *
     * @param string $path
     *
     * @throws Security_Exception
     * @return File
     */
    public function fetchFile($path) {
        $file = $this->cabinet->fetchFile($path);
        if(!$this->checkPermissions($file, Security_Priviledge::READ)) {
            throw new Security_Exception("Not allowed to read file '{$file->getPath()}'");
        }
        return $file;
    }

    /**
     * Delete a file
     *
     * @throws Security_Exception
     * @param File $file
     */
    public function deleteFile(File $file) {
        if(!$this->checkPermissions($file, Security_Priviledge::WRITE)) {
            throw new Security_Exception("Not allowed to delete file '{$file->getPath()}'");
        }

        $this->cabinet->deleteFile($file);
    }

    /**
     * Check if a file exists
     *
     * @param string $path
     *
     * @return bool
     */
    public function fileExists($path) {
        return $this->cabinet->fileExists($path);
    }

    /**
     * @param File $file
     * @param string $priviledge
     *
     * @return bool
     */
    protected function checkPermissions($file, $priviledge) {
        if(isset($this->user)) {
            // check if current user is owner of the file
            if($file->getOwner() == $this->user->getUname()) {
                return true;
            }
        }

        foreach($file->getPermissions() as $permission) {
            if($permission->priviledge != $priviledge) {
                // provided priviledge is not requested
                continue;
            }

            if($permission->context == Security_Permission::CONTEXT_ALL) {
                // provided priviledge applies to everyone
                return true;
            }

            if(!isset($this->user)) {
                // no user is set, can't check for user / group permissions
                continue;
            }

            if($permission->context == Security_Permission::CONTEXT_USER && $permission->subject == $this->user->getUname()) {
                // permission is explicitly granted for this user
                return true;
            }

            if($permission->context == Security_Permission::CONTEXT_GROUP && in_array($permission->subject, $this->user->getGroups())) {
                // permission is granted for all users in group
                return true;
            }
        }
        return false;
    }
}

abstract class Security_Priviledge {
    /** Read permission
     */
    const READ = 'r';
    /** Write permission
     */
    const WRITE = 'w';
    /** Execute permission: allows to execute applications enclosed in a file
     */
    const EXECUTE = 'x';
    /** Delete permission
     */
    const DELETE = 'd';
}

abstract class Security_Permission {
    /**
     * Permission applies to everyone
     */
    const CONTEXT_ALL = 'a';

    /**
     * Permission applies to a specific user
     */
    const CONTEXT_USER = 'u';

    /**
     * Permission applies to a specific group
     */
    const CONTEXT_GROUP = 'g';
}

class Security_Exception extends Exception {

}