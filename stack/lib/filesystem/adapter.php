<?php
namespace stack\filesystem;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

/**
 * Adapts files from and to database for FileSystem
 * Including modules
 * Module adaption counts on public final function BaseModule_Abstract::getData to return its name.
 * This will be saved within the document.
 */
class Adapter_File implements \stack\Interface_Adapter {
    /**
     * @var \stack\Shell
     */
    private $shell;

    /**
     * @param \stack\FileSystem_Module $fileSystem
     */
    public function __construct(\stack\Shell $shell) {
        $this->shell = $shell;
    }

    /**
     * Adapt a stdClass object to a File instance.
     *
     * @param $doc
     * @throws Exception
     * @return \stack\filesystem\File
     */
    public function fromDatabase($doc) {
        // cut prefix from path
        $id = \lean\Text::offsetLeft($doc->_id, 'stack:/');
        $revision = isset($doc->_rev) ? $doc->_rev : null;
        $file = new \stack\filesystem\File($id, $doc->meta->owner, $revision);

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
            else if ($permission->entity == \stack\security\Permission_All::ENTITY_ID) {
                // group
                $file->addPermission(new \stack\security\Permission_All($permission->priviledge));
            }
            else
                throw new Exception('Unknown entity in permission');
        }

        // load module if module name exists
        if (isset($doc->moduleName)) {
            $module = $this->shell->createModule($doc->moduleName, $doc->module);
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
            $doc->moduleName = $file->getModule()->getName();
        }
        else {
            $doc->module = null;
        }
        return $doc;
    }
}