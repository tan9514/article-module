<?php

namespace Modules\Article\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Article\Http\Controllers\Controller;
use Modules\Article\Http\Requests\Api\ArticleAgreePageListRequest;
use Modules\Article\Http\Requests\Api\ArticleAgreeRequest;
use Modules\Article\Http\Requests\Api\ArticleCatListRequest;
use Modules\Article\Http\Requests\Api\ArticleDetailsRequest;
use Modules\Article\Http\Requests\Api\ArticleFavoritePageListRequest;
use Modules\Article\Http\Requests\Api\ArticleFavoriteRequest;
use Modules\Article\Http\Requests\Api\ArticlePageListRequest;
use Modules\Article\Http\Requests\Api\ArticleRankingRequest;
use Modules\Article\Entities\Article;
use Modules\Article\Entities\ArticleAgree;
use Modules\Article\Entities\ArticleAttr;
use Modules\Article\Entities\ArticleAttrs;
use Modules\Article\Entities\ArticleCat;
use Modules\Article\Entities\ArticleCats;
use Modules\Article\Entities\ArticleFavorite;
use Modules\Article\Entities\ArticleRead;
use Modules\Article\Entities\ArticleTag;
use Modules\Article\Entities\ArticleTags;

class ArticleController extends Controller
{
    /**
     * 获取文章分类列表
     */
    public function getCatList(ArticleCatListRequest $request)
    {
        $request->check();
        function getChildren($pid){
            $list = ArticleCat::where([
                ["cat_pid", "=", $pid],
                ["status", "=", "1"],
            ])->orderBy("sort")->get();
            foreach ($list as &$item){
                $item["show_cat_pic"] = $item->show_cat_pic;
                $item["children"] = getChildren($item["id"]);
            }
            return $list;
        }
        $list = getChildren(0);
        return $this->success($list,"获取成功");
    }

    /**
     * 获取文章分页列表
     * @param ArticlePageListRequest $request
     * @return mixed
     */
    public function getArticlePageList(ArticlePageListRequest $request)
    {
        $request->check();

        $page = $request->input("page") ?? 1;
        $pagesize = $request->input("limit") ?? 10;
        $cat_id = $request->input("cat_id") ?? "";
        $title = $request->input("title") ?? "";
        $keys = $request->input("keys") ?? "";
        $author = $request->input("author") ?? "";
        $attr = $request->input("attr") ?? "";
        $tag = $request->input("tag") ?? "";
        $sort = $request->input("sort") ?? "";

        $where = [
            ["article.status", "=", "1"],
        ];
        if($cat_id != "") $where[] = ["acs.cat_id", "=", $cat_id];
        if($title != "") $where[] = ["article.title", "like", "%{$title}%"];
        if($keys != "") $where[] = ["article.keys", "like", "%{$keys}%"];
        if($author != "") $where[] = ["article.author", "like", "%{$author}%"];
        if($attr != ""){
            $attrInfo = ArticleAttr::where("name", $attr)->first();
            $where[] = ["aas.attr_id", "=", $attrInfo->id ?? 0];
        }
        if($tag != ""){
            $tagInfo = ArticleTag::where("name", $tag)->first();
            $where[] = ["ats.tag_id", "=", $tagInfo->id ?? 0];
        }
        switch ($sort){
            case "1":   // 1=阅读量倒序
                $sort = "read_count";
                $sortType = "desc";
                break;
            case "2":   // 2=点赞量倒序
                $sort = "agree_count";
                $sortType = "desc";
                break;
            case "3":   // 3=收藏量倒序
                $sort = "favorite_count";
                $sortType = "desc";
                break;
            default:
                $sort = "article.sort";
                $sortType = "asc";
        }
        //排序：阅读量，点赞量，收藏量，当天（阅读量，点赞量，收藏量），一周（阅读量，点赞量，收藏量），当月（阅读量，点赞量，收藏量）

        //获取总条数
        $count = Article::leftJoin('article_cats as acs','article.id','=','acs.article_id')
            ->leftJoin('article_attrs as aas','article.id','=','aas.article_id')
            ->leftJoin('article_tags as ats','article.id','=','ats.article_id')
            ->where($where)
            ->groupBy("article.id")
            ->get("article.*");
        $count = count($count);

        $offset = ($page-1)*$pagesize;
        $prefix = DB::getConfig('prefix');
        $list = Article::leftJoin('article_cats as acs','article.id','=','acs.article_id')
            ->leftJoin('article_attrs as aas','article.id','=','aas.article_id')
            ->leftJoin('article_tags as ats','article.id','=','ats.article_id')
            ->where($where)
            ->offset($offset)
            ->limit($pagesize)
            ->groupBy("article.id")
            ->orderBy($sort, $sortType)->orderBy("article.id", "desc")
            ->selectRaw("({$prefix}article.read + {$prefix}article.virtual_read) as read_count,({$prefix}article.agree + {$prefix}article.virtual_agree) as agree_count,({$prefix}article.favorite + {$prefix}article.virtual_favorite) as favorite_count,{$prefix}article.*")
            ->get();
        foreach ($list as &$item){
            $item["show_pic"] = $item->show_pic;
            $item['catArr'] = ArticleCats::join('article_cat as ac','article_cats.cat_id','=','ac.id')
                ->where("article_cats.article_id", $item->id)->pluck("name");
            $item['attrArr'] = ArticleAttrs::join('article_attr as aa','article_attrs.attr_id','=','aa.id')
                ->where("article_attrs.article_id", $item->id)->pluck("name");
            $item['tagArr'] = ArticleTags::join('article_tag as at','article_tags.tag_id','=','at.id')
                ->where("article_tags.article_id", $item->id)->pluck("name");
        }
        return $this->success(compact('count','list'));
    }

