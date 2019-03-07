<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Model extends Eloquent
{
    /**
     * @return string
     */
    public static function tableName()
    {
        return with(new static)->getTable();
    }

    // со временем добавятся и другие кастомные методы и свойства

    // если все модели со временем станут наследоваться от BaseModel, то перенесётся туда, а Model уничтожится
}
