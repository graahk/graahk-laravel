<?php

namespace App\Models;

use AngryMoustache\Media\Models\Attachment;
use App\Enums\Format;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Artist extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function cards()
    {
        return $this->hasMany(Card::class);
    }
}
