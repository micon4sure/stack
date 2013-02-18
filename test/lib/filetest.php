<?
namespace stack\test;
use lean\util\Dump;
use stack\Cabinet;
use stack\File;

class FileTest extends StackTest {
    /**
     * Create a file cabinet
     *
     * @return \stack\Cabinet
     */
    private function createCabinet() {
        $client = new \couchClient($this->environment->get('stack.database.dsn'), $this->environment->get('stack.database.name'));
        return new Cabinet($client);
    }


    /**
     * Make sure getPath returns the id of the document
     */
    public function testPath() {
        $doc = $this->createTestDocument();
        $path = $doc->_id;
        $file = new File($doc);

        $this->assertEquals($path, $file->getPath());
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
        $read->setData(['foo' => 'bar']);
        $cabinet->saveFile($read);

        // read again
        $readAgain = $cabinet->fetchFile('/gnark');
        $data = $readAgain->getData();
        $this->assertEquals($data->foo, 'bar');

        // delete file, make sure it's gone
        $cabinet->deleteFile($readAgain);
        $this->assertFalse($cabinet->fileExists('/gnark'));
    }
}