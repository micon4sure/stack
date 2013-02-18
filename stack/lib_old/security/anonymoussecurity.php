<?php
namespace stack\security;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */


/**
 * Will always deny permission
 */
class AnonymousSecurity implements \stack\Interface_Security {
    /**
     * Check if a user has permission to access a file in ways of $permission (r/w/x)
     *
     * @param \stack\filesystem\File $file
     * @param string $priviledge
     *
     * @return bool
     */
    public function checkFilePermission(\stack\filesystem\File $file, $priviledge) {
        // check individual user and group permissions
        foreach ($file->getPermissions() as $permission) {
            // check priviledge name
            if ($permission->getPriviledge() != $priviledge) {
                continue;
            }

            // check for "all" permission
            if ($permission->getEntity() == \stack\security\Permission_All::ENTITY_ID) {
                return true;
            }
        }
    }
}