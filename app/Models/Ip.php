<?php

namespace App\Models;

class Ip extends BaseModel
{
   protected $table = 'iptable';

   /*get user*/
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}