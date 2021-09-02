@extends('admin.public.header')
@section('title','文章标签列表')

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
            <button class="layui-btn layui-btn-sm layui-bg-red" lay-event="batch_delete"><i class="layui-icon layui-icon-delete"></i>批量删除</button>
            <button class="layui-btn layui-btn-normal layui-btn-sm data-add-btn" lay-event="add"><i class="layui-icon layui-icon-add-circle"></i>新增</button>
        </div>
    </script>
    <!-- 操作按钮 -->
    <script type="text/html" id="barOperate">
        <a class="layui-btn layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</a>
        <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
    </script>
@endsection

@section('listscript')
    <script type="text/javascript">
        layui.use(['form','table','laydate'], function(){
            var table = layui.table, $=layui.jquery, form = layui.form, laydate = layui.laydate;
            // 渲染表格
            table.render({
                elem: '#tableList',
                url:'/admin/article_tag/ajaxList',
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
                    // { //自定义头部工具栏右侧图标。如无需自定义，去除该参数即可
                    //     title: '搜索',
                    //     layEvent: 'TABLE_SEARCH',
                    //     icon: 'layui-icon-search'
                    // }
                ],
                title: '文章标签列表',
                cols: [[
                    {type: 'checkbox', align: 'center'},
                    {field:'id', title:'ID', width:80, align: 'center', unresize: true, sort: true},
                    {field:'name', title:'标签名称',  align: 'center'},
                    {title:'操作', toolbar: '#barOperate', align: 'center'}
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
                            title: '新增标签',
                            type: 2,
                            shade: 0.2,
                            maxmin:true,
                            skin:'layui-layer-lan',
                            shadeClose: true,
                            area: ['80%', '80%'],
                            content: '/admin/article_tag/edit',
                            end:function() {
                                table.reload('listReload');
                            }
                        });
                        break;
                    case "batch_delete": // 批量删除
                        if(!ids.length){
                            return layer.msg('请勾选要删除的数据',{icon: 2});
                        }
                        layer.confirm('确定删除选中的数据吗？', {
                            title : "操作确认",
                            skin: 'layui-layer-lan'
                        },function(index){
                            $.ajax({
                                url:'/admin/article_tag/del',
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
                            content: '/admin/article_tag/edit?id='+id,
                            end:function(res) {
                                table.reload('listReload');
                            }
                        });
                        break;
                    case "del":  // 删除功能
                        layer.confirm('确定删除 ' + data.name + ' 标签吗？', {
                            title : "删除标签",
                            skin: 'layui-layer-lan'
                        },function(index){
                            $.ajax({
                                url:'/admin/article_tag/del',
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
                }
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
