<div style="display: block;text-align: center">
    <video
    id="video-active"
    class="video-active"
    width="640"
    height="390"
    controls="controls">
    <source src="<?php echo $video->url?>" type="video/mp4">
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
function doQuery() {
    $.ajax({
        type: 'GET',
        cache: false,
        async: false,
        url: "/mod/rtw/view.php?id=10&c=videoquiz&a=query",
        success: function(response) {
            if(response != '') {
                $.colorbox({html:response});
            }
        }
    });
}
$(document).ready(function(){
    //count();
    //console.log(rand_numbers);
    //console.log(max_num);
});
</script>