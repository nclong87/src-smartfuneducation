<div style="display: block">
    <div id="left_column" style="height: 500px">
        <?php
        foreach ($quests as $game => $ele) {
            $link = '/mod/rtw/view.php?id='.$course_module->id.'&c='.$ele->controller;
            if($ele->action != '') {
                $link.= '&a='.$ele->action;
            }
            if(isset($array_games[$game])) { //player da choi game nay
                $icon_class = 'icon_'.$game;
            } else {
                $icon_class = 'icon_question';
            }
            ?>
        <div class="quest pos<?php echo $ele->pos?>">
            <center><a href="<?php echo $link?>" class="<?php echo $icon_class?>"></a></center>
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
        }
        ?>
        <div style="position: absolute; width: 100%; text-align: center; bottom: 10px;">
            <a  class="button" href="/mod/rtw/view.php?id=<?php echo $course_module->id?>&c=map">Trở về Map</a>
        </div>
    </div>
    <div id="right_column">
        <?php echo $widget_player_info?>
    </div>
</div>