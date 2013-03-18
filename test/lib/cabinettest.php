<?
namespace stack\test;
use lean\util\Dump;
use stack\Cabinet;
use stack\File;
use stack\Module;
use stack\ModuleFactory;
use stack\Module_Default;

class CabinetTest extends StackTest {

    /**
     * @var \couchClient
     */
    protected $client;

    /**
     * create couch client
     */
    public function setUp() {
        parent::setUp();
        $this->client = new \couchClient($this->environment->get('stack.database.dsn'), $this->environment->get('stack.database.name'));
    }
    /**
     * Create a file cabinet
     *
     * @return \stack\Cabinet
     */
    private function createCabinet() {
        $factory = new ModuleFactory();
        $factory->registerWorker(CabinetTest_MockModule_User::TYPE_ID, function(\stdClass $data) {
            return new CabinetTest_MockModule_User($data->uname);
        });
        return new Cabinet($this->client, $factory);
    }

    /**
     * Make sure createFile works as intended
     */
    public function testCRUD() {
        $cabinet = $this->createCabinet();

        // create a file
        $created = $cabinet->createFile('/gnark');
        // fetch it from the cabinet
        $read = $cabinet->fetchFile('/gnark');

        // make sure both have the same path
        $this->assertEquals($created->getPath(), $read->getPath());

        // add some data to the read file
        $read->getModule()->getData()->foo = 'bar';
        $cabinet->storeFile($read);

        // read again
        $readAgain = $cabinet->fetchFile('/gnark');
        $this->assertEquals($readAgain->getModule()->getData()->foo, 'bar');

        // delete file, make sure it's gone
        $cabinet->deleteFile($readAgain);
        $this->assertFalse($cabinet->fileExists('/gnark'));
    }

    public function testCRUDWithModule() {
        $cabinet = $this->createCabinet();

        // create a file
        $created = $cabinet->createFile('/gnark');
        $created->setModule(new CabinetTest_MockModule_User('jack'));
        $created->store();
        // fetch it from the cabinet
        $read = $cabinet->fetchFile('/gnark');

        // make sure both have the same path
        $this->assertEquals($created->getPath(), $read->getPath());
        // make sure username is still the same
        $this->assertEquals($created->getModule()->getUsername(), 'jack');

        // add some data to the read file
        $read->getModule()->setUsername('tyler');
        $cabinet->storeFile($read);

        // read again
        $readAgain = $cabinet->fetchFile('/gnark');
        $this->assertEquals($readAgain->getModule()->getUsername(), 'tyler');

        // delete file, make sure it's gone
        $cabinet->deleteFile($readAgain);
        $this->assertFalse($cabinet->fileExists('/gnark'));
    }

    public function testModules() {
        $factory = new ModuleFactory();
        $factory->registerWorker(CabinetTest_MockModule::TYPE_ID, function(\stdClass $data) {
                return new CabinetTest_MockModule($data);
            });
        $cabinet = new Cabinet($this->client, $factory);
    }
}

/**
 * Class CabinetTest_Module
 *
 * @package stack\test
 */
class CabinetTest_MockModule extends Module {
    const TYPE_ID = 'test.mock';
}

class CabinetTest_MockModule_User extends Module {
    const TYPE_ID = 'test.mock.user';

    public function __construct($username) {
        parent::__construct();
        $this->getData()->uname = $username;
    }

    public function getUsername() {
        return $this->getData()->uname;
    }

    public function setUsername($username) {
        $this->getData()->uname = $username;
    }
}