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

class MigrationInit001 implements \lean\Migration {
    /**
     * Create the database and write system files
     */
    public function up() {
        $env = \lean\Registry_Stateless::instance()->get('stack.environment');
        $manager = $env->createManager();

        $manager->init();

        // create initial files
        $files = array(
            \stack\Root::ROOT_PATH,
            \stack\Root::ROOT_PATH_SYSTEM,
            \stack\Root::ROOT_PATH_SYSTEM_RUN,
            \stack\Root::ROOT_PATH_USERS,
            \stack\Root::ROOT_PATH_GROUPS,
            \stack\Root::ROOT_USER_PATH_HOME,
        );
        foreach ($files as $path) {
            $file = new \stack\filesystem\File($manager, $path, \stack\Root::ROOT_UNAME);
            $manager->writeFile($file);
        }

        // create root user file + module
        $file = new \stack\filesystem\File($manager, \stack\Root::ROOT_PATH_USERS_ROOT, \stack\Root::ROOT_UNAME);
        $file->setModule(new \stack\module\User(\stack\Root::ROOT_UNAME, \stack\Root::ROOT_USER_PATH_HOME));
        $manager->writeFile($file);

        // create system run files
        $modules = array(
            'adduser' => new \stack\module\AddUser(),
            'deluser' => new \stack\module\DelUser(),
            'addgroup' => new \stack\module\AddGroup(),
            'delgroup' => new \stack\module\DelGroup(),
        );
        foreach($modules as $name => $module) {
            $path = Root::ROOT_PATH_SYSTEM_RUN . "/$name";
            $file = new \stack\filesystem\File($manager, $path, \stack\Root::ROOT_UNAME);
            $file->setModule($module);
            $manager->writeFile($file);
        }
    }

    public function down() {
    }
}

return new MigrationInit001();