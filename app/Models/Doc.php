<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doc extends Model
{
    public function users()
    {
        return $this->hasMany(Field::class, 'doc_id');
    }
}
