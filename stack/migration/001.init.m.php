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
     * @throws \Exception
     */
    public function up() {
        $context = \lean\Registry::instance()->get('stack.context');
        $shell = $context->getShell();
        $shell->init();


        // create initial files
        $files = array(
            \stack\Root::ROOT_PATH,
            \stack\Root::ROOT_PATH_SYSTEM,
            \stack\Root::ROOT_PATH_SYSTEM_RUN,
            \stack\Root::ROOT_PATH_USERS,
            \stack\Root::ROOT_PATH_GROUPS,
            \stack\Root::ROOT_USER_PATH_HOME,
        );
        $context->pushSecurity(new \stack\security\PriviledgedSecurity());
        try {
            foreach ($files as $path) {
                $file = new \stack\filesystem\File($path, \stack\Root::ROOT_UNAME);
                $shell->writeFile($file);
            }

            // create root user file + module
            $file = new \stack\filesystem\File(\stack\Root::ROOT_PATH_USERS_ROOT, \stack\Root::ROOT_UNAME);
            $user = new \stack\module\User(\stack\Root::ROOT_UNAME, \stack\Root::ROOT_USER_PATH_HOME);
            $password = uniqid();
            $user->changePassword($password);
            $file->setModule($user);
            $shell->writeFile($file);

            file_put_contents(STACK_ROOT . '/rootpw', $password);

            // create system run files
            $modules = array(
                'adduser' => new \stack\module\run\AddUser($shell),
                'deluser' => new \stack\module\run\DelUser($shell),
                'addgroup' => new \stack\module\run\AddGroup($shell),
                'delgroup' => new \stack\module\run\DelGroup($shell),
            );
            foreach($modules as $name => $module) {
                $path = Root::ROOT_PATH_SYSTEM_RUN . "/$name";
                $file = new \stack\filesystem\File($path, \stack\Root::ROOT_UNAME);
                $file->setModule($module);
                $shell->writeFile($file);
            }
        } catch(\Exception $e) {
            $context->pullSecurity();
            throw $e;
        }
        $context->pullSecurity();
    }

    public function down() {
        $context = \lean\Registry::instance()->get('stack.context');
        $shell = $context->getShell();
        $shell->nuke();
    }
}

return new MigrationInit001();