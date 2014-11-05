<div style="display: block;text-align: center;padding: 20px">
   
    <h1 style="color: green">Xin chúc mừng, bạn đã hoan tat trả lời cau hoi!</h1>
    <?php 
    $i = 1;
    if(isset($answer_ranking)){ 
        foreach ($answer_ranking as $a){ ?>
            <i>Lựa chọn <?php echo $i ?> :  <b><?php echo $a ?></b></i> 
        <?php
            $i++;
            } 
        }
    ?>
    <?php
    if(isset($change_coin)) {
    ?>
            <br> <i>Bạn được cộng thêm <b><?php echo $change_coin?></b> xu</i></br>
    <?php
    }
    ?>
    <br clear="all"/>
</div>