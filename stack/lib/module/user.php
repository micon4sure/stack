<?php
namespace stack\module;
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

/**
 * User module.
 */
class User extends BaseModule {
    const NAME = 'stack.user';

    /**
     * Indicates if a user is uber or not
     * @var bool
     */
    private $uber = false;
    /**
     * System internal user name
     * @var string
     */
    private $uname;
    /**
     * Home directory of the user
     * @var string
     */
    private $home;
    /**
     * The groups the user is in. Array of group names
     * @var array
     */
    private $groups = array();
    /**
     * Hashing is done internally
     * @var string
     */
    private $password;

    /**
     * @param string $uname
     * @param string $home
     */
    public function __construct($uname, $home = null) {
        if($home === null) {
            $home = \stack\Root::ROOT_PATH_HOME . "/$uname";
        }
        $this->setUname($uname);
        $this->setHome($home);
    }

    /**
     * @return bool
     */
    public function isUber() {
        return $this->uber;
    }
    /**
     * @param $uber
     * @return User
     */
    public function setUber($uber) {
        $this->uber = $uber;
        return $this;
    }

    /**
     * @return string
     */
    public function getUname() {
        return $this->uname;
    }
    /**
     * @param $uname
     * @return User
     */
    public function setUname($uname) {
        $this->uname = $uname;
        return $this;
    }

    /**
     * @param $home
     * @return User
     */
    public function setHome($home) {
        $this->home = $home;
        return $this;
    }
    /**
     * @return string
     */
    public function getHome() {
        return $this->home;
    }

    /**
     * @param Group $group
     */
    public function addToGroup(Group $group) {
        $this->groups[] = $group->getGname();
    }

    /**
     * @return array
     */
    public function getGroups() {
        return $this->groups;
    }

    /**
     * Set the hash of the new password
     * @param $password
     * @throws \InvalidArgumentException
     */
    public function changePassword($password) {
        if(!\lean\Text::len($password)) {
            throw new \InvalidArgumentException('No password provided. Password must not be empty.');
        }
        $this->password = sha1($password);
    }

    /**
     * @param $password
     */
    public function setPasswordHash($hash) {
        $this->password = $hash;
    }

    /**
     * @param $data
     * @return object
     */
    protected function export($data) {
        return (object)array(
            'uname' => $this->getUname(),
            'password' => $this->password,
            'home' => $this->getHome(),
            'uber' => $this->isUber(),
            'groups' => $this->groups);
    }

    /**
     * @static
     * @param $data
     * @return User
     * @throws \InvalidArgumentException
     */
    public static function create($data) {
        if(!isset($data->uname, $data->home))
            throw new \InvalidArgumentException('User parameters are missing data. Need uname and home.');
        $instance = new static($data->uname, $data->home);
        $uber = isset($data->uber) ? $data->uber : false;
        $instance->setUber($uber);
        $password = isset($data->password) ? $data->password : false;
        $instance->setPasswordHash($password);
        return $instance;
    }

    /**
     * Check if the password hash matches the saved one
     * @param $password
     * @return bool
     */
    public function auth($password) {
        return sha1($password) == $this->password;
    }

}