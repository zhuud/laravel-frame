<?php

namespace App\Console\Commands;

use Overtrue\LaravelPinyin\Facades\Pinyin;
use App\Libs\Error;
use App\Libs\Client;
use App\Models\City;
use Illuminate\Console\Command;

class SyncCity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature    = 'op:sync-city';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description  = '同步高德城市数据信息';

    /**
     * 请求地址和key
     */
    const URI               = 'http://restapi.amap.com/v3/config/district';

    /**
     * 成功状态码
     */
    const SUCCESS_CODE      = 10000;

    const TYPE_MAP          = [
        'province'  => 1,
        'city'      => 2,
        'district'  => 3,
    ];


    /**
     * Execute the console command.
     *
     * @param City $cityModel
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function handle(City $cityModel)
    {
        $result     = $this->getApi();
        $bar        = $this->output->createProgressBar(count($result));

        $cityModel->truncate();

        foreach ($result as $province) {

            $cityData   = [];
            $this->genData($province, 0, $cityData);

            foreach ($province['districts'] as $city) {

                $this->genData($city, $province['adcode'], $cityData);

                foreach ($city['districts'] as $district) {
                    $this->genData($district, $city['adcode'], $cityData);
                    $bar->advance();
                }
            }

            // 入库
            $cityModel->insert($cityData);
            $this->table(['adcode','citycode','name','pinyin','class','parent_id','type',], $cityData);
        }
    }

    /**
     * 生成数据
     *
     * @param array $data
     * @param int $parentId
     * @param array $cityData
     */
    protected function genData(array $data, int $parentId, array &$cityData)
    {
        $level = data_get(self::TYPE_MAP, $data['level']);
        if (empty($level)) {
            return;
        }
        $date = date('Y-m-d H:i:s');
        $cityData[] = [
            'adcode'    => $data['adcode'],
            'citycode'  => empty($data['citycode']) ? 0 : $data['citycode'],
            'name'      => $data['name'],
            'pinyin'    => Pinyin::abbr($data['name']),
            'class'     => $this->getCityClass($data['name']),
            'parent_id' => $parentId,
            'type'      => data_get(self::TYPE_MAP, $data['level'],3),
            'created_at' => $date,
            'updated_at' => $date,
        ];
    }

    /**
     * 获取城市等级
     *
     * @param $name
     * @return int
     */
    protected function getCityClass(string $name) : int
    {
        foreach (config('city') as $class => $item) {
            if (in_array($name, $item))     return  $class;
        }

        return  0;
    }

    /**
     * 请求高德API
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getApi() : array
    {
        $response = Client::parse()->request('GET', self::URI, [
            'query' => [
                'subdistrict'   => 3,
                'extensions'    => 'base',
                'key'           => config('api.amap.gaodeKey'),
            ],
        ]);

        $result  = json_decode($response->getBody()->getContents(), true);

        if (self::SUCCESS_CODE != $result['infocode']) {
            Error::apiErr($result['info']);
        }

        return  data_get($result, 'districts.0.districts', []);
    }
}
