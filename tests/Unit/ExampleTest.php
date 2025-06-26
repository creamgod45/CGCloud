<?php

namespace Tests\Unit;

use App\Lib\Utils\CGFileSystem\CGFileSystem;
use Exception;
use Illuminate\Foundation\Testing\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_that_true_is_true(): void
    {
        $this->markTestSkipped('Path not available in test environment');
        try {
            dump(CGFileSystem::baseFileObject('C:\Users\fuyin\Desktop\CGClound\routes\api.php'));
            $this->assertTrue(true);
            dump(CGFileSystem::baseFileObject('C:\Users\fuyin\Desktop\CGClound\routes'));
            $this->assertTrue(true);
            dump(CGFileSystem::baseFileObject('C:\Users\fuyin\Desktop\CGClound\routes\\'));
            $this->assertTrue(true);
            dump(CGFileSystem::baseFileObject('C:/Users/fuyin/Desktop/CGClound/routes/'));
            $this->assertTrue(true);
            dump(CGFileSystem::baseFileObject('C:/Users/fuyin/Desktop/CGClound/routes'));
            $this->assertTrue(true);
            $object = CGFileSystem::getCGFileObject('C:/Users/fuyin/Desktop/CGClound/routes/api.php');
            dump($object);
            dump($object->renameToNewInstance('ffmpeg-streaming.log'));
            dump($object->renameToNewInstance('_thumb%03d.jpg'));
            $this->assertTrue(true);
        } catch (Exception $e) {
            dump($e);
            $this->fail();
        }
        $this->assertTrue(true);
    }

    public function test_that_true_is_true2(): void
    {
        $this->markTestSkipped('Path not available in test environment');
        try {
            dump(CGFileSystem::getCGFileObject('C:\Users\fuyin\Desktop\CGClound\storage\app\ShareTable\TEMP\Patch\TEMP_qmBuJCeaAz\8bd161a3d9f36619cc2cc4b20cd9de80'));
            $this->assertTrue(true);
        } catch (Exception $e) {
            dump($e);
            $this->fail();
        }
        $this->assertTrue(true);
    }
}
