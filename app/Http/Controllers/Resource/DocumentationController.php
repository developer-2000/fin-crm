<?php

namespace App\Http\Controllers\Resource;

class DocumentationController
{
    public function __invoke(string $pathDoc)
    {
        $path = storage_path() . "/files/documentation/".$pathDoc;
        $fileName = \basename($path);
        return response()->download($path, $fileName,['Content-Type' => 'multipart/form-data']);
    }
}
