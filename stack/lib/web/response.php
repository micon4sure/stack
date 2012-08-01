<?php
namespace stack\web;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

/**
 * Response delivers structure to communications with client side
 */
class Response {

    private $headers = ['stack.anonymous' => [], 'stack.id' => []];

    /**
     * @var int HTTP status code
     */
    private $statusCode;

    /**
     * @var string
     */
    private $statusMessage;

    /**
     * @param int $code HTTP status code
     * @param string $message
     */
    public function __construct($code = 200, $message = 'OK') {
        $this->setContentType('text/plain');
        $this->setStatusCode($code);
        $this->setStatusMessage($message);
    }

    /**
     * @param int $code HTTP status code
     */
    public function setStatusCode($code) {
        $this->statusCode = $code;
    }
    /**
     * @param $message
     */
    public function setStatusMessage($message) {
        $this->statusMessage = $message;
    }

    /**
     * Set Content type of the output
     *
     * @param string $type
     * @param string $charset
     */
    public function setContentType($type, $charset = 'utf-8') {
        $this->setHeader('Content-Type', "$type;charset=$charset");
    }

    /**
     * @param string $header
     * @param string|null $key
     * @return void
     */
    public function setHeader($header, $key = null) {
        if($key === null) {
            $this->headers['stack.anonymous'][] = $header;
            return;
        }
        $this->headers['stack.id'][$key] = $header;
    }

    /**
     * Send the previously set headers.
     * If the key is numeric (array),
     */
    public function send() {
        header('HTTP/1.0 ' . $this->statusCode . ' ' . $this->statusMessage);
        foreach($this->headers['stack.anonymous'] as $value) {
            header($value);
        }
        foreach($this->headers['stack.id'] as $id => $value) {
            header("$id: $value");
        }
    }
}

/**
 * Send data with json
 */
class Response_JSON extends Response  {
    /**
     * @var object
     */
    protected $data;

    /**
     * @param array $data
     * @param int $code
     * @param string $message
     */
    public function __construct(array $data, $code = 200, $message = 'OK') {
        parent::__construct($code, $message);
        $this->data = new \lean\util\Object($data);
    }

    /**
     * Set JSON headers and JSON encode $this->data
     */
    public function send() {
        $this->setContentType('application/json');
        parent::send();
        echo json_encode($this->data->toArray());
    }
}

/**
 * Temporary redirect response
 */
class Response_HTTP302 extends Response {
    public function __construct($redirect) {
        $this->setHeader("Location: $redirect");
        parent::__construct(302, 'Found');
    }
}

/**
 * Response not found flavour - 404
 */
class Response_HTTP404 extends Response {
    /**
     * @param array $data
     */
    public function __construct() {
        parent::__construct(404, 'Not Found');
    }
}


/**
 *
 */
class Response_HTML extends Response {
    /**
     * @var string
     */
    private $markup;

    /**
     * @param int $code
     * @param string $message
     * @param string|null $html
     */
    public function __construct($code = 200, $message = 'OK', $html = null) {
        parent::__construct($code, $message);
        $this->markup = $html;
    }

    /**
     *
     */
    public function send() {
        $this->setContentType('text/html');
        parent::send();
        echo $this->markup;
    }
}