<link rel="stylesheet" href="/mod/rtw/cores/situation_quiz/css/style.css"/>
<div id="rtw_content">
    <h1 class="questionText"><?php echo $question->questiontext?></h1>   
    <form id="submitForm">
        <textarea id="essay-answer-box"></textarea>
        <br/>
        <input type="button" value="Bỏ qua" onclick="doIgnore()"/>
        <input type="button" value="Trả lời" style="margin-left: 20px" onclick="doEval()"/>
        <br>
    </form>
</div>
     
<script>
    var seq = <?php echo $seq?> ;
// var remain_seconds = <?php echo $remain_seconds?> ;
    var course_module = '<?php echo $course_module->id?>';
    var counter;
                                                     
    function getEssay() {
        var answer = $("#essay-answer-box").val();
        return answer;
    }

    function doIgnore() {
        var next = parseInt(seq) + 1;
        location.href = "/mod/rtw/view.php?id="+course_module+"&c=situation_quiz&a=question&seq="+ next;
    }

    function callAjaxEvaluation() {
        var ans = getEssay();
        $.ajax({
            type: "POST",
            cache: false,
            async: false,
            url: "/mod/rtw/ajax.php?id="+course_module+"&c=situation_quiz&a=evaluation&seq="+seq,            
            data: {"essay": ans},
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

    // Return false if answer is bad, otherwise
    function checkEssay(result) {
        if (result == "") return false;
        return true;
    }

    function doEval() {
        var result = getEssay();
        
        if (!checkEssay(result)) {
            alert("Vui lòng chọn đáp án trả lời!");
            return;
        }
        
        blockUI("Đang kiểm tra dữ liệu, vui lòng chờ đợi trong giây lát...");
        setTimeout("callAjaxEvaluation()",500);
    }                                                     

</script>
