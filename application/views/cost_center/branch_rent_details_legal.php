<?php include __DIR__ . '../../header.php'; ?>
<center><h3 style="margin-top: 0"><span class="mdi mdi-sitemap fa-lg"></span> Branch Rent Details </h3></center><div class="clearfix"></div><hr>

<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
</a>

<?php if(!empty($branch_rent_details)){ ?>
	<table id="branch_data" class="branch_data table table-condensed table-full-width table-bordered table-hover display">
		<thead id="header" style="background: #2dbbc1;">
			<th style="text-align:center;">ID</th>
                        <th style="text-align:center;">Branch Name</th>
			<th style="text-align:center;">ABM Name</th>
                        <th style="text-align:center;">ABM Contact</th>
			<th style="text-align:center;">Branch Address</th>
			<th style="text-align:center;">Deposit Amount</th>
			<th style="text-align:center;">Status</th>
			<th style="width:15%;text-align: center;">Edit </th>
		</thead>

		<tbody class="data_1">
			<?php
			$i = 1;
                        $temp_branch='';
			foreach ($branch_rent_details as $rentow) { 
 $q = $this->common_model->getSingleRow('cost_center_branch',array('branch_id'=>$rentow['branch_id']));
  $u = $this->common_model->getSingleRow('users',array('id_users'=>$q['branch_contact_person']));

 if($temp_branch!=$rentow['branch_id']){
				?>
				<tr>
					<td style="text-align:center;"><?php echo $rentow['id']; ?></td>
                                        <td><?php echo $q['branch_name']; ?></td>
					<td><?php echo $u['user_name']; ?></td>
                                        <td><?php echo $u['user_contact']; ?></td>
					<td><?php echo $q['branch_address']; ?></td>
					
					<td><?php echo $rentow['deposit_amt']; ?></td>  
					<?php if(($rentow['legal_approve'])==1){?>                      
						<td><?php echo 'Approved'; ?></td>                        
					<?php }else{ ?>
						<td><?php echo 'Pending'; ?></td>                        
					<?php } ?>
					<td style="text-align: center;">
						<a class="thumbnail btn-link waves-effect edit-btn" href="<?php echo base_url('branch_rent_details/'.$rentow['branch_id']); ?>" style="margin: 0" >
							<span class="mdi mdi-pen text-danger fa-lg"></span>
						</a>
					</td>
				</tr>
			<?php
 }
                        $temp_branch=$rentow['branch_id'];
                                        } 

			?>
		</tbody>

	</table>
<?php } ?>
<script type="text/javascript">
	$(document).ready(function () {

		$("#branch_rent_form").validate({
			errorElement : 'span',
			submitHandler: function(form) {
				var formData = new FormData($('#branch_rent_form')[0]);
				$('#btn-submit').attr('disabled', true).html('Loading');
				$.ajax({
					url:base_url + 'branch_rent_details_store',
					type: "POST",
					data: formData,
					async: true,
					dataType:"JSON",
					cache: false,
					contentType: false,
					processData: false,
					success:function(response)
					{
						if(response.status)
						{
							alert(response.message);
							setTimeout(function(){
								window.location.href = base_url + 'branch_rent_details';
							},2000);
						}else{
							alert(response.message);
						}
						$('#btn-submit').attr('disabled', false).html('Save');
					}
				});
				return false;
			}
		});

		$(document).on("change", "#branch_name", function (event) {
			window.location.href = base_url + 'branch_rent_details/'+$(this).val();
		});

		$(document).on("change", "#rent_tenure", function (event) {
			$('#asd').remove();   
			var container = $('<div class="controls" id="asd">');
			var option = $("#rent_tenure").val();
			for(i=1;i<=option;i++) 
			{
				container.append('<input style="display: block;" class="form-control" type=text id="rent_incr_ratio" class="span3 input-left-top-margins" name="rent_incr_ratio[]" id="Description' + i +'"' + 'placeholder="' + i + ' Year Ratio" />');
			}
			$('#titleAdd').after(container);   
		});


		$(document).on("change", "#rent_start_date", function (event) {

			var permonamt=0;
			var start_date=$('#rent_start_date').val();
			dt = new Date(start_date);
			var day = ("0" + (dt.getDate())).slice(-2);
			var month = ("0" + (dt.getMonth() + 1)).slice(-2);
			var year = (parseFloat(dt.getFullYear()) + parseFloat($('#rent_tenure').val()));
			var datestring = year+ "-" + month + "-" + day ;
			$('#rent_end_date').val(((datestring)));
		});
	});
</script>
<?php include __DIR__ . '../../footer.php'; ?>