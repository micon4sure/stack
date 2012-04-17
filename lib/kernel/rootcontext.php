<?php
namespace enork\kernel;

class RootContext implements \enork\kernel\Context {

    /**
     * Check for permission to create a user.
     *
     * @param \enork\User $user the user to be created
     * @return bool
     */
    public function checkUserCreatePermission(\enork\User $user) {
        return true;
    }

    /**
     * Check for permission to delete a user.
     *
     * @param \enork\User $user the user to be deleted
     * @return bool
     */
    public function checkUserDeletePermission(\enork\User $user) {
        return true;
    }

    /**
     * Check if a user has permission to access a file in ways of $permission (r/w/x)
     *
     * @param \enork\File  $file
     * @param string       $permission
     * @return bool
     */
    public function checkFilePermission(\enork\File $file, $permission) {
        return true;
    }
}