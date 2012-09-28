<?php
namespace stack\module\web;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, which is found under /path/to/stack/LICENSE
 */

abstract class LayeredModule extends BaseModule {
    /**
     * @var \lean\Document;
     */
    private $document;

    /**
     * @var \lean\Template
     */
    private $layout;

    /**
     * Create document, layout and view
     */
    public function init(\stack\Application $application) {
        parent::init($application);
        $this->document = $this->createDocument();
        $this->layout = $this->createLayout();
    }

    /**
     * Display hirarchy
     * controller
     *  -> document
     *      -> layout
     *          -> view
     */
    protected function display() {
        $document = $this->getDocument();
        $layout = $this->getLayout();
        $view = $this->getView();

        // stack
        $document->set('layout', $layout);
        $layout->set('view', $view);

        return new \stack\web\Response_HTML($document->render());
    }

    /**
     * @return \lean\Document
     */
    protected function createDocument() {
        $file = \lean\ROOT_PATH . '/template/document.php';
        return new \lean\Document($file);
    }

    /**
     * @return \lean\Template
     */
    protected function createLayout() {
        return \stack\Template::createTemplate(\stack\Template::APPLICATION_TEMPLATE_DIRECTORY_LAYOUT, '/default.php');
    }

    /**
     * @param \lean\Partial $partial
     */
    protected function addPartial(\lean\Partial $partial) {
        $this->getView()->setCallback($partial->getName(), $partial);
    }

    /**
     * @return \lean\Document
     */
    public function getDocument() {
        return $this->document;
    }

    /**
     * @return \lean\Template
     */
    protected function getLayout() {
        return $this->layout;
    }

    /**
     * @param $template
     *
     * @return \lean\Template
     */
    protected function createView($template) {
        return \stack\Template::createTemplate(\stack\Template::APPLICATION_TEMPLATE_DIRECTORY_VIEW, '/' . static::NAME . '/' . $template);
    }

    /**
     * @return \lean\Template
     */
    abstract protected function getView();
}