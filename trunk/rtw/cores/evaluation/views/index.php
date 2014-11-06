<div id="rtw_content">
    <?php
    foreach ($array as $element) {
    ?>
    <h2><?php echo $element->text_question?></h2>
    <textarea disabled="true" name="txtAnswer" id="txtAnswer" style="width: 99%" rows="5"><?php echo $element->answer ?></textarea>
    <?php
    }
    ?>
    <fieldset>
        <legend>Bạn hãy cho điểm bài viết này của người chơi (thang điểm từ 0 đến 3)</legend>
        <h5>1. Có trả lời đầu đủ câu hỏi ko (6 mục, khó khăn & cách giải quyết; thuận lợi & cách phát huy; mong đợi & kế hoạch thực hiện)</h5>
        <input  id="point_section_1" value="0" placeholder="Nhập số điểm từ 0 đến 3"/>
        <h5 style="margin-top: 30px">2. Các câu trả lời có trả lời đúng với câu hỏi?</h5>
        <input  id="point_section_2" value="0" placeholder="Nhập số điểm từ 0 đến 3"/>
        <h5 style="margin-top: 30px">3. Câu trả lời có hay và thuyết phục?</h5>
        <input  id="point_section_3" value="0" placeholder="Nhập số điểm từ 0 đến 3"/>
        <br><br>
        <button onclick="doSubmit()">Gửi đánh giá</button>
        <a style="margin-left: 20px" href="/mod/rtw/view.php?id=<?php echo $course_module->id?>&c=map&a=level&l=<?php echo $player_info->current_level?>"><button>Trở về Map</button></a>
    </fieldset>
</div>
<script>
var course_module = '<?php echo $course_module->id?>';
function doSubmit() {
    var point_section_1 = $("#point_section_1").val();
    var point_section_2 = $("#point_section_2").val();
    var point_section_3 = $("#point_section_3").val();
    ajaxLoadingBegin();
    $.ajax({
        type: 'POST',
        cache: false,
        url: "/mod/rtw/ajax.php?id="+course_module+"&c=evaluation&a=submit",
        data : {
            'point_section_1' : point_section_1,
            'point_section_2' : point_section_2,
            'point_section_3' : point_section_3
        },
        success: function(response) {
            if(response != '') {
                $.colorbox({
                    html:response,
                    width : "90%",
                    'onClosed' : function () {
                        
                    }
                });
            } 
        }
    });
}
</script>