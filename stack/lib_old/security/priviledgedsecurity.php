<?php
namespace stack\security;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

/**
 * Will always grant security permission
 */
class PriviledgedSecurity implements \stack\Interface_Security {
    /** Check if a user has permission to access a file in ways of $permission (r/w/x)
     *
     * @param \stack\filesystem\File $file
     * @param string $priviledge
     *
     * @internal param \stack\module\User $user
     * @return bool
     */
    public function checkFilePermission(\stack\filesystem\File $file, $priviledge) {
        return true;
    }
}