<?php
namespace stack;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

/**
 * @package stack
 */
use stack\module\User;

interface FileAccess {
    /**
     * @param $path
     *
     * @return File
     */
    public function createFile($path, User $owner);

    /**
     * Save a file
     *
     * @param File $file
     */
    public function storeFile(File $file);

    /**
     * Read a file
     *
     * @param string $path
     *
     * @return File
     */
    public function fetchFile($path);

    /**
     * Delete a file
     *
     * @param File $file
     */
    public function deleteFile(File $file);

    /**
     * Check if a file exists
     *
     * @param string $path
     *
     * @return bool
     */
    public function fileExists($path);
}