<?php
namespace stack\module\web;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, which is found under /path/to/stack/LICENSE
 */

class DirectoryModule extends LayeredModule {
    public function run() {
        ob_start();
        \lean\util\Dump::create()->flush()->goes($this);
        return new \stack\web\Response_HTML(ob_get_clean());
    }

    /**
     * @return \lean\Template
     */
    protected function getView() {
        // TODO: Implement getView() method.
    }

    protected function createLayout() {
        return \stack\Template::createTemplate(\stack\Template::STACK_TEMPLATE_DIRECTORY_LAYOUT, '/directory_index.php');
    }
}