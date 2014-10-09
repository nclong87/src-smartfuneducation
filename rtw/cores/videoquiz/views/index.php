<div style="display: block;text-align: center">
    <video
    id="video-active"
    class="video-active"
    width="640"
    height="390"
    controls="controls">
    <source src="<?php echo path_videos.$video->url?>" type="video/mp4">
</video>
    <br clear="all">
    <a class="button" href="/mod/rtw/view.php?id=<?php echo $course_module->id?>&c=map">Trở về Map</a>
</div>
<script>
var rand_numbers = <?php echo $rands?>;
var course_module = '<?php echo $course_module->id?>';
var video = document.getElementById("video-active");
var current_time = 0;
var counter;
function showQuestion(time) {
    $.ajax({
        type: 'GET',
        cache: false,
        async: false,
        url: "/mod/rtw/ajax.php?id="+course_module+"&c=videoquiz&a=question&time="+time,
        success: function(response) {
            if(response != '') {
                $.colorbox({
                    html:response,
                    width : "90%",
                    'onClosed' : function () {
                        video.play();
                    }
                });
            } else {
                video.play();
            }
        }
    });
}
$(document).ready(function(){
    console.log(rand_numbers);
    video.addEventListener("timeupdate", function () {
        //  Current time  
        var vTime = parseInt(video.currentTime);
        console.log(vTime);
        if(vTime != current_time && $.inArray(vTime,rand_numbers) >=0) {
            current_time = vTime;
            video.pause();
            showQuestion(vTime);
        }
    }, false);
});
</script>