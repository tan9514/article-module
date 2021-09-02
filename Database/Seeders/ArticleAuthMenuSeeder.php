<?php
namespace Modules\Article\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * @author liming
 * @date 2021-07-02 10:50
 */
class ArticleAuthMenuSeeder extends Seeder
{
    public function run()
    {
        if (Schema::hasTable('auth_menu')){
            $arr = $this->defaultInfo();
            if(!empty($arr) && is_array($arr)) {
                // 删除原来已存在的菜单
                $module = config('articleconfig.module') ?? "";
                if($module != ""){
                    DB::table('auth_menu')->where("module", $module)->delete();
                }

                $this->addInfo($arr);
            }
        }
    }

    /**
     * 遍历新增菜单
     * @param array $data
     * @param int $pid
     */
    private function addInfo(array $data, $pid = 0)
    {
        foreach ($data as $item) {
            $newPid = DB::table('auth_menu')->insertGetId([
                'pid' => $item['pid'] ?? $pid,
                'href' => $item['href'],
                'title' => $item['title'],
                'icon' => $item['icon'],
                'type' => $item['type'],
                'status' => $item['status'],
                'sort' => $item['sort'] ?? 0,
                'remark' => $item['remark'],
                'target' => $item['target'],
                'createtime' => $item['createtime'],
                'module' => $item["module"],
                'menus' => $item["menus"],
            ]);
            if($newPid <= 0) break;
            if(isset($item["contents"]) && is_array($item["contents"]) && !empty($item["contents"])) $this->addInfo($item["contents"], $newPid);
        }
    }

