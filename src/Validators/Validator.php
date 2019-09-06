<?php

namespace Radionovel\Validators;

use Radionovel\Contracts\ValidatorContract;

/**
 * Class Validator
 * @package Radionovel\Validators
 */
class Validator implements ValidatorContract
{

    /**
     * @var array
     */
    private $excludeDirectories = ['.', '..'];

    /**
     * @var array
     */
    protected $allowedExtensions;

    /**
     * Validator constructor.
     * @param array $allowedExtensions
     */
    public function __construct(array $allowedExtensions = ['*'])
    {
        $this->setAllowedExtensions($allowedExtensions);
    }

    /**
     * @param $file
     * @return bool
     */
    public function validate($file): bool
    {
        $path_info = pathinfo($file);
        if (is_dir($file)) {
            return !in_array(strtolower($path_info['basename']), $this->excludeDirectories);
        } elseif (is_file($file)) {
            if (in_array('*', $this->allowedExtensions)) {
                return true;
            }
            return in_array(strtolower($path_info['extension']), $this->allowedExtensions);
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAllowedExtensions(): array
    {
        return $this->allowedExtensions;
    }

    /**
     * @param array $allowedExtensions
     */
    public function setAllowedExtensions(array $allowedExtensions): void
    {
        $this->allowedExtensions = array_map('strtolower',$allowedExtensions);
    }
}
