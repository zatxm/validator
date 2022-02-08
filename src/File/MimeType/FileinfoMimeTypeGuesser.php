<?php

namespace Zatxm\Validation\File\MimeType;

use Zatxm\Validation\File\Exception\FileNotFoundException;
use Zatxm\Validation\File\Exception\AccessDeniedException;

/**
 * Guesses the mime type using the PECL extension FileInfo.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class FileinfoMimeTypeGuesser implements MimeTypeGuesserInterface
{
    private $magicFile;

    /**
     * Constructor.
     *
     * @param string $magicFile A magic file to use with the finfo instance
     *
     * @link http://www.php.net/manual/en/function.finfo-open.php
     */
    public function __construct($magicFile = null)
    {
        $this->magicFile = $magicFile;
    }

    /**
     * Returns whether this guesser is supported on the current OS/PHP setup.
     *
     * @return bool
     */
    public static function isSupported()
    {
        return function_exists('finfo_open');
    }

    /**
     * {@inheritdoc}
     */
    public function guess($path)
    {
        if (!is_file($path)) {
            throw new FileNotFoundException($path);
        }

        if (!is_readable($path)) {
            throw new AccessDeniedException($path);
        }

        if (!self::isSupported()) {
            return;
        }

        if (!$finfo = new \finfo(FILEINFO_MIME_TYPE, $this->magicFile)) {
            return;
        }

        return $finfo->file($path);
    }
}
