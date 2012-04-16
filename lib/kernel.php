<?php
namespace enork;

/**
 * TODO exception messages
 */
class Kernel {
    const PERMIT_READ = 'r';
    const PERMIT_WRITE = 'w';
    const PERMIT_EXECUTE = 'x';

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
     * @var Kernel_PermissionManager
     */
    private $permissionManager;

    /**
     * @var Kernel_Adapter
     */
    private $adapter;

    /**
     * Create the kernel.
     * @param string $dsn
     * @param string $dbName
     */
    public function __construct($dsn, $dbName) {
        $this->couchClient = new \couchClient($dsn, $dbName);
        $this->permissionManager = new Kernel_PermissionManager($this);
        $this->adapter = new Kernel_Adapter();
    }

    /**
     * @return User
     */
    public function getRootUser() {
        return $this->rootUser;
    }

    /**
     * @return User
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
        if(!$this->couchClient->databaseExists()) {
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
        $this->rootUser = $this->getUser('root');
        $this->rootFile = $this->getFile('/', $this->rootUser);
    }

    /**
     * Destroy the database.
     */
    public function destroy() {
        if($this->couchClient->databaseExists()) {
            $this->couchClient->deleteDatabase();
        }
    }

    /**
     * Get a user by their uname
     * @param string $uname
     * @return \enork\User
     */
    public function getUser($uname) {
        try {
            $doc = $this->couchClient->getDoc("user:$uname");
        }
        catch(\couchNotFoundException $e) {
            throw new Exception_UserNotFound();
        }
        return $this->adapter->toUser($doc);
    }

    /**
     * @param User $user
     * @param User $creator
     * @throws \enork\Exception_PermissionDenied|\enork\Exception_UserExists
     */
    public function createUser(User $user, User $creator) {
        if(!$this->permissionManager->checkUserCreatePermission($creator)) {
            throw new Exception_PermissionDenied();
        }
        $doc = $this->adapter->fromUser($user);
        try {
            $this->couchClient->storeDoc($doc);
        }
        catch(\couchConflictException $e) {
            throw new Exception_UserExists();
        }
    }

    /**
     * Get a files by its path
     * @param string $path
     * @param \enork\User $user User asking to get the file
     * @return \enork\File
     */
    public function getFile($path, User $user) {
        $doc = $this->couchClient->getDoc("file:$path");
        $path = \lean\Text::offsetLeft($doc->_id, 'file:');
        $owner = \lean\Text::offsetLeft($doc->owner, 'user:');
        $file = new File($path, $owner, $doc->permissions);
        if(!$this->permissionManager->checkFilePermission($user, $file, self::PERMIT_READ)) {
            throw new Exception_PermissionDenied();
        }
        return $file;
    }
}

class Kernel_PermissionManager {
    /**
     * Check for permission to create a user.
     * @param User $user
     * @return bool
     */
    public function checkUserCreatePermission(User $user) {
        return $user->getUber();
    }

    /**
     * Check if a user has permission to access a file in ways of $permit (r/w/x)
     * @param User $user
     * @param File $file
     * @param string $permit
     * @return bool
     */
    public function checkFilePermission(User $user, File $file, $permit) {
        if($file->getOwner() == $user->getUname()) {
            return true;
        }

        return $this->checkPermissions($user, $file->getPermissions(), $permit);
    }

    /**
     * @param User $user
     * @param array $permissions
     * @param $requested the requested permission type
     * @return bool
     */
    protected function checkPermissions(User $user, array $permissions, $requested) {
        if($user->getUber()) {
            return true;
        }

        foreach($permissions as $permission) {
            // check if permit is the requested one
            if($permission->permit != $requested) {
                continue;
            }

            // check if user is in group permission is valid for
            if($permission->type == self::PERMISSION_TYPE_GROUP) {
                if(in_array($permission->group, $user->getGroups()))
                    return true;
            }
            // check if user has an explicit permission
            else if($permission->type == self::PERMISSION_TYPE_USER && $permission->user == $user->getUname()) {
                return true;
            }
        }
        return false;
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

    }

    /**
     * @param \object $doc
     * @return File
     */
    public function toFile($doc) {

    }
}