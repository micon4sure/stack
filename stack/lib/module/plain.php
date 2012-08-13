<?php
namespace stack\module;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

/**
 * Just stores data.
 */
class Plain extends BaseModule {

    const NAME = 'stack.plain';

    protected function export($data) {
        return $data;
    }
}
?>