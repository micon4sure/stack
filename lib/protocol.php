<?php
namespace stackos;
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

interface Protocol {
    /** Try to log the user in
     *
     * @abstract
     * @param $user
     * @param $password
     * @return boolean
     * @throws Exception_PermissionDenied with PERMISSION_DENIED_CREDENTIALS_REVOKED
     */
    public function login($user, $password);

    /** Get a user by their uname
     *
     * @param string $uname
     * @return \stackos\User
     */
    public function getUser($uname);

    /** Create a user in the system
     *
     * @param User $user
     * @throws \stackos\Exception_MissingSecurityStrategy
     * @throws \stackos\Exception_PermissionDenied PERMISSION_DENIED_CANT_CREATE_ROOT_USER
     * @throws \stackos\Exception_UserExists
     * @return boolean
     */
    public function createUser(User $user);

    /** Get a files by its path
     *
     * @param string $path
     * @throws \stackos\Exception_MissingSecurityStrategy
     * @throws \stackos\Exception_PermissionDenied
     * @return \stackos\File
     */
    public function getFile($path);

    /** Write a file to the file system.
     * Check permissions to write to parent file first.
     *
     * @param File $file
     * @return File
     * @throws Exception_PermissionDenied
     */
    public function createFile(File $file);
}