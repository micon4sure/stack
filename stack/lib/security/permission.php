<?php
namespace stack\security;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class Permission {
    private $entity;
    private $holder;
    private $priviledge;

    public function __construct($holder, $priviledge, $entity) {
        $this->holder = $holder;
        $this->priviledge = $priviledge;
        $this->entity = $entity;
    }

    public function getHolder() {
        return $this->holder;
    }
    public function getPriviledge() {
        return $this->priviledge;
    }
    public function getEntity() {
        return $this->entity;
    }
}

class Permission_Group extends Permission {
    const ENTITY_ID = 'g';
    public function __construct($holder, $priviledge) {
        parent::__construct($holder, $priviledge, self::ENTITY_ID);
    }
}
class Permission_User extends Permission {
    const ENTITY_ID = 'u';
    public function __construct($holder, $priviledge) {
        parent::__construct($holder, $priviledge, self::ENTITY_ID);
    }
}