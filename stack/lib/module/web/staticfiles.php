<?php
namespace stack\module\web;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class StaticFiles extends BaseModule {

    const NAME = 'stack.web.static_files';
    const LOCATION = '/static';

    public function run() {
        $path = explode('/', $this->getRequest()->getPath());
        // shift off empty entry for leading slash
        array_shift($path);
        // shift off static prefix
        array_shift($path);

        $bundleName = array_shift($path);

        $path = join('/', $path);

        $bundle = $this->getApplication()->getBundle($bundleName);

        if(!$bundle instanceof \stack\Bundle_AssetProvider) {
            throw new Exception('Bundle is not an asset provider');
        }
        /**
         * @var \stack\Bundle_AssetProvider $bundle
         */

        $fileName = $bundle->getAssetPath() . "/$path";

        if(!file_exists($fileName)) {
            if($this->getApplication()->getEnvironment()->isDebug()) {
                return new \stack\web\Response_HTTP404("File not found: $fileName");
            } else {
                return new \stack\web\Response_HTTP404("File not found.");
            }
        }

        return new \stack\web\Response_File($fileName);
    }
}