<?php
namespace stackos;
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

class User extends Document {
    /**
     * @var string
     */
    private $uname;
    /**
     * @var null|string
     */
    private $home;
    /**
     * @var bool
     */
    private $uber = false;
    /**
     * @var array
     */
    private $groups = array();

    /**
     * @param Kernel $kernel
     * @param string $uname
     * @param array  $groups
     * @param string $home
     * @param array  $permissions
     */
    public function __construct(Kernel $kernel, $uname, array $groups = array(), $home = null, array $permissions = array()) {
        parent::__construct($kernel, $permissions);
        $this->uname = $uname;
        if ($home === null) {
            $home = "/home/$uname";
        }
        $this->groups = $groups;
        $this->home = $home;
    }

    /**
     * @return string
     */
    public function getUname() {
        return $this->uname;
    }

    /**
     * @param string $name
     */
    public function addToGroup($name) {
        if (!in_array($name, $this->groups)) {
            $this->groups[] = $name;
        }
    }

    /**
     * @return array
     */
    public function getGroups() {
        return $this->groups;
    }

    /**
     * @return string
     */
    public function getHome() {
        return $this->home;
    }

    /**
     * @return bool
     */
    public function getUber() {
        return $this->uber;
    }

    /**
     * @param bool $uber
     * @return User
     */
    public function setUber($uber) {
        $this->uber = $uber;
        return $this;
    }
}