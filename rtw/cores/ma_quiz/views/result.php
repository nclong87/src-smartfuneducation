<link rel="stylesheet" href="/mod/rtw/cores/ma_quiz/css/result.css"
<div style="display: block;text-align: center;padding: 20px">
     <div id="result-wrapper">
    <?php if($point == 1) { ?>
    <h1 style="color: green">Bạn đã trả lời chính xác, bạn thật giống một chuyên gia!</h1>
    <?php } else { ?>

    <h1 style="color: red">Đáp án của bạn chưa đúng!</h1>
    <span>Đáp án đúng là: </span>
    <table style="align:text">
      <?php foreach ($correct_answers as $answer) { ?>

                                                      <tr>
          <td><?php echo $answer['question'] ?></td>
         <td><?php echo $answer['answer'] ?></td>                                                      
      <tr>
      <?php } ?>
    </table>
    <?php } ?>

    <?php if(isset($change_coin)) { ?>
        <i>Bạn được cộng thêm <b><?php echo $change_coin?></b> xu</i>
    <?php } ?>     
    <br clear="all"/>
    </div>         
</div>
<script>
    $('a.colorbox').colorbox.resize({'width':500, 'height': 600});
</script>
