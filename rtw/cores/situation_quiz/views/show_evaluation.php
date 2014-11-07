<div id="rtw_content">
    <h1 class="questionText">Kết quả giải đáp tình huống</h1>
    <form id="submitForm">
        <table class="result">
           <tr>
               <th>
                    Câu trả lời của bạn
               </th>
               <th>
                    Đáp án gợi ý cho tình huống
               </th>
           </tr>
           <tr>
               <td>
                    <?php echo $essay ?>
               </td>
               <td>
                    <?php echo $hint?>
               </td>
           </tr>
         </table>
         <br/>
         <table class="self-eval">
           <tr>
              <td>
                 <div style="font-weight: bold">Bạn đánh giá thế nào về đáp án của bạn?<br></div>
                 
              </td>
           </tr>
           <tr>
               <td>              
                   <input type="radio" name="evaluation" value="1"/>Hoàn toàn không tương thích
               </td>
               <td>
                   <input type="radio" name="evaluation" value="2"/>Tương thích một phần
               </td>
               <td>
                   <input type="radio" name="evaluation" value="3"/>Hoàn toàn tương thích 
               </td>
           </tr>
        </table>
        <br/>
        <input type="button" value="Bỏ qua" onclick="doIgnore()"/>
        <input type="button" value="Đánh giá" style="margin-left: 20px" onclick="doEvalAnswer()"/>
    </form>
</div>
<script>     
     function getEvalAns() {
         var eval_ans = $("input[name='evaluation']:checked").val();         
         return eval_ans;
     }

     function checkEvalAns(eval_ans) {
         if (!eval_ans) {             
             return false;
         }
         return true;
     }

     function doEvalAnswer() {
         var result = getEvalAns();

         console.log(checkEvalAns(result));
        if (!checkEvalAns(result)) {
            alert("Vui lòng chọn đáp án trả lời!");
            return;
        }
        
        blockUI("Đang kiểm tra dữ liệu, vui lòng chờ đợi trong giây lát...");
        setTimeout("callAjaxAnswer()",500);
     }
     
     function callAjaxAnswer() {
         var essay = "<?php echo $essay; ?>";
         var ans = getEvalAns();
          $.ajax({
            type: "POST",
            cache: false,
            async: false,
            url: "/mod/rtw/ajax.php?id="+course_module+"&c=situation_quiz&a=answer&seq="+seq,            
            data: {"answer": ans, "essay": essay},
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
</script>
     