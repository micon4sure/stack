<?php
namespace stackos\security;
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

class DefaultSecurity implements \stackos\Security {
    /** Check if a user has permission to access a document in ways of $permission (r/w/x)
     *
     * @param Document $document
     * @param string   $priviledge
     *
     * @return bool
     */
    public function checkDocumentPermission(\stackos\module\UserModule $user, \stackos\Document $document, $priviledge) {
        // grant uber all priviledges
        if ($user->isUber()) {
            return true;
        }

        // grant owner all priviledges except execute
        if ($document->getOwner() == $user->getUname() && $priviledge != \stackos\Security_Priviledge::EXECUTE) {
            return true;
        }

        // check individual user and group permissions
        foreach ($document->getPermissions() as $permission) {
            // check priviledge name
            if ($permission->getPriviledge() != $priviledge) {
                continue;
            }
            // check if user has a group permission
            if ($permission->getEntity() == \stackos\security\Permission_Group::ENTITY_ID) {
                if (in_array($permission->getHolder(), $user->getGroups()))
                    return true;
            }
            // check if user has an explicit permission
            else if ($permission->getEntity() == \stackos\security\Permission_User::ENTITY_ID) {
                if ($permission->getHolder() == $user->getUname())
                    return true;
            }
        }
        return false;
    }
}