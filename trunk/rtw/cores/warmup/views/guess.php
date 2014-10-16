<?php
	global $OUTPUT;
	global $USER;
?>
<script language="Javascript">
function SelectMoveRows(SS1,SS2)
{
    var SelID='';
    var SelText='';
    // Move rows from SS1 to SS2 from bottom to top
    for (i=SS1.options.length - 1; i>=0; i--)
    {
        if (SS1.options[i].selected == true)
        {
            SelID=SS1.options[i].value;
            SelText=SS1.options[i].text;
            var newRow = new Option(SelText,SelID);
            SS2.options[SS2.length]=newRow;
            SS1.options[i]=null;
        }
    }
    UpdateHidMembers(document.assignform.groupmembers);
}
function UpdateHidMembers(SelList)
{
    var ID='';
    for (x=0; x < SelList.length; x++)
    {
    	// Swap rows
        if(ID=='')
           ID = SelList[x].value;
        else
           ID = ID + ";" + SelList[x].value;
    }
    document.assignform.hidMembers.value=ID;
}

</script>
<div id="rtw_content" style="display: block;text-align: center">
	<h3><?php echo $coursename;?> - Khởi động</h3>
	 Nhận diện thành viên nhóm mình: <br/>thực hiện bằng cách chọn thành viên nhóm từ danh sách lớp và chuyển sang nhóm của bạn. Để kết thúc phần này, nhất nút "Gửi dự đoán".
     <form id="assignform" name="assignform" style="width:850px; margin:0 auto;" action="view.php?id=<?php echo $id;?>&c=warmup&a=send" method="post">
     	<input type="hidden" name="hidMembers" id="hidMembers" value="">
     	<table id="assigningrole" summary="" class="admintable roleassigntable generaltable" cellspacing="0">
     	<tr>
     		<td id="existingcell" width="350">
     			<p><label for="removeselect">Danh sách lớp</label></p>
					<select name="students[]" id="students" multiple="multiple"  style="width:350px;" size="20">
					  	<?php 
					  	if (!empty($students)) {
					  		foreach ($students as $user) {
								if($USER->id!=$user->id)
									echo '<option value="'.$user->id.'">'.fullname($user).' ('.$user->email.')</option>';
					  		}
					  	}
					  	?>
					</select>
		 	</td>
		 	<td id="buttonscell" width="100">
	          <div id="addcontrols">
	          	<input 
	          	onclick="SelectMoveRows(document.assignform.students,document.assignform.groupmembers)"
	          	name="remove" id="remove" type="button" value="<?php echo get_string('add').'&nbsp;'.$OUTPUT->rarrow(); ?>" title="<?php print_string('add'); ?>" />
	              
	          </div>
	
	          <div id="removecontrols">
					<input 
					onclick="SelectMoveRows(document.assignform.groupmembers,document.assignform.students)" 
					name="add" id="add" type="button" value="<?php echo $OUTPUT->larrow().'&nbsp;'.get_string('remove'); ?>" title="<?php print_string('remove'); ?>" /><br />	              
	          </div>
		 	</td>
		 	<td id="potentialcell">
		 		<p><label for="addselect">Nhóm của bạn: <b><?php echo $groupname;?></b></label></p>
				 <select name="groupmembers[]" id="groupmembers" multiple="multiple" style="width:350px;" size="20">
				    	<?php 
				    	echo '<option value="'.$USER->id.'">'.fullname($USER).' ('.$USER->email.')</option>';
					  	//if (!empty($groupmembers)) {
					  	//	foreach ($groupmembers as $user) {
						//		echo '<option value="'.$user->id.'">'.fullname($user).' ('.$user->email.')</option>';
					  	//	}
					  	//}
					  	?>
				</select>
				 
			</td>
		</tr> 
		
        </table>
        <a class="button" href="/mod/rtw/view.php?id=<?php echo $course_module->id?>&c=map&a=level&l=<?php echo $player_info->current_level?>">Trở về Map</a>
        <input id="btSubmit" style="margin-left: 20px; margin-bottom: 0px;" type="submit" value="Gửi dự đoán" />
        <br clear="all"/>
        <i style="display:block;margin-top: 10px">Thời gian gửi dự đoán còn <b id="remain_seconds"><?php echo $remain_seconds?></b> giây</i>
     </form>
</div>
<script language="Javascript">
    var remain_seconds = <?php echo $remain_seconds?> ;
    var counter;
    function countDown() {
        if(remain_seconds > 0) {
            remain_seconds--;
            $("#remain_seconds").text(remain_seconds);
            counter = setTimeout("countDown()",1000);
        } else {
            $("#btSubmit")[0].disabled = true;
        }
    }
    $(document).ready(function() {
        $("#btSubmit")[0].disabled = false;
        countDown();
        UpdateHidMembers(document.assignform.groupmembers);
    });
</script>