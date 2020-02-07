<?php

namespace App\Actions;

use App\Facility;
use Carbon\Carbon;
use Lorisleiva\Actions\Action;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Query\Builder;

class FacilityList extends Action
{
    protected string $keyName = 'all-facilities';

    protected array $selectCols =[
        'code',
        'name',
        'tel',
        'address',
        'gmap_address',
        'adult',
        'child',
        'latitude',
        'longitude',
        'updated_at',
    ];

    protected int $dataCountLimit;

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'latitude' => [
                'required',
                'numeric',
            ],
            'longitude' => [
                'required',
                'numeric',
            ],
            'radius' => [ // meters
                'required',
                'numeric',
            ],
        ];
    }

    /**
     * Execute the action and return a result.
     *
     * @return mixed
     */
    public function handle(): JsonResponse
    {
        $mode = config('app.list-mode');

        $this->dataCountLimit = config('app.list-limit');

        $result = $mode === 'db' ? $this->getDbData() : $this->getFileData();

        $result->transform(
            function ($facility) {
                $facility->address = $facility->address ?? $facility->gmap_address;
                unset($facility->gmap_address);

                if (!$facility instanceof Facility) {
                    $facility->updated_at = Carbon::parse($facility->updated_at)->toISOString();
                }

                return $facility;
            }
        );

        return response()->json(
            [
                'data' => $result,
            ]
        );
    }

    protected function calculate($fromLat, $fromLon, $toLat, $toLon, $unit = "km")
    {
        $unit = strtolower($unit);

        if (($fromLat === $toLat) && ($fromLon === $toLon)) {
            return 0;
        } else {
            $theta = $fromLon - $toLon;

            $dist = sin(deg2rad($fromLat)) * sin(deg2rad($toLat)) + cos(deg2rad($fromLat)) * cos(deg2rad($toLat)) * cos(
                    deg2rad($theta)
                );

            $dist = rad2deg(acos($dist));

            $miles = $dist * 60 * 1.1515;

            if ($unit == "km") {
                return ($miles * 1.609344);
            } else {
                if ($unit == "nautical") {
                    # Nautical Miles
                    return ($miles * 0.8684);
                } else {
                    return $miles;
                }
            }
        }
    }

    protected function updateCache()
    {
        $list = Facility::select(['id', 'latitude', 'longitude'])->get()->toArray();

        $expiresAt = now()->addHours(12);

        Cache::put($this->keyName, $list, $expiresAt);

        return $list;
    }

    protected function getFileData()
    {
        $data = $this->validated();
        $result = collect();

        $list = collect(Cache::has($this->keyName) ? Cache::get($this->keyName) : $this->updateCache());

        $list->each(function ($item) use ($data, $result) {
            $limit = $data['radius'] / 1000;

            $distance = $this->calculate(
                $item['latitude'],
                $item['longitude'],
                $data['latitude'],
                $data['longitude']
            );

            if ($distance <= $limit) {
                $result->push([
                    'id' => $item['id'],
                    'distance' => $distance,
                ]);
            }
        });

        $result = $result->sortBy('distance', SORT_REGULAR)->take($this->dataCountLimit)->map->id;

        return Facility::select($this->selectCols)
            ->find($result->values()->all());
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function getDbData()
    {
        $data = $this->validated();

        return DB::table(
            function (Builder $query) use ($data) {
                $query->selectRaw(
                    'ST_Distance_Sphere(
                     point(longitude, latitude),
                     point(?, ?)
                 ) as meters',
                    [$data['longitude'], $data['latitude']]
                )
                    ->addSelect($this->selectCols)
                    ->from('facilities')
                    ->where('adult', '>', 0)
                    ->where('child', '>', 0);
            },
            'facilities'
        )
            ->whereNotNull('meters')
            ->where('meters', '<=', $data['radius'])
            ->orderBy('meters')
            ->limit($this->dataCountLimit)
            ->get();
    }
}
