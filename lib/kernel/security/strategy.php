<?php
namespace stackos\kernel\security;

interface Strategy {
    /** Group context: PERMISSION_\w+ for group $foo
     */
    const PERMISSION_HOLDER_TYPE_GROUP = 'g';
    /** Group context: PERMISSION_\w+ for user $bar
     */
    const PERMISSION_HOLDER_TYPE_USER = 'u';

    /** Check for permission to create a user.
     *
     * @param User $user the user to be created
     *
     * @return bool
     */
    public function checkUserCreatePermission(\stackos\User $user);

    /** Check for permission to delete a user.
     *
     * @param User $user the user to be deleted
     *
     * @return bool
     */
    public function checkUserDeletePermission(\stackos\User $user);

    /** Check if a user has permission to access a document in ways of $permission (r/w/x)
     *
     * @param Document $document
     * @param string   $permission
     *
     * @return bool
     */
    public function checkDocumentPermission(\stackos\User $user, \stackos\Document $document, $permission);
}