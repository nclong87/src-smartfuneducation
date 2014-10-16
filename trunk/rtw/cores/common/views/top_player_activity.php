<ul style="margin-left: 0px; list-style: none outside none;" >
    <?php
    foreach ($top_player_activity as $ele) {
    ?>
    <li>
        <div class="info_avatar">
            <?php echo $ele->picture?>
        </div>
        <div class="info_player">
            <span><b><?php echo $ele->firstname . $ele->lastname?></b></span>
            <span><?php echo $ele->username?></span>
        </div>
        <div class="info_game">
            <span><?php echo number_format($ele->current_coin)?> xu</span>
            <span><?php echo number_format($ele->current_xp)?> xp</span>
        </div>
    </li>
    <?php
    }
    ?>
</ul>