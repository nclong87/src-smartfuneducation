<div id="rtw_content" style="display: block;text-align: center">
    <h2>Hãy xem ảnh bên dưới, câu hỏi sẽ hiện ra sau <span id="countdown-timer"><?php echo ($picture->countdown>0 ? $picture->countdown : 30) ?></span> giây</h2>
    <div id="picture_content">

        <img src="<?php echo $picture->url?>" />

    </div>
    <br clear="all">
    <a class="button" href="/mod/rtw/view.php?id=<?php echo $course_module->id?>&c=map&a=level&l=<?php echo $player_info->current_level?>">Trở về Map</a>
</div>
<script>
var rand_numbers = <?php echo $rands?>;
var course_module = '<?php echo $course_module->id?>';
var video = document.getElementById("video-active");
var current_time = 0;
function showQuestion(time) {
    $.ajax({
        type: 'GET',
        cache: false,
        async: false,
        url: "/mod/rtw/ajax.php?id="+course_module+"&c=picturequest&a=question",
        success: function(response) {
            if(response != '') {
                $.colorbox({
                    html:response,
                    width : "90%",
                    'onClosed' : function () {
                        location.reload();
                    }
                });
            }
        }
    });
}
$(document).ready(function(){
    console.log(rand_numbers);
    var counter = parseInt($("#countdown-timer").text());
    var done = false;
    var interval = setInterval(function(){
        counter--;
        if (counter < 0) {
            showQuestion();
            clearInterval(interval);
        }else{
            $("#countdown-timer").text(counter);
        }
    },1000)
});
</script>