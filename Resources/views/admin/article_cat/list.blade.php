@extends('admin.public.header')
@section('title','文章分类列表')

@section('listsearch')
    <fieldset class="table-search-fieldset" style="display:none">
        <legend>搜索信息</legend>
        <div style="margin: 10px 10px 10px 10px">
            <form class="layui-form layui-form-pane form-search" action="" id="searchFrom">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <button type="submit" class="layui-btn layui-btn-sm layui-btn-normal"  lay-submit lay-filter="data-search-btn"><i class="layui-icon"></i> 搜 索</button>
                    </div>
                </div>
            </form>
        </div>
    </fieldset>
@endsection

@section('listcontent')
    <table class="layui-hide" id="tableList" lay-filter="tableList"></table>
    <!-- 表头左侧按钮 -->
    <script type="text/html" id="toolbarColumn">
        <div class="layui-btn-container">
            <button class="layui-btn layui-btn-sm layuimini-btn-primary" onclick="window.location.reload();" ><i class="layui-icon layui-icon-refresh-3"></i></button>
            <button class="layui-btn layui-btn-normal layui-btn-sm data-add-btn" lay-event="add"><i class="layui-icon layui-icon-add-circle"></i>新增</button>
            <button class="layui-btn layui-btn-sm layui-btn-primary" lay-event="btn-expand"><i class="layui-icon layui-icon-down"></i>全部展开</button>
            <button class="layui-btn layui-btn-sm layui-btn-primary" lay-event="btn-fold"><i class="layui-icon layui-icon-up"></i>全部折叠</button>
        </div>
    </script>
@endsection

