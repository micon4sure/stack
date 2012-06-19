<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
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