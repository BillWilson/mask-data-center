<?php

namespace App\Console\Commands;

use App\Logs;
use App\Facility;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Console\Command;
use Box\Spout\Reader\CSV\Sheet;
use Box\Spout\Reader\CSV\Reader;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client as HttpClient;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class UpdateInventoriesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mask-inventory:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update mask data';

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
        $this->filePath = storage_path('app/public/inventories.csv');
        $this->temp = collect();

        parent::__construct();
    }

    /**
     * @throws \Box\Spout\Reader\Exception\ReaderNotOpenedException
     * @throws \Throwable
     */
    public function handle(): void
    {
        $link = 'http://data.nhi.gov.tw/Datasets/Download.ashx?rid=A21030000I-D50001-001&l=https://data.nhi.gov.tw/resource/mask/maskdata.csv';
        $this->client->request('GET', $link, [ 'sink' => $this->filePath]);

        /* @var Reader $reader */
        $reader = tap(ReaderEntityFactory::createCSVReader(), fn (Reader $reader) => $reader->open($this->filePath));

        $count = 1;
        foreach ($reader->getSheetIterator() as $sheet) {
            /* @var Sheet $sheet */
            foreach ($sheet->getRowIterator() as $row) {
                $data = collect($row->getCells())->map->getValue();

                if ($count > 1) {
                    $sheet->getRowIterator()->end();
                    $this->updateData($data);
                }
                $count++;
            }
            $this->updateData(collect(), true);
        }

        $reader->close();
    }

    /**
     * @param Collection $data
     * @param bool       $forceSubmit
     *
     * @throws \Throwable
     */
    protected function updateData(Collection $data, bool $forceSubmit = false): void
    {
        if ($this->temp->count() > 99 or $forceSubmit) {
            DB::transaction(function () {
                $this->temp->each(function (array $value){
                    $facility = Facility::where('code', $value['code'])->first();

                    $facility->update($value);
                    Arr::pull($value, 'code');

                    $value = array_merge($value,['facility_id' => $facility->id]);

                    Logs::insertOnDuplicateKey(
                        array_merge($value,['created_at' => now()]),
                        $value
                    );
                });

                $this->temp = collect();
            });
        } else {
            $facility = Facility::where('code', $data->get(0))->first();

            if (!$facility) {
                Facility::create([
                    'code'          => $data->get(0),
                    'name'          => $data->get(1),
                    'tel'           => $data->get(3),
                    'address'       => $data->get(2),
                    'gmap_address'  => $data->get(2),
                    'adult'         => (int)$data->get(4),
                    'child'         => (int)$data->get(5),
                ]);
            }

            $this->temp->push([
                'code'          => $data->get(0),
                'adult'         => (int)$data->get(4),
                'child'         => (int)$data->get(5),
                'updated_at'    => Carbon::parse($data->get(6), 'Asia/Taipei')->setTimezone('UTC'),
            ]);
        }
    }
}
