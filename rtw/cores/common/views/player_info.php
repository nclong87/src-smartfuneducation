<div class="box" id="player_info">
    <span class="box_title">Quá trình học tập</span>
    <span style="display: block; position: relative; padding-left: 45px; padding-top: 6px; height: 30px;margin-bottom: 10px;" ><span class="icons icon_coin"></span><b><?php echo number_format($player_info->current_coin)?></b> xu</span>
    <span style="display: block; position: relative; padding-left: 45px; padding-top: 6px; height: 30px;margin-bottom: 10px;"><span class="icons icon_xp"></span><b><?php echo number_format($player_info->current_xp)?></b> điểm kinh nghiệm</span>

</div>
<div class="box" style="margin-top: 20px">
    <span class="box_title">Các hoạt động</span>
    <a href="/mod/rtw/ajax.php?id=<?php echo $course_module->id ?>&c=common&a=properties" class="colorbox" style="display: block; position: relative; padding-left: 45px; padding-top: 6px; height: 30px; margin-bottom: 10px;" ><span class="icons icon_properties"></span>Tài sản cá nhân</a>
    <a href="/mod/rtw/view.php?id=<?php echo $course_module->id ?>&c=lottery" style="display: block; position: relative; padding-left: 45px; padding-top: 6px; height: 30px; margin-bottom: 10px;" ><span class="icons icon_group_activity"></span>Quay số trúng thưởng</a>
    <a href="/mod/rtw/ajax.php?id=<?php echo $course_module->id ?>&c=common&a=group_activity" class="colorbox" style="display: block; position: relative; padding-left: 45px; padding-top: 6px; height: 30px; margin-bottom: 10px;" ><span class="icons icon_group_activity"></span>Họat động nhóm</a>
    <a href="/mod/rtw/ajax.php?id=<?php echo $course_module->id ?>&c=common&a=trend" class="colorbox" style="display: block; position: relative; padding-left: 45px; padding-top: 6px; height: 30px; margin-bottom: 10px;" ><span class="icons icon_trend"></span>Quá trình học tập</a>
</div>

<div class="box" style="margin-top: 20px">
    <span class="box_title">Học viên tích cực</span>
    <div id="top_player_activity">
        Loading...
    </div>
</div>
<script>
$(document).ready(function(){
    $.ajax({
        type: 'GET',
        cache: false,
        async: false,
        url: "/mod/rtw/ajax.php?id=<?php echo $course_module->id ?>&c=common&a=top_player_activity",
        success: function(response) {
            $("#top_player_activity").html(response);
        }
    });
});
</script>