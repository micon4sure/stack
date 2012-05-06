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

/**
 * Indicates that a class knows how to convert objects of its type from and to json
 */
interface File_JSONizable {
    public function fromJSON();
    public function toJSON();
}

/**
 * Abstraction of a file in the file system
 */
class File {
    /**
     * @var File_Meta
     */
    private $meta;

    /**
     * @var \stack\module\BaseModule
     */
    private $module;

    /**
     * @param string $path
     * @param string $owner
     * @param string $revision
     */
    public function __construct($path, $owner, $revision = null) {
        $this->meta = new File_Meta($this, $path, $owner, $revision);
    }

    /**
     *
     * @param \stack\module\BaseModule $module
     */
    public function setModule(\stack\module\BaseModule $module) {
        $this->module = $module;
    }

    /**
     * @return module\BaseModule
     */
    public function getModule() {
        return $this->module;
    }

    /**
     * meta
     * @return string
     */
    public function getPath() {
        return $this->meta->getPath();
    }

    /**
     * meta
     * @return null|string
     */
    public function getRevision() {
        return $this->meta->getRevision();
    }

    /**
     * meta
     * @return string
     */
    public function getOwner() {
        return $this->meta->getOwner();
    }

    /**
     * @param string $owner
     */
    public function setOwner($owner) {
        $this->owner = $owner;
    }

    /**
     * meta
     * @param security\Permission $permission
     */
    public function addPermission(\stack\security\Permission $permission) {
        $this->meta->addPermission($permission);
    }

    public function setRevision($revision) {
        $this->meta->setRevision($revision);
    }

    /**
     * meta
     * @return array
     */
    public function getPermissions() {
        return $this->meta->getPermissions();
    }
}

/**
 * Meta information about the file
 * - owner
 * - path
 * - revision
 * - (permissions)
 * - creationTime
 * - manipulationTime
 */
class File_Meta {
    /**
     * @var File
     */
    private $file;
    /**
     * @var string
     */
    private $path;
    /**
     * @var string
     */
    private $owner;
    /**
      * @var null|string
      */
    private $revision;
    /**
     * @var array
     */
    private $permissions = array();

    /**
     * @param File $file
     * @param string $path
     * @param string $owner
     * @param null $revision
     */
    public function __construct(File $file, $path, $owner, $revision = null) {
        $this->revision = $revision;
        $this->file = $file;
        $this->setPath($path);
        $this->setOwner($owner);
    }
    /**
     * @param string $path
     */
    public function setPath($path) {
        $this->path = $path;
    }
    /**
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * @param $revision
     */
    public function setRevision($revision) {
        $this->revision = $revision;
    }

    /**
     * Return the revision of the file or null if it has not been saved yet
     * @return null|string
     */
    public function getRevision() {
        return $this->revision;
    }

    /**
     * @param $owner
     */
    public function setOwner($owner) {
        $this->owner = $owner;
    }

    /**
     * @return string
     */
    public function getOwner() {
        return $this->owner;
    }

    public function getPermissions() {
        return $this->permissions;
    }
    public function addPermission(\stack\security\Permission $permission) {
        $this->permissions[] = $permission;
    }
}