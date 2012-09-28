<?php
namespace stack;

class Template {
    const STACK_TEMPLATE_DIRECTORY = 'std';
    const STACK_TEMPLATE_DIRECTORY_DOCUMENT = 'stdd';
    const STACK_TEMPLATE_DIRECTORY_VIEW = 'stdv';

    const APPLICATION_TEMPLATE_DIRECTORY = 'atd';
    const APPLICATION_TEMPLATE_DIRECTORY_LAYOUT = 'atdl';
    const APPLICATION_TEMPLATE_DIRECTORY_VIEW = 'atdv';
    const APPLICATION_TEMPLATE_DIRECTORY_PARTIAL = 'atdp';

    public static function createTemplate($pathId, $template) {
        $path = self::getTemplatePath($pathId);
        return new \lean\Template($path . $template);
    }

    public static function getTemplatePath($pathId) {
        switch($pathId) {
            // stack template directories
            case self::STACK_TEMPLATE_DIRECTORY:
                return STACK_ROOT . '/stack/template';
            case self::STACK_TEMPLATE_DIRECTORY_DOCUMENT:
                return self::getTemplatePath(self::STACK_TEMPLATE_DIRECTORY) . '/document';
            case self::STACK_TEMPLATE_DIRECTORY_VIEW:
                return self::getTemplatePath(self::STACK_TEMPLATE_DIRECTORY) . '/view';

            // application template paths
            case self::APPLICATION_TEMPLATE_DIRECTORY:
                return STACK_APPLICATION_ROOT . '/template';
            case self::APPLICATION_TEMPLATE_DIRECTORY_LAYOUT:
                return self::getTemplatePath(self::APPLICATION_TEMPLATE_DIRECTORY) . '/layout';
            case self::APPLICATION_TEMPLATE_DIRECTORY_VIEW:
                return self::getTemplatePath(self::APPLICATION_TEMPLATE_DIRECTORY) . '/view';
            case self::APPLICATION_TEMPLATE_DIRECTORY_PARTIAL:
                return self::getTemplatePath(self::APPLICATION_TEMPLATE_DIRECTORY) . '/partial';
        }
    }
}