<?php

namespace App\Models;

use App\Models\Api\ViettelKey;
use Illuminate\Database\Eloquent\Model;

class ViettelSender extends Model
{
   protected $fillable =['customer_id','viettel_key_id','warehouse_id','name','address','phone',
                         'post_id','province_id','district_id','wards_id','province_name',
                         'district_name','wards_name'];

   public function key(){
        return $this->belongsTo(ViettelKey::class, 'viettel_key_id', 'id');
   }
}
