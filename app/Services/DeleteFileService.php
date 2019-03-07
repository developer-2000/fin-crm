<?php
namespace App\Services;

use Transliterate;
use App\Models\FileEntity;
use App\Models\File;

class DeleteFileService
{
    protected $entity;

    /*
    @param model -> entity
    Entity must have relation - files (morphToMany)
    */
    public function __construct($entity)
    {
        $this->entity = $entity;
    }

    public function delete(int $fileId):bool
    {
        $file = File::find($fileId);
        $check = FileEntity::filter($file->id, $this->entity->id)->count();

        if ($check == 1) {
          if (unlink(storage_path() . $file->path)) {
              $count = $file->entities($this->entity)->detach($this->entity->id);
              if ($count) {
                $this->entity->files()->where('file_id', $file->id)->delete();

                  return true;
             }
          }
            return false;
        } else {
            $count = $file->entities($this->entity)->detach($this->entity->id);

            if ($count) {
                return true;
            }
            return false;
        }

        return false;
    }
}
