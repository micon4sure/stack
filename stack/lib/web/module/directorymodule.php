<?php
namespace stack\web\module;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, which is found under /path/to/stack/LICENSE
 */

class DirectoryModule extends BaseModule {
    public function run() {
        \lean\util\Dump::flat($this);
    }
}