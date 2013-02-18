<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class Cabinet {
    /**
     * @param \couchClient $client
     */
    public function __construct(\couchClient $client) {
        $this->client = $client;
    }

    /**
     * @param $path
     *
     * @return File
     */
    public function createFile($path) {
        $document = new \stdClass();
        $document->_id = 'stack:/' . $path;
        $file = new File($document);
        $this->saveFile($file);
        return $file;
    }

    /**
     * Save a file
     *
     * @param File $file
     */
    public function saveFile(File $file) {
        $this->client->storeDoc($file->getDocument());
    }

    /**
     * Read a file
     *
     * @param string $path
     *
     * @return File
     */
    public function fetchFile($path) {
        $document = $this->client->getDoc('stack:/' . $path);
        return new File($document);
    }

    /**
     * Delete a file
     *
     * @param File $file
     */
    public function deleteFile(File $file) {
        $this->client->deleteDoc($file->getDocument());
    }

    /**
     * Check if a file exists
     *
     * @param string $path
     *
     * @return bool
     */
    public function fileExists($path) {
        try {
            $this->fetchFile($path);
            return true;
        } catch(\couchNotFoundException $e) {
            return false;
        }
    }
}