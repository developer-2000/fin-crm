<?php
namespace App\Services;

use App\Models\UserAccess;

class AccessService
{
  private $access;
  private $entity;
  private $entityId;

  public function _construct(array $obj,string $en,int $id)
  {
    $this->access = $obj;
    $this->entity = $en;
    $this->entityId = $id;
  }
  public function set()
  {
    foreach ($this->access as $rule) {
    //  $rule
    }
  }

  private function setByEntity($rule)
  {
    // code...
  }
}
