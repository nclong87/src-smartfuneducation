<div id="rtw_content" style="display: block;text-align: center">
    <h2><?php echo $coursename;?> - Khởi động</h2>
    Kết quả thực hiện:<br/>
    - Thời gian bạn bắt đầu chơi:<?php echo $start;?><br/>
    - Thời gian kết thúc:<?php echo $end;?><br/>
    Điểm cộng (trả lời đúng): <?php echo $coins1;?><br/>
    Điểm trừ (trễ giờ): <?php echo $coins2;?><br/>
    Tổng cộng:<?php echo $total;?>
    <br clear="all"/>
    <a class="button" href="/mod/rtw/view.php?id=<?php echo $course_module->id?>&c=map&a=level&l=<?php echo $player_info->current_level?>">Trở về Map</a>
</div>
<script>
   updatePlayerInfo('<?php echo $current_coin?>'); 
</script>