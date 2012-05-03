<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * fileation files (the "Software"), to deal in the Software without restriction, including without limitation
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

class Shell {
    /**
     * @var \stack\Filesystem
     */
    private static $defaultFilesystem;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var string
     */
    private $cwd;

    /**
     * @param \stack\Filesystem $filesystem
     */
    public function __construct(\stack\Filesystem $filesystem) {
        $this->filesystem = $filesystem;
    }

    /**
     * Create an instance of Traveler with the passed $access or self::$defaultAccess.
     * Throw exception if neither are set.
     * Take precedent to $access, overwrite self::$defaultAccess with it if set.
     *
     * @static
     * @param null|\stack\Filesystem $filesystem
     * @throws filesystem\Exception_NeedAccess
     * @return Shell
     */
    public static function instance(\stack\Filesystem $filesystem = null) {
        if(isset($filesystem)) {
            self::$defaultFilesystem = $filesystem;
        }
        if(!isset(self::$defaultFilesystem)) {
            throw new \stack\filesystem\Exception_NeedAccess('Need to be passed a filesystem at least once.');
        }
        return new static(self::$defaultFilesystem);
    }

    /**
     * Classic change dir
     * @param string $path
     * @return
     */
    public function cd($path) {
        $chunks = explode('/', $path);
        return $path;
    }

    /**
     * Return current working dir
     */
    public function getCWD() {

    }
}