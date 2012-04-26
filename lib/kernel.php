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

const ROOT_UNAME = 'root';
const ROOT_PATH = '/';
const ROOT_PATH_HOME = '/root';
const ROOT_PATH_USERS = '/root/users';
const ROOT_PATH_GROUPS = '/root/groups';

/**
 * Interface to the file system
 */
class Kernel {
    /**
     * @var \couchClient
     */
    private $couchClient;

    /**
     * @param string $dsn
     * @param string $dbName
     */
    public function __construct($dsn, $dbName) {
        $this->couchClient = new \couchClient($dsn, $dbName);
        $this->adapter = new \stackos\module\Adapter_Document($this);
    }

    /**
     * @param string $path
     * @return Document
     * @throws Exception_DocumentNotFound
     */
    public function readDocument($path) {
        try {
            $doc = $this->couchClient->getDoc("sodoc:$path");
        } catch(\couchNotFoundException $e) {
            throw new Exception_DocumentNotFound("Document at path '$path' could not be found", Exception_DocumentNotFound::CODE, $e);
        }
        return $this->adapter->fromDatabase($doc);
    }

    /**
     * @param Document $document
     */
    public function writeDocument(Document $document) {
        $doc = $this->adapter->toDatabase($document);
        $this->couchClient->storeDoc($doc);
    }

    /**
     * @param Document $document
     */
    public function deleteDocument(Document $document) {
        $this->couchClient->deleteDoc($this->adapter->toDatabase($document));
    }

    /**
     * Create the database and write system files
     */
    public function init() {
        $this->couchClient->createDatabase();
        $files = array(
            ROOT_PATH,
            ROOT_PATH_HOME,
            ROOT_PATH_USERS,
            ROOT_PATH_GROUPS
        );
        foreach ($files as $path) {
            $document = new Document($this, $path, ROOT_UNAME);
            $this->writeDocument($document);
        }
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