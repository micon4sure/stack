<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */


/**
 * Context is the way to provide access among units inside the Shell.
 * It may also be passed around outside the Shell to alter the context.
 * (Like registering a module or pushing a Security)
 * Also handles security concerns over the implemented Interface_SecurityAccess
 */
class Context extends \lean\Registry_State implements Interface_Security, Interface_SecurityAccess {
    /**
     * @var TestEnvironment
     */
    private $environment;
    /**
     * @var Shell
     */
    private $shell;

    /**
     * @var FileSystem
     */
    private $fileSystem;

    /**
     * @var \lean\Stack
     */
    private $security;

    /**
     * Initiate a context for the passed environment
     *
     * @param TestEnvironment $environment
     */
    public function __construct(Environment $environment) {
        $this->environment = $environment;
        $this->security = new \lean\Stack();
    }

    /* : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : :  accessors for internals */
    /**
      * @return TestEnvironment
      */
    public function getEnvironment() {
        return $this->environment;
    }
    /**
     * @return Shell
     */
    public function getShell() {
        if($this->shell) {
            return $this->shell;
        }
        $fs = $this->getFilesystem();
        return $this->shell = $this->environment->createShell($this, $fs);
    }

    /**
     * Use this method only to access the file system.
     *
     * @return Filesystem
     */
    protected function getFileSystem() {
        return $this->fileSystem ?: $this->fileSystem = $this->environment->createFilesystem($this);
    }

    /* : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : SecurityAccess */
    /**
     * @param Interface_Security $security
     */
    public function pushSecurity(Interface_Security $security) {
        $this->security->push($security);
    }

    /**
     * @return  Interface_Security
     */
    public function pullSecurity() {
        return $this->security->pull();
    }

    /**
     * @return mixed
     */
    protected function currentSecurity() {
        return $this->security->current();
    }

    /* : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : Security */
    /**
     * Check if a user has permission to access a file in ways of $permission (r/w/x)
     *
     * @param \stack\filesystem\File  $file
     * @param string                  $priviledge
     * @return bool
     */
    public function checkFilePermission(\stack\filesystem\File $file, $priviledge) {
        if($this->security->count() == 0) {
            throw new \stack\fileSystem\Exception_NoSecurity();
        }

        return $this->security->current()->checkFilePermission($file, $priviledge);
    }
}