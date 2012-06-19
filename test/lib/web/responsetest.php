<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

class ResponseTest extends StackOSTest {
    public function testResponseJSON() {
        $response = new \stack\web\Response_JSON(['test' => null]);
        $responseErr = new \stack\web\Response_JSON_HTTP404(['test' => null]);
    }
}