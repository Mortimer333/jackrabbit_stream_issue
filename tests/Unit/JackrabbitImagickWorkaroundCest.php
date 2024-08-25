<?php

declare(strict_types=1);


namespace App\Tests\Unit;

use App\Tests\Support\UnitTester;
use PHPCR\SessionInterface;

final class JackrabbitImagickWorkaroundCest
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

    public function imagickDoesNotThrowExceptionWhenProxyHandleIsUsed(UnitTester $I): void
    {
        $tryFile = $I->getTryFilePath();
        $path = $this->path;
        $streamOriginal = fopen($tryFile, 'r');
        $name = basename($path);
        $I->newFile($name, $streamOriginal);
        $this->session->save();
        $this->session->clear();
        $I->assertTrue($this->session->nodeExists($path));
        $retrievedStream = $I->getResource($path);
        $I->expectThrowable(\ImagickException::class, function () use ($retrievedStream) {
            $imagick = new \Imagick();
            $imagick->readImageFile($retrievedStream);
            $imagick->destroy();
        });
        rewind($retrievedStream);

        // Coping invalid stream to new one
        // (basically create new file from corrupted handle and using new handle)
        // does not rise exception when handled by Imagick
        $tmp = tmpfile();
        stream_copy_to_stream($retrievedStream, $tmp);
        rewind($tmp);
        $imagick = new \Imagick();
        $imagick->readImageFile($tmp);
        $imagick->destroy();
    }
}
