<?php
namespace stackos\security;

abstract class Priviledge {
    /** Read permission
     */
    const READ = 'r';
    /** Write permission
     */
    const WRITE = 'w';
    /** Execute permission: allows to execute applications enclosed in a file
     *  Allows transversion into directory (TODO unimplemented)
     */
    const EXECUTE = 'x';
}