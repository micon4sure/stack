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

class AdhocStrategy implements \stackos\security\Strategy {

    const CALLBACK_CHECK_USER_CREATE = 'checkUserCreatePermission';
    const CALLBACK_CHECK_USER_DELETE = 'checkUserDeletePermission';
    const CALLBACK_CHECK_DOCUMENT = 'checkDocumentPermission';

    protected $defaultStrategy;
    protected $callbacks = array();

    public function __construct(\stackos\Kernel$kernel, \stackos\security\Strategy $defaultStrategy = null) {
        if ($defaultStrategy === null) {
            $defaultStrategy = new \stackos\security\BaseStrategy($kernel);
        }
        $this->defaultStrategy = $defaultStrategy;

    }

    public function setCallback($name, $callable) {
        $this->callbacks[$name] = $callable;
    }

    /** Check for permission to create a user.
     *
     * @param \stackos\User $user
     * @return bool
     */
    public function checkUserCreatePermission(\stackos\User $user) {
        if (array_key_exists(self::CALLBACK_CHECK_USER_CREATE, $this->callbacks)) {
            return call_user_func($this->callbacks[self::CALLBACK_CHECK_USER_CREATE], $user);
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
        if (array_key_exists(self::CALLBACK_CHECK_USER_DELETE, $this->callbacks)) {
            return call_user_func($this->callbacks[self::CALLBACK_CHECK_USER_DELETE], $user);
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
        if (array_key_exists(self::CALLBACK_CHECK_DOCUMENT, $this->callbacks)) {
            return call_user_func($this->callbacks[self::CALLBACK_CHECK_DOCUMENT], $user, $document, $permission);
        }
        return $this->defaultStrategy->checkDocumentPermission($user, $document, $permission);
    }
}