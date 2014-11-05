<div style="display: block;text-align: left;padding: 20px;width: 94%">
    <h1>Đã hết câu hỏi cho ảnh này</h1>
    <form id="submitForm">
        <br clear="all"/>
        <input type="button" value="Trở về" onclick="doIgnore()"/>
        <br>
    </form>
</div>
<script>
    function doIgnore() {
        $.colorbox.close();
        location.href = "/mod/rtw/view.php?id="+course_module+"&c=picturequest&a=intro";
    }
    
</script>