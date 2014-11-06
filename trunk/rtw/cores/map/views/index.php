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
                        echo '<div class="pos pos'.$num.'" data-ref="'.$groupObj->name.'">';
                        print_group_picture($groupObj, $course->id);
                        //echo '<div class="bubbleInfo"><div class="popup">'.$groupObj->name.'</div></div>';
                        echo '</div>';
                    }
                }
                ?>
                <span style="display: block; position: absolute; width: 150px; text-align: center; left: -36px; color: red; font-weight: bold; z-index: 999; bottom: -90px;"><?php echo $config_rtw->levels->{'lv'.$i}->name ?></span>
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
<script>
$(document).ready(function() {
    $.each($('.level .pos'),function (){
        var title = $(this).attr("data-ref");
        $(this).frosty({
            content: '<div style="display:block;padding:10px">'+title+'</div>',
            html: true
        });
    });
});
</script>