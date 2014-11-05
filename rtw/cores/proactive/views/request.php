<div id="rtw_content" style="text-align: center">
    <h1>Hãy mời người chơi đánh giá đáp án của bạn</h1> 
    <?php echo html_writer::table($table)?>
    <div style="margin-top: 20px">
        <a href="/mod/rtw/view.php?id=<?php echo $course_module->id?>&c=map&a=level&l=<?php echo $player_info->current_level?>"><button>Trở về Map</button></a>
    </div>
</div>
<script>
var course_module = '<?php echo $course_module->id?>';
function invite(user_id,bt) {
    bt.disabled = true;
    ajaxLoadingBegin();
    $.ajax({
        type: 'GET',
        cache: false,
        async: false,
        url: "/mod/rtw/ajax.php?id="+course_module+"&c=proactive&a=request_user&uid="+user_id
    });
}
</script>