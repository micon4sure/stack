<?php
namespace enork\kernel;
/*
 * Copyright (C) <2012> <Michael Saller>
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
