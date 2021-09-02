@extends('admin.public.header')
@section('title','文章列表')

@section('listsearch')
    <fieldset class="table-search-fieldset" style="display:none">
        <legend>搜索信息</legend>
        <div style="margin: 10px 10px 10px 10px">
            <form class="layui-form layui-form-pane form-search" action="" id="searchFrom">
                <div class="layui-form-item">
                    <div class="layui-inline">
                        <label class="layui-form-label">分类</label>
                        <div class="layui-input-inline">
                            <select name="cat_id" lay-search>
                                <option value="">全部</option>
                                <?php
                                function setOptionList($optionList, $num, $infoCatPid, $infoId){
                                    $optionTest = "|";
                                    for ($nu = 0; $nu < $num; $nu++){
                                        $optionTest .= "—";
                                    }
                                    foreach ($optionList as $optionItem){
                                        if($infoId > 0 && ($optionItem["cat_pid"] == $infoId || $optionItem["id"] == $infoId)) continue;
                                        $selected = "";
                                        if($optionItem["id"] == $infoCatPid) $selected = "selected";
                                        echo "<option value='".$optionItem["id"]."' ".$selected." >".$optionTest.$optionItem["name"]."</option>";
                                        if(isset($optionItem["list"]) && !empty($optionItem["list"])){
                                            setOptionList($optionItem["list"], ++$num, $infoCatPid, $infoId);
                                        }
                                    }
                                }
                                setOptionList($catArr, 1, isset($info->cat_pid) ? $info->cat_pid : 0, isset($info->id) ? $info->id : 0);
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-label">属性</label>
                        <div class="layui-input-inline">
                            <select name="attr_id" lay-search>
                                <option value="">全部</option>
                                @foreach($attrArr as $attrInfo)
                                <option value="{{$attrInfo->id}}">{{$attrInfo->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-label">标签</label>
                        <div class="layui-input-inline">
                            <select name="tag_id" lay-search>
                                <option value="">全部</option>
                                @foreach($tagArr as $tagInfo)
                                    <option value="{{$tagInfo->id}}">{{$tagInfo->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-label">状态</label>
                        <div class="layui-input-inline">
                            <select name="status" lay-search>
                                <option value="">全部</option>
                                @foreach($statusArr as $k => $statusInfo)
                                    <option value="{{$k}}">{{$statusInfo}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <br>
                    <div class="layui-inline">
                        <label class="layui-form-label">标题</label>
                        <div class="layui-input-inline">
                            <input type="text" name="title" placeholder="主标题模糊查询" class="layui-input" />
                        </div>
                    </div>

                    <div class="layui-inline">
                        <label class="layui-form-label">來源</label>
                        <div class="layui-input-inline">
                            <input type="text" name="source" placeholder="來源模糊查询" class="layui-input" />
                        </div>
                    </div>

                    <br>
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
            <button class="layui-btn layui-btn-sm layui-bg-red" lay-event="batch_delete"><i class="layui-icon layui-icon-delete"></i>批量删除</button>
            <button class="layui-btn layui-btn-sm" lay-event="batch_edit_cat"><i class="layui-icon layui-icon-edit"></i>批量修改</button>
            <button class="layui-btn layui-btn-normal layui-btn-sm data-add-btn" lay-event="add"><i class="layui-icon layui-icon-add-circle"></i>新增</button>
        </div>
    </script>
@endsection

@section('listscript')
    <script type="text/javascript">
        layui.use(['form','table','laydate'], function(){
            var table = layui.table, $=layui.jquery, form = layui.form, laydate = layui.laydate;
            // 渲染表格
            table.render({
                elem: '#tableList',
                url:'/admin/article/ajaxList',
                parseData: function(res) { //res 即为原始返回的数据
                    return {
                        "code": res.code, //解析接口状态
                        "msg": res.message, //解析提示文本
                        "count": res.data.count, //解析数据长度
                        "data": res.data.list //解析数据列表
                    }
                },
                cellMinWidth: 80,//全局定义常规单元格的最小宽度
                toolbar: '#toolbarColumn',//开启头部工具栏，并为其绑定左侧模板
                defaultToolbar: [
                    'filter',
                    'exports',
                    'print',
                    { //自定义头部工具栏右侧图标。如无需自定义，去除该参数即可
                        title: '搜索',
                        layEvent: 'TABLE_SEARCH',
                        icon: 'layui-icon-search'
                    }
                ],
                title: '文章列表',
                cols: [[
                    {type: 'checkbox', align: 'center'},
                    {field:'id', title:'ID', width:80, align: 'center', unresize: true, sort: true},
                    {field: 'pic', title: '缩略图', align: 'center', width:80, event: 'show_pic',
                        templet: function (info){
                            if(info.show_pic == "") return "";
                            return '<a class="showPicImages" href="javascript:void(0)" data-src="' + info.show_pic + '" title="点击查看"><img style="width:50px;" src="'+info.show_pic+'"></a>'
                        }
                    },
                    {field:'title', title:'标题', width:250,
                        templet: function (info){
                            return '<p>主标题：' + info.title + '</p>' +
                                '<p>副标题：' + info.sub_title + '</p';
                        }
                    },
                    {field:'describe', title:'摘要', align: 'center', hide: true,},
                    {field:'catArr', title:'分类',
                        templet: function (info){
                            let spanTest = "";
                            for (let i = 0; i < info.catArr.length; i++){
                                spanTest += "<span style='background-color: rgb(165, 103, 63); color: #fff; padding: height: 26px; line-height: 26px; display: inline-block; position: relative; padding: 0px 5px; margin: 2px 5px 2px 0; border-radius: 3px; align-items: baseline;'>"+info.catArr[i]+"</span>";
                            }
                            return spanTest;
                        }
                    },
                    {field:'attrArr', title:'属性',
                        templet: function (info){
                            let spanTest = "";
                            for (let i = 0; i < info.attrArr.length; i++){
                                spanTest += "<span style='background-color: rgb(165, 103, 63); color: #fff; padding: height: 26px; line-height: 26px; display: inline-block; position: relative; padding: 0px 5px; margin: 2px 5px 2px 0; border-radius: 3px; align-items: baseline;'>"+info.attrArr[i]+"</span>";
                            }
                            return spanTest;
                        }
                    },
                    {field:'tagArr', title:'标签',
                        templet: function (info){
                            let spanTest = "";
                            for (let i = 0; i < info.tagArr.length; i++){
                                spanTest += "<span style='background-color: rgb(165, 103, 63); color: #fff; padding: height: 26px; line-height: 26px; display: inline-block; position: relative; padding: 0px 5px; margin: 2px 5px 2px 0; border-radius: 3px; align-items: baseline;'>"+info.tagArr[i]+"</span>";
                            }
                            return spanTest;
                        }
                    },
                    {field:'source', title:'来源', width: 200, align: 'center'},
                    {field:'author', title:'作者', width: 100, align: 'center', hide: true,},
                    {field: 'read', title: '阅读量', width:120,
                        templet: function (info){
                            return '<p>实际：' + info.read + '</p>' +
                                '<p>虚拟：' + info.virtual_read + '</p';
                        }
                    },
                    {field: 'agree', title: '点赞量', width:120, hide: true,
                        templet: function (info){
                            return '<p>实际：' + info.agree + '</p>' +
                                '<p>虚拟：' + info.virtual_agree + '</p';
                        }
                    },
                    {field: 'favorite', title: '收藏量', width:120, hide: true,
                        templet: function (info){
                            return '<p>实际：' + info.favorite + '</p>' +
                                '<p>虚拟：' + info.virtual_favorite + '</p';
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
                    {field:'sort', title:'排序', width:80, align: 'center', sort: true, edit: true},
                    {field:'created_at', title:'创建时间', width:180, align: 'center', hide: true,},
                    {title:'操作', width: 120, align: 'center',
                        templet: function (info){
                            let aa = '<a class="layui-btn layui-btn-xs" style="display: flex" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</a>' +
                                '<a class="layui-btn layui-btn-danger layui-btn-xs" style="display: flex; margin: 5px 0" lay-event="del"><i class="layui-icon layui-icon-delete"></i>移入回收站</a>';
                            if(info.outer_chain.length > 0){
                                aa += '<a class="layui-btn layui-btn-normal layui-btn-xs" style="display: flex; margin: 5px 0" target="_blank" href="'+info.outer_chain+'"><i class="layui-icon layui-icon-link"></i>前往外链</a>';
                            }
                            return aa;
                        }
                    }
                ]],
                id: 'listReload',
                limits: [10, 20, 30, 50, 100,200],
                limit: 10,
                page: true,
                text: {
                    none: '抱歉！暂无数据~' //默认：无数据。注：该属性为 layui 2.2.5 开始新增
                }
            });

            //头工具栏事件
            table.on('toolbar(tableList)', function(obj){
                var checkStatus = table.checkStatus(obj.config.id);
                var ids = [];
                var data = checkStatus.data;
                for (var i=0;i<data.length;i++){
                    ids.push(data[i].id);
                }
                switch(obj.event){
                    case "add": // 新增
                        var index = layer.open({
                            title: '新增文章',
                            type: 2,
                            shade: 0.2,
                            maxmin:true,
                            skin:'layui-layer-lan',
                            shadeClose: true,
                            area: ['80%', '80%'],
                            content: '/admin/article/edit',
                        });
                        break;
                    case "batch_delete": // 批量删除
                        if(!ids.length){
                            return layer.msg('请勾选要移入回收站的数据',{icon: 2});
                        }
                        layer.confirm('确定把选中的数据移入回收站吗？', {
                            title : "操作确认",
                            skin: 'layui-layer-lan'
                        },function(index){
                            $.ajax({
                                url:'/admin/article/del',
                                type:'post',
                                data:{'id':ids},
                                dataType:"JSON",
                                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                success:function(data){
                                    if(data.code == 0){
                                        layer.msg(data.message,{icon: 1,time:1500},function(){
                                            table.reload('listReload');
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
                    case 'batch_edit_cat': // 批量修改
                        if(!ids.length){
                            return layer.msg('请勾选要批量修改的数据',{icon: 2});
                        }
                        layer.open({
                            title: '批量修改数据',
                            type: 2,
                            shade: 0.2,
                            maxmin:true,
                            skin: 'layui-layer-lan',
                            shadeClose: true,
                            area: ['80%', '80%'],
                            content: '/admin/article/batchEdit?ids='+ids,
                        });
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
                            title: data.title + ' - 编辑',
                            type: 2,
                            shade: 0.2,
                            maxmin:true,
                            skin:'layui-layer-lan',
                            shadeClose: true,
                            area: ['80%', '80%'],
                            content: '/admin/article/edit?id='+id,
                        });
                        break;
                    case "del":  // 删除功能
                        layer.confirm('确定把 ' + data.title + ' 文章移入回收站吗？', {
                            title : "文章移入回收站",
                            skin: 'layui-layer-lan'
                        },function(index){
                            $.ajax({
                                url:'/admin/article/del',
                                type:'post',
                                data:{'id':id},
                                dataType:"JSON",
                                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                                success:function(data){
                                    if(data.code == 0){
                                        layer.msg(data.message,{icon: 1,time:1500},function(){
                                            table.reload('listReload');
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
                    case "show_pic":    // 缩略图展示
                        if(data.show_pic != "") {
                            var img_infor = "<img src='" + data.show_pic + "' />";
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

            // 表格修改事件
            table.on('edit(tableList)', function (obj) {
                console.log(obj);
                var id = obj.data.id;
                var field = obj.field;
                var value = obj.value;
                if(value == ""){
                    layer.msg("修改的值不能未空",{icon: 2, time: 1000},function(){
                        window.location.reload();
                    });
                }else{
                    $.ajax({
                        url:'/admin/article/saveField',
                        type:'post',
                        data:{
                            id: id,
                            field: field,
                            value: value,
                        },
                        dataType:"JSON",
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                        success:function(data){
                            if(data.code == 0){
                                layer.msg(data.message,{icon: 1,time:1000},function(){
                                    window.location.reload();
                                });
                            }else{
                                layer.msg(data.message,{icon: 2,time:1000},function(){
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
                }
            });

            //监听状态操作
            form.on('switch(isOpen)', function(obj){
                var checked = obj.elem.checked;
                var id = obj.value;
                $.ajax({
                    url:'/admin/article/saveStatus',
                    type:'post',
                    data:{'status':checked,'id':id},
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
