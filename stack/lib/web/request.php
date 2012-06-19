<?php
namespace stack\web;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */


class Request {
    private $stack_internal_items;
    public function __construct($items = array()) {
        $this->stack_internal_items = $items;
    }
    public function __get($key) {
        return $this->stack_internal_items[$key];
    }
    public function export() {
        return (object)['data' => $this->stack_internal_items];
    }
}