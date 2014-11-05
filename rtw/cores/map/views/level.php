<div style="display: block">
    <div id="left_column" style="min-height: 750px">
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
            if($game != 'warmup' && isset($current_members[$ele->controller])) {
                foreach ($current_members[$ele->controller] as $ele1) {
                    $fullname = $ele1->firstname .' '. $ele1->lastname;
                    echo '<div data-ref="'.  htmlspecialchars($fullname).'" class="member pos'.$ele1->pos.'">'.$ele1->picture.'</div>';
                }
            }
            ?>
        </div>
            <?php
        }
        ?>
        <div style="position: absolute; width: 100%; text-align: center; bottom: 10px;">
            <a href="/mod/rtw/view.php?id=<?php echo $course_module->id?>&c=map"><button>Trở về Map</button></a>
        </div>
    </div>
    <div id="right_column">
        <?php echo $widget_player_info?>
    </div>
</div>
<script>
$(document).ready(function() {
    $.each($('.quest div.member'),function (){
        var title = $(this).attr("data-ref");
        $(this).frosty({
            content: '<div style="display:block;padding:10px">'+title+'</div>',
            html: true
        });
    });
});
</script>