<?php
namespace stack\module;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

/**
 * User module.
 * Password is saved in SHA1 by changePassword.
 *
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
    private $uName;
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
    private $uPass;

    /**
     * @param string $uname
     * @param string $home
     */
    public function __construct($uName, $home = null) {
        if($home === null) {
            $home = \stack\Root::ROOT_PATH_HOME . "/$uName";
        }
        $this->setUname($uName);
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
        return $this->uName;
    }
    /**
     * @param $uname
     * @return User
     */
    public function setUname($uName) {
        $this->uName = $uName;
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
        $this->uPass = sha1($password);
    }

    /**
     * @param $password
     */
    public function setPasswordHash($hash) {
        $this->uPass = $hash;
    }

    /**
     * @param $data
     * @return object
     */
    protected function export($data) {
        return (object)array(
            'uName' => $this->getUname(),
            'uPass' => $this->uPass,
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
        if(!isset($data->uName, $data->uPass, $data->home))
            throw new \InvalidArgumentException('User parameters are missing data. Need uName, uPass and home.');
        $instance = new static($data->uName, $data->home);
        $uber = isset($data->uber) ? $data->uber : false;
        $instance->setUber($uber);
        $instance->setPasswordHash($data->uPass);
        return $instance;
    }

    /**
     * Check if the password hash matches the saved one
     * @param $password
     * @return bool
     */
    public function auth($password) {
        return $password == $this->uPass;
    }

}