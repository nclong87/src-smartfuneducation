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
var rand_numbers = <?php echo $rands?>;
var num = 0;
var max_num = <?php echo $max_num?>;
function count() {
    num++;
    if(num >= max_num) {
        return;
    }
    if($.inArray(num,rand_numbers) >=0) {
        alert(num);
    }
    setTimeout("count()",1000);
}
$(document).ready(function(){
    count();
    //console.log(rand_numbers);
    //console.log(max_num);
});
</script>