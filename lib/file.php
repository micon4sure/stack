<?php
namespace enork;
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

class File extends Document {
    private $path;
    private $owner;
    private $permissions;
    private $kernel;

    public function __construct(Kernel $kernel, $path, $owner, array $permissions) {
        parent::__construct($kernel);
        $this->path = $path;
        $this->owner = $owner;
        $this->permissions = $permissions;
        $this->kernel = $kernel;
    }

    public function setOwner($owner) {
        $this->owner = $owner;
    }

    public function getOwner() {
        return $this->owner;
    }

    public function setPath($path) {
        $this->path = $path;
    }

    public function getPath() {
        return $this->path;
    }

    public function getPermissions() {
        return $this->permissions;
    }

    /**
     * Get the parent file from the kernel, if any.
     * @return File
     * @throws Exception_RootHasNoParent|Exception_PermissionDenied|Exception_FileNotFound
     */
    public function getParent() {
        if ($this->getPath() == '/') {
            throw new Exception_RootHasNoParent("Root file '/' does not have a parent.");
        }
        return $this->kernel->getFile(dirname($this->getPath()));
    }

    protected function getKernel() {
        return $this->kernel;
    }
}