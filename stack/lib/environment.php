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

use stack\Filesystem;

/**
 * Custom stack environment
 */
class Environment extends \lean\Environment {
    public function __construct($environmentName) {
        parent::__construct(STACK_ROOT . '/stack/config/environment.ini', $environmentName);
    }


    /**
     * Create a Shell with the passed fileSystem or a created one
     *
     * @param \stack\Context $context
     * @param Filesystem $fileSystem
     * @return Shell
     */
    public function createShell(Context $context, $fileSystem) {
        return new Shell($context, $fileSystem);
    }

    /**
     * @param \stack\Context $context
     * @return Filesystem
     */
    public function createFilesystem(Context $context) {
        return new \stack\FileSystem($context, $this->get('stack.database.url'), $this->get('stack.database.name'));
    }
}