    /**
     * 获取文章详情
     * @param ArticleDetailsRequest $request
     * @return mixed
     */
    public function getDetails(ArticleDetailsRequest $request)
    {
        $request->check();
        $id = $request->input("id");
        $nInfo = $info = Article::where([
            ["id", "=", $id],
            ["status", "=", "1"],
        ])->first();
        if(!$info) return $this->failed("文章不存在", config('articlecommon.error'));

        $readModel = new ArticleRead();
        $readModel->uid = Auth::guard('api')->id() ?? 0;
        $readModel->article_id = $info->id;
        $readModel->save();

        $info["read"] = ArticleRead::where("article_id", $info->id)->count();
        $info->save();

        $info["show_pic"] = $info->show_pic;
        $info["read_count"] = $info["read"] + $info["virtual_read"];
        $info["agree_count"] = $info["agree"] + $info["virtual_agree"];
        $info["favorite_count"] = $info["favorite"] + $info["virtual_favorite"];
        $info['catArr'] = ArticleCats::join('article_cat as ac','article_cats.cat_id','=','ac.id')
            ->where("article_cats.article_id", $info->id)->pluck("name");
        $info['attrArr'] = ArticleAttrs::join('article_attr as aa','article_attrs.attr_id','=','aa.id')
            ->where("article_attrs.article_id", $info->id)->pluck("name");
        $info['tagArr'] = ArticleTags::join('article_tag as at','article_tags.tag_id','=','at.id')
            ->where("article_tags.article_id", $info->id)->pluck("name");
        return $this->success($info);
    }

    /**
     * 新增 || 取消点赞
     * @param ArticleAgreeRequest $request
     * @return mixed
     */
    public function agree(ArticleAgreeRequest $request)
    {
        $request->check();

        $article_id = $request->input("article_id");
        $info = $info = Article::where([
            ["id", "=", $article_id],
        ])->first();
        if(!$info) return $this->failed("文章不存在", config('articlecommon.error'));

        $type = $request->input("type");
        if(!in_array($type, [0,1])) return $this->failed("操作类型不存在", config('articlecommon.error'));

        $uid = Auth::guard('api')->id();
        if($uid <= 0) return $this->failed("请先登录", config('articlecommon.error'));

        if($type == 1){
            $agreeInfo = ArticleAgree::where([
                ["uid", "=", $uid],
                ["article_id", "=", $article_id],
            ])->first();
            if(!$agreeInfo){
                $ArticleAgreeModel = new ArticleAgree();
                $ArticleAgreeModel->uid = $uid;
                $ArticleAgreeModel->article_id = $article_id;
                if(!$ArticleAgreeModel->save()) return $this->failed("点赞失败", config('articlecommon.error'));
            }
            $msg = "点赞成功";
        }else{
            ArticleAgree::where([
                ["uid", "=", $uid],
                ["article_id", "=", $article_id],
            ])->delete();
            $msg = "取消点赞成功";
        }

        $info["agree"] = ArticleAgree::where("article_id", $article_id)->count();
        $info->save();
        return $this->success([], $msg);
    }

