<?php
namespace stack\web;
    /*
    * Copyright (C) 2012 Michael Saller
    * Licensed under MIT License, see /path/to/stack/LICENSE
    * Parts lifted from Slim framework: http://www.slimframework.com/
    */

/**
 * Abstracts an HTTP request
 */
class Request {
    const METHOD_HEAD = 'HEAD';
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_OPTIONS = 'OPTIONS';

    /**
     * Get HTTP method
     * @return string
     */
    public function getMethod() {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Is this a GET request?
     * @return bool
     */
    public function isGet() {
        return $this->getMethod() === self::METHOD_GET;
    }

    /**
     * Is this a POST request?
     * @return bool
     */
    public function isPost() {
        return $this->getMethod() === self::METHOD_POST;
    }

    /**
     * Is this a PUT request?
     * @return bool
     */
    public function isPut() {
        return $this->getMethod() === self::METHOD_PUT;
    }

    /**
     * Is this a DELETE request?
     * @return bool
     */
    public function isDelete() {
        return $this->getMethod() === self::METHOD_DELETE;
    }

    /**
     * Is this a HEAD request?
     * @return bool
     */
    public function isHead() {
        return $this->getMethod() === self::METHOD_HEAD;
    }

    /**
     * Is this a OPTIONS request?
     * @return bool
     */
    public function isOptions() {
        return $this->getMethod() === self::METHOD_OPTIONS;
    }

    /**
     * Is this an XHR request? (alias of Slim_Http_Request::isAjax)
     * @return bool
     */
    public function isXHR() {
        return $this->get('stackXHROverride') || isset($_SERVER['X_REQUESTED_WITH']) && $_SERVER['X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * Fetch GET data
     *
     * This method returns a key-value array of data sent in the HTTP request query string, or
     * the value of the array key if requested; if the array key does not exist, NULL is returned.
     *
     * @param   string $key
     * @return  array|string|null
     */
    public function get($key = null) {
        if ($key === null) {
            return $_GET;
        }

        return isset($_GET[$key])
            ? $_GET[$key]
            : null;
    }

    /**
     * Fetch POST data
     *
     * This method returns a key-value array of data sent in the HTTP request body, or
     * the value of a hash key if requested; if the array key does not exist, NULL is returned.
     *
     * @param   string $key
     * @return  array|mixed|null
     * @throws  RuntimeException If environment input is not available
     */
    public function post($key = null) {
        if ($key === null) {
            return $_GET;
        }

        return isset($_GET[$key])
            ? $_GET[$key]
            : null;
    }

    /**
     * Fetch PUT data (alias for post)
     * @param   string $key
     * @return array|mixed|null
     */
    public function put($key = null) {
        return $this->post($key);
    }

    /**
     * Fetch DELETE data (alias for post)
     * @param   string $key
     * @return  array|mixed|null
     */
    public function delete($key = null) {
        return $this->post($key);
    }

    /**
     * Get the requested path
     *
     * @return string
     */
    public function getPath() {
        return $_SERVER['PHP_SELF'];
    }
}