    /**
     * 设置后台管理菜单路由信息
     * @pid 父级
     * @href 路由
     * @title 菜单标题
     * @icon 图标
     * @type int 类型 0 顶级目录 1 目录 2 菜单 3 按钮
     * @status 状态 1 正常 2 停用
     * @remark 备注
     * @target 跳转方式
     * @createtime 创建时间
     */
    private function defaultInfo()
    {
        $module = config('articleconfig.module') ?? "";
        $time = time();
        return [
            [
                "pid" => 10005,
                "href" => "",
                "title" => "文章管理",
                "icon" => 'fa fa-envelope',
                "type" => 1,
                "status" => 1,
                "sort" => 100,
                "remark" => "文章管理",
                "target" => "_self",
                "createtime" => $time,
                'module' => $module,
                "menus" => $module == "" ? $module : $module . "-1",
                "contents" => [
                    [   //  属性
                        "href" => "/admin/article_attr/list",
                        "title" => "文章属性",
                        "icon" => 'fa fa-file-text-o',
                        "type" => 2,
                        "status" => 1,
                        "remark" => "文章属性",
                        "target" => "_self",
                        "createtime" => $time,
                        'module' => $module,
                        "menus" => $module == "" ? $module : $module . "-2",
                        "contents" => [
                            [
                                "href" => "/admin/article_attr/list",
                                "title" => "查看文章属性",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "查看文章属性",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-3",
                            ],
                            [
                                "href" => "/admin/article_attr/ajaxList",
                                "title" => "异步获取文章属性信息",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "异步获取文章属性信息",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-4",
                            ],
                            [
                                "href" => "/admin/article_attr/del",
                                "title" => "删除文章属性",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "删除文章属性",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-5",
                            ],
                            [
                                "href" => "/admin/article_attr/edit",
                                "title" => "新增|编辑文章属性",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "新增|编辑文章属性",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-6",
                            ],
                        ],
                    ],
                    [   //  标签
                        "href" => "/admin/article_tag/list",
                        "title" => "文章标签",
                        "icon" => 'fa fa-file-text-o',
                        "type" => 2,
                        "status" => 1,
                        "remark" => "文章标签",
                        "target" => "_self",
                        "createtime" => $time,
                        'module' => $module,
                        "menus" => $module == "" ? $module : $module . "-7",
                        "contents" => [
                            [
                                "href" => "/admin/article_tag/list",
                                "title" => "查看文章标签",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "查看文章标签",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-8",
                            ],
                            [
                                "href" => "/admin/article_tag/ajaxList",
                                "title" => "异步获取文章标签信息",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "异步获取文章标签信息",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-9",
                            ],
                            [
                                "href" => "/admin/article_tag/del",
                                "title" => "删除文章标签",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "删除文章标签",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-10",
                            ],
                            [
                                "href" => "/admin/article_tag/edit",
                                "title" => "新增|编辑文章标签",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "新增|编辑文章标签",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-11",
                            ],
                        ],
                    ],
                    [   //  文章分类
                        "href" => "/admin/article_cat/list",
                        "title" => "文章分类",
                        "icon" => 'fa fa-file-text-o',
                        "type" => 2,
                        "status" => 1,
                        "remark" => "文章分类",
                        "target" => "_self",
                        "createtime" => $time,
                        'module' => $module,
                        "menus" => $module == "" ? $module : $module . "-12",
                        "contents" => [
                            [
                                "href" => "/admin/article_cat/list",
                                "title" => "查看文章分类",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "查看文章分类",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-13",
                            ],
                            [
                                "href" => "/admin/article_cat/ajaxList",
                                "title" => "异步获取文章分类信息",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "异步获取文章分类信息",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-14",
                            ],
                            [
                                "href" => "/admin/article_cat/del",
                                "title" => "删除文章分类",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "删除文章分类",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-15",
                            ],
                            [
                                "href" => "/admin/article_cat/edit",
                                "title" => "新增|编辑文章分类",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "新增|编辑文章分类",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-16",
                            ],
                            [
                                "href" => "/admin/article_cat/saveStatus",
                                "title" => "开启|关闭文章分类",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "开启|关闭文章分类",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-17",
                            ],
                            [
                                "href" => "/admin/article_cat/saveRecommend",
                                "title" => "开启|关闭文章分类推荐",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "开启|关闭文章分类推荐",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-18",
                            ],
                        ],
                    ],
                    [   // 文章
                        "href" => "",
                        "title" => "文章",
                        "icon" => 'fa fa-envelope',
                        "type" => 1,
                        "status" => 1,
                        "remark" => "文章",
                        "target" => "_self",
                        "createtime" => $time,
                        'module' => $module,
                        "menus" => $module == "" ? $module : $module . "-19",
                        "contents" => [
                            [   //  回收站
                                "href" => "/admin/article_recycle_bin/list",
                                "title" => "回收站",
                                "icon" => 'fa fa-file-text-o',
                                "type" => 2,
                                "status" => 1,
                                "remark" => "回收站",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-20",
                                "contents" => [
                                    [
                                        "href" => "/admin/article_recycle_bin/list",
                                        "title" => "查看回收站列表",
                                        "icon" => 'fa fa-window-maximize',
                                        "type" => 3,
                                        "status" => 1,
                                        "remark" => "查看回收站列表",
                                        "target" => "_self",
                                        "createtime" => $time,
                                        'module' => $module,
                                        "menus" => $module == "" ? $module : $module . "-21",
                                    ],
                                    [
                                        "href" => "/admin/article_recycle_bin/ajaxList",
                                        "title" => "异步回收站列表信息",
                                        "icon" => 'fa fa-window-maximize',
                                        "type" => 3,
                                        "status" => 1,
                                        "remark" => "异步回收站列表信息",
                                        "target" => "_self",
                                        "createtime" => $time,
                                        'module' => $module,
                                        "menus" => $module == "" ? $module : $module . "-22",
                                    ],
                                    [
                                        "href" => "/admin/article_recycle_bin/del",
                                        "title" => "删除文章",
                                        "icon" => 'fa fa-window-maximize',
                                        "type" => 3,
                                        "status" => 1,
                                        "remark" => "删除文章",
                                        "target" => "_self",
                                        "createtime" => $time,
                                        'module' => $module,
                                        "menus" => $module == "" ? $module : $module . "-23",
                                    ],
                                    [
                                        "href" => "/admin/article_recycle_bin/details",
                                        "title" => "文章详情",
                                        "icon" => 'fa fa-window-maximize',
                                        "type" => 3,
                                        "status" => 1,
                                        "remark" => "文章详情",
                                        "target" => "_self",
                                        "createtime" => $time,
                                        'module' => $module,
                                        "menus" => $module == "" ? $module : $module . "-24",
                                    ],
                                    [
                                        "href" => "/admin/article_recycle_bin/recovery",
                                        "title" => "移出回收站",
                                        "icon" => 'fa fa-window-maximize',
                                        "type" => 3,
                                        "status" => 1,
                                        "remark" => "移出回收站",
                                        "target" => "_self",
                                        "createtime" => $time,
                                        'module' => $module,
                                        "menus" => $module == "" ? $module : $module . "-25",
                                    ],
                                ],
                            ],
                            [   //  文章
                                "href" => "/admin/article/list",
                                "title" => "文章",
                                "icon" => 'fa fa-file-text-o',
                                "type" => 2,
                                "status" => 1,
                                "remark" => "文章列表",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-26",
                                "contents" => [
                                    [
                                        "href" => "/admin/article/list",
                                        "title" => "查看文章列表",
                                        "icon" => 'fa fa-window-maximize',
                                        "type" => 3,
                                        "status" => 1,
                                        "remark" => "查看文章列表",
                                        "target" => "_self",
                                        "createtime" => $time,
                                        'module' => $module,
                                        "menus" => $module == "" ? $module : $module . "-27",
                                    ],
                                    [
                                        "href" => "/admin/article/ajaxList",
                                        "title" => "异步获取文章列表信息",
                                        "icon" => 'fa fa-window-maximize',
                                        "type" => 3,
                                        "status" => 1,
                                        "remark" => "异步获取文章列表信息",
                                        "target" => "_self",
                                        "createtime" => $time,
                                        'module' => $module,
                                        "menus" => $module == "" ? $module : $module . "-28",
                                    ],
                                    [
                                        "href" => "/admin/article/del",
                                        "title" => "移入回收站",
                                        "icon" => 'fa fa-window-maximize',
                                        "type" => 3,
                                        "status" => 1,
                                        "remark" => "移入回收站",
                                        "target" => "_self",
                                        "createtime" => $time,
                                        'module' => $module,
                                        "menus" => $module == "" ? $module : $module . "-29",
                                    ],
                                    [
                                        "href" => "/admin/article/edit",
                                        "title" => "新增|编辑文章",
                                        "icon" => 'fa fa-window-maximize',
                                        "type" => 3,
                                        "status" => 1,
                                        "remark" => "新增|编辑文章",
                                        "target" => "_self",
                                        "createtime" => $time,
                                        'module' => $module,
                                        "menus" => $module == "" ? $module : $module . "-30",
                                    ],
                                    [
                                        "href" => "/admin/article/batchEdit",
                                        "title" => "批量修改文章",
                                        "icon" => 'fa fa-window-maximize',
                                        "type" => 3,
                                        "status" => 1,
                                        "remark" => "批量修改文章",
                                        "target" => "_self",
                                        "createtime" => $time,
                                        'module' => $module,
                                        "menus" => $module == "" ? $module : $module . "-31",
                                    ],
                                    [
                                        "href" => "/admin/article/saveStatus",
                                        "title" => "开启|关闭文章",
                                        "icon" => 'fa fa-window-maximize',
                                        "type" => 3,
                                        "status" => 1,
                                        "remark" => "开启|关闭文章",
                                        "target" => "_self",
                                        "createtime" => $time,
                                        'module' => $module,
                                        "menus" => $module == "" ? $module : $module . "-32",
                                    ],
                                    [
                                        "href" => "/admin/article/saveField",
                                        "title" => "文章单字段修改",
                                        "icon" => 'fa fa-window-maximize',
                                        "type" => 3,
                                        "status" => 1,
                                        "remark" => "文章单字段修改",
                                        "target" => "_self",
                                        "createtime" => $time,
                                        'module' => $module,
                                        "menus" => $module == "" ? $module : $module . "-33",
                                    ],
                                    [
                                        "href" => "/admin/article/xmSelect",
                                        "title" => "获取附属属性字段列表",
                                        "icon" => 'fa fa-window-maximize',
                                        "type" => 3,
                                        "status" => 1,
                                        "remark" => "获取附属属性字段列表",
                                        "target" => "_self",
                                        "createtime" => $time,
                                        'module' => $module,
                                        "menus" => $module == "" ? $module : $module . "-34",
                                    ],
                                ],
                            ],
                        ]
                    ],
                ]
            ],
        ];
    }
}