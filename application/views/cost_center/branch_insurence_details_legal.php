<?php include __DIR__ . '../../header.php'; ?>
<center><h3 style="margin-top: 0"><span class="mdi mdi-sitemap fa-lg"></span> Insurence Details </h3></center><div class="clearfix"></div><hr>

<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
</a>
<?php if(!empty($branch_insurence_details)){ ?>
	<table id="branch_data" class="branch_data table table-condensed table-full-width table-bordered table-hover display">
		<thead id="header" style="background: #2dbbc1;">
			<th style="text-align:center;">Channel Partner ID</th>
			<th style="text-align:center;">Channel Partner Name</th>
			<th style="text-align:center;">Address</th>
			<th style="text-align:center;">Contact</th>
			<th style="text-align:center;">Email</th>
			<th style="text-align:center;">Deposit Amount</th>
			<th style="text-align:center;">Status</th>
			<th style="width:15%;text-align: center;">Edit </th>
		</thead>

		<tbody class="data_1">
			<?php
			$i = 1;
			foreach ($branch_insurence_details as $rentow) { 

				?>
				<tr>
					<td style="text-align:center;"><?php echo $rentow['id']; ?></td>
					<td><?php echo $rentow['owner_name']; ?></td>
					<td><?php echo $rentow['owner_address']; ?></td>
					<td><?php echo $rentow['owner_occupation']; ?></td>
					<td><?php echo $rentow['owner_email']; ?></td>      
					<td><?php echo $rentow['deposit_amt']; ?></td>  
					<?php if(!empty($rentow['rent_doc'])){?>                      
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
			<?php } 

			?>
		</tbody>

	</table>
<?php } ?>
<script type="text/javascript">
	$(document).ready(function () {
		$("#branch_insurence_form").validate({
			errorElement : 'span',
			submitHandler: function(form) {
				var formData = new FormData($('#branch_insurence_form')[0]);
				console.log(formData);
				$('#btn-submit').attr('disabled', true).html('Loading');
				$.ajax({
					url:base_url + 'branch_insurence_details_store',
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
								window.location.href = base_url + 'branch_insurence_details';
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
			window.location.href = base_url + 'branch_insurence_details/'+$(this).val();
		});

		$(document).on("focusout", "#premium_amt", function (event) {
			var permonamt=0;
			permonamt=parseFloat($('#premium_amt').val())+ parseFloat($('#gst_amt').val());
			$('#total_premium_amt').val(((permonamt)).toFixed(2));
		});
		$(document).on("focusin", "#premium_amt", function (event) {
			var permonamt=0;        

			permonamt=parseFloat($('#furni_fixtures_amt').val()) + parseFloat($('#stock_amt').val()) + parseFloat($('#elec_equip_amt').val()) + parseFloat($('#section_3A_amt').val()) + parseFloat($('#section_3B_amt').val()) + parseFloat($('#plate_glass_amt').val()) + parseFloat($('#neon_glowsign_amt').val()) + parseFloat($('#baggage_amt').val()) + parseFloat($('#section_10A_amt').val());
			$('#total_sum_insured').val(((permonamt)).toFixed(2));
		});
		
		$(document).on("focusout", "#gst_amt", function (event) {
			var permonamt=0;
			permonamt=parseFloat($('#premium_amt').val())+ parseFloat($('#gst_amt').val());
			$('#total_premium_amt').val(((permonamt)).toFixed(2));
		});
		
	});
</script>
<?php include __DIR__ . '../../footer.php'; ?>