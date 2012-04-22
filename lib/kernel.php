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

    /** Create the kernel.
     *
     * @param string $dsn
     * @param string $dbName
     */
    public function __construct($dsn, $dbName) {
        $this->couchClient = new \couchClient($dsn, $dbName);
        $this->adapter = new Kernel_Adapter($this);
    }

    /** Push a context onto the stack.
     *
     * @param kernel\Context $context
     * @return \stackos\Kernel
     */
    public function pushSecurityStrategy(\stackos\kernel\security\Strategy $strategy) {
        $this->securityStrategyStack[] = $strategy;
        return $this;
    }

    /** Pop a context off the stack
     *
     * @return \stackos\kernel\Context
     */
    public function pullSecurityStrategy() {
        if (!count($this->securityStrategyStack)) {
            throw new Exception_MissingSecurityStrategy("There is no active context on the stack.");
        }
        return array_pop($this->securityStrategyStack);
    }

    /** Get the current context from the stack.
     *
     * @throws\stackos\Exception_MissingSecurityStrategy
     * @return \stackos\kernel\Context
     */
    public function currentStrategy() {
        if (!count($this->securityStrategyStack)) {
            throw new Exception_MissingSecurityStrategy("There is no active context on the stack.");
        }
        return end($this->securityStrategyStack);
    }

    /** Initialize the kernel.
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
                // create root user
                $rootUser = new User($this, ROOT_UNAME, array('root'), '/root');
                $rootUser->setUber(true);
                $doc = $this->adapter->fromUser($rootUser);
                $this->couchClient->storeDoc($doc);
                // create root directories
                $initDirs = array(ROOT_PATH, ROOT_PATH_HOME, ROOT_PATH_USERS, ROOT_PATH_GROUPS);
                foreach($initDirs as $dir) {
                    $file = new File($this, $dir, ROOT_UNAME);
                    $doc = $this->adapter->fromFile($file);
                    $this->couchClient->storeDoc($doc);
                }
            }
            // finally pop strategy
            catch(\Exception $e) {
                $this->pullSecurityStrategy();
                throw $e;
            }
            $this->pullSecurityStrategy();
        }
    }

    /** Destroy the database.
     */
    public function destroy() {
        if ($this->couchClient->databaseExists()) {
            $this->couchClient->deleteDatabase();
        }
    }

    /** Get a user by their uname
     *
     * @param \stackos\User $user
     * @param string        $uname
     * @return \stackos\User
     */
    public function getUser(User $user, $uname) {
        // get document
        try {
            $doc = $this->couchClient->getDoc("user:$uname");
        }
        catch (\couchNotFoundException $e) {
            throw new Exception_UserNotFound("The user with the uname '$uname' was not found.");
        }

        if (!$this->currentStrategy()->checkDocumentPermission($user, new User($this, $uname), \stackos\kernel\security\Priviledge::READ)) {
            throw new Exception_PermissionDenied("Permission to read user '$uname' was denied.",
                Exception_PermissionDenied::PERMISSION_READ_USER_DENIED);
        }

        // return user abstracted via User instance
        return $this->adapter->toUser($doc);
    }

    /**
     * @param User $user
     * @throws \stackos\Exception_MissingSecurityStrategy|\stackos\Exception_PermissionDenied|\stackos\Exception_UserExists
     */
    public function createUser(User $user) {
        // check if user has write privileges on users file
        $file = new File($this, ROOT_PATH_USERS, new User($this, 'root'));
        $readPermission = $this->currentStrategy()
            ->checkDocumentPermission($user, $file, \stackos\kernel\security\Priviledge::WRITE);
        if (!$readPermission) {
            throw new Exception_PermissionDenied("The permission to create the user has been denied",
                Exception_PermissionDenied::PERMISSION_CREATE_USER_DENIED);
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
     * @param User $user
     * @param string $path
     * @return File
     * @throws Exception_FileNotFound|Exception_PermissionDenied
     */
    public function getFile(User $user, $path) {
        try {
            $doc = $this->couchClient->getDoc("file:$path");
        }
        catch (\couchNotFoundException $e) {
            throw new Exception_FileNotFound("File '$path' was not found'");
        }
        $file = $this->adapter->toFile($doc);
        if (!$this->currentStrategy()->checkDocumentPermission($user, $file, \stackos\kernel\security\Priviledge::READ)) {
            throw new Exception_PermissionDenied("Permission to read file '$path' was denied.",
                Exception_PermissionDenied::PERMISSION_READ_FILE_DENIED);
        }
        return $file;
    }


    /** Write a file to the file system
     * Check permissions to write to parent file first
     *
     * @param User $user
     * @param File $file
     * @return File
     * @throws Exception_PermissionDenied
     */
    public function createFile(User $user, File $file) {
        // check if file exists
        $exists = $this->fileExists($user, $file->getPath());
        if($exists) {
            throw new Exception_FileExists("The file at '{$file->getPath()}' could not be created. It exists already.");
        }
        // check for write priviledges on the parent file
        $this->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        $parent = $file->getParent($user);
        $this->pullSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
        $permission = $this->currentStrategy()->checkDocumentPermission($user, $parent, \stackos\kernel\security\Priviledge::WRITE);
        if (!$permission) {
            throw new Exception_PermissionDenied("Permission to create file at path '{$file->getPath()}' was denied.",
                Exception_PermissionDenied::PERMISSION_CREATE_FILE_DENIED);
        }
        // actually write document
        $doc = $this->adapter->fromFile($file);
        $this->couchClient->storeDoc($doc);


        return $file;
    }

    /** Check if a file exists
     * TODO dont check with exceptions; build fileExists into kernel
     *
     * @param $user
     * @param $path
     * @return bool
     */
    public function fileExists($user, $path) {
        try {
            // file exists always possible
            $this->pushSecurityStrategy(new \stackos\kernel\security\PrivilegedStrategy());
            $this->getFile($user, $path);
            $this->pullSecurityStrategy();
            return true;
        }
        catch(Exception_FileNotFound $e) {
            $this->pullSecurityStrategy();
            return false;
        }
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