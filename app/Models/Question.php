<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = ['questionnaire_id','category','question_text','type','weight'];

    protected $casts = ['category' => 'string'];

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function categoryName()
    {
        return $this->belongsTo(EdomCategory::class, 'category', 'id')->withDefault(['value' => 'Uncategorized']);
    }

    public function responses(): HasMany
    {
        return $this->hasMany(Response::class);
    }

    public function getCategoryAttribute($value)
    {
        return strtoupper(trim($value));
    }

    public function setCategoryAttribute($value)
    {
        $this->attributes['category'] = strtoupper(trim($value));
    }

    public function toArray()
    {
        $array = parent::toArray();
        return $array;
    }
}
