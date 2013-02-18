<?php
namespace stack\module\run;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class DelGroup extends \stack\module\BaseModule {

    const NAME = \stack\Root_Modules::MODULE_DELGROUP;

    public function run(\stack\Context $context, $gname) {
        $path = \stack\Root::ROOT_PATH_USERS . "/$gname";
        $file = $context->getShell()->readFile($path);
        $context->getShell()->deleteFile($file);
    }
}