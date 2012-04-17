<?php
namespace enork\kernel;

interface Context {
    /**
     * Check for permission to create a user.
     *
     * @param User $user the user to be created
     *
     * @return bool
     */
    public function checkUserCreatePermission(\enork\User $user);

    /**
     * Check for permission to delete a user.
     *
     * @param User $user the user to be deleted
     *
     * @return bool
     */
    public function checkUserDeletePermission(\enork\User $user);

    /**
     * Check if a user has permission to access a file in ways of $permission (r/w/x)
     *
     * @param File     $file
     * @param string   $permission
     *
     * @internal param string $permit
     *
     * @return bool
     */
    public function checkFilePermission(\enork\File $file, $permission);
}