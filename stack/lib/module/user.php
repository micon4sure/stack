<?php
namespace stack\module;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

use stack\Module;

class User extends Module {

    /**
     * @param \stdClass $data
     */
    public function __construct($data) {
        parent::__construct($data);
        if(!isset($this->data->groups)) {
            // make sure groups array exists
            $this->data->groups = [];
        }
    }

    /**
     * @param $uname
     *
     * @return User
     */
    public static function create($uname) {
        $data = new \stdClass();
        $data->uname = $uname;
        $data->groups = [];
        return new self($data);
    }

    /**
     * @param string $name
     *
     * @throws Exception
     */
    public function addToGroup($name) {
        if(in_array($name, $this->data->groups)) {
            throw new Exception('User already in group.');
        }
        $this->data->groups[] = $name;
    }

    /**
     * @return string[]
     */
    public function getGroups() {
        return $this->data->groups;
    }

    /**
     * @return string
     */
    public function getUname() {
        return $this->data->uname;
    }
}