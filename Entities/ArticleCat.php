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
use Illuminate\Support\Facades\Schema;

class ArticleCat extends BaseModel
{
    use HasFactory;
    protected $table = "article_cat";
    protected $guarded = [];

    /**
     * 设置是否推荐数组值
     * @return string[]
     */
    public static function getRecommendArr()
    {
        return [
            0 => "否",
            1 => "是",
        ];
    }

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
     * 根据父级ID值获取所有的子分类
     * @param int $pid 父级ID
     * @param false $status 是否查询父级ID信息
     * @return object
     */
    public static function getCatArr($pid = 0, $status = false)
    {
        if($status){
            $list = ArticleCat::where("id", $pid)->first();
            if(!$list) return (object)[];
            $list["list"] = self::getCatArr($pid);
        }else{
            $list = ArticleCat::where("cat_pid", $pid)->get();
            foreach ($list as &$item){
                $item["list"] = self::getCatArr($item->id);
            }
        }
        return $list;
    }

    /**
     * 根据指定的ID 获取该ID的所有子分类
     * @param int $pid
     * @return array
     */
    public static function getCids(int $pid)
    {
        function getCidArr(array $pid){
            $arr = ArticleCat::whereIn("cat_pid", $pid)->pluck("id")->toArray();
            if(count($arr) > 0){
                $iArr = getCidArr($arr);
                return array_merge($pid, $iArr);
            }else{
                return $pid;
            }
        }
        return getCidArr([$pid]);
    }

    /**
     * 设置分类图片添加域名
     * @return string
     */
    public function getShowCatPicAttribute()
    {
        if($this->cat_pic == "") return "";

        $ht = env('APP_URL') ?? "";
        if($ht == ""){
            $http_type = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
            $ht = $http_type . $_SERVER["HTTP_HOST"];
        }
        return $ht . "/" . $this->cat_pic;
    }
}