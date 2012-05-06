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

// use
use stack\filesystem\File;
use stack\filesystem\FileAccess;
use stack\security;
use stack\security_Priviledge;

/**
 * Facade for the filesystem
 */
class Filesystem implements FileAccess, SecurityAccess {
    /**
     * @var FileManager
     */
    private $fileManager;
    /**
     * @var array
     */
    private $securityStack = array();
    /**
     * @param \stack\filesystem\FileManager_Module $module
     */
    public function __construct(\stack\filesystem\FileManager_Module $module) {
        $this->fileManager = $module;
    }

    /**
     * This method should ne called from outside \stack\filesystem
     *
     * @return FileManager|filesystem\FileManager_Module
     */
    public function getFileManger() {
        return $this->fileManager;
    }

    /**
     * @param Security $security
     */
    public function pushSecurity(Security $security) {
        array_push($this->securityStack, $security);
    }
    /**
     * @return  Security
     */
    public function pullSecurity() {
        return array_pop($this->securityStack);
    }
    /**
     * @return  Security
     */
    protected function currentSecurity() {
        if(!count($this->securityStack))
            throw new \stack\filesystem\Exception_NoSecurity('Filesystem needs Security.');
        return end($this->securityStack);
    }

    /**
     * @param string $path
     * @return \stdClass
     * @throws Exception_FileNotFound
     * @throws Exception_PermissionDenied
     */
    public function readFile($path) {
        $file = $this->fileManager->readFile($path);
        if(!$this->currentSecurity()->checkFilePermission($file, Security_Priviledge::READ)) {
            throw new \stack\filesystem\Exception_PermissionDenied("READ (r) permission to file at path '$path' was denied.");
        }
        return $file;
    }

    public function readFilesInPath($path) {
        $fileNames = array_filter(explode('/', $path));
        $paths = array();
        // attention: recycling path var
        $path = '';
        foreach($fileNames as $fileName) {
            $paths[] = $path .= "/$fileName";
        }
        return $this->readFiles($paths);
    }

    /**
     * Read an array of paths
     * @param array $paths
     * @return array
     */
    public function readFiles(array $paths) {
        $result = array();
        foreach($paths as $path) {
            $result[] = $this->readFile($path);
        }
        return $result;
    }

    /**
     * @param File $file
     */
    public function writeFile($file) {
        // check permission
        if(!$this->currentSecurity()->checkFilePermission($file, Security_Priviledge::WRITE)) {
            $path = $file->getPath();
            throw new Exception_PermissionDenied("WRITE (w) permission to file at path '$path' was denied.");
        }
        $this->fileManager->writeFile($file);
        return $file;
    }

    /**
     * @param File $file
     * @return void
     */
    public function deleteFile($file) {
        // check permission
        if(!$this->currentSecurity()->checkFilePermission($file, Security_Priviledge::DELETE)) {
            $path = $file->getPath();
            throw new Exception_PermissionDenied("DELETE (d) permission to file at path '$path' was denied.");
        }
        return $this->fileManager->deleteFile($file);
    }

    /**
     * Check if current security will allow READ(r) of all files in the path
     * @param $path
     * @return bool
     */
    public function checkTraversionPermissions($path) {
        $this->pushSecurity(new \stack\security\PriviledgedSecurity());
        try {
            $files = $this->readFilesInPath($path);
        }
        catch(\stack\filesystem\Exception_PermissionDenied $e) {
            $this->pullSecurity();
            return false;
        }
        catch(\Exception $e) {
            $this->pullSecurity();
            throw $e;
        }
        $this->pullSecurity();
        foreach($files as $file) {
            if(!$this->currentSecurity()->checkFilePermission($file, Security_Priviledge::EXECUTE)) {
                return false;
            }
        }
        return true;
    }

    public function init() {
        $this->fileManager->init();
    }

    /**
     * Warning: destroys database
     * @return void
     */
    public function nuke() {
        $this->fileManager->nuke();
    }

    public function createFile($path, $owner) {
        $file = new \stack\filesystem\File($path, $owner);
        $this->writeFile($file);
        return $file;
    }

    /**
     * Register a module factory callable
     *
     * @implements Shell_ModuleRegistry
     * @param string $name
     * @param \Closure $factory
     * @throws Exception_ModuleConflict|Exception_ModuleFactoryNotCallable
     * @throws Exception_ModuleConflict
     */
    public function registerModule($name, $factory) {
        $this->fileManager->registerModule($name, $factory);
    }
}