<?php
namespace enork\kernel;
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

class PrivilegedContext implements \enork\kernel\Context {

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