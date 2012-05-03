<?php
namespace stack\filesystem;
/*
 * Copyright (C) 2012 Michael Saller
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * fileation files (the "Software"), to deal in the Software without restriction, including without limitation
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
const ROOT_PATH_HOME = '/home';
const ROOT_USER_PATH_HOME = '/root';
const ROOT_USER_PATH_SYSTEM = '/root/system';
const ROOT_USER_PATH_GROUPS = '/root/groups';
const ROOT_USER_PATH_USERS = '/root/users';
const ROOT_USER_PATH_USERS_ROOT = '/root/users/root';

/**
 * Interface to the File system, including Modules
 */
class FileManager implements FileAccess {
    /**
     * @var \couchClient
     */
    private $couchClient;

    /**
     * @var \stack\filesystem\module\Adapter
     */
    protected $adapter;

    /**
     * @var array
     */
    private $moduleFactories = array();

    /**
     * @param string $dsn
     * @param string $dbName
     */
    public function __construct($dsn, $dbName) {
        $this->couchClient = new \couchClient($dsn, $dbName);
    }

    /**
     * Register a module factory callable
     *
     * @param string $name
     * @param \Closure $moduleFactory
     * @throws Exception_ModuleConflict|Exception_ModuleFactoryNotCallable
     */
    public function registerModuleFactory($name, $moduleFactory) {
        if(!is_callable($moduleFactory))
            throw new Exception_ModuleFactoryNotCallable("The modulefactory '$name' is not callable");
        if(array_key_exists($name, $this->moduleFactories))
            throw new Exception_ModuleConflict("Module with name '$name' is already registered", Exception_ModuleConflict::MODULE_WITH_NAME_ALREADY_REGISTERED);
        $this->moduleFactories[$name] = $moduleFactory;
    }

    /**
     * Register a module class bei their class name.
     * Register module with the NAME constant of the class and the static method create as factory
     * @param string $name
     */
    public function registerModule($name) {
        $this->registerModuleFactory($name::NAME, array($name, 'create'));
    }

    /**
     * Create a module instance via a registered factory callable
     *
     * @param $name
     * @param $data
     * @return mixed
     * @throws Exception_ModuleNotFound
     */
    public function createModule($name, $data) {
        // call module factory to create module
        if(!isset($this->moduleFactories[$name]))
            throw new Exception_ModuleNotFound();
        $module = call_user_func($this->moduleFactories[$name], $data);
        // check for validity
        if(!$module instanceof \stack\filesystem\module\BaseModule) {
            throw new \stack\filesystem\Exception_InvalidModule($name, $module, $data);
        }
        return $module;
    }

    /**
     * Lazy adapter loader.
     * Deriving classes are encouraged to overwrite this and use the protected $adapter variable if doing so
     *
     * @return Adapter_File
     */
    protected function getAdapter() {
        return $this->adapter ?: $this->adapter = new Adapter_File($this);
    }

    /**
     * @param string $path
     * @return File
     * @throws Exception_FileNotFound
     */
    public function readFile($path) {
        try {
            $doc = $this->couchClient->getDoc("stack:/$path");
        } catch(\couchNotFoundException $e) {
            throw new Exception_FileNotFound("File at path '$path' could not be found", null, $e);
        }
        return $this->getAdapter()->fromDatabase($doc);
    }

    /**
     * @param File $file
     */
    public function writeFile($file) {
        // write the file to the file system
        // set revision to file instance
        $doc = $this->getAdapter()->toDatabase($file);
        $response = $this->couchClient->storeDoc($doc);
        $file->setRevision($response->rev);
        return $doc;
    }

    /**
     * @param File $file
     */
    public function deleteFile($file) {
        $this->couchClient->deleteDoc($this->adapter->toDatabase($file));
    }

    /**
     * Create the database and write system files
     */
    public function init() {
        $this->couchClient->createDatabase();
        $files = array(
            ROOT_PATH,
            ROOT_USER_PATH_SYSTEM,
            ROOT_USER_PATH_HOME,
            ROOT_USER_PATH_USERS,
            ROOT_USER_PATH_GROUPS
        );
        foreach ($files as $path) {
            $file = new File($this, $path, ROOT_UNAME);
            $this->writeFile($file);
        }
        $file = new File($this, ROOT_USER_PATH_USERS_ROOT, ROOT_UNAME);
        $file->setModule(new \stack\filesystem\module\User(ROOT_UNAME, ROOT_USER_PATH_HOME));
        $this->writeFile($file);
    }

    /**
     * Delete the database (if exists)
     */
    public function destroy() {
        if ($this->couchClient->databaseExists()) {
            $this->couchClient->deleteDatabase();
        }
    }
}