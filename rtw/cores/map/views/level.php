<div style="display: block;text-align: center">
    <div id="left_column">
        <?php
        $i = 0;
        foreach ($quests as $ele) {
            $link = '/mod/rtw/view.php?id='.$course_module->id.'&c='.$ele->controller;
            if($ele->action != '') {
                $link.= '&a='.$ele->action;
            }
            ?>
        <div class="quest pos<?php echo $i?>">
            <center><a href="<?php echo $link?>" class="img"></a></center>
            <span class="quest_name"><?php echo $ele->name?></span>
            <?php
            if(isset($current_members[$ele->controller])) {
                foreach ($current_members[$ele->controller] as $ele1) {
                    echo '<div class="member pos'.$ele1->pos.'">'.$ele1->picture.'<span class="name">'.$ele1->firstname .' '. $ele1->lastname.'</span></div>';
                }
            }
            ?>
        </div>
            <?php
            $i++;
        }
        ?>
    </div>
    <div id="right_column">
        <?php echo $widget_player_info?>
    </div>
    <br clear="all">
        <a class="button" href="/mod/rtw/view.php?id=<?php echo $course_module->id?>&c=map">Trở về Map</a>
</div>