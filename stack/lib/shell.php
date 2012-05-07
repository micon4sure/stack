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

interface Shell_ModuleRegistry {
    /**
     * Register a module factory callable
     *
     * @param string $name
     * @param callable $factory
     * @throws Exception_ModuleConflict|Exception_ModuleFactoryNotCallable
     * @throws Exception_ModuleConflict
     */
    public function registerModule($name, $factory);
}


class Shell implements \stack\filesystem\FileAccess, Shell_ModuleRegistry, SecurityAccess {
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

    public function login($uname, $password) {
        try {
            $ufile = $this->filesystem->readFile(Root::ROOT_PATH_USERS . '/' . $uname);
        } catch(\stack\filesystem\Exception_FileNotFound $e) {
            throw new Exception_UserNotFound("The user with the uname '$uname' was not found.");
        }
        $this->currentUser = $ufile->getModule()->auth($password);

    }

    /**
     * Check if a user is logged in to the shell
     *
     * @param null $message
     * @throws Exception_NeedToBeLoggedIn
     */
    public function checkLoggedIn($message = null) {
        if(!$this->loggedIn) {
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
            throw new Exception_ExecutionError(
                "The file at path '$fileName' could not be executed\n\n : " . $e->getMessage() . "\n\n", 0, $e
            );
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
     * @implements Shell_ModuleRegistry
     * @param $name
     * @param $callable
     */
    public function registerModule($name, $callable) {
        $this->filesystem->registerModule($name, $callable);
    }

    /**
     * @implements SecurityAccess
     * @param Security $security
     */
    public function pushSecurity(Security $security) {
        $this->filesystem->pushSecurity($security);
    }

    /**
     * @implements SecurityAccess
     * @return Security
     */
    public function pullSecurity() {
        return $this->filesystem->pullSecurity();
    }
}