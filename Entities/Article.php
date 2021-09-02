<?php
/**
 * Created By PhpStorm.
 * User: Li Ming
 * Date: 2021-06-25 15:44
 * Fun:
 */

namespace Modules\Article\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Schema;

class Article extends BaseModel
{
    use HasFactory, SoftDeletes;
    protected $table = "article";
    protected $guarded = [];

    /**
     * 设置状态数组值
     * @return string[]
     */
    public static function getStatusArr()
    {
        return [
            1 => "开启",
            0 => "关闭",
        ];
    }

    /**
     * 设置分类图片添加域名
     * @return string
     */
    public function getShowPicAttribute()
    {
        if($this->pic == "") return "";

        $ht = env('APP_URL') ?? "";
        if($ht == ""){
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            $ht = $http_type . $_SERVER["HTTP_HOST"];
        }
        return $ht . "/" . $this->pic;
    }
}