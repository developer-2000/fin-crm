<?php
namespace App\Services;

use Transliterate;
use App\Models\FileEntity;
use App\Models\File as ModelFile;
use Illuminate\Http\UploadedFile;

class UploadFileService
{
    private $path;
    private $entityId;
    private $originName;
    private $file;
    private $duplicate;
    private $model;

    public function __construct(UploadedFile $file, $model)
    {
        $this->path = '/files/';
        $this->file = $file;
        $this->entityId = $model->id;
        $this->duplicate = (object)['filename' =>'', 'ext' =>'', 'prefix' =>0 ];
        $this->model = $model;
    }
    /*
    * You can set castom name folder by file
    * @folder
    */
    public function upload($folder = null):bool
    {
        $this->setPathUpload($folder);
        $newName = $this->newFileName();

        if ($this->file->move(storage_path() .$this->path, $newName)) {
            $this->path .= $newName;
            if ($this->store()) {
                return true;
            }
        }

        return false;
    }

    private function newFileName():string
    {
        $ext = $this->file->getClientOriginalExtension();
        $explodeName = explode('.', $this->file->getClientOriginalName());//pathinfo($this->file->getClientOriginalName(), PATHINFO_FILENAME);
        $this->originName = $explodeName[0];
        $name = (string)Transliterate::make(
              $this->originName,
              [
                'type' => 'filename',
                'lowercase' => true
              ]
            );

        $this->duplicate->filename = $name;
        $this->duplicate->ext = $ext;
        $fileName = $this->checkDuplicate($name . '.' . $ext);

        return $fileName;
    }

    private function store()
    {
        $entity = $this->model::find($this->entityId);
        if ($entity) {
            $modelFile = ModelFile::create(
          [
            'name' => $this->originName,
            'path' => $this->path,
            'option' => \json_encode([
              'name' => $this->file->getClientOriginalName(),
              'size' => $this->file->getSize(),
              'type' => $this->file->getClientMimeType()
            ])
          ]
        );
            $this->file->id = $modelFile->id;
            $morph = $modelFile->entities($this->model)->save($entity);

            return $morph->id;
        }
        return null;
    }

    private function checkDuplicate(string $fileName)
    {
        $path = storage_path() . $this->path . $fileName;
        if (\file_exists($path)) {
            $this->duplicate->prefix++;
            return $this->checkDuplicate(
                  $this->duplicate->filename.'('.$this->duplicate->prefix.').'.
                  $this->duplicate->ext
                  );
        }

        return $fileName;
    }

    private function setPathUpload($folder = null)
    {
        if ($folder == null) {
            $folder = mb_strtolower(class_basename($this->model));
        }

        $this->path  .= mb_strtolower($folder) . '/' . $this->model->id . '/';
    }

    public function getUplodedFileId():int
    {
        return $this->file->id;
    }
}
