<?php
namespace enork;
/*
 * Copyright (C) <2012> <Michael Saller>
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

class Kernel {
    const PERMISSION_READ = 'r';
    const PERMISSION_WRITE = 'w';
    const PERMISSION_EXECUTE = 'x';

    const PERMISSION_TYPE_GROUP = 'g';
    const PERMISSION_TYPE_USER = 'u';

    /**
     * @var \couchClient
     */
    private $couchClient;

    /**
     * @var User;
     */
    private $rootUser;

    /**
     * @var File
     */
    private $rootFile;

    /**
     * @var Kernel_Adapter
     */
    private $adapter;

    /**
     * @var array
     */
    private $contextStack = array();

    /**
     * @var User
     */
    private $user;

    /**
     * Create the kernel.
     *
     * @param string $dsn
     * @param string $dbName
     */
    public function __construct($dsn, $dbName) {
        $this->couchClient = new \couchClient($dsn, $dbName);
        $this->adapter = new Kernel_Adapter();
    }

    /**
     * Push a context onto the stack.
     *
     * @param kernel\Context $context
     * @return \enork\Kernel
     */
    public function pushContext(kernel\Context $context) {
        $this->contextStack[] = $context;
        return $this;
    }

    /**
     * Pop a context off the stack
     * @return \enork\kernel\Context
     */
    public function popContext() {
        if (!count($this->contextStack)) {
            throw new Exception_MissingContext("There is no active context on the stack.");
        }
        return array_pop($this->contextStack);
    }

    /**
     * Get the current context from the stack.
     * @throws\enork\Exception_MissingContext
     * @return \enork\kernel\Context
     */
    public function currentContext() {
        if (!count($this->contextStack)) {
            throw new Exception_MissingContext("There is no active context on the stack.");
        }
        return end($this->contextStack);
    }

    /**
     * @return \enork\User
     */
    public function getRootUser() {
        return $this->rootUser;
    }

    /**
     * @return \enork\File
     */
    public function getRootFile() {
        return $this->rootFile;
    }

    /**
     * Initialize the kernel.
     * Make sure the database exists.
     * If not:
     * + create database
     * + create filesystem root
     * + create root user
     * + create root user home
     */
    public function init() {
        if (!$this->couchClient->databaseExists()) {
            $this->couchClient->createDatabase();
            // root file
            $rootFile = new \stdClass;
            $rootFile->_id = 'file:/';
            $rootFile->parent = null;
            $rootFile->owner = 'user:root';
            $rootFile->permissions = array();
            $this->couchClient->storeDoc($rootFile);

            // root user
            $rootUser = new \stdClass;
            $rootUser->_id = 'user:root';
            $rootUser->home = 'file:/root';
            $rootUser->uber = true;
            $rootUser->groups = array('root');
            $this->couchClient->storeDoc($rootUser);

            // root home
            $rootHome = new \stdClass;
            $rootHome->_id = 'file:/root';
            $rootHome->parent = 'file:/';
            $rootHome->owner = 'user:root';
            $rootHome->permissions = array();
            $this->couchClient->storeDoc($rootHome);
        }

        // save root user and file for later use
        $this->pushContext(new \enork\kernel\RootContext());
        $this->rootUser = $this->getUser('root');
        $this->rootFile = $this->getFile('/');
        $this->popContext();
    }

    /**
     * Destroy the database.
     */
    public function destroy() {
        if ($this->couchClient->databaseExists()) {
            $this->couchClient->deleteDatabase();
        }
    }

    /**
     * Get a user by their uname
     *
     * @param string $uname
     * @return \enork\User
     */
    public function getUser($uname) {
        try {
            $doc = $this->couchClient->getDoc("user:$uname");
        }
        catch (\couchNotFoundException $e) {
            throw new Exception_UserNotFound("The user with the uname '$uname' was not found.");
        }
        return $this->adapter->toUser($doc);
    }

    /**
     * @param User $user
     * @throws \enork\Exception_MissingContext|\enork\Exception_PermissionDenied|\enork\Exception_UserExists
     */
    public function createUser(User $user) {
        if (!$this->currentContext()->checkUserCreatePermission($user)) {
            throw new Exception_PermissionDenied("The permission to create the user has been denied");
        }
        $doc = $this->adapter->fromUser($user);
        try {
            $this->couchClient->storeDoc($doc);
        }
        catch (\couchConflictException $e) {
            throw new Exception_UserExists("Could not create user with uname '{$user->getUname()}'. Already exists.");
        }
    }

    /**
     * Get a files by its path
     *
     * @param string $path
     * @throws \enork\Exception_MissingContext|\enork\Exception_PermissionDenied
     * @return \enork\File
     */
    public function getFile($path) {
        $doc = $this->couchClient->getDoc("file:$path");
        $path = \lean\Text::offsetLeft($doc->_id, 'file:');
        $owner = \lean\Text::offsetLeft($doc->owner, 'user:');
        $file = new File($path, $owner, $doc->permissions);
        if (!$this->currentContext()->checkFilePermission($file, self::PERMISSION_READ)) {
            throw new Exception_PermissionDenied("Permission to receive file '$path' was denied.");
        }
        return $file;
    }

    public function createFile(File $file) {
        $doc = $this->adapter->fromFile($file);
    }
}

class Kernel_Adapter {
    /**
     * @param User $user
     * @return \object
     */
    public function fromUser(User $user) {
        $doc = new \stdClass;
        $doc->_id = 'user:' . $user->getUname();
        $doc->home = 'file:' . $user->getHome();
        $doc->groups = $user->getGroups();
        $doc->uber = $user->getUber();
        return $doc;
    }

    /**
     * @param \object $doc
     * @return User
     */
    public function toUser($doc) {
        // cut prefixes and create user
        $uname = \lean\Text::offsetLeft($doc->_id, 'user:');
        $home = \lean\Text::offsetLeft($doc->home, 'file:');
        $user = new User($uname, $doc->groups, $home);
        $user->setUber($doc->uber);
        return $user;
    }

    /**
     * @param File $file
     * @return \object
     */
    public function fromFile(File $file) {
        \lean\Dump::flat($file);
        $doc = new \stdClass;

    }


    /**
     * @param \object $doc
     * @return File
     */
    public function toFile($doc) {

    }
}