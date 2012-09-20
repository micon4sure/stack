<?php
namespace stack\web\module;
    /*
    * Copyright (C) 2012 Michael Saller
    * Licensed under MIT License, see /path/to/stack/LICENSE
    */

/**
 * Forward to a location saved in the module data
 */
class Forward extends BaseModule {

    const NAME = 'stack.forward';

    public function run() {
        return new \stack\web\Response_HTTP303('/system/web/login');
    }
}
?>