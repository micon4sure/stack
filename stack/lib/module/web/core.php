<?php
namespace stack\module\web;
    /*
    * Copyright (C) 2012 Michael Saller
    * Licensed under MIT License, see /path/to/stack/LICENSE
    */

/**
 * Provides often used web assets
 */
class Core extends BaseModule {

    const NAME = 'stack.web.core';

    public function run() {
    }

    /**
     * Get a complete absolute filesystem path
     *
     * @param $asset
     *
     * @return mixed
     */
    public function getAssetPath($asset) {
        return STACK_ROOT . '/stack/www/' . static::NAME . $asset;
    }
    /**
     * Get a relative (web) path
     *
     * @param $path
     *
     * @return mixed
     */
    public function getAssetWebPath($asset) {
        return '/static/' . self::NAME . $asset;
    }
}
?>