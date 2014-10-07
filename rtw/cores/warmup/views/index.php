<div style="display: block;text-align: center">
    <h2><?php echo $coursename;?> - Khởi động</h2>
    <p>Nhận diện thành viên nhóm mình</p>
    <p style="color: red;"><?php echo ($isbelonggroup?"":"Hiện bạn chưa được phân vào nhóm nào cả, do đó bạn không tiếp tục game được.");?></p>
    <form id="assignform" action="view.php?id=<?php echo $id;?>&c=warmup&a=guess" method="post">
    	<input type="submit" value="Bắt đầu tham gia" <?php echo ($isbelonggroup?"":"disabled");?>/>
    </form>
</div>