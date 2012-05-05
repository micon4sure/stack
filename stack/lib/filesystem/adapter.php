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
 * Describes the interface of an adapter that will adapt a file to a database compatible format and back
 */
interface Adapter {
    public function fromDatabase($doc);

    public function toDatabase(\stack\filesystem\File $doc);
}

/**
 * Adapts files from and to database for FileManager
 * Including modules
 */
class Adapter_File implements Adapter {
    /**
     * @var \stack\filesystem\FileManager
     */
    private $manager;

    /**
     * @param \stack\filesystem\FileManager $manager
     */
    public function __construct(\stack\filesystem\FileManager $manager) {
        $this->manager = $manager;
    }

    /**
     * Adapt a stdClass object to a File instance.
     *
     * @param $doc
     * @return \stack\filesystem\File
     */
    public function fromDatabase($doc) {
        // cut prefix from path
        $id = \lean\Text::offsetLeft($doc->_id, 'stack:/');
        $revision = isset($doc->_rev) ? $doc->_rev : null;
        $file = new \stack\filesystem\File($this->manager, $id, $doc->meta->owner, $revision);

        // add permissions
        foreach ($doc->meta->permissions as $permission) {
            if ($permission->entity == \stack\security\Permission_User::ENTITY_ID) {
                // user
                $file->addPermission(new \stack\security\Permission_User($permission->holder, $permission->priviledge));
            }
        else if ($permission->entity == \stack\security\Permission_Group::ENTITY_ID) {
            // group
                $file->addPermission(new \stack\security\Permission_Group($permission->holder, $permission->priviledge));
            }
            else
                throw new Exception('Unknown entity in permission');
        }

        // load module if module name exists
        if (isset($doc->module->name)) {
            $module = $this->manager->createModule($doc->module->name, $doc->module);
            $file->setModule($module);
        }
        return $file;
    }

    /**
     * Adapt a File instance to a stdClass object
     *
     * @param \stack\filesystem\File $file
     * @return \stdClass
     */
    public function toDatabase(\stack\filesystem\File $file) {
        $doc = new \stdClass();
        // prepend prefix to path
        $doc->_id = 'stack:/' . $file->getPath();
        if ($file->getRevision() !== null) {
            $doc->_rev = $file->getRevision();
        }

        // create new database doc
        // - meta
        $doc->meta = new \stdClass;
        // -- permissions
        $permissions = $file->getPermissions();
        $doc->meta->permissions = array();
        foreach ($permissions as $permission) {
            $doc->meta->permissions[] = array(
                'holder' => $permission->getHolder(),
                'entity' => $permission->getEntity(),
                'priviledge' => $permission->getPriviledge());
        }
        $doc->meta->owner = $file->getOwner();
        // -- module
        if ($file->getModule() instanceof \stack\module\BaseModule) {
            $doc->module = $file->getModule()->getData();
        }
        else {
            $doc->module = null;
        }
        return $doc;
    }
}