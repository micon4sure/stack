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

class Context extends \lean\Registry_State implements Interface_ModuleRegistry {
    /**
     * @var Environment
     */
    private $environment;
    /**
     * @var Shell
     */
    private $shell;
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var filesystem\FileManager
     */
    private $fileManager;

    /**
     * @var Context
     */
    private static $instance;

    public static function instance() {

    }

    /**
     * Initiate a context for the passed environment
     *
     * @param Environment $environment
     */
    public function __construct(Environment $environment) {
        $this->environment = $environment;
    }

    /**
     * @return Environment
     */
    public function getEnvironment() {
        return $this->environment;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem() {
        if($this->filesystem)
            return $this->filesystem;
        $this->filesystem = $this->environment->createFileSystem();
        return $this->filesystem;
    }

    /**
     * Register a module class bei their class name.
     * Register module with the NAME constant of the class and the static method create as factory
     * @param string $name
     * @param $callable
     */
    public function registerModule($name, $callable) {
        $this->getFileManager()->registerModule($name, $callable);
    }

    /**
     * @return Shell
     */
    public function getShell() {
        if($this->shell) {
            return $this->shell;
        }
        $fs = $this->getFilesystem();
        return $this->shell = $this->environment->createShell($fs);
    }

    protected function getFileManager() {
        return $this->filesystem->getFileManger();
    }
}