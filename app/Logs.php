<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Logs
 *
 * @property int $id
 * @property int $facility_id
 * @property int $adult
 * @property int $child
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property-read \App\Logs $facility
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logs newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logs newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logs query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logs whereAdult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logs whereChild($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logs whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logs whereFacilityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logs whereId($value)
 * @mixin \Eloquent
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Logs whereUpdatedAt($value)
 */
class Logs extends Model
{
    protected static $unguarded = true;

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Logs::class);
    }
}
