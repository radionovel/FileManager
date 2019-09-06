<?php

namespace Radionovel\Contracts;

/**
 * Interface ValidatorContract
 * @package Radionovel
 */
interface ValidatorContract
{

    /**
     * ValidatorContract constructor.
     * @param array $allowedExtensions
     */
    public function __construct(array $allowedExtensions);

    /**
     * @param $file
     * @return bool
     */
    public function validate($file): bool;

    /**
     * @param array $allowedExtensions
     * @return mixed
     */
    public function setAllowedExtensions(array $allowedExtensions);
}
