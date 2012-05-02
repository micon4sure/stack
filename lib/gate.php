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

class Gate implements DocumentAccess {
    /**
     * @var DocumentAccess
     */
    private $access;
    /**
     * @var array
     */
    private $securityStack = array();

    /**
     * @param DocumentAccess $access
     */
    public function __construct(DocumentAccess $access) {
        $this->access = $access;
    }

    /**
     * @param Security $security
     */
    public function pushSecurity(Security $security) {
        array_push($this->securityStack, $security);
    }
    /**
     * @return  Security
     */
    public function pullSecurity() {
        return array_pop($this->securityStack);
    }
    /**
     * @return  Security
     */
    protected function currentSecurity() {
        return end($this->securityStack);
    }

    /**
     * @param string $path
     * @return \stdClass
     * @throws Exception_DocumentNotFound
     */
    public function readDocument($path) {
        $document = $this->access->readDocument($path);
        if(!$this->currentSecurity()->checkDocumentPermission($document)) {

        }
    }

    /**
     * @param Document $document
     */
    public function writeDocument($document) {
        $this->access->writeDocument($document);
    }

    /**
     * @param Document $document
     */
    public function deleteDocument($document) {
        return $this->access->deleteDocument($document);
    }
}