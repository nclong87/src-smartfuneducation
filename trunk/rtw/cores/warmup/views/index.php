<div id="rtw_content" style="display: block;text-align: center">
    <h2>Khởi động</h2>
    <p>Bạn có 5 phút để hoàn thành trò chơi “Khởi động”. Nhiệm vụ của bạn là phải đi tìm tất cả các thành viên có cùng mã số nhóm với mình để đưa vào danh sách. Nếu hoàn thành xuất sắc bạn sẽ nhận được 5 xu, sai 1 thành viên bạn sẽ bị trừ 1 xu, mỗi 5 phút trễ bạn sẽ bị trừ 1 xu. Chúc bạn thành công!</p>
    <p style="color: red;"><?php echo ($isbelonggroup?"":"Hiện bạn chưa được phân vào nhóm nào cả, do đó bạn không tiếp tục game được.");?></p>
    <form id="assignform" action="view.php?id=<?php echo $id;?>&c=warmup&a=guess" method="post">
        <a class="button" href="/mod/rtw/view.php?id=<?php echo $course_module->id?>&c=map&a=level&l=<?php echo $player_info->current_level?>">Trở về Map</a>
        <input style="margin-left: 20px; margin-bottom: 0px;" type="submit" value="Bắt đầu tham gia" <?php echo ($isbelonggroup?"":"disabled");?>/>
    </form>
</div>