<?php
namespace stackos;
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

const ROOT_UNAME = 'root';
const ROOT_PATH = '/';
const ROOT_PATH_HOME = '/root';
const ROOT_PATH_USERS = '/root/users';
const ROOT_PATH_GROUPS = '/root/groups';

class Kernel {
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
    private $securityStrategyStack = array();

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
        $this->adapter = new Kernel_Adapter($this);
    }

    /**
     * Push a context onto the stack.
     *
     * @param kernel\Context $context
     * @return \stackos\Kernel
     */
    public function pushSecurityStrategy(\stackos\kernel\security\Strategy $strategy) {
        $this->securityStrategyStack[] = $strategy;
        return $this;
    }

    /**
     * Pop a context off the stack
     *
     * @return \stackos\kernel\Context
     */
    public function popSecurityStrategy() {
        if (!count($this->securityStrategyStack)) {
            throw new Exception_MissingSecurityStrategy("There is no active context on the stack.");
        }
        return array_pop($this->securityStrategyStack);
    }

    /**
     * Get the current context from the stack.
     *
     * @throws\stackos\Exception_MissingSecurityStrategy
     * @return \stackos\kernel\Context
     */
    public function currentContext() {
        if (!count($this->securityStrategyStack)) {
            throw new Exception_MissingSecurityStrategy("There is no active context on the stack.");
        }
        return end($this->securityStrategyStack);
    }

    /**
     * Lazy get root user
     * @return \stackos\User
     */
    public function getRootUser() {
        if ($this->rootUser === null) {
            return $this->rootUser = $this->getUser(ROOT_UNAME);
        }
        return $this->rootUser;
    }

    /**
     * Lazy get root file
     * @return \stackos\File
     */
    public function getRootFile() {
        if ($this->rootFile === null) {
            $this->rootFile = $this->getFile($this->getRootUser(), ROOT_PATH);
        }
        return $this->rootFile;
    }

    /**
     * Initialize the kernel.
     * Make sure the database exists.
     * If not:
     * + create database
     * + create root user
     * + create /
     * + create /root
     * + create /root/users
     * + create /root/groups
     */
    public function init() {
        if (!$this->couchClient->databaseExists()) {
            $this->couchClient->createDatabase();
            $this->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
            try {
                // root user
                $rootUser = new User($this, ROOT_UNAME, array('root'), '/root');
                $rootUser->setUber(true);
                $doc = $this->adapter->fromUser($rootUser);
                $this->couchClient->storeDoc($doc);
                $initDirs = array(ROOT_PATH, ROOT_PATH_HOME, ROOT_PATH_USERS, ROOT_PATH_GROUPS);
                foreach($initDirs as $dir) {
                    $file = new File($this, $dir, ROOT_UNAME);
                    $doc = $this->adapter->fromFile($file);
                    $this->couchClient->storeDoc($doc);
                }
            }
            // finally pop context
            catch(\Exception $e) {
                // roll back context in case of exception
                $this->popSecurityStrategy();
                throw $e;
            }
            $this->popSecurityStrategy();
        }
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
     * @return \stackos\User
     */
    public function getUser($uname) {
        // get document
        try {
            $doc = $this->couchClient->getDoc("user:$uname");
        }
        catch (\couchNotFoundException $e) {
            throw new Exception_UserNotFound("The user with the uname '$uname' was not found.");
        }

        // return user abstracted via User instance
        return $this->adapter->toUser($doc);
    }

    /**
     * @param User $user
     * @throws \stackos\Exception_MissingSecurityStrategy|\stackos\Exception_PermissionDenied|\stackos\Exception_UserExists
     */
    public function createUser(User $user) {

        if (!$this->currentContext()->checkDocumentPermission($user, $this->getFile($user, ROOT_PATH_USERS), \stackos\kernel\security\Strategy::PERMISSION_TYPE_READ)) {
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

    /** Get a file by its path
     *
     * @param string $path
     * @throws \stackos\Exception_MissingSecurityStrategy|\stackos\Exception_PermissionDenied
     * @return \stackos\File
     */
    public function getFile(User $user, $path) {
        try {
            $doc = $this->couchClient->getDoc("file:$path");
        }
        catch (\couchNotFoundException $e) {
            throw new Exception_FileNotFound("File '$path' was not found'");
        }
        $file = $this->adapter->toFile($doc);
        if (!$this->currentContext()->checkDocumentPermission($user, $file, \stackos\kernel\security\Strategy::PERMISSION_TYPE_READ)) {
            throw new Exception_PermissionDenied("Permission to receive file '$path' was denied.");
        }
        return $file;
    }

    /**
     * Write a file to the file system
     * Check permissions to write to parent file first
     *
     * @param File $file
     * @return File
     * @throws Exception_PermissionDenied
     */
    public function createFile(File $file) {
        // don't allow creation of root file
        if($file->getPath() == '/') {
            throw new Exception_PermissionDenied("Not allowed to create or delete root file.", Exception_PermissionDenied::PERMISSION_DENIED_CANT_CREATE_ROOT);
        }
        // check file permission on the document
        if (!$this->currentContext()->checkFilePermission($file, \stackos\kernel\Context::PERMISSION_TYPE_READ)) {
            throw new Exception_PermissionDenied("Permission to create file at path '{$file->getPath()}' was denied.", Exception_PermissionDenied::PERMISSION_DENIED_MISSING_PERMISSION);
        }

        // actually write document
        $doc = $this->adapter->fromFile($file);
        $this->couchClient->storeDoc($doc);

        return $file;
    }
}

/**
 * Adapts from and to couchDB documents to instances of the appropriate type
 */
class Kernel_Adapter {
    /**
     * @var Kernel
     */
    private $kernel;

    /**
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel) {
        $this->kernel = $kernel;
    }

    /**
     * Adapt User instance to be saved as a couchdb document
     *
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
     * Adapt a couchdb document to a User instance
     *
     * @param \object $doc
     * @return User
     */
    public function toUser($doc) {
        // cut prefixes and create user
        $uname = \lean\Text::offsetLeft($doc->_id, 'user:');
        $home = \lean\Text::offsetLeft($doc->home, 'file:');
        $user = new User($this->kernel, $uname, $doc->groups, $home);
        $user->setUber($doc->uber);
        return $user;
    }

    /**
     * Adapt File instance to be saved as a couchdb document
     *
     * @param File $file
     * @return \object
     */
    public function fromFile(File $file) {
        $doc = new \stdClass;
        $doc->_id = 'file:' . $file->getPath();
        $doc->owner = 'user:' . $file->getOwner();
        $doc->permissions = $file->getPermissions();
        return $doc;
    }

    /**
     * Adapt a couchdb document to a File instance
     *
     * @param \object $doc
     * @return File
     */
    public function toFile($doc) {
        $path = \lean\Text::offsetLeft($doc->_id, 'file:');
        $owner = \lean\Text::offsetLeft($doc->owner, 'user:');
        return new File($this->kernel, $path, $owner, $doc->permissions);
    }
}