<div id="rtw_content">


    <h1><?php echo $question->questiontext?></h1>
    <form id="submitForm">
        <table class="option">
        <?php
        foreach ($question->options->answers as $option) {
        ?>
                    <tr>  
                        <td>
                <div name="answer" style="margin-left:  10%; margin-right:100px;"><label for="option_<?php echo $option->id ?>"><?php echo $option->answer ?></label>
                </div>
                        </td>
                        <td>
                <div id="ranking" >
                    <input type="text" name="options[<?php echo $option->id ?>]" style="width: 50px; margin-left : 50%;"
                       id="option_<?php echo $option->id ?>" />
                </div>
                        </td>
                </tr>
                
        <?php       
        }
        ?>
            </table>
        <br clear="all"/>
        <a class="button" href="/mod/rtw/view.php?id=<?php echo $course_module->id?>&c=map&a=level&l=<?php echo $player_info->current_level?>">Trở về Map</a>
        <input type="button" value="Answer" style="margin-left: 20px" onclick="doAnswer()"/>
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
        location.href = "/mod/rtw/view.php?id="+course_module+"&c=ranking&a=question&seq="+ next;
    }
    function callAjaxAnswer() {
        var str = $( "#submitForm" ).serialize();
        $.ajax({
            type: 'POST',
            cache: false,
            async: false,
            url: "/mod/rtw/ajax.php?id="+course_module+"&c=ranking&a=answer&seq="+seq,
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
        var length = document.getElementsByName("answer").length;
        var rankArray = document.getElementsByTagName('input');
        var num = 0;
        for(var i in rankArray){
            num++;
            if(num > length){
                break;
            }
            var v = rankArray[i].value;
            if(v === ''){
                alert("Vui lòng nhập đủ các lựa chọn!");
                return;
            } else {
                var reg = new RegExp('[1-12]');
                if(reg.test(v) === false){
                    alert("Lựa chọn phải là kiểu số 1-12");
                    return;
                }
            }
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
    });
    
</script>