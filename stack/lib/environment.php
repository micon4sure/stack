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

class Environment extends \lean\Environment {
    public function __construct($environmentName) {
        parent::__construct(STACK_ROOT . '/stack/config/environment.ini', $environmentName);
    }
    /**
     * Create a Shell with the passed filesystem or a created one
     *
     * @param null $filesystem
     * @return Shell
     */
    public function createShell($filesystem = null) {
        return new Shell($filesystem ?: $this->createFilesystem());
    }
    /**
     * Create a new FileManager from the data provided by the environment configuration
     * @return filesystem\FileManager
     */
    public function createFileManager() {
        return new \stack\filesystem\FileManager_Module($this->get('stack.database.url'), $this->get('stack.database.name'));
    }
    /**
     * Create a new filesystem with the provided manager or a created one
     * @return Filesystem
     */
    public function createFilesystem($manager = null) {
        return new Filesystem($manager ?: $this->createFileManager());
    }
}