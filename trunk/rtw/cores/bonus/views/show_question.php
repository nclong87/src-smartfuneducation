<div id="rtw_content">
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
    var seq = <?php echo $seq?> ;
    var remain_seconds = <?php echo $remain_seconds?> ;
    var course_module = '<?php echo $course_module->id?>';
    var counter;
    function doIgnore() {
        var next = parseInt(seq) + 1;
        location.href = "/mod/rtw/view.php?id="+course_module+"&c=bonus&a=question&seq="+ next;
    }
    function callAjaxAnswer() {
        var str = $( "#submitForm" ).serialize();
        $.ajax({
            type: 'POST',
            cache: false,
            async: false,
            url: "/mod/rtw/ajax.php?id="+course_module+"&c=bonus&a=answer&seq="+seq,
            data : str,
            success: function(response) {
                $.unblockUI();
                if(response != '') {
                    $.colorbox({
                        html:response,
                        'onClosed' : function () {
                            doIgnore();
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