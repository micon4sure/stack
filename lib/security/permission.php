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

abstract class Permission {
    /**
     * @var string he type of holder [user/group]
     */
    private $entity;
    private $holder;
    private $priviledge;

    /**
     * @param string $entity
     * @param \stackos\User $holder
     * @param string $priviledge
     */
    protected function __construct($entity, $holder, $priviledge) {
        $this->entity = $entity;
        $this->holder = $holder;
        $this->priviledge = $priviledge;
    }

    public function getEntity() {
        return $this->entity;
    }

    public function getHolder() {
        return $this->holder;
    }

    public function getPriviledge() {
        return $this->priviledge;
    }
}

class Permission_Group extends Permission {
    public static function create($group, $priviledge) {
        return new self(Strategy::PERMISSION_ENTITY_GROUP, $group, $priviledge);
    }
}

class Permission_User extends Permission {
    public static function create($user, $priviledge) {
        return new self(Strategy::PERMISSION_ENTITY_USER, $user, $priviledge);
    }
}