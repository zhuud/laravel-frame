<?php

namespace App\Console\Commands;

use App\Http\Libs\Client;
use App\Http\Models\DouYinUser;
use App\Http\Models\DouYinVideo;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Console\Command;
use QL\QueryList;
use QL\Ext\AbsoluteUrl;


class SpiderDouYin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider:DouYin {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '抖音热榜用户、视频';

    /**
     *  模型
     */
    protected $videoModel;
    protected $userModel;


    /**
     * Execute the console command.
     */
    public function handle()
    {
        set_time_limit(0);
        if (file_exists(storage_path('douyin'))) exit;
        mkdir(storage_path('douyin'));

        $actionName = $this->argument('action');
        $callback   = [$this, 'spider' . $actionName];

        if (!is_callable($callback)) {

            $this->error('不存在对应的action参数 ' . $actionName);

            return;
        }

        $this->init();

        return  call_user_func($callback);
    }

    /**
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function spiderUsers()
    {
        $cookJar = $this->login();

        for ($i = 232; $i <= 300; $i++) {

            $this->info("正在爬取第{$i}页数据...");

            // 爬取视频数据
            $link = $this->getUserList($i,$cookJar);

            foreach ($link as $url) {

                $arr = $this->getUserDetail($url);

                $data = [
                    'uid'           => $arr[1],
                    'event_date'    => trim($arr[12],'  '),
                    'nickname'      => $arr[13],
                    'signature'     => $arr[2],
                    'gender'        => (string) DouYinUser::GENDERMAP[$arr[3]],
                    'gender_map'    => $arr[3],
                    'birthday'      => $arr[8],
                    'location'      => $arr[7],
                    'constellation' => (string) DouYinUser::CONSTELLATIONMAP[$arr[9]],
                    'constellation_map' => $arr[9],
                    'aweme_count'   => implode('', explode(',',$arr[6])),
                    'follower_count'    => implode('', explode(',',$arr[4])),
                    'total_favorited'   => implode('', explode(',',$arr[5])),
                    'weibo_name'    => $arr[10],
                    'weibo_url'     => $arr[11],
                    'avatar'        => $arr[0],
                ];

                $data = array_map(function (&$item) {

                    return  '未填写' === $item  ? ''   : $item;
                },$data);
                '' == $data['birthday'] ? $data['birthday'] = '1970-01-01' : null;

                // 入库
                $this->userModel->updateOrCreate(['uid' => $arr[1]], $data);
            }
        }
    }

    /**
     * 爬取用户列表
     *
     * @param $i
     * @param $cookJar
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getUserList($i,$cookJar)
    {
        sleep(19);
        $response = $this->getClient()->request('GET', "https://kolranking.com/?page={$i}",['cookies' => $cookJar]);

        $contents = $response->getBody()->getContents();

        $link = QueryList::getInstance()
            ->use(AbsoluteUrl::class)
            ->rules([
                'link' => ['td>a','href'],
                ])
            ->html($contents)
            ->absoluteUrl('https://jh1z.com/')
            ->query()
            ->getData()
            ->all();

        return  array_column($link,'link');
    }

    /**
     * @return CookieJar
     */
    private function login()
    {
        $jar = new CookieJar();
        $domain = 'kolranking.com';
        $cookies = [
            'laravel_session' => 'eyJpdiI6Im1DWG4ybTNlUXl1MmlvMnFwa3Y0YVE9PSIsInZhbHVlIjoicXpiTmZ0VXNNczZ4MmR2eUNueWdrOXlwZFA2QW1DXC90RHJPTk1nTnZcL21XZnMxV0xnc040Z3N0MGg0alwvblwvRDVDWVgxXC9hR2hLanlIYVhIMlgwcFwvQXc9PSIsIm1hYyI6ImU5Njk4YWM4NmFjMjJlMWE5YTVhMDYxOTQ4ZjhlM2VlMDdkZjQ1NzlkMTExY2RhNmRlM2Y3NzI3ODdhN2NhMGEifQ%3D%3D',
        ];

        return  $jar->fromArray($cookies,$domain);
    }

    /**
     * 爬取用户详情
     *
     * @param $url
     * @return array
     */
    private function getUserDetail($url)
    {
        sleep(19);

        $arr = QueryList::getInstance()
            ->rules([
                'name' => ['div>h1', 'text'],
                'src'  => ['tr>td>img', 'src'],
                'text' => ['tr>td:nth-child(2)', 'text'],
            ])
            ->get($url)
            ->absoluteUrl('https://jh1z.com/')
            ->query()
            ->getData()
            ->all();
        $arr[0]['text'] = $arr[0]['src'];
        array_push($arr,['text' => mb_substr($arr[0]['name'],0,mb_strripos($arr[0]['name'],'的个人资料'))]);

        return  array_column($arr,'text');
    }



    /**
     * 评论数据抓取
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function spiderVideos()
    {
        for ($i = 2000; $i <= 8000; $i++) {

            $this->info("正在爬取第{$i}页数据...");

            // 爬取视频数据
            $arr = $this->getVideoList($i);

            for ($j = 0; $j < count($arr[0]); $j++) {

                $data = [
                    'nickname'      => $arr[0][$j],
                    'event_date'    => $arr[3][$j],
                    'aweme_id'      => $arr[1][$j],
                    'publish_time'  => $arr[2][$j],
                    'describe'      => $arr[4][$j],
                    'broadcasted'   => $arr[5][$j],
                    'favorited'     => $arr[6][$j],
                    'commented'     => $arr[7][$j],
                    'shared'        => $arr[8][$j],
                ];

                // 爬取视频详情数据
                $detail = $this->getVideoDetail($arr[3][$j],$arr[1][$j]);

                $data += [
                    'aweme_url'     => $detail[0],
                ];

                // 入库
                $this->videoModel->updateOrCreate(['aweme_id' => $arr[1][$j]], $data);
            }
        }
    }

    /**
     * 爬取视频列表
     *
     * @param $i
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getVideoList($i)
    {
        sleep(1);
        $response = $this->getClient()->request('GET', "http://app.toobigdata.com/douyinvideo/hot?page={$i}");

        $contents = $response->getBody()->getContents();

        $reg = '#<div class="col-md-9">.*<h3>抖音(.*)的视频.*<a href=.*aweme_id=(.*)">.*</h3>.*<p>发布于: (.*)</p>.*<p>采集于: (.*)</p>.*<p>简介: (.*)</p>.*<p>播放: (.*)</p>.*<p>点赞: (.*)</p>.*<p>评论: (.*)</p>.*<p>分享: (.*)</p>#isU';
        preg_match_all($reg,$contents,$arr);
        array_shift($arr);

        return  $arr;
    }

    /**
     * 爬取视频详情数据
     *
     * @param $eventDate
     * @param $awemeId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getVideoDetail($eventDate, $awemeId)
    {
        sleep(1);
        $response = $this->getClient()->request('GET', "http://app.toobigdata.com/douyinvideo/view?event_date={$eventDate}&aweme_id={$awemeId}");

        $contents = $response->getBody()->getContents();

        $reg = '#<tr><th>视频地址</th><td>(.*)</td></tr>#isU';
        preg_match($reg,$contents,$arr);
        array_shift($arr);

        return  $arr;
    }

    /**
     * 初始化
     */
    private function init()
    {
        $this->videoModel   = new DouYinVideo();
        $this->userModel    = new DouYinUser();
    }

    /**
     * 获取客户端实例
     */
    private function getClient()
    {
        return  new Client([
            'cookies' => true,
        ]);
    }
}