    /**
     * 获取个人点赞文章分页列表
     * @param ArticleAgreePageListRequest $request
     */
    public function getAgreePageList(ArticleAgreePageListRequest $request)
    {
        $request->check();
        $uid = Auth::guard('api')->id();
        if($uid <= 0) return $this->failed("请先登录", config('articlecommon.error'));

        $page = $request->input("page") ?? 1;
        $pagesize = $request->input("limit") ?? 10;

        $where = [
            ["article.status", "=", "1"],
            ["aa.uid", "=", $uid],
        ];

        //获取总条数
        $count = Article::join("article_agree as aa", 'article.id', "=", 'aa.article_id')->where($where)->count();

        $offset = ($page-1)*$pagesize;
        $prefix = DB::getConfig('prefix');
        $list = Article::join("article_agree as aa", 'article.id', "=", 'aa.article_id')
            ->where($where)
            ->offset($offset)
            ->limit($pagesize)
            ->orderBy("article.sort")->orderBy("article.id", "desc")
            ->selectRaw("({$prefix}article.read + {$prefix}article.virtual_read) as read_count,({$prefix}article.agree + {$prefix}article.virtual_agree) as agree_count,({$prefix}article.favorite + {$prefix}article.virtual_favorite) as favorite_count,{$prefix}article.*")
            ->get();
        foreach ($list as &$item){
            $item["show_pic"] = $item->show_pic;
            $item['catArr'] = ArticleCats::join('article_cat as ac','article_cats.cat_id','=','ac.id')
                ->where("article_cats.article_id", $item->id)->pluck("name");
            $item['attrArr'] = ArticleAttrs::join('article_attr as aa','article_attrs.attr_id','=','aa.id')
                ->where("article_attrs.article_id", $item->id)->pluck("name");
            $item['tagArr'] = ArticleTags::join('article_tag as at','article_tags.tag_id','=','at.id')
                ->where("article_tags.article_id", $item->id)->pluck("name");
        }
        return $this->success(compact('count','list'));
    }

    /**
     * 新增 || 取消收藏
     * @param ArticleFavoriteRequest $request
     * @return mixed
     */
    public function favorite(ArticleFavoriteRequest $request)
    {
        $request->check();

        $article_id = $request->input("article_id");
        $info = $info = Article::where([
            ["id", "=", $article_id],
        ])->first();
        if(!$info) return $this->failed("文章不存在", config('articlecommon.error'));

        $type = $request->input("type");
        if(!in_array($type, [0,1])) return $this->failed("操作类型不存在", config('articlecommon.error'));

        $uid = Auth::guard('api')->id();
        if($uid <= 0) return $this->failed("请先登录", config('articlecommon.error'));

        if($type == 1){
            $agreeInfo = ArticleFavorite::where([
                ["uid", "=", $uid],
                ["article_id", "=", $article_id],
            ])->first();
            if(!$agreeInfo){
                $ArticleAgreeModel = new ArticleFavorite();
                $ArticleAgreeModel->uid = $uid;
                $ArticleAgreeModel->article_id = $article_id;
                if(!$ArticleAgreeModel->save()) return $this->failed("收藏失败", config('articlecommon.error'));
            }
            $msg = "收藏成功";
        }else{
            ArticleFavorite::where([
                ["uid", "=", $uid],
                ["article_id", "=", $article_id],
            ])->delete();
            $msg = "取消收藏成功";
        }

        $info["favorite"] = ArticleFavorite::where("article_id", $article_id)->count();
        $info->save();
        return $this->success([], $msg);
    }

