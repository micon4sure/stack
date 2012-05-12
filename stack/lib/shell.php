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
 * Facade to the underlying implementation levels: Security, File and Module.
 * Also handles the current User's session (for the lifetime of the Shell object) and their current working file.
 */
class Shell implements Interface_ModuleRegistry, Interface_FileAccess {
    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var string
     */
    private $currentWorkingFile;

    /**
     * @var \stack\module\User
     */
    private $currentUser;

    /**
     * @var Context
     */
    private $context;

    /**
     * @param \stack\Context $context
     * @param \stack\Filesystem $fileSystem
     */
    public function __construct(Context $context, \stack\Filesystem $fileSystem) {
        $this->fileSystem = $fileSystem;
        $this->context = $context;
    }

    /**
     * Check the given credentials, throw Exception_UserNotFound if user is not in the system.
     *
     *
     * @param $uname
     * @param $password
     * @throws Exception_CorruptModuleInUserFile
     * @return bool
     */
    public function login($uname, $password) {
        $file = $this->readUser($uname);
        if(!$file->getModule() instanceof \stack\module\BaseModule) {
            throw new Exception_CorruptModuleInUserFile();
        }
        $user = $file->getModule();
        $loggedIn = $user->auth($password);
        if(!$loggedIn) {
            return false;
        }
        $this->currentUser = $user;
        return true;
    }

    /**
     * Read a user file and return its module
     *
     * @param string $uname
     * @return \stack\module\User
     * @throws Exception_UserNotFound
     */
    public function readUser($uname) {
        try {
            return $this->fileSystem->readFile(Root::ROOT_PATH_USERS . '/' . $uname);
        } catch(\stack\filesystem\Exception_FileNotFound $e) {
            throw new Exception_UserNotFound("The user with the uname '$uname' was not found.");
        }
    }

    /**
     * Read a group file and return its module
     *
     * @param string $gname
     * @return \stack\module\Group
     * @throws Exception_GroupNotFound
     */
    public function readGroup($gname) {
        try {
            return $this->fileSystem->readFile(Root::ROOT_PATH_USERS . '/' . $gname);
        } catch(\stack\filesystem\Exception_FileNotFound $e) {
            throw new Exception_GroupNotFound("The group with the gname '$gname' was not found.");
        }
    }

    /**
     * Unset the currently active user
     *
     * @return Shell
     */
    public function logout() {
        unset($this->currentUser);
        return $this;
    }

    /**
     * Check if a user is logged in to the shell
     *
     * @param null $message
     * @throws Exception_NeedToBeLoggedIn
     */
    public function checkLoggedIn($message = null) {
        if(!$this->currentUser !== null) {
            throw new Exception_NeedToBeLoggedIn($message ?: 'Need to be logged in to perform this action.');
        }
    }

    /**
     * Read a file, take out its module, call run on it with the args passed to the method (slightly changed)
     *
     * @param Context $context
     * @param $fileName
     * @throws Exception_ExecutionError
     */
    public function execute($fileName) {
        $file = $this->fileSystem->readFile($fileName);
        $args = func_get_args();
        array_shift($args); // shift fileName argument
        array_unshift($args, $this->context); // unshift the context as new first argument
        $module = $file->getModule();
        try {
            call_user_func_array(array($module, 'run'), $args);
        } catch(\Exception $e) {
            throw new Exception_ExecutionError("The file at path '$fileName' could not be executed\n\n : " . $e->getMessage() . "\n\n", 0, $e);
        }
    }

    /**
     * Classic change dir
     * @param string $path
     * @return \stack\filesystem\File
     */
    public function cd($path) {
        $this->checkLoggedIn();
        $this->context->checkTraversionPermissions($path);
        return $this->fileSystem->readFile($path);
    }

    /**
     * Initialize a new database
     */
    public function init() {
        $this->fileSystem->init();
    }

    /**
     * See if the
     * @param $path
     * @return bool
     */
    public function isInCWF($path) {
        $this->checkLoggedIn();
        return \lean\Text::left($this->getCurrentWorkingFile(), $path) == $path;
    }

    /**
     * @return string
     */
    public function getCurrentWorkingFile() {
        $this->checkLoggedIn();
        return $this->currentWorkingFile;
    }

    /**
     * @implements Interface_ModuleRegistry
     * @param $name
     * @param $callable
     */
    public function registerModule($name, $callable) {
        $this->fileSystem->registerModule($name, $callable);
    }

    /* : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : : FileAccess */
    /**
     * @param string $path
     * @throws filesystem\Exception_PermissionDenied
     * @return \stack\File
     */
    public function readFile($path) {
        $file = $this->fileSystem->readFile($path);
        if(!$this->context->checkFilePermission($file, Security_Priviledge::READ)) {
            throw new \stack\filesystem\Exception_PermissionDenied("READ (r) permission to file at path '$path' was denied.");
        }
        return $file;
    }

    /**
     * @param \stack\filesystem\File $file
     * @return \stack\filesystem\File
     * @throws Exception_PermissionDenied
     */
    public function writeFile(\stack\filesystem\File $file) {
        // check permission
        if(!$this->context->checkFilePermission($file, Security_Priviledge::WRITE)) {
            $path = $file->getPath();
            throw new Exception_PermissionDenied("WRITE (w) permission to file at path '$path' was denied.");
        }
        $this->fileSystem->writeFile($file);
        return $file;
    }

    /**
     * @param \stack\filesystem\File $file
     * @throws Exception_PermissionDenied
     * @return void
     */
    public function deleteFile(\stack\filesystem\File $file) {
        // check permission
        if(!$this->context->checkFilePermission($file, Security_Priviledge::DELETE)) {
            $path = $file->getPath();
            throw new Exception_PermissionDenied("DELETE (d) permission to file at path '$path' was denied.");
        }
        return $this->fileSystem->deleteFile($file);
    }

    /**
     * Factory reset method
     * @return void
     */
    public function nuke() {
        try {
            $rootFile = $this->fileSystem->readFile('/');
        }
        catch(Exception $e) {
            // database is not initialized
            return;
        }
        if(!$this->context->checkFilePermission($rootFile, Security_Priviledge::DELETE)) {
            throw new Exception_PermissionDenied("Permission to nuke denied!");
        }
        $this->fileSystem->nuke();
    }

}