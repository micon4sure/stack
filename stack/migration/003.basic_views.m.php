<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class Web003 implements \lean\Migration {
    /**
     * Create the database and write system files
     * @throws \Exception
     */
    public function up() {
        $function = 'function (doc) {
                var path = doc._id;
                var dirname = path.replace(/\/[^\/]*$/, "");
                emit (dirname, doc);
    	    }';

        $context = \lean\Registry::instance()->get('stack.context');
        $context->getShell()->createIndex('stack', 'ls', $function);
    }

    public function down() {
    }
}

return new Web003();