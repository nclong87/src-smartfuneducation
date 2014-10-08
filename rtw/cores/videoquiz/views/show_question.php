<div style="display: block;text-align: left;padding: 20px">
    <h1><?php echo $question->questiontext?></h1>
    <form id="submitForm">
        <?php
        foreach ($question->options->answers as $option) {
            if ($is_multichoice == false) {
                ?>
                <div class="option">
                    <input type="radio" name="options[]" id="option_<?php echo $option->id ?>" value="<?php echo $option->id ?>"/>
                    <label for="option_<?php echo $option->id ?>"><?php echo $option->answer ?></label>
                </div>
                <?php
            } else {
                ?>
                <div class="option">
                    <input type="checkbox" name="options[]" id="option_<?php echo $option->id ?>" value="<?php echo $option->id ?>"/>
                    <label for="option_<?php echo $option->id ?>"><?php echo $option->answer ?></label>
                </div>
                <?php
            }
        }
        ?>
        <br clear="all"/>
        <input type="button" value="Bỏ qua" onclick="doIgnore()"/>
        <input type="button" value="Trả lời" style="margin-left: 20px" onclick="doAnswer()"/>
        <br>
        <i>Thời gian trả lời : <b id="remain_seconds"><?php echo $remain_seconds?></b> giây</i>
    </form>
</div>
<script>
    var time = <?php echo $time?> ;
    var remain_seconds = <?php echo $remain_seconds?> ;
    function doIgnore() {
        $.colorbox.close();
    }
    function callAjaxAnswer() {
        var str = $( "#submitForm" ).serialize();
        $.ajax({
            type: 'POST',
            cache: false,
            async: false,
            url: "/mod/rtw/ajax.php?id="+course_module+"&c=videoquiz&a=answer&time="+time,
            data : str,
            success: function(response) {
                $.unblockUI();
                if(response != '') {
                    $.colorbox({
                        html:response,
                        'onClosed' : function () {
                            video.play();
                        }
                    });
                }
            }
        });
    }
    function doAnswer() {
        var str = $( "#submitForm" ).serialize();
        if(str == '') {
            alert("Vui lòng chọn đáp án trả lời!");
            return;
        }
        clearTimeout(counter);
        blockUI("Đang kiểm tra dữ liệu, vui lòng chờ đợi trong giây lát...");
        setTimeout("callAjaxAnswer()",500);
    }
    function countDown() {
        if(remain_seconds > 0) {
            remain_seconds--;
            $("#remain_seconds").text(remain_seconds);
            counter = setTimeout("countDown()",1000);
        } else {
            doIgnore();
        }
    }
    $(document).ready(function() {
        countDown();
        //count();
        //console.log(rand_numbers);
        //console.log(max_num);
    });
</script>