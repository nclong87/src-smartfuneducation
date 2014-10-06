<?php
	global $OUTPUT;
	global $USER;
?>
<div style="display: block;text-align: center">
	<h3><?php echo $coursename;?> - Khởi động</h3>
	 Nhận diện thành viên nhóm mình: <br/>thực hiện bằng cách chọn thành viên nhóm từ danh sách lớp và chuyển sang nhóm của bạn. Để kết thúc phần này, nhất nút "Gửi dự đoán".
     <form id="assignform" style="width:850px; margin:0 auto;" action="" method="post">
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
	          	<input onclick="move('right');" name="remove" id="remove" type="button" value="<?php echo get_string('add').'&nbsp;'.$OUTPUT->rarrow(); ?>" title="<?php print_string('add'); ?>" />
	              
	          </div>
	
	          <div id="removecontrols">
					<input onclick="move('left');" name="add" id="add" type="button" value="<?php echo $OUTPUT->larrow().'&nbsp;'.get_string('remove'); ?>" title="<?php print_string('remove'); ?>" /><br />	              
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
				 
				 <br/>
				 <input type="submit" value="Gửi dự đoán"/>
			</td>
		</tr> 
		
        </table>
     </form>
</div>
<script>
//function to move an element from a box to the other
function move(sens)
{               
	var i, sourceSel, targetSel; 

	  if (sens == 'right') {
	    sourceSel = document.getElementById('students'); 
	    targetSel = document.getElementById('groupmembers');
	  } else {
	    sourceSel = document.getElementById('groupmembers'); 
	    targetSel = document.getElementById('students');
	  }

	  i = sourceSel.options.length;
	  while (i--) {
	    if (sourceSel.options[i].selected) {
	      targetSel.appendChild(sourceSel.options[i]);
	    }
	  }
}
	</script>