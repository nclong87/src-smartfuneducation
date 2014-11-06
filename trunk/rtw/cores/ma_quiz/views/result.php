<link rel="stylesheet" href="/mod/rtw/cores/ma_quiz/css/result.css"
<div style="display: block;text-align: center;padding: 20px">
     <div id="result-wrapper">
    <?php if($point == 1) { ?>
    <h1 style="color: green">Bạn đã trả lời chính xác, bạn thật giống một chuyên gia!</h1>
    <?php } else { ?>

     <h1 style="color: red">Đáp án của bạn chưa đúng! Đáp án đúng là: </h1>     
     <h2 style="color: red">
      <?php foreach ($correct_answers as $answer) { 
         echo $answer['answer'] . " - " . $answer['question'] . "; <br/>";
      } ?>
    <?php } ?>
    </h2>
    <?php if(isset($change_coin)) { ?>
        <i>Bạn được cộng thêm <b><?php echo $change_coin?></b> xu</i>
    <?php } ?>     
    <br clear="all"/>
    </div>         
</div>
<script>
         //('a.colorbox').colorbox.resize({'width':500, 'height': 600});
</script>
