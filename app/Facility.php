<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Facility
 *
 * @property int $id
 * @property string $name
 * @property string $tel
 * @property string $address
 * @property string|null $gmap_address
 * @property string|null $notice
 * @property int $adult
 * @property int $child
 * @property float $latitude
 * @property float $longitude
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Logs[] $logs
 * @property-read int|null $logs_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereAdult($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereChild($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereGmapAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereNotice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereTel($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string $code
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Facility whereCode($value)
 */
class Facility extends Model
{
    protected static $unguarded = true;

    public function logs(): HasMany
    {
        return $this->hasMany(Logs::class);
    }
}
