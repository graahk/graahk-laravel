<?php

namespace App\Models;

use AngryMoustache\Media\Models\Attachment;
use Illuminate\Database\Eloquent\Model;

class AvatarBorder extends Model
{
    protected $fillable = [
        'name',
        'attachment_id',
    ];

    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }

    public function users()
    {
        return $this->morphToMany(User::class, 'unlock', 'user_unlocks')
            ->where('enabled', true);
    }
}
