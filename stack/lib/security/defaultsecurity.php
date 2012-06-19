<?php
namespace stack\security;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

/**
 * Checks permissions against a user
 */
class DefaultSecurity implements \stack\Interface_Security {
    /**
     * @var \stack\module\User
     */
    private $user;

    /**
     * @param \stack\module\User $user
     */
    public function __construct(\stack\module\User $user) {
        $this->user = $user;
    }

    /**
     * Check if a user has permission to access a file in ways of $permission (r/w/x)
     *
     * @param \stack\filesystem\File $file
     * @param string $priviledge
     *
     * @return bool
     */
    public function checkFilePermission(\stack\filesystem\File $file, $priviledge) {
        // grant uber all priviledges
        if ($this->user->isUber()) {
            return true;
        }

        // grant owner all priviledges except execute
        if ($file->getOwner() == $this->user->getUname() && $priviledge != \stack\Security_Priviledge::EXECUTE) {
            return true;
        }

        // check individual user and group permissions
        foreach ($file->getPermissions() as $permission) {
            // check priviledge name
            if ($permission->getPriviledge() != $priviledge) {
                continue;
            }
            // check if user has a group permission
            if ($permission->getEntity() == \stack\security\Permission_Group::ENTITY_ID) {
                if (in_array($permission->getHolder(), $this->user->getGroups()))
                    return true;
            }
            // check if user has an explicit permission
            else if ($permission->getEntity() == \stack\security\Permission_User::ENTITY_ID) {
                if ($permission->getHolder() == $this->user->getUname())
                    return true;
            }
        }
        return false;
    }
}