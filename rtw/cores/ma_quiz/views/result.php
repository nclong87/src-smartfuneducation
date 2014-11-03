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
    if(isset($change_coin)) {
    ?>
    <i>Bạn được cộng thêm <b><?php echo $change_coin?></b> xu</i>
    <?php
    }
    ?>
    <br clear="all"/>
</div>