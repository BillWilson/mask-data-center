<?php

namespace App\Actions;

use Carbon\Carbon;
use Lorisleiva\Actions\Action;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class FacilityList extends Action
{
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
            'radius' => [
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
        $result = $this->getData();

        $result->transform(function ($item){
            $item->address = $item->address ?? $item->gmap_address;
            $item->updated_at = Carbon::parse($item->updated_at)->toISOString();
            unset($item->gmap_address);
            return $item;
        });

        return response()->json([
            'data' => $result
        ]);
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    protected function getData()
    {
        $data = $this->validated();

        return DB::table(function (Builder $query) use ($data){
            $query->selectRaw('ST_Distance_Sphere(
                     point(longitude, latitude),
                     point(?, ?)
                 ) as meters', [$data['longitude'], $data['latitude']])
                ->addSelect([
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
                ])
                ->from('facilities')
                ->where('adult', '>', 0)
                ->where('child', '>', 0)
            ;
        }, 'facilities')
            ->whereNotNull('meters')
            ->where('meters', '<', $data['radius'])
            ->orderBy('meters')
            ->limit(40)
            ->get()
        ;
    }
}
