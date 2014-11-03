<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
<script src="/mod/rtw/cores/ma_quiz/scripts/main.js"></script>
<link rel="stylesheet" href="/mod/rtw/cores/ma_quiz/css/style.css"/>
<div id="rtw_content">
    <h1><?php echo $question->questiontext?></h1>
    <table id="drag-form-layout">
        <tr style="align:center">
            <td>
                <div class="items-wrapper">
                <ul class="items">
                <?php foreach ($subquestions as $subquestion) { ?>
                    <li><?php echo $subquestion?></li>
                <?php } ?>
                </ul>
                </div>
            </td>
            <td>
               <div class="docks">
               <?php foreach ($answers as $answer) { ?>
                    <div class="dock-wrapper">
                       <span class="dock-label"><?php echo $answer?></span>                                                     
                        <ul class="dock" data-dock="<?php echo $answer?>"></ul>
                    </div>
               <?php } ?>
               </div>
            </td>
       <tr>
    </table>
                                                     
    <form id="submitForm">
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
        location.href = "/mod/rtw/view.php?id="+course_module+"&c=ma_quiz&a=question&seq="+ next;
    }
                                                     
    function callAjaxAnswer() {
        var result = getResult();
        console.log(result);
        $.ajax({
            type: "POST",
            cache: false,
            async: false,
            url: "/mod/rtw/ajax.php?id="+course_module+"&c=ma_quiz&a=answer&seq="+seq,            
            data :{'answers': result},
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

    Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
           if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    }
        
    // Return false if answer is bad, otherwise
    function checkAnswer(result) {
        if (Object.size(result) == 0) {
            return false;
        }
        return true;
    }

    function doAnswer() {
        var result = getResult();
        if (!checkAnswer(result)) {
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
    });
</script>
