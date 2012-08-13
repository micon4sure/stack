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
 * Also handles security concerns via the implemented security interfaces
 * Last but not least: provides access to the default session and the user
 */
class Context extends \lean\Registry_State implements Interface_Security, Interface_SecurityAccess {
    /**
     * @var Environment
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
     * @var \lean\Session
     */
    private $session;

    /**
     * @var \stack\module\User
     */
    private $user;

    /**
     * Initiate a context for the passed environment
     * @param \stack\Environment $environment
     */
    public function __construct(Environment $environment) {
        $this->environment = $environment;
        $this->security = new \lean\Stack();
    }

    /* : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : :  accessors for internals */
    /**
     * @return \stack\Environment
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

    /**
     * Get previously set user from session
     *
     * @return null|module\User
     * @throws \Exception
     */
    public function getUser() {
        if($this->user)
            return $this->user;
        if(!$this->getSession()->uName) {
            return null;
        }

        $this->pushSecurity(new \stack\security\PriviledgedSecurity());
        try {
            $file = $this->getShell()->readFile(Root::ROOT_PATH_USERS . '/' . $this->getSession()->uName);
            $this->user = $file->getModule();
        } catch(\Exception $e) {
            $this->pullSecurity();
            throw $e;
        }
        return $this->user;
    }

    /**
     * Set user to session to use in later requests
     *
     * @param $uName
     */
    public function setUser($uName) {
        $this->getSession()->uName = $uName;
    }

    /**
     * Lazy get session
     *
     * @return \lean\Session
     */
    public function getSession() {
        return $this->session ?: $this->session = new \lean\Session('stack.context');
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