@section('listscript')
    <script type="text/javascript">
        layui.use(['form','table','laydate', 'treetable'], function(){
            var table = layui.table, $=layui.jquery, form = layui.form, treetable = layui.treetable, laydate = layui.laydate;
            // 渲染表格
            treetable.render({
                treeColIndex: 0,
                treeSpid: 0,
                treeIdName: 'id',
                treePidName: 'cat_pid',
                treeDefaultClose: true,
                elem: '#tableList',
                toolbar: '#toolbarColumn',
                url: '/admin/article_cat/ajaxList',
                page: false,
                id: 'listReload',
                cols: [[
                    // {type: 'checkbox', align: 'center'},
                    {field: 'name', width: 250, title: '分类名称'},
                    {field: 'sub_name', width: 200, title: '副名称'},
                    {field: 'cat_pic', title: '缩略图', align: 'center', event: 'show_cat_pic',
                        templet: function (info){
                            if(info.show_cat_pic == "") return "";
                            return '<a class="showCatPicImages" href="javascript:void(0)" data-src="' + info.show_cat_pic + '" title="点击查看"><img style="width:50px;" src="'+info.show_cat_pic+'"></a>'
                        }
                    },
                    {field:'is_recommend', title:'推荐', width:100, align: 'center',
                        templet: function(info){
                            if(info.is_recommend == 1){
                                return '<input type="checkbox" name="is_recommend" value="'+info.id+'" lay-skin="switch" lay-text="是|否" lay-filter="isRecommend" checked>'
                            }else{
                                return '<input type="checkbox" name="is_recommend" value="'+info.id+'" lay-skin="switch" lay-text="是|否" lay-filter="isRecommend">'
                            }
                        }
                    },
                    {field:'status', title:'状态', width:100, align: 'center',
                        templet: function(info){
                            if(info.status == 1){
                                return '<input type="checkbox" name="status" value="'+info.id+'" lay-skin="switch" lay-text="{{$statusTest}}" lay-filter="isOpen" checked>'
                            }else{
                                return '<input type="checkbox" name="status" value="'+info.id+'" lay-skin="switch" lay-text="{{$statusTest}}" lay-filter="isOpen">'
                            }
                        }
                    },
                    {field: 'sort', width: 80, align: 'center', title: '排序'},
                    {title:'操作',
                        templet: function (info){
                            let aa = '<a class="layui-btn layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</a>' +
                                '<a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>';
                            if(info.cat_outer_chain.length > 0){
                                aa += '<a class="layui-btn layui-btn-normal layui-btn-xs" target="_blank" href="'+info.cat_outer_chain+'"><i class="layui-icon layui-icon-link"></i>前往外链</a>';
                            }
                            return aa;
                        }
                    }
                ]],
                done: function () {}
            });

            //头工具栏事件
            table.on('toolbar(tableList)', function(obj){
                switch(obj.event){
                    case "add": // 新增
                        var index = layer.open({
                            title: '新增分类',
                            type: 2,
                            shade: 0.2,
                            maxmin:true,
                            skin:'layui-layer-lan',
                            shadeClose: true,
                            area: ['80%', '80%'],
                            content: '/admin/article_cat/edit',
                        });
                        break;
                    case "btn-expand": // 监听全部展开
                        treetable.expandAll('#tableList');
                        break;
                    case "btn-fold": // 监听全部折叠
                        treetable.foldAll('#tableList');
                        break;
                    case 'TABLE_SEARCH': // 搜索功能
                        var display = $(".table-search-fieldset").css("display"); //获取标签的display属性
                        if(display == 'none'){
                            $(".table-search-fieldset").show();
                        }else{
                            $(".table-search-fieldset").hide();
                        }
                        break;
                };
            });

            // 监听行工具事件
            table.on('tool(tableList)', function(obj){
                var data = obj.data;
                var id = data.id;
                switch (obj.event){
                    case "edit":  // 编辑功能
                        var index = layer.open({
                            title: data.name + ' - 编辑',
                            type: 2,
                            shade: 0.2,
                            maxmin:true,
                            skin:'layui-layer-lan',
                            shadeClose: true,
                            area: ['80%', '80%'],
                            content: '/admin/article_cat/edit?id='+id,
                        });
                        break;
                    case "del":  // 删除功能
                        layer.confirm('确定删除 ' + data.name + ' 分类以及它的所有子分类吗？', {
                            title : "删除分类",
                            skin: 'layui-layer-lan'
                        },function(index){
                            $.ajax({
                                url:'/admin/article_cat/del',
                                type:'post',
                                data:{'id':id},
                                dataType:"JSON",
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                success:function(data){
                                    if(data.code == 0){
                                        layer.msg(data.message,{icon: 1,time:1500},function(){
                                            window.location.reload();
                                        });
                                    }else{
                                        layer.msg(data.message,{icon: 2});
                                    }
                                },
                                error:function(e){
                                    layer.msg(data.message,{icon: 2});
                                },
                            });
                        });
                        break;
                    case "show_cat_pic":    // 分类缩略图展示
                        if(data.show_cat_pic != "") {
                            var img_infor = "<img src='" + data.show_cat_pic + "' />";
                            layer.open({
                                title: false,
                                type: 1,
                                closeBtn: 0,
                                area: ['auto'],
                                skin: 'layui-layer-nobg', //没有背景色
                                shadeClose: true,
                                content: img_infor,
                            });
                        }
                        break;
                }
            });

            //监听状态操作
            form.on('switch(isOpen)', function(obj){
                var checked = obj.elem.checked;
                var id = obj.value;
                $.ajax({
                    url:'/admin/article_cat/saveStatus',
                    type:'post',
                    data:{'status':checked,'id':id},
                    dataType:"JSON",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success:function(data){
                        if(data.code == 0){
                            layer.msg(data.message,{icon: 1,time:1500});
                        }else{
                            layer.msg(data.message,{icon: 2,time:1000},function(){
                                window.location.reload();
                            });
                        }
                    },
                    error: function (){
                        layer.msg("请求失败",{icon: 2,time:1000},function(){
                            window.location.reload();
                        });
                    }

                });
            });

            //监听推荐操作
            form.on('switch(isRecommend)', function(obj){
                var checked = obj.elem.checked;
                var id = obj.value;
                $.ajax({
                    url:'/admin/article_cat/saveRecommend',
                    type:'post',
                    data:{'is_recommend':checked,'id':id},
                    dataType:"JSON",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success:function(data){
                        if(data.code == 0){
                            layer.msg(data.message,{icon: 1,time:1500});
                        }else{
                            layer.msg(data.message,{icon: 2},function(){
                                window.location.reload();
                            });
                        }
                    },
                    error: function (){
                        layer.msg("请求失败",{icon: 2},function(){
                            window.location.reload();
                        });
                    }
                });
            });

            // 监听搜索操作
            form.on('submit(data-search-btn)', function (data) {
                //执行搜索重载
                table.reload('listReload', {
                    where: data.field,
                    page: {
                        curr: 1
                    }
                });
                return false;
            });
        });
    </script>
@endsection
