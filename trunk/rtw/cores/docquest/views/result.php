<div style="display: block;text-align: center;padding: 20px">
    <?php
    if($point == 1) {
    ?>
    <h1 style="color: green">Bạn đã trả lời chính xác, bạn thật giống một chuyên gia!</h1>
    <?php
    } else {
    ?>
    <h1 style="color: red">Đáp án của bạn chưa đúng!</h1>
    <h3>Đáp án đúng là: </h3>
    <?php foreach ($answers as $answer):?>
        <h3><?php echo $answer->answer?></h3>
    <?php endforeach?>
    <?php  
    }
    ?>
    <?php
    if(isset($change_coin)) {
    ?>
    <i>Bạn được cộng thêm <b><?php echo $change_coin?></b> xu</i>
    <?php
    }
    ?>
    <br clear="all"/>
</div>
<script>
<?php
if(isset($change_coin)) {
    $current_coin = number_format($player_info->current_coin + $change_coin);
?>
    updatePlayerInfo('<?php echo $current_coin ?>');
<?php
}
?>
</script>