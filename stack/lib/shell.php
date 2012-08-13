<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
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
     * Read a user file
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
     * Read a group file
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
     * Read a file, take out its module, call run on it with the args passed to the method (slightly changed)
     *
     * @param string $path
     * @throws Exception_ExecutionError
     * @throws filesystem\Exception_PermissionDenied
     * @internal param \stack\Context $context
     */
    public function execute($path) {
        $file = $this->readFile($path);
        if(!$this->context->checkFilePermission($file, Security_Priviledge::READ)) {
            throw new \stack\filesystem\Exception_PermissionDenied("Execute (x) permission to file at path '$path' was denied.");
        }
        $args = func_get_args();
        array_shift($args); // shift fileName argument
        array_unshift($args, $this->context); // unshift the context as new first argument
        $module = $file->getModule();
        try {
            call_user_func_array(array($module, 'run'), $args);
        } catch(\Exception $e) {
            throw new Exception_ExecutionError("The file at path '$path' could not be executed\n\n : " . $e->getMessage() . "\n\n", 0, $e);
        }
    }

    /**
     * Initialize a new database
     */
    public function init() {
        $this->fileSystem->init();
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
     * @return \stack\fileSystem\File
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
     * @throws Exception_PermissionDenied
     * @return void
     */
    public function nuke() {
        try {
            $rootFile = $this->fileSystem->readFile('/');
        }
        catch(Exception_FileNotFound $e) {
            // database is not initialized
            return;
        }
        if(!$this->context->checkFilePermission($rootFile, Security_Priviledge::DELETE)) {
            throw new Exception_PermissionDenied("Permission to nuke denied!");
        }
        $this->fileSystem->nuke();
    }

}