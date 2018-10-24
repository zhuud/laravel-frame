<?php

namespace App\Libs;

use OSS\OssClient;

/**
 * Class Oss
 * @package App\Libs
 */
class Oss
{
    /**
     * ossClient
     */
    private static $ossClient;


    /**
     * Oss constructor.
     *
     * @param $ossName
     * @param bool $useInternal
     * @throws \OSS\Core\OssException
     */
    private function __construct($ossName, $useInternal = false)
    {
        $config   = config("filesystems.disks.{$ossName}");

        $endpoint = $useInternal ? $config['endpoint_internal']
                                 : ($config['isCName'] ? $config['domain'] : $config['endpoint']);

        self::$ossClient = new OssClient($config['access_id'], $config['access_key'], $endpoint, $config['isCName']);

        self::$ossClient->setUseSSL($config['isSsl']);
    }

    /**
     * 解析Oss客户端
     *
     * @param $ossName
     * @param bool $useInternal
     * @return OssClient
     * @throws \OSS\Core\OssException
     */
    public static function parse($ossName, $useInternal = false)
    {
        if (!(self::$ossClient instanceof OssClient)) {

            new static($ossName, $useInternal);
        }

        return self::$ossClient;
    }
}