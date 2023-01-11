<?php

namespace App\Http\Helpers;

use App\Exceptions\StorageException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * A helper class providing functions useful for managing files and file names.
 */
class FileHelper
{
    /**
     * Deletes a file if it exists.
     *
     * @param string $path
     * path to the file
     * @throws StorageException
     */
    public static function deleteFileIfExists(string $path)
    {
        if (! $path || ! Storage::exists($path))
            return;
        if (! Storage::delete($path))
            throw new StorageException('The file wasn\'t deleted.');
    }

    /**
     * Transforms a string by replacing all spaces by dashes and removing all characters which are not a letter,
     * a digit or a dash.
     *
     * @param string $string
     * the string to be transformed
     * @return string
     * the transformed string
     */
    public static function sanitizeString(string $string)
    {
        $stringWithoutSpaces = Str::replace(' ', '-', $string);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $stringWithoutSpaces);
    }

    /**
     * Appends a file's extension to a string. If the file has no extension, nothing is appended.
     *
     * @param string $path
     * path to the file whose extension should be extracted
     * @param string $fileName
     * the string to which the extension should be appended
     * @return string
     * the extended string
     */
    public static function appendFileExtension(string $path, string $fileName)
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return (empty($extension)) ? $fileName : "$fileName.$extension";
    }
}
