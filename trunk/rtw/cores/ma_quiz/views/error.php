<div style="display: block;text-align: center;padding: 20px;color: red">
    <?php echo $error_message?>
    <div style="margin-top: 20px">
    <a class="button" href="/mod/rtw/view.php?id=<?php echo $course_module->id?>&c=map">Trở về Map</a>
    <a style="margin-left: 20px" class="button" href="/mod/rtw/view.php?id=<?php echo $course_module->id?>&c=ma_quiz&a=question&seq=<?php echo $seq+1 ?>">Câu hỏi tiếp theo</a>
    </div>
</div>