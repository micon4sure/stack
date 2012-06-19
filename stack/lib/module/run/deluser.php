<?php
namespace stack\module\run;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class DelUser extends \stack\module\BaseModule {

    const NAME = \stack\Root_Modules::MODULE_DELUSER;

    public function run(\stack\Context $context, $uname) {
        $path = \stack\Root::ROOT_PATH_USERS . "/$uname";
        $file = $context->getShell()->readFile($path);
        $context->getShell()->deleteFile($file);
    }
}