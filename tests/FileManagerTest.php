<?php

namespace RadionovelTest;

use PHPUnit\Framework\TestCase;
use Radionovel\Exceptions\AccessErrorException;
use Radionovel\Exceptions\PathNotExistsException;
use Radionovel\FileManager;

/**
 * Class FileManagerTest
 * @package RadionovelTest
 */
class FileManagerTest extends TestCase
{

    /**
     * @throws AccessErrorException
     * @throws PathNotExistsException
     */
    public function testGetDirectories()
    {
        $manager = new FileManager(__DIR__ . DIRECTORY_SEPARATOR . 'data');

        $directories = $manager->getDirectories();

        $this->assertCount(2, $directories);

    }

    /**
     * @throws AccessErrorException
     * @throws PathNotExistsException
     */
    public function testGetAllFiles()
    {
        $manager = new FileManager(__DIR__ . DIRECTORY_SEPARATOR . 'data');

        $files = $manager->getFiles();

        $this->assertCount(2, $files);
    }

    /**
     * @throws AccessErrorException
     * @throws PathNotExistsException
     */
    public function testGetFilesByExtension()
    {
        $manager = new FileManager(__DIR__ . DIRECTORY_SEPARATOR . 'data', ['txt']);

        $files = $manager->getFiles();
        $this->assertCount(1, $files);

        $manager->getValidator()->setAllowedExtensions(['dat']);
        $files = $manager->getFiles();
        $this->assertCount(1, $files);

    }
    /**
     * @throws AccessErrorException
     * @throws PathNotExistsException
     */
    public function testGetRecursiveFilesByExtension()
    {
        $manager = new FileManager(__DIR__ . DIRECTORY_SEPARATOR . 'data', ['txt']);

        $files = $manager->getFiles('', true);
        $this->assertCount(3, $files);

        $manager->getValidator()->setAllowedExtensions(['dat']);
        $files = $manager->getFiles('', true);
        $this->assertCount(2, $files);
    }
}
