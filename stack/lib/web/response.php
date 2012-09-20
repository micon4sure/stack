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

    /**
     * stack.anonymous are headers without values, stack.id is for headers with key/value
     * @var array
     */
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
        header('HTTP/1.1 ' . $this->statusCode . ' ' . $this->statusMessage);
        foreach($this->headers['stack.anonymous'] as $value) {
            header($value);
        }
        foreach($this->headers['stack.id'] as $id => $value) {
            header("$id: $value");
        }
    }
}

/**
 * Plain text response
 */
class Response_Plain extends Response {

    /**
     * @var string
     */
    private $content;

    /**
     * @param string $content
     * @param int $code
     * @param string $message
     */
    public function __construct($content = '', $code = 200, $message = 'OK') {
        parent::__construct($code, $message);
        $this->content = $content;
    }

    /**
     *
     */
    public function send() {
        parent::send();
        echo $this->content;
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
     * @param mixed $data
     * @param int $code
     * @param string $message
     */
    public function __construct($data, $code = 200, $message = 'OK') {
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
class Response_HTTP303 extends Response {
    /**
     * @param string $redirect
     */
    public function __construct($redirect) {
        $this->setHeader("Location: $redirect");
        parent::__construct(303, 'Found');
    }
}

/**
 * Response not found flavour - 404
 */
class Response_HTTP404 extends Response_Plain {
    /**
     * @param string $content
     */
    public function __construct($content = '') {
        parent::__construct($content, 404, 'Not Found');
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
    public function __construct($html, $code = 200, $message = 'OK') {
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