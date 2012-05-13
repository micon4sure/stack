<?php
namespace stack;
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
 * Context is the way to provide access among units inside the Shell.
 * It may also be passed around outside the Shell to alter the context.
 * (Like registering a module or pushing a Security)
 * Also handles security concerns over the implemented Interface_SecurityAccess
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
     * Initiate a context for the passed environment
     *
     * @param Environment $environment
     */
    public function __construct(Environment $environment) {
        $this->environment = $environment;
        $this->security = new \lean\Stack();
    }

    /* : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : :  accessors for internals */
    /**
      * @return Environment
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
    public function checkFilePermission(\stack\filesystem\File $file, $priviledge)
    {
        return $this->security->current()->checkFilePermission($file, $priviledge);
    }
}