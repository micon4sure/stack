<?php
namespace stack\module\run;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class AddGroup extends \stack\module\BaseModule {

    const NAME = \stack\Root_Modules::MODULE_ADDGROUP;

    protected function export($data) {
        return $data;
    }

    public function run(\stack\Context $context, $gname) {
        $group = new \stack\module\Group($gname);
        $file = new \stack\filesystem\File(\stack\Root::ROOT_PATH_USERS . '/' . $group->getGname(), \stack\Root::ROOT_UNAME);
        $file->setModule($group);
        $context->getShell()->writeFile($file);
    }
}