<?php
namespace enork\kernel;

class UserContext implements Context {
    /**
     * @var \enork\User
     */
    private $user;

    /**
     * @param \enork\User $user
     */
    public function __construct(\enork\User $user) {
        $this->setUser($user);
    }

    /**
     * Set the contextual user.
     *
     * @param \enork\User $user
     */
    public function setUser(\enork\User $user) {
        $this->user = $user;
    }


    /**
     * Check for permission to create a user.
     *
     * @param \enork\User $user
     * @return bool
     */
    public function checkUserCreatePermission(\enork\User $user) {
        return $this->user->getUber();
    }

    /**
     * Check for permission to delete a user.
     *
     * @param \enork\User $user
     * @return bool
     */
    public function checkUserDeletePermission(\enork\User $user) {
        return $this->user->getUber();
    }

    /**
     * Check if a user has permission to access a file in ways of $permission (r/w/x)
     *
     * @param \enork\File  $file
     * @param string       $permission
     * @return bool
     */
    public function checkFilePermission(\enork\File $file, $permission) {
        if ($file->getOwner() == $this->user->getUname()) {
            return true;
        }

        return $this->checkPermissions($file->getPermissions(), $permission);
    }

    /**
     * @param array  $permissions
     * @param string $requested the requested permission type
     * @return bool
     */
    protected function checkPermissions(array $permissions, $requested) {
        if ($this->user->getUber()) {
            return true;
        }

        foreach ($permissions as $permission) {
            // check if permit is the requested one
            if ($permission->permit != $requested) {
                continue;
            }

            // check if user is in group permission is valid for
            if ($permission->type == self::PERMISSION_TYPE_GROUP) {
                if (in_array($permission->group, $this->user->getGroups())) {
                    return true;
                }
            }
            // check if user has an explicit permission
            else if ($permission->type == self::PERMISSION_TYPE_USER && $permission->user == $this->user->getUname()) {
                return true;
            }
        }
        return false;
    }
}
