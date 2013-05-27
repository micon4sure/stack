<?
namespace stack\test;
use lean\util\Dump;
use stack\Cabinet;
use stack\File;
use stack\Module;
use stack\ModuleFactory;
use stack\Module_Default;
use stack\module\User;

class CabinetTest extends StackTest {

    /**
     * Create a file cabinet
     *
     * @return \stack\Cabinet
     */
    private function createCabinet() {
        return new Cabinet($this->client, new ModuleFactory());
    }

    /**
     * Make sure createFile works as intended
     */
    public function testCRUD() {
        $cabinet = $this->createCabinet();
        $owner = User::create('testUser');

        // create a file
        $created = $cabinet->createFile('/gnark', $owner);
        // fetch it from the cabinet
        $read = $cabinet->fetchFile('/gnark');

        // make sure both have the same path
        $this->assertEquals($created->getPath(), $read->getPath());

        // add some data to the read file
        $read->getModule()->getData()->foo = 'bar';
        $cabinet->storeFile($read);
        // add some more data, save again
        $read->getModule()->getData()->qux = 'kos';
        $cabinet->storeFile($read);

        // read again
        $readAgain = $cabinet->fetchFile('/gnark');
        $this->assertEquals($readAgain->getModule()->getData()->foo, 'bar');
        $this->assertEquals($readAgain->getModule()->getData()->qux, 'kos');

        // delete file, make sure it's gone
        $cabinet->deleteFile($readAgain);
        $this->assertFalse($cabinet->fileExists('/gnark'));
    }
}