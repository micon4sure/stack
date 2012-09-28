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
    /**
     * @param Shell $shell
     * @return mixed
     */
    public function registerModules(Shell $shell);

    /**
     * @return string
     */
    public function getName();
}

/**
 * I have web assets and I know where they are.
 */
interface Bundle_AssetProvider {
    /**
     * @return mixed
     */
    public function getAssetPath();
}

abstract class Bundle_Abstract implements Bundle {
    /**
     * @var Application
     */
    private $application;

    /**
     * @param Application $application
     */
    public function __construct(Application $application) {
        $this->application = $application;
    }

    /**
     * @return Application
     */
    protected function getApplication() {
        return $this->application;
    }

    /**
     * @return string
     */
    public function getName() {
        return static::NAME;
    }
}

/**
 * Core functionality
 */
class Bundle_Core extends Bundle_Abstract implements Bundle_AssetProvider {

    const NAME = 'stack.bundle.core';

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

    /**
     * @return string
     */
    public function getAssetPath() {
        return STACK_ROOT . '/www/' . self::NAME;
    }
}

/**
 * Web bundle
 */
class Bundle_Web extends Bundle_Abstract implements Bundle_AssetProvider {

    const NAME = 'stack.bundle.web';

    /**
     * @param Shell $shell
     */
    public function registerModules(Shell $shell) {
        $shell->registerModule(\stack\module\web\Forward::NAME, function($data) {
                return new \stack\module\web\Forward($data);
            });
        $shell->registerModule(\stack\module\web\Login::NAME, function($data) {
                return new \stack\module\web\Login($data);
            });

        $shell->registerModule(\stack\module\web\Core::NAME, function($data) {
               return new \stack\module\web\Core($data);
            });

        $application = $this->getApplication();
        $shell->registerModule(\stack\module\web\StaticFiles::NAME, function($data) use ($application) {
                return new \stack\module\web\StaticFiles($data, $application);
            });
    }

    /**
     * @return mixed
     */
    public function getAssetPath() {
        return STACK_ROOT . '/www/' . self::NAME;
    }
}