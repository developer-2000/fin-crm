<?php

namespace App\Models;

use App\Http\Requests\Request;
use App\Models\BaseModel;
use Illuminate\Support\Facades\DB;


class Sessions extends BaseModel
{
   protected $table = 'sessions';

   public function getOneSession($id, $userId = null)
   {
       $query = DB::table($this->table)
           ->where('id', $id);
       if ($userId) {
           $query = $query->where('user_id', $userId);
       }
       return $query->first();
   }

   public function setUserIdAndRole($id, $userId, $data)
   {
           return DB::table($this->table)
           ->where('id', $id)
           ->where('user_id', $userId)
           ->update($data);
   }

   public function deleteAllSessionsOneUser($userId)
   {
       return DB::table($this->table)
           ->where('user_id', $userId)
           ->delete();
   }

   public function getAllSessionOneUser($userId)
   {
       return DB::table($this->table)
           ->where('user_id', $userId)
           ->get();
   }

   public function deleteOtherSession($sessionId, $userId)
   {
       return DB::table($this->table)
           ->where('id', '!=', $sessionId)
           ->where('user_id', $userId)
           ->delete();
   }
}