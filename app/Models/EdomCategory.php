<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EdomCategory extends Model
{
    protected $table = 'edom_categories';

    protected $fillable = ['key', 'value'];

    public function questions()
    {
        return $this->hasMany(Question::class, 'category', 'id');
    }
}
