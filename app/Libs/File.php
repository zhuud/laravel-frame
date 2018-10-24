<?php

namespace App\Libs;

/**
 * Class File
 * @package App\Libs
 */
class File
{
    /**
     * 删除目录
     *
     * @param string $dir
     * @return bool
     */
    public static function delDir(string $dir) : bool
    {
        //先删除目录下的文件：
        $dh = opendir($dir);

        while ($file=readdir($dh)) {

            if($file!="." && $file!="..") {

                $fullpath=$dir."/".$file;
                if(!is_dir($fullpath)) {

                    unlink($fullpath);
                } else {

                    self::delDir($fullpath);
                }
            }
        }

        closedir($dh);

        //删除当前文件夹：
        if(rmdir($dir)) return true;

        return false;
    }

    /**
     * 创建文件夹
     *
     * @param $path
     */
    public static function checkOrMakeDir(String $path)
    {
        is_dir($path) || mkdir($path, 0777, true);
    }

    /**
     * 按文件路径创建文件夹
     *
     * @param $filePath
     * @param bool $prefix
     */
    public static function mkDirAccToFile(String $filePath, $prefix = false)
    {
        $storePath = mb_substr($filePath, 0, mb_strrpos($filePath, '/'));

        if ($prefix) {

            $storePath = $prefix . '/' . $storePath;
        }

        if (!file_exists($storePath)) {

            mkdir($storePath,0777, true);
        }
    }

    /**
     * 文件大小格式转换
     *
     * @param $bytes
     * @return string
     */
    public static function sizeConvert(string $bytes) : string
    {
        $unit  = ['B','KB','MB','GB','TB','PB'];

        if ($bytes == 0) return '0 B';

        return @round($bytes / pow(1000,($i = floor(log($bytes,1000)))),2) .' '. (isset($unit[$i]) ? $unit[$i] : 'B');
    }
}