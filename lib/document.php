<?php
namespace stackos;

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
 * Abstraction of a file in the system
 */
class Document {
    /**
     * @var Kernel
     */
    private $kernel;
    /**
     * @var string
     */
    private $path;
    /**
     * @var null|string
     */
    private $revision;
    /**
     * @var string
     */
    private $owner;

    /**
     * @param Kernel $kernel
     * @param string $path
     * @param string $owner
     * @param string $revision
     */
    public function __construct(Kernel $kernel, $path, $owner, $revision = null) {
        $this->kernel = $kernel;
        $this->path = $path;
        $this->owner = $owner;
        $this->revision = $revision;
    }

    /**
     * @return Kernel
     */
    protected function getKernel() {
        return $this->kernel;
    }

    /**
     * Save the document in the database
     */
    public function save() {
        $this->kernel->writeDocument($this);
    }

    /**
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Return the revision of the document or null if it has not been saved yet
     * @return null|string
     */
    public function getRevision() {
        return $this->revision;
    }

    /**
     * @param $owner
     */
    public function setOwner($owner) {
        $this->owner = $owner;
    }

    /**
     * @return string
     */
    public function getOwner() {
        return $this->owner;
    }
}