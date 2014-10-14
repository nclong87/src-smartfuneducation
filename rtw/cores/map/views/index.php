<div style="display: block;text-align: center">
    <div id="map">
        <?php
        $i = 1;
        for($i = 1; $i <= 1; $i ++) {
            $quest_class = 'quest'.$i;
            ?>
        <div class="quest <?php echo $quest_class?>">
            <a href="/mod/rtw/view.php?id=10&c=map&a=level&l=<?php echo $i?>"></a>
            <?php
            if(isset($data[$i])) {
                foreach ($data[$i] as $num => $group_name) {
                    echo '<div class="pos pos'.$num.'">'.$group_name.'</div>';
                }
            }
            ?>
        </div>
            <?php
        }
        ?>
    </div>
</div>