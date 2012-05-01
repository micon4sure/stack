<?php
namespace stackos\module;
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

class UserModule extends \stackos\module\BaseModule {
    const NAME = 'stackos.user';

    private $uber = false;
    private $uname;
    private $home;

    public function __construct($uname, $home) {
        $this->setUname($uname);
        $this->setHome($home);
    }

    public function isUber() {
        return $this->uber;
    }
    public function setUber($uber) {
        $this->uber = $uber;
        return $this;
    }

    public function getUname() {
        return $this->uname;
    }
    public function setUname($uname) {
        $this->uname = $uname;
        return $this;
    }

    public function setHome($home) {
        $this->home = $home;
        return $this;
    }
    public function getHome() {
        return $this->home;
    }

    protected function export($data) {
        return (object)array('uname' => $this->getUname(), 'home' => $this->getHome(), 'uber' => $this->isUber());
    }

    public static function create($data) {
        if(!isset($data->uname, $data->home))
        throw new \InvalidArgumentException('User parameters are missing data. Need uname and home.');
        $instance = new static($data->uname, $data->home);
        $uber = isset($data->uber) ? $data->uber : false;
        $instance->setUber($uber);
        return $instance;
    }
}