<?php
namespace stack\module;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

/**
 * Not more than a container for the group name at this point
 */
class Group extends BaseModule {

    const NAME = 'stack.group';

    private $gname;

    public function __construct($gname) {
        $this->gname = $gname;
    }

    public function getGname() {
        return $this->gname;
    }
    public function setGname($gname) {
        $this->gname = $gname;
    }
    protected function export($data) {
        return (object)array('gname' => $this->gname);
    }

    public static function create($data) {
        if(!isset($data->gname))
            throw new \InvalidArgumentException('Group name missing.');
        return new static($data->gname);
    }

    public function __toString() {
        return $this->getGname();
    }
}