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
 * Holds constants about the root user and file + surroundings
 */
class Root {
    const ROOT_UNAME = 'root';
    const ROOT_PATH = '/';
    const ROOT_PATH_HOME = '/home';
    const ROOT_PATH_SYSTEM = '/system';
    const ROOT_PATH_SYSTEM_RUN = '/system/run';
    const ROOT_PATH_GROUPS = '/groups';
    const ROOT_PATH_USERS = '/users';
    const ROOT_PATH_USERS_ROOT = '/users/root';
    const ROOT_USER_PATH_HOME = '/root';
}

class Root_Modules {
    const MODULE_PLAIN = 'stack.plain';
    const MODULE_USER = 'stack.user';
    const MODULE_GROUP = 'stack.group';
    const MODULE_ADDUSER = 'stack.adduser';
    const MODULE_DELUSER = 'stack.deluser';
    const MODULE_ADDGROUP = 'stack.addgroup';
    const MODULE_DELGROUP = 'stack.delgroup';
}