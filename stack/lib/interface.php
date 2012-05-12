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
 * I know how to decide if a file may be accessed or not.
 */
interface Interface_Security {
    /**
     * Check if a user has permission to access a file in ways of $permission (r/w/x)
     *
     * @param \stack\filesystem\File  $file
     * @param string                  $priviledge
     * @return bool
     */
    public function checkFilePermission(\stack\filesystem\File $file, $priviledge);
}

/**
 * I know how to hold module factories.
 */
interface Interface_ModuleRegistry {
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

/**
 * I know how to adapt couchDB documents to File objects and back.
 */
interface Interface_Adapter {
    public function fromDatabase($doc);

    public function toDatabase(\stack\filesystem\File $doc);
}

/**
 * I know how to handle a security stack.
 */
interface Interface_SecurityAccess {
    /**
     * @abstract
     * @param Interface_Security $security
     * @return mixed
     */
    public function pushSecurity(Interface_Security $security);
    /**
     * @abstract
     * @return mixed
     */
    public function pullSecurity();
}

/**
 * I know how to handle files.
 */
interface Interface_FileAccess {
    /**
     * @param string $path
     * @return \stdClass
     * @throws Exception_FileNotFound
     */
    public function readFile($path);
    /**
     * @abstract
     * @param filesystem\File $file
     * @return mixed
     */
    public function writeFile(\stack\filesystem\File $file);
    /**
     * @abstract
     * @param filesystem\File $file
     * @return mixed
     */
    public function deleteFile(\stack\filesystem\File $file);
    /**
     * Factory reset method
     * @return void
     */
    public function nuke();
}

/**
 * I know how to convert objects of my type from and to json
 */
interface Interface_JSONizable {
    /**
     * @abstract
     * @return mixed
     */
    public function fromJSON();
    /**
     * @abstract
     * @return string
     */
    public function toJSON();
}