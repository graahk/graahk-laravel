<?php

namespace App\Models;

use App\Models\Interfaces\Collectible;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = [
        'user_id',
        'collectible',
        'amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function add(Collectible $item, int $amount = 1): self
    {
        $string = $item->toCollectibleString();

        if ($item->alternateArt) {
            $string = "{$string}/alternate:{$item->alternateArt->id}";
        }

        $collection = self::firstOrNew([
            'user_id' => auth()->id(),
            'collectible' => $string,
        ]);

        $collection->amount += $amount;
        $collection->save();

        return $collection;
    }
}
