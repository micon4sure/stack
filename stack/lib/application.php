<?php
namespace stack;
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
 * Provides an interface for modules in /root/system/run.
 * A custom Application class is expected for projects written on stack.
 * Also holds context.
 */
class Application {
    /**
     * @var Context
     */
    private $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context) {
        $this->context = $context;
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getRunPath($path) {
        return Root::ROOT_PATH_SYSTEM_RUN . "/$path";
    }

    /**
     * Execute a file via call_user_func_array
     *
     * @param string $path
     * @return mixed
     */
    protected function runFile($path) {
        $shell = $this->context->getShell();
        $file = $shell->readFile($path);

        $args = func_get_args();
        array_shift($args); // shift off path
        array_unshift($args, $this->context); // unshift the context as new first argument
        return call_user_func_array(array($file->getModule(), 'run'), $args);
    }

    /**
     * @param string $uname
     * @return \stack\module\User
     */
    public function getUser($uname) {
        $user = $this->context->getShell()->readUser($uname);
        return $user->getModule();
    }

    /**
     * @param string $uname
     * @param string $password
     */
    public function addUser($uname, $password) {
        $this->runFile($this->getRunPath('adduser'), $uname, $password);
    }

    /**
     * @param $uname
     */
    public function delUser($uname) {
        $this->runFile($this->getRunPath('deluser'), $uname);
    }

    /**
     * @param string $gname
     * @param string $founder
     */
    public function addGroup($gname, $founder) {
        $this->runFile($this->getRunPath('addgroup'), $gname, $founder);
    }

    /**
     * @param $gname
     */
    public function delGroup($gname) {
        $this->runFile($this->getRunPath('delgroup'), $gname);
    }

    /**
     * @param string $gname
     * @return \stack\module\Group
     */
    public function getGroup($gname) {
        $group = $this->context->getShell()->readGroup($gname);
        return $group->getModule();
    }
}