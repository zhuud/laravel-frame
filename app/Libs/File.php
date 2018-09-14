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
     * 文件大小格式转换
     *
     * @param $bytes
     * @return string
     */
    static public function sizeConvert(string $bytes) : string
    {
        $unit  = ['B','KB','MB','GB','TB','PB'];

        if ($bytes == 0) return '0 B';

        return @round($bytes / pow(1000,($i = floor(log($bytes,1000)))),2) .' '. (isset($unit[$i]) ? $unit[$i] : 'B');
    }
}