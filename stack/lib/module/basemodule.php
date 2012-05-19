<?php
namespace stack\module;
/*
 * Copyright (C) 2012 Michael Saller
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 */

/**
 * Default implements BaseModule_Abstract
 */
class BaseModule extends BaseModule_Abstract {
    protected function export($data) {
        return $data;
    }

    public static function create($data) {
        return new static($data);
    }
}

/**
 * Module to be saved in a file
 */
abstract class BaseModule_Abstract {

    /**
     * @var null|\stdClass
     */
    protected $data;

    /**
     * @param null $data
     */
    public function __construct($data = null) {
        if($data = null)
            $data = new \stdClass();
        $this->data = $data;
    }

    /**
     * final to ensure that name always is in data
     *
     * @return mixed
     */
    public final function getData() {
        $data = $this->export($this->data) ?: new \stdClass;
        $data->name = $this->getName();
        return $data;
    }

    /**
     * Create JSONizable data
     *
     * @abstract
     * @param $data
     * @return \stdClass
     */
    protected abstract function export($data);

    /**
     * @param $data
     * @return \stack\module\BaseModule_Abstract
     */
    public function setData($data) {
        $this->data = $data;
        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return static::NAME;
    }
}