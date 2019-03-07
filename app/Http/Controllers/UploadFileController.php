<?php

namespace App\Http\Controllers;

use App\Services\UploadFileService;
use App\Http\Requests\UploadFileRequest;
use App\Models\Documentation;

class UploadFileController extends Controller
{
    public function __invoke(UploadFileRequest $request)
    {
        $uploadService = new UploadFileService(
        $request->file,
        $this->getModel($request)
      );

        if ($uploadService->upload()) {
            return response()->json(['success' => true, 'id' => $uploadService->getUplodedFileId() ]);
        }
        return response()->json(
          [
            'success' => false,
            'message' => 'Error service upload'
         ]
       );
    }

    private function getModel($request)
    {
        switch ($request->entity) {
        case 'documentation':
          return Documentation::find($request->entity_id);
          break;
      }
        exit(
          json_encode([
          'success' => false,
          'message' => "Model not found!",
      ]));
    }
}
