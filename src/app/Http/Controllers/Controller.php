<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\throwException;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Deletes the file at the given path, if the file exists.
     *
     * @param $path
     * @return void
     */
    protected function deleteFileIfExists($path)
    {
        if (! $path || ! Storage::exists($path)) return;
        if (! Storage::delete($path)) throwException(new Exception('The file wasn\'t deleted.'));
    }

    /**
     * Returns the given string with all the spaces replaced by dashes and non-alphanumerical symbols removed.
     *
     * @param $string
     * @return array|string|string[]|null
     */
    protected function removeSpecialCharacters($string)
    {

        $string = str_replace(' ', '-', $string);
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string);

    }

}
