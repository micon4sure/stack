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

class PrivilegedStrategy implements Strategy {

    /**
     * Check for permission to create a user.
     *
     * @param \stackos\User $user the user to be created
     * @return bool
     */
    public function checkUserCreatePermission(\stackos\User $user) {
        return true;
    }

    /**
     * Check for permission to delete a user.
     *
     * @param \stackos\User $user the user to be deleted
     * @return bool
     */
    public function checkUserDeletePermission(\stackos\User $user) {
        return true;
    }

    /** Check if a user has permission to access a document in ways of $permission (r/w/x)
     *
     * @param \stackos\User     $user
     * @param \stackos\Document $document
     * @param string            $permission
     *
     * @return bool
     */
    public function checkDocumentPermission(\stackos\User $user, \stackos\Document $document, $permission) {
        return true;
    }
}