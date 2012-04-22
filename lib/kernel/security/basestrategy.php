<?php
namespace stackos\kernel\security;
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
class BaseStrategy implements Strategy {
    /**
     * @var \stackos\Kernel
     */
    private $kernel;

    /**
     * @param \stackos\Kernel $kernel
     */
    public function __construct(\stackos\Kernel $kernel) {
        $this->kernel = $kernel;
    }

    /**
     * @return \stackos\Kernel
     */
    protected function getKernel() {
        return $this->kernel;
    }

    /** Check for permission to create a user.
     *
     * @param User $user the user to be created
     *
     * @return bool
     */
    public function checkUserCreatePermission(\stackos\User $user) {
        $this->getKernel()->pushSecurityStrategy(new PrivilegedStrategy());
        $file = $this->getKernel()->getFile($user, '/root/users');
        $check = $this->checkDocumentPermission($user, $file, Priviledge::WRITE);
        $this->getKernel()->pullSecurityStrategy();
        return $check;
    }

    /**
     * Check for permission to delete a user.
     *
     * @param User $user the user to be deleted
     *
     * @return bool
     */
    public function checkUserDeletePermission(\stackos\User $user) {
        $this->getKernel()->pushSecurityStrategy(new PrivilegedStrategy());
        $file = $this->getKernel()->getFile($user, '/root/users');
        $check = $this->checkDocumentPermission($user, $file, Priviledge::WRITE);
        $this->getKernel()->pullSecurityStrategy();
        return $check;
    }

    /** Check if a user has permission to access a document in ways of $permission (r/w/x)
     *
     * @param \stackos\User     $user
     * @param \stackos\Document $document
     * @param string            $priviledge
     *
     * @return bool
     */
    public function checkDocumentPermission(\stackos\User $user, \stackos\Document $document, $priviledge) {
        // grant uber all priviledges
        if ($user->getUber()) {
            return true;
        }

        // grant file owner all priviledges except execute
        $check = $document instanceof \stackos\File
            && $document->getOwner() == $user->getUname()
            && $priviledge != Priviledge::EXECUTE;
        if($check)
            return true;

        // check for permissions inside document
        foreach ($document->getPermissions() as $permission) {
            // check if this permission has been requested
            if ($permission->getPriviledge() != $priviledge) {
                continue;
            }

            // check if user is in group permission is valid for
            if ($permission->getHolderType() == self::PERMISSION_HOLDER_TYPE_GROUP) {
                return (bool)array_intersect($user->getGroups(), $user->getGroups());
            }
            // check if user has an explicit permission
            else if ($permission->getHolderType() == self::PERMISSION_HOLDER_TYPE_USER) {
                return $permission->getHolder() == $user->getUname();
            }
        }
        return false;
    }
}