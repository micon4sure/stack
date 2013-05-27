<?php
namespace stack\test;
/*
 * Copyright (C) 2012 Michael Saller
 * Licensed under MIT License, see /path/to/stack/LICENSE
 */

use lean\util\Dump;
use stack\Module;
use stack\ModuleFactory;
use stack\Module_Default;
use stack\test\StackTest;

class ModuleFactoryTest extends StackTest {
    public function testDefaultModule() {
        $factory = new ModuleFactory();
        $module = $factory->createModule(ModuleFactory::DEFAULT_MODULE_TYPE, new \stdClass());

        $this->assertInstanceOf('stack\Module_Default', $module);
    }

    public function testCustomModule() {
        $factory = new ModuleFactory();
        $factory->addMappings([
            'mock' => '\stack\test\ModuleFactoryTest_MockModule'
        ]);
        $module = $factory->createModule('mock', new \stdClass());

        $this->assertInstanceOf('stack\test\ModuleFactoryTest_MockModule', $module);
    }
}

class ModuleFactoryTest_MockModule extends Module {

}