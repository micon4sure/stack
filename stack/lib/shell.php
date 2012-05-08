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
class Shell implements Interface_SecurityAccess, Interface_FileAccess, Interface_ModuleRegistry {
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $currentWorkingFile;

    /**
     * @var \stack\module\User
     */
    private $currentUser;

    /**
     * @param \stack\Filesystem $filesystem
     */
    public function __construct(\stack\Filesystem $filesystem) {
        $this->filesystem = $filesystem;
    }

    /**
     * Check the given credentials, throw Exception_UserNotFound if user is not in the system.
     *
     *
     * @param $uname
     * @param $password
     * @return bool
     * @throws Exception_UserNotFound
     */
    public function login($uname, $password) {
        try {
            $ufile = $this->filesystem->readFile(Root::ROOT_PATH_USERS . '/' . $uname);
        } catch(\stack\filesystem\Exception_FileNotFound $e) {
            throw new Exception_UserNotFound("The user with the uname '$uname' was not found.");
        }
        $user = $ufile->getModule();
        $loggedIn = $user->auth($password);
        if(!$loggedIn) {
            return false;
        }
        $this->currentUser = $user;
        return true;
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
    public function execute(Context $context, $fileName) {
        $file = $this->filesystem->readFile($fileName);
        $args = func_get_args();
        array_shift($args); // shift context argument
        array_shift($args); // shift fileName argument
        array_unshift($args, $context); // reunshift the context as first argument
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
        $this->filesystem->checkTraversionPermissions($path);
        return $this->filesystem->readFile($path);
    }

    /**
     * Initialize a new database
     */
    public function init() {
        $this->filesystem->init();
    }

    /**
     * Warning: destroys database
     * @return void
     */
    public function nuke() {
        $this->filesystem->nuke();
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
     * @implements FileAccess
     * @param string $path
     * @return \stdClass
     * @throws Exception_FileNotFound
     */
    public function readFile($path) {
        return $this->filesystem->readFile($path);
    }

    /**
     * @implements FileAccess
     * @param File $file
     * @return void
     */
    public function writeFile(\stack\filesystem\File $file) {
        return $this->filesystem->writeFile($file);
    }

    /**
     * @implements FileAccess
     * @param File $file
     * @return void
     */
    public function deleteFile($file) {
        return $this->filesystem->deleteFile($file);
    }

    /**
     * @implements Interface_ModuleRegistry
     * @param $name
     * @param $callable
     */
    public function registerModule($name, $callable) {
        $this->filesystem->registerModule($name, $callable);
    }

    /**
     * @implements Interface_SecurityAccess
     * @param Security $security
     */
    public function pushSecurity(Interface_Security $security) {
        $this->filesystem->pushSecurity($security);
    }

    /**
     * @implements Interface_SecurityAccess
     * @return Security
     */
    public function pullSecurity() {
        return $this->filesystem->pullSecurity();
    }
}