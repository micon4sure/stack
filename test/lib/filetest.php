<?php
namespace test;

class FileTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \enork\Kernel
     */
    private static $kernel;

    public function setUp() {
        self::resetKernel();
    }

    private static function resetKernel() {
        self::$kernel = new \enork\Kernel('http://root:root@127.0.0.1:5984', 'enork');
        self::$kernel->destroy();
        self::$kernel->init();
    }

    public function testNoContextOnStack() {
        try {
            self::$kernel->getFile('/');
            $this->fail('Expecting Exception_MissingContext');
        }
        Catch(\enork\Exception_MissingContext $e) {
            // pass
        }
    }

    public function testGetRootFile() {
        // provoke no context on stack exception
        self::$kernel->pushContext(new \enork\kernel\RootContext());
        $root = self::$kernel->getFile('/');
        $this->assertTrue($root instanceof \enork\File);
        $this->assertEquals('root', $root->getOwner());
        $this->assertEquals($root, self::$kernel->getRootFile());
    }

    public function testGetRootHome() {
        self::$kernel->pushContext(new \enork\kernel\RootContext());
        $root = self::$kernel->getFile('/root');
        $this->assertTrue($root instanceof \enork\File);
        $this->assertEquals('root', $root->getOwner());
    }

    public function testCreateFile() {
        self::$kernel->pushContext(new \enork\kernel\RootContext());
    }
}