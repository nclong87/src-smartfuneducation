<div id="rtw_content">
    <h1><?php echo $question->questiontext?></h1>
    <form id="submitForm">
        <textarea name="txtAnswer" id="txtAnswer" style="width: 99%" rows="5"><?php echo isset($question->answer)?$question->answer:'' ?></textarea>
        <br clear="all"/>
        <input type="button" value="Bỏ qua" onclick="doIgnore()"/>
        <input type="button" value="Trả lời" style="margin-left: 20px" onclick="doAnswer()"/>
        <br>
        <!--i>Thời gian trả lời : <b id="remain_seconds"><?php echo $remain_seconds?></b> giây</i-->
    </form>
</div>
<script>
    var seq = <?php echo $seq?> ;
    var remain_seconds = <?php echo $remain_seconds?> ;
    var course_module = '<?php echo $course_module->id?>';
    var counter;
    function doIgnore() {
        var next = parseInt(seq) + 1;
        location.href = "/mod/rtw/view.php?id="+course_module+"&c=proactive&a=question&seq="+ next;
    }
    function callAjaxAnswer() {
        var str = $( "#submitForm" ).serialize();
        //ajaxLoadingBegin();
        $.ajax({
            type: 'POST',
            cache: false,
            async: false,
            url: "/mod/rtw/ajax.php?id="+course_module+"&c=proactive&a=answer&seq="+seq,
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
                } else {
                    doIgnore();
                }
            }
        });
    }
    function doAnswer() {
        var str = $( "#submitForm #txtAnswer" ).val();
        if(str == '') {
            alert("Vui lòng nhập đáp án trả lời!");
            return;
        }
        clearTimeout(counter);
        blockUI("Hệ thống đang lưu câu trả lời của bạn, vui lòng chờ đợi trong giây lát...");
        setTimeout("callAjaxAnswer()",300);
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
        //countDown();
        $("#txtAnswer").focus();
    });
</script>