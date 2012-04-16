<?php
namespace test;

class KernelTest extends \PHPUnit_Framework_TestCase {
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

    // ### tests
    public function testGetRootUser() {
        $root = self::$kernel->getUser('root');
        $this->assertTrue($root instanceof \enork\User);
        $this->assertEquals('/root', $root->getHome());
        $this->assertEquals(array('root'), $root->getGroups());
    }

    public function testGetRootFile() {
        $root = self::$kernel->getFile('/', self::$kernel->getUser('root'));
        $this->assertTrue($root instanceof \enork\File);
        $this->assertEquals('root', $root->getOwner());
    }

    public function testGetRootHome() {
        $root = self::$kernel->getFile('/root', self::$kernel->getUser('root'));
        $this->assertTrue($root instanceof \enork\File);
        $this->assertEquals('root', $root->getOwner());
    }

    public function testCreateUser() {
        $user = new \enork\User('test', array(), '/home/test');
        self::$kernel->createUser($user, self::$kernel->getRootUser());

        // provoke UserExists exception
        try {
            self::$kernel->createUser($user, self::$kernel->getRootUser());
            $this->fail('Expecting Exception_UserExists');
        }
        catch(\enork\Exception_UserExists $e) {
            // expected.
        }

        // provoke PermissionDenied exception
        try {
            $newUser = new \enork\User('test2', array(), '/home/test2');
            self::$kernel->createUser($user, $user);
            $this->fail('Expecting Exception_UserExists');
        }
        catch(\enork\Exception_PermissionDenied $e) {
            // expected.
        }
    }
}