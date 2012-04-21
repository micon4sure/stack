<?php
namespace stackos;

interface Protocol {
    /**
     * Try to log the user in
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
     * @throws \stackos\Exception_MissingContext
     * @throws \stackos\Exception_PermissionDenied PERMISSION_DENIED_CANT_CREATE_ROOT_USER
     * @throws \stackos\Exception_UserExists
     * @return boolean
     */
    public function createUser(User $user);

    /** Get a files by its path
     *
     * @param string $path
     * @throws \stackos\Exception_MissingContext
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