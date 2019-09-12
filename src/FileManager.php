<?php

namespace Radionovel;

use Radionovel\Contracts\ValidatorContract;
use Radionovel\Exceptions\AccessErrorException;
use Radionovel\Exceptions\PathNotExistsException;
use Radionovel\Validators\Validator;
use RuntimeException;

/**
 * Class FileManager
 * @package Radionovel
 */
class FileManager
{
    /**
     * @var bool|string
     */
    private $basePath = '';
    /**
     * @var array
     */
    private $allowedExtensions = ['*'];
    /**
     * @var ValidatorContract
     */
    private $validator = null;

    /**
     * FileManager constructor.
     * @param string $base_path
     * @param array $allowedExtensions
     */
    public function __construct(string $base_path, array $allowedExtensions = ['*'])
    {
        $this->basePath = realpath($base_path);
        if (false === $this->basePath) {
            throw new RuntimeException('Wrong base path');
        }

        $this->allowedExtensions = $allowedExtensions;
    }

    /**
     * @return ValidatorContract
     */
    public function getValidator(): ValidatorContract
    {
        if (null === $this->validator) {
            $this->validator = new Validator($this->allowedExtensions);
        }

        return $this->validator;
    }

    /**
     * @param ValidatorContract $validator
     */
    public function setValidator(ValidatorContract $validator): void
    {
        $this->validator = $validator;
    }

    /**
     * @param $file
     * @return bool
     */
    private function validate($file): bool
    {
        return $this->getValidator()->validate($file);
    }

    /**
     * @param array $extensions
     */
    public function setAllowFileExtensions(array $extensions = ['*'])
    {
        $this->allowedExtensions = $extensions;
    }

    /**
     * @param string $relative_path
     * @return array
     * @throws AccessErrorException
     * @throws PathNotExistsException
     */
    public function getDirectories(string $relative_path = '')
    {
        $directories = [];
        $dir = $this->retrieveFullPath($relative_path);
        if (is_dir($dir)) {
            $files = scandir($dir);
            foreach ($files as $file) {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $file) && $this->validate($dir . DIRECTORY_SEPARATOR . $file)) {
                    $directories[] = $file;
                }
            }
        }
        return $directories;
    }

    /**
     * @param string $relative_path
     * @param bool $recursive
     * @param bool $include_relative_path
     * @return array
     * @throws AccessErrorException
     * @throws PathNotExistsException
     */
    public function getFiles(string $relative_path = '', $recursive = false, $include_relative_path = false): array
    {
        $file_list = [];
        $dir = $this->retrieveFullPath($relative_path);
        if (is_dir($dir)) {
            $files = scandir($dir);
            $tmp_file_list = [];
            foreach ($files as $file) {
                $file_path = $dir . DIRECTORY_SEPARATOR . $file;

                if (!$this->validate($file_path)) {
                    continue;
                }

                if (is_file($file_path)) {
                    $tmp_file_list[] =
                        ($include_relative_path ? $relative_path . DIRECTORY_SEPARATOR : '') . $file;
                } elseif (true === $recursive && is_dir($file_path)) {
                    $file_list[] =
                        $this->getFiles(
                            $relative_path . DIRECTORY_SEPARATOR . $file,
                            $recursive,
                            $include_relative_path
                        );
                }
            }

            $file_list[] = $tmp_file_list;
        }

        $file_list = array_merge(...$file_list);
        asort($file_list);
        return $file_list;
    }

    /**
     * @param $relative_path
     * @return bool|string
     * @throws AccessErrorException
     * @throws PathNotExistsException
     */
    private function retrieveFullPath($relative_path): string
    {
        if (substr($relative_path, 0, 1) !== DIRECTORY_SEPARATOR) {
            $relative_path = DIRECTORY_SEPARATOR . $relative_path;
        }

        $real_path = realpath($this->basePath . $relative_path);

        if (false === $real_path) {
            throw new PathNotExistsException('Path not exists');
        }

        if (false === strpos($real_path, $this->basePath)) {
            throw new AccessErrorException('Access denied');
        }

        return $real_path;
    }
}
