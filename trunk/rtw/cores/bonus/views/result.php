<div style="display: block;text-align: center;padding: 20px">
    <?php
    if($point == 1) {
    ?>
    <h1 style="color: green">Bạn đã trả lời chính xác, bạn thật giống một chuyên gia!</h1>
    <?php
    } else {
    ?>
    <h1 style="color: red">Đáp án của bạn chưa đúng!</h1>
    <?php  
    }
    ?>
    <?php
    if(isset($plus_lottery_turn)) {
    ?>
    <i>Bạn được nhận thêm <b><?php echo $plus_lottery_turn?></b> lượt quay số trúng thưởng!</i>
    <?php
    }
    ?>
    <br clear="all"/>
</div>