<?php

namespace App\Console\Commands;

use App\Http\Libs\Client;
use Illuminate\Console\Command;


class SpiderWYMusic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spider:WYMusic {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '网易云音乐蜘蛛';

    /**
     * 公共请求头
     */
    protected $headerCommon = [];


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $actionName = $this->argument('action');
        $callback   = [$this, 'spider' . $actionName];

        if (!is_callable($callback)) {

            $this->error('不存在对应的action参数 ' . $actionName);

            return;
        }

        return  call_user_func($callback);
    }

    /**
     * 评论数据抓取
     *
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function spiderComments()
    {
        $rid        = 'R_SO_4_551338687';
        $url        = 'http://music.163.com/weapi/v1/resource/comments/' . $rid . '?csrf_token=';
        $headers    = $this->headerCommon + [
            'Cookie'    => 'appver=1.5.0.75771;',
        ];
        $encSecKey  = '257348aecb5e556c066de214e531faadd1c55d814f9be95fd06d6bff9f4c7a41f831f6394d5a3fd2e3881736d94a02ca919d952872e7d0a50ebfa1769a7a62d512f5f1ca21aec60bc3819a9c3ffca5eca9a0dba6d6f7249b06f5965ecfff3695b54e1c28f3f624750ed39e7de08fc8493242e26dbc4484a01c76f739e135637c';
        $params     = json_encode([
            'rid'           => $rid,
            'offset'        => 0,
            'total'         => 'true',
            'limit'         => '20',
            'csrf_token'    => '',
        ]);

        $key1       = '0CoJUm6Qyw8W8jud';
        $iv         = '0102030405060708';
        $cipher     = 'AES-128-CBC';
        dump($params);
        $paramsEn1  = base64_encode(openssl_encrypt($params, $cipher, $key1, OPENSSL_RAW_DATA, $iv));
        $key2       = str_repeat('F', 16);
        dump($paramsEn1);
        $paramsEn2  = base64_encode(openssl_encrypt($paramsEn1, $cipher, $key2, OPENSSL_RAW_DATA, $iv));
        dump($paramsEn2);
        $optRequest = [
            'form_params'   => [
                'params'    => $paramsEn2,
                'encSecKey' => $encSecKey,
            ],
            'headers'   => $headers,
        ];
        //dump(openssl_get_cipher_methods());
        dump($optRequest);
        dump('l0DReXb5aAvihEQDNTMbTa7+nPPGN/H00lOvE/h4C7Jbe8xoNDCJD7J4fMb+crzbZbu19Fk/icpW+RfQSWqvxA==');
        dump('O5/yxckUkfK03FP34r7bgJVnmX5k2/G/l+JCIrgOQwyl+J53UnkgD1kh9z4b6IVTfsjcxGSpGImwZD9kofBZOZTdVyWchjIZes8sb6tnAWuqdC4/j7mOH13VQxx3TDUy');
        //$request    = new Request('POST', $url, $optRequest);
        //$response   = $this->_client()->send($request, ['headers' => $headers]);
        //dump($response->getBody()->getContents());
        $response   = $this->getClient()->request('POST', $url, $optRequest);
        var_dump($response->getBody()->getContents());
        /*
        $promise    = $this->_client()->sendAsync($request, ['headers' => $headers]);
        $promise->then(
            function (ResponseInterface $response) {

                $this->info($response->getStatusCode());
                $this->info($response->getBody());
            },
            function (RequestException $e) {

                $this->error($e->getMessage());
                $this->error($e->getRequest()->getMethod());
            }
        );
        $promise->wait();
        */
    }

    /**
     * 获取客户端实例
     */
    private function getClient()
    {
        return  new Client([
            'timeout'   => 3.0,
        ]);
    }
}
