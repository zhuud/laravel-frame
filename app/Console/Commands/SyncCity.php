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
        $bar        = $this->output->createProgressBar($result);

        $cityModel->truncate();

        foreach ($result as $province) {

            $cityData   = [];
            $cityData[] = $this->genData($province, 0);

            foreach ($province['districts'] as $city) {

                $cityData[] = $this->genData($city, $province['adcode']);

                // 空的数据跳过(香港澳门数据adcode重复 数据问题 数据忽略)
                if ($province['name'] == '香港特别行政区' || $province['name'] == '澳门特别行政区')   continue;

                foreach ($city['districts'] as $district) {

                    $cityData[] = $this->genData($district, $city['adcode']);
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
     * @return array
     */
    protected function genData(array $data, int $parentId) : array
    {
        return  [
            'adcode'    => $data['adcode'],
            'citycode'  => empty($data['citycode']) ? 0 : $data['citycode'],
            'name'      => $data['name'],
            'pinyin'    => Pinyin::abbr($data['name']),
            'class'     => $this->getCityClass($data['name']),
            'parent_id' => $parentId,
            'type'      => isset(self::TYPE_MAP[$data['level']]) ?? 3,
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
                'key'           => config('api.amap.key'),
            ],
        ]);

        $result  = Client::getResult($response->getBody()->getContents());

        if (self::SUCCESS_CODE != $result['infocode']) {

            Error::apiErr($result['info']);
        }

        return  $result['districts'][0]['districts'];
    }
}
