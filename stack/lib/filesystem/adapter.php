<?php
namespace stack\filesystem;
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

    public function toDatabase(\stack\filesystem\Document $doc);
}

/**
 * Adapter for the base document class
 */
class Adapter_Document implements Adapter {
    /**
     * @var \stack\filesystem\DocumentManager
     */
    private $manager;

    /**
     * @param \stack\filesystem\DocumentManager $manager
     */
    public function __construct(\stack\filesystem\DocumentManager $manager) {
        $this->manager = $manager;
    }

    /**
     * Adapt a stdClass object to a Document instance.
     *
     * @param $doc
     * @return \stack\filesystem\Document
     */
    public function fromDatabase($doc) {
        // cut prefix from path
        $id = \lean\Text::offsetLeft($doc->_id, 'stack:/');
        $revision = isset($doc->_rev) ? $doc->_rev : null;
        $document = new \stack\filesystem\Document($this->manager, $id, $doc->meta->owner, $revision);

        // add permissions
        foreach ($doc->meta->permissions as $permission) {
            if ($permission->entity == \stack\filesystem\security\Permission_User::ENTITY_ID) {
                // user
                $document->addPermission(new \stack\filesystem\security\Permission_User($permission->holder, $permission->priviledge));
            }
        else if ($permission->entity == \stack\filesystem\security\Permission_Group::ENTITY_ID) {
            // group
                $document->addPermission(new \stack\filesystem\security\Permission_Group($permission->holder, $permission->priviledge));
            }
            else
                throw new Exception('Unknown entity in permission');
        }

        // load module if module name exists
        if (isset($doc->module->name)) {
            $module = $this->manager->createModule($doc->module->name, $doc->module);
            $document->setModule($module);
        }
        return $document;
    }

    /**
     * Adapt a Document instance to a stdClass object
     *
     * @param \stack\filesystem\Document $document
     * @return \stdClass
     */
    public function toDatabase(\stack\filesystem\Document $document) {
        $doc = new \stdClass();
        // prepend prefix to path
        $doc->_id = 'stack:/' . $document->getPath();
        if ($document->getRevision() !== null) {
            $doc->_rev = $document->getRevision();
        }

        // create new database doc
        // - meta
        $doc->meta = new \stdClass;
        // -- permissions
        $permissions = $document->getPermissions();
        $doc->meta->permissions = array();
        foreach ($permissions as $permission) {
            $doc->meta->permissions[] = array(
                'holder' => $permission->getHolder(),
                'entity' => $permission->getEntity(),
                'priviledge' => $permission->getPriviledge());
        }
        $doc->meta->owner = $document->getOwner();
        // -- module
        if ($document->getModule() instanceof \stack\filesystem\module\BaseModule) {
            $doc->module = $document->getModule()->getData();
        }
        else {
            $doc->module = null;
        }
        return $doc;
    }
}