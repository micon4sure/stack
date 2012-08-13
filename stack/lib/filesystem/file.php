<?php
namespace stack\fileSystem;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */


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
     * @return \stack\module\BaseModule
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
        $this->meta->setOwner($owner);
    }

    /**
     * meta
     * @param \stack\security\Permission $permission
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
 * Meta information about the file and its contents
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

    /**
     * @return array
     */
    public function getPermissions() {
        return $this->permissions;
    }

    /**
     * @param \stack\security\Permission $permission
     */
    public function addPermission(\stack\security\Permission $permission) {
        $this->permissions[] = $permission;
    }
}