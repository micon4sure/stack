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



class Shell {
    /**
     * @var \stack\Filesystem
     */
    private static $defaultFilesystem;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $currentWorkingFile;

    private $loggedIn = false;

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
        return $this->loggedIn = $ufile->getModule()->auth($password);
    }

    public function checkLoggedIn($message = null) {
        if(!$this->loggedIn) {
            throw new Exception_NeedToBeLoggedIn($message ?: 'Need to be logged in to perform this action.');
        }
    }

    /**
     * Create an instance of Shell with the passed $access or self::$defaultAccess.
     * Throw exception if neither are set.
     * Take precedent to $access, overwrite defaultAccess with it if set.
     * :::
     * Be aware that the default filesystem can be overwritten at any time.
     * Method is possible subject to change, don't rely.
     * :::
     *
     * @static
     * @param null|\stack\Filesystem $filesystem
     * @throws filesystem\Exception_NeedAccess
     * @return Shell
     */
    public static function instance(\stack\Filesystem $filesystem = null) {
        if(isset($filesystem)) {
            self::$defaultFilesystem = $filesystem;
        }
        if(!isset(self::$defaultFilesystem)) {
            throw new \stack\filesystem\Exception_NeedAccess('Need to be passed a filesystem at least once.');
        }
        return new static(self::$defaultFilesystem);
    }

    /**
     * Classic change dir
     * @param string $path
     * @return \stack\filesystem\File
     */
    public function cd($path) {
        $this->filesystem->checkTraversionPermissions($path);
        return $this->filesystem->readFile($path);
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
        return \lean\Text::left($this->getCurrentWorkingFile(), $path) == $path;
    }

    public function getCurrentWorkingFile() {
        return $this->currentWorkingFile;
    }

    public function getUser($uname) {
        $uname =
        $file = $this->filesystem->readFile(Root::ROOT_PATH_USERS . "/$uname");
    }

    public function saveUser(\stack\module\User $user) {
    }
}