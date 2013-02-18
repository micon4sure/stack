<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

/**
 * Kind of a cheap enum for the default priviledges
 */
abstract class Security_Priviledge {
    /** Read permission
     */
    const READ = 'r';
    /** Write permission
     */
    const WRITE = 'w';
    /** Execute permission: allows to execute applications enclosed in a file
     */
    const EXECUTE = 'x';
    /** Delete permission
     */
    const DELETE = 'd';
}