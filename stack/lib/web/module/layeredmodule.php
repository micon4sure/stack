<?php
namespace stack\web\module;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, which is found under /path/to/stack/LICENSE
 */

abstract class LayeredModule extends \stack\module\BaseModule {
    /**
     * @var \lean\Document;
     */
    private $document;

    /**
     * @var \lean\Template
     */
    private $layout;

    /**
     * @var \lean\Template
     */
    private $view;

    /**
     * Create document, layout and view
     */
    public function init(\stack\Application $application) {
        parent::init($application);
        $this->document = $this->createDocument();
        $this->layout = $this->createLayout();
        $this->view = $this->createView();
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
        $view->setData($this->data->getArrayCopy());

        // stack
        $document->set('layout', $layout);
        $layout->set('view', $view);

        $document->display();
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
        $file = $this->getApplication()->getSetting('stack.template.layout.directory') . '/default.php';
        return new \lean\Template($file);
    }

    /**
     * @return \lean\Template
     */
    abstract protected function createView();

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
     * @return \lean\Template
     */
    protected function getView() {
        return $this->view;
    }

}