<?php
namespace stackos\module;

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
 * Describes the interface of an adapter that will adapt a document to a database compatible format and back
 */
interface Adapter {
    public function fromDatabase($doc);
    public function toDatabase(\stackos\Document $doc);
}

/**
 * Adapter for the base document class
 */
class Adapter_Document implements Adapter {
    /**
     * @var \stackos\Kernel
     */
    private $kernel;

    /**
     * @param \stackos\Kernel $kernel
     */
    public function __construct(\stackos\Kernel $kernel) {
        $this->kernel = $kernel;
    }

    /**
     * @param $doc
     * @return \stackos\Document
     */
    public function fromDatabase($doc) {
        // cut prefix from path
        $id = \lean\Text::offsetLeft($doc->_id, 'sodoc:');
        $revision = isset($doc->_rev) ? $doc->_rev : null;
        $document = new \stackos\Document($this->kernel, $id, \stackos\ROOT_UNAME, $revision);
        return $document;
    }

    /**
     * @param \stackos\Document $document
     * @return \stdClass
     */
    public function toDatabase(\stackos\Document $document) {
        $doc = new \stdClass();
        // prepend prefix to path
        $doc->_id = 'sodoc:' . $document->getPath();
        if($document->getRevision() !== null) {
            $doc->_rev = $document->getRevision();
        }
        $doc->owner = $document->getOwner();
        return $doc;
    }
}