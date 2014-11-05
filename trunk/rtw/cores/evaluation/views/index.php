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
        <legend>Bạn hãy cho điểm 3 tính huống người chơi đã mô tả ở trên</legend>
        <h3>Người chơi có mô tả đầy đủ 3 tình huống? (1 điểm cho 1 tình huống, tối đa 3 điểm)</h3>
        <input  id="point_section_1" value="" placeholder="Nhập số điểm từ 0 đến 3"/>
        <h3>Người chơi mô tả có rõ ràng hay không? (tối đa 3 điểm)</h3>
        <input  id="point_section_2" value="" placeholder="Nhập số điểm từ 0 đến 3"/>
        <h3>Tất cả những tình huống này có thực sự chủ động? (tối đa 3 điểm)</h3>
        <input  id="point_section_3" value="" placeholder="Nhập số điểm từ 0 đến 3"/>
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