<div style="display: block;text-align: center">
    <video
    id="video-active"
    class="video-active"
    width="640"
    height="390"
    controls="controls">
    <source src="http://local.moodle/pluginfile.php/101/mod_resource/content/1/Lop hoc vui nhon.mp4" type="video/mp4">
</video>
</div>
<script>
var video = document.getElementById("video-active");
$(document).ready(function(){
    video.addEventListener("timeupdate", function () {
        //  Current time  
        var vTime = parseInt(video.currentTime);
        console.log(vTime);
        if(vTime == 10) {
            video.pause();
        }
        
    }, false);
});
</script>