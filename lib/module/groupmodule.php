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

class GroupModule extends \stackos\module\BaseModule {
    const NAME = 'stackos.group';

    private $gname;

    public function __construct($gname) {
        $this->gname = $gname;
    }

    public function getGname() {
        return $this->gname;
    }
    public function setGname($gname) {
        $this->data->gname = $gname;
    }
    protected function export($data) {
        return (object)array('gname' => $this->gname, 'home' => $this->home);
    }

    public static function create($data) {
        if(!isset($data->gname))
            throw new \InvalidArgumentException('Group name missing.');
        return new static($data->gname, $data->home);
    }

    public function __toString() {
        return $this->getGname();
    }
}