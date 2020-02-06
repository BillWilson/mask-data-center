<?php

namespace App\Console\Commands;

use App\Facility;
use Illuminate\Console\Command;
use Box\Spout\Reader\CSV\Sheet;
use Box\Spout\Reader\CSV\Reader;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as HttpClient;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class CreateFacilitiesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mask-facility:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create facility data';

    /**
     * @var HttpClient
     */
    protected $client;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var Collection
     */
    protected $temp;

    /**
     * Create a new command instance.
     *
     * @param HttpClient $client
     */
    public function __construct(HttpClient $client)
    {
        $this->client = $client;
        $this->temp = collect();
        $this->filePath = storage_path('app/public/facilities.csv');

        parent::__construct();
    }

    /**
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException
     * @throws \Throwable
     */
    public function handle(): void
    {
        # Alternative https://raw.githubusercontent.com/kiang/pharmacies/master/json/points.json
        #             https://raw.githubusercontent.com/kurotanshi/mask-map/master/public/med-stores_geojson.json

        $link = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQEmg4lHhss-5DOkfQPBFADoTjWsOF6j_QuwkifEzYQAq8USqkx8Sj0dSYIfvLsDcND3ugodOWxRtcc/pub?gid=1593882819&single=true&output=csv';
        $this->client->request('GET', $link, [ 'sink' => $this->filePath]);

        /* @var Reader $reader */
        $reader = tap(ReaderEntityFactory::createCSVReader(), fn (Reader $reader) => $reader->open($this->filePath));

        $count = 1;
        foreach ($reader->getSheetIterator() as $sheet) {
            /* @var Sheet $sheet */
            foreach ($sheet->getRowIterator() as $row) {
                $data = collect($row->getCells())->map->getValue();

                if ($count > 1) {
                    $this->createData($data);
                }
                $count++;
            }

            $this->createData(collect(), true);
        }

        $reader->close();
    }

    /**
     * @param Collection $data
     * @param bool       $forceSubmit
     *
     * @throws \Throwable
     */
    protected function createData(Collection $data, bool $forceSubmit = false): void
    {
        if ($this->temp->count() > 99 or $forceSubmit) {
            DB::transaction(function () {
                Facility::insertOnDuplicateKey($this->temp->toArray());
            });

            $this->temp = collect();
        } else {
            $time = now();
            $this->temp->push([
                'code'          => $data->get(0),
                'name'          => $data->get(1),
                'tel'           => $data->get(2),
                'address'       => $data->get(3),
                'gmap_address'  => $data->get(4),
                'adult'         => 0,
                'child'         => 0,
                'latitude'      => empty($data->get(5)) ? null : $data->get(5),
                'longitude'     => empty($data->get(6)) ? null : $data->get(6),
                'updated_at'    => $time,
                'created_at'    => $time,
            ]);
        }
    }
}
