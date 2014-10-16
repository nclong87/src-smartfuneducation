<div style="display: inline-block;">
    <div id="left_column">
        <div id="map">
            <?php
            $i = 1;
            $total_level = $config_rtw->levels->num;
            for($i = 1; $i <= $total_level; $i ++) {
                $quest_class = 'level'.$i;
                ?>
            <div class="level <?php echo $quest_class?>" title="<?php echo $config_rtw->levels->{'lv'.$i}->name ?>">
                <?php
                if($player_info->current_level >= $i) {
                ?>
                <a href="/mod/rtw/view.php?id=<?php echo $course_module->id?>&c=map&a=level&l=<?php echo $i?>"></a>
                <?php
                }
                ?>
                <?php
                if(isset($data[$i])) {
                    $pos = array(0,1,2,3,4,5,6);
                    shuffle($pos);
                    foreach ($data[$i] as $groupObj) {
                        $num = rtw_pick_one($pos);
                        echo '<div class="pos pos'.$num.'" title="'.$groupObj->name.'">';
                        print_group_picture($groupObj, $course->id);
                        echo '</div>';
                    }
                }
                ?>
            </div>
                <?php
            }
            ?>
        </div>
    </div>
    <div id="right_column">
        <?php echo $widget_player_info?>
    </div>
</div>