    /**
     * 获取个人收藏文章分页列表
     * @param ArticleFavoritePageListRequest $request
     * @return mixed
     */
    public function getFavoritePageList(ArticleFavoritePageListRequest $request)
    {
        $request->check();
        $uid = Auth::guard('api')->id();
        if($uid <= 0) return $this->failed("请先登录", config('articlecommon.error'));

        $page = $request->input("page") ?? 1;
        $pagesize = $request->input("limit") ?? 10;

        $where = [
            ["article.status", "=", "1"],
            ["af.uid", "=", $uid],
        ];

        //获取总条数
        $count = Article::join("article_favorite as af", 'article.id', "=", 'af.article_id')->where($where)->count();

        $offset = ($page-1)*$pagesize;
        $prefix = DB::getConfig('prefix');
        $list = Article::join("article_favorite as af", 'article.id', "=", 'af.article_id')
            ->where($where)
            ->offset($offset)
            ->limit($pagesize)
            ->orderBy("article.sort")->orderBy("article.id", "desc")
            ->selectRaw("({$prefix}article.read + {$prefix}article.virtual_read) as read_count,({$prefix}article.agree + {$prefix}article.virtual_agree) as agree_count,({$prefix}article.favorite + {$prefix}article.virtual_favorite) as favorite_count,{$prefix}article.*")
            ->get();
        foreach ($list as &$item){
            $item["show_pic"] = $item->show_pic;
            $item['catArr'] = ArticleCats::join('article_cat as ac','article_cats.cat_id','=','ac.id')
                ->where("article_cats.article_id", $item->id)->pluck("name");
            $item['attrArr'] = ArticleAttrs::join('article_attr as aa','article_attrs.attr_id','=','aa.id')
                ->where("article_attrs.article_id", $item->id)->pluck("name");
            $item['tagArr'] = ArticleTags::join('article_tag as at','article_tags.tag_id','=','at.id')
                ->where("article_tags.article_id", $item->id)->pluck("name");
        }
        return $this->success(compact('count','list'));
    }

    /**
     * 根据阅读量获取排行榜信息列表
     * @param ArticleRankingRequest $request
     * @return mixed
     */
    public function ranking(ArticleRankingRequest $request)
    {
        $request->check();
        $limit = $request->input("limit") ?? 10;
        $type = $request->input("type");

        $time = time();
        switch ($type){
            case "1":  // 当天
                $start = date("Y-m-d", $time) . " 00:00:00";
                $end = date("Y-m-d", $time) . " 23:59:59";
                break;
            case "2":  // 当周
                $gdate = date("Y-m-d", $time);
                $w = date("w", $time);//取得一周的第几天,星期天开始 0  1  2  3  4  5  6
                $dn = $w ? $w - 1 : 6; // 要减去的天数

                $start = date("Y-m-d", strtotime("$gdate -".$dn." days")) . " 00:00:00";
                $end = date("Y-m-d", strtotime("$start +6 days")) . " 23:59:59";
                break;
            case "3":  // 当月
                $year = date("Y", $time);
                $month = date("m", $time);
                $day = date("d", $time);
                $t = date('t', $time); // 本月一共有几天
                $start = date("Y-m-d H:i:s", mktime(0, 0, 0, $month, 1, $year));     // 创建本月开始时间
                $end = date("Y-m-d H:i:s", mktime(23, 59, 59, $month, $t, $year));  // 创建本月结束时间
                break;
        }


        if(isset($start) && $start != "" && isset($end) && $end != ""){
            $list = Article::leftJoin('article_read as ar','article.id','=','ar.article_id')
                ->where([
                    ["article.status", "=", '1'],
                ])
                ->whereBetween("ar.created_at", [$start, $end])
                ->limit($limit)
                ->groupBy("article.id")
                ->orderBy("read_count", "desc")->orderBy("article.id", "desc")
                ->select("article.*",DB::raw("count(*) as read_count"))
                ->get();
        }else{
            $list = Article::where([
                        ["status", "=", '1'],
                    ])
                    ->limit($limit)
                    ->orderBy("read", "desc")->orderBy("id", "desc")
                    ->select("*","read as read_count")
                    ->get();
        }

        foreach ($list as &$item){
            $item["show_pic"] = $item->show_pic;
            $item['catArr'] = ArticleCats::join('article_cat as ac','article_cats.cat_id','=','ac.id')
                ->where("article_cats.article_id", $item->id)->pluck("name");
            $item['attrArr'] = ArticleAttrs::join('article_attr as aa','article_attrs.attr_id','=','aa.id')
                ->where("article_attrs.article_id", $item->id)->pluck("name");
            $item['tagArr'] = ArticleTags::join('article_tag as at','article_tags.tag_id','=','at.id')
                ->where("article_tags.article_id", $item->id)->pluck("name");
        }
        return $this->success(compact('list'));


//                $>where('repair_user',$engineer['member_id'])
//                    ->where('order_status',1)
//                    ->whereBetween('create_time',[strtotime($start),strtotime($end)])
//                    ->where('status',0)
//                    ->orderBy('create_time', 'desc')
//                    ->paginate($this->number);

        return $this->success(compact('start','end', 'w','dn','type'));

    }
}
