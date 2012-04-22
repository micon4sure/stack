<?php
namespace stackos\kernel\security;

class AdhocStrategy implements \stackos\kernel\security\Strategy {

    const CALLBACK_CHECK_USER_CREATE = 'checkUserCreatePermission';
    const CALLBACK_CHECK_USER_DELETE = 'checkUserDeletePermission';
    const CALLBACK_CHECK_DOCUMENT = 'checkDocumentPermission';

    protected $defaultStrategy;
    protected $callbacks = array();

    public function __construct(\stackos\Kernel$kernel, \stackos\kernel\security\Strategy $defaultStrategy = null) {
        if($defaultStrategy === null) {
            $defaultStrategy = new \stackos\kernel\security\BaseStrategy($kernel);
        }
        $this->defaultStrategy = $defaultStrategy;

    }
    public function setCallback($name, $callable) {
        $this->callbacks[$name] = $callable;
    }

    /** Check for permission to create a user.
     *
     * @param User $user the user to be created
     *
     * @return bool
     */
    public function checkUserCreatePermission(\stackos\User $user) {
        if(array_key_exists(self::CALLBACK_CHECK_USER_CREATE, $this->callbacks)) {
            return call_user_func(array($this->defaultStrategy, $this->callbacks[self::CALLBACK_CHECK_USER_CREATE]), $user);
        }
        return $this->defaultStrategy->checkUserCreatePermission($user);
    }

    /** Check for permission to delete a user.
     *
     * @param User $user the user to be deleted
     *
     * @return bool
     */
    public function checkUserDeletePermission(\stackos\User $user) {
        if(array_key_exists(self::CALLBACK_CHECK_USER_DELETE, $this->callbacks)) {
            return call_user_func(array($this->defaultStrategy, $this->callbacks[self::CALLBACK_CHECK_USER_CREATE]), $user);
        }
        return $this->defaultStrategy->checkUserDeletePermission($user);
    }

    /** Check if a user has permission to access a document in ways of $permission (r/w/x)
     *
     * @param Document $document
     * @param string   $permission
     *
     * @return bool
     */
    public function checkDocumentPermission(\stackos\User $user, \stackos\Document $document, $permission) {
        if(array_key_exists(self::CALLBACK_CHECK_DOCUMENT, $this->callbacks)) {
            return call_user_func($this->callbacks[self::CALLBACK_CHECK_DOCUMENT], $user, $document, $permission);
        }
        return $this->defaultStrategy->checkDocumentPermission($user, $document, $permission);
    }
}