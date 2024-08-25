<?php

declare(strict_types=1);


namespace App\Tests\Unit;

use App\Tests\Support\UnitTester;
use PHPCR\SessionInterface;

final class JackrabbitImagickIssueCest
{
    protected string $path = '/test.jpg';
    protected SessionInterface $session;

    public function _before(UnitTester $I): void
    {
        $this->session = $I->getPhpcrSession();

        // Remove test file if exists before test
        if ($this->session->nodeExists($this->path)) {
            $this->session->removeItem($this->path);
            $this->session->save();
            $this->session->clear();
        }
    }

    public function testImagickCannotInitializeStreamFromJackrabbit(UnitTester $I): void
    {
        $tryFile = $I->getTryFilePath();
        $path = $this->path;
        $name = basename($this->path);

        // Open "try" file and verify its validity
        $streamOriginal = fopen($tryFile, 'r');
        $I->assertNotFalse($streamOriginal);
        $contents = stream_get_contents($streamOriginal);
        rewind($streamOriginal);
        $I->assertNotEmpty($contents);
        $I->assertEquals('resource', gettype($streamOriginal));
        $I->assertEquals('stream', get_resource_type($streamOriginal));

        // Verify that given file is readable by Imagick
        $imagick = new \Imagick();
        $imagick->readImageFile($streamOriginal);
        $imagick->destroy();
        rewind($streamOriginal);

        // Create new node in Jackrabbit using "try" file
        $I->newFile($name, $streamOriginal);
        $this->session->save();
        $this->session->clear();
        $I->assertTrue($this->session->nodeExists($path));

        // Verify that node is properly saved in Jackrabbit
        $retrievedStream = $I->getResource($path);
        $I->assertEquals('resource', gettype($retrievedStream));
        $I->assertEquals('stream', get_resource_type($retrievedStream));
        $I->assertEquals($contents, stream_get_contents($retrievedStream));
        fclose($retrievedStream);

        // Just to be sure, clear session and retrieve stream once again.
        $this->session->clear();
        $retrievedStream = $I->getResource($path);

        // And now, without any further actions on file handle, pass it to Imagick.
        $imagick = new \Imagick();
        $imagick->readImageFile($retrievedStream); /* THIS WILL THROW */
        $imagick->destroy();
    }
}
