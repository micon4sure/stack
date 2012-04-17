<?php
namespace test;

class UserTest extends \PHPUnit_Framework_TestCase {
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

    public function testGetRootUser() {
        $root = self::$kernel->getUser('root');
        $this->assertTrue($root instanceof \enork\User);
        $this->assertEquals('/root', $root->getHome());
        $this->assertEquals(array('root'), $root->getGroups());
        $this->assertEquals($root, self::$kernel->getRootUser());
    }

    public function testGetUnknownUser() {
        try {
            self::$kernel->popContext();
            $this->fail('Expecting Exception_MissingContext');
        }
        catch(\enork\Exception_MissingContext $e) {
            // pass
        }
    }
}