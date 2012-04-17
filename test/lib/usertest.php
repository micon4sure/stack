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
    }

    public function testGetRootHome() {
        self::$kernel->pushContext(new \enork\kernel\RootContext());
        $root = self::$kernel->getFile('/root');
        $this->assertTrue($root instanceof \enork\File);
        $this->assertEquals('root', $root->getOwner());
    }

    public function testCreateUser() {
        self::$kernel->pushContext(new \enork\kernel\RootContext());
        $user = new \enork\User('test', array(), '/home/test');
        self::$kernel->createUser($user);

        // provoke UserExists exception
        try {
            self::$kernel->createUser($user);
            $this->fail('Expecting Exception_UserExists');
        }
        catch(\enork\Exception_UserExists $e) {
            // pass
        }

        // provoke PermissionDenied exception
        try {
            new \enork\kernel\RootContext();
            self::$kernel->pushContext(new \enork\kernel\UserContext($user));
            self::$kernel->createUser($user);
            $this->fail('Expecting Exception_PermissionDenied');
        }
        catch(\enork\Exception_PermissionDenied $e) {
            // pass
        }
    }
}