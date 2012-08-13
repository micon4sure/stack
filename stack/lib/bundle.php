<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, which is found under /path/to/stack/LICENSE
 */


/**
 * I loosely hold together a set of modules and upon request add them to the application
 */
interface Bundle {
    public function registerModules(Shell $shell);
}

/**
 * Core functionality
 */
class Bundle_Core implements Bundle {
    /**
     * @param Shell $shell
     */
    public function registerModules(Shell $shell) {
        $shell->registerModule(Root_Modules::MODULE_PLAIN, function($data) {
            return new \stack\module\Plain($data);
        });
        $shell->registerModule(Root_Modules::MODULE_USER, function($data) {
            return \stack\module\User::create($data);
        });
        $shell->registerModule(Root_Modules::MODULE_GROUP, function($data) {
            return \stack\module\Group::create($data);
        });
        $shell->registerModule(Root_Modules::MODULE_ADDUSER, function($data) {
            return \stack\module\run\AddUser::create($data);
        });
        $shell->registerModule(Root_Modules::MODULE_DELUSER, function($data) {
            return \stack\module\run\DelUser::create($data);
        });
        $shell->registerModule(Root_Modules::MODULE_ADDGROUP, function($data) {
            return \stack\module\run\AddGroup::create($data);
        });
        $shell->registerModule(Root_Modules::MODULE_DELGROUP, function($data) {
            return \stack\module\run\DelGroup::create($data);
        });
    }    
}

/**
 * Web bundle
 */
class Bundle_Web implements Bundle {

    /**
     * @param Shell $shell
     */
    public function registerModules(Shell $shell) {
        $shell->registerModule(\stack\web\module\Login::NAME, function($data) {
            return new \stack\web\module\Login($data);
        });
    }
}