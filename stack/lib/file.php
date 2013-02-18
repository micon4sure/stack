<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class File {
    /**
     * @param \stdClass $document the raw document
     */
    public function __construct(\stdClass $document) {
        $this->document = $document;
    }

    /**
     * Get the path, internally saved as the document's id
     *
     * @return mixed
     */
    public function getPath() {
        return $this->document->_id;
    }

    /**
     * Get the actual raw document
     *
     * @return \stdClass
     */
    public function getDocument() {
        return $this->document;
    }

    /**
     * @param mixed $data
     */
    public function setData($data) {
        $this->document->data = $data;
    }

    /**
     * @return mixed
     */
    public function getData() {
        return $this->document->data;
    }
}