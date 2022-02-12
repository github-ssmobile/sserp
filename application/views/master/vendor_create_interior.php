<?php include __DIR__ . '../../header.php'; ?>
<style type="text/css">
	.sticky {
		position: fixed;
		top: 0;
		/*width: 100%;*/
	}

	.sticky + .content {
		/*padding-top: 102px;*/
	}
	.data_1{
		position: inherit;
	}
</style>
<center><h3 style="margin-top: 0"><span class="mdi mdi-account-network fa-lg"></span>Interior Vendor Details </h3></center><div class="clearfix"></div><hr>
<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
</a>
<?php
if(!empty($vendor_details)){ 
// print_r($this->session->userdata());die();
	?>
	<div class="thumbnail" style="padding: 0; margin: 0; min-height: 600px; overflow: auto">

		<div id="purchase" style="padding: 20px 10px; margin: 0">
			<form id="vendor_create_form" method="post" >

				<div class="col-md-8 thumbnail col-md-offset-2">

					<label class="col-md-3 col-md-offset-1">Vendor Name</label>
					<div class="col-md-5">
						<input type="text" class="form-control" placeholder="Enter Vendor Name" name="vendor_name" id="vendor_name" required="" value="<?php if(!empty($vendor_details['vendor_name'])){ echo $vendor_details['vendor_name'];} ?>" style="text-transform:uppercase"/>
					</div>
					<div class="clearfix"></div><br>
					
					<label class="col-md-3 col-md-offset-1">Contact Person</label>
					<div class="col-md-5">
						<input type="text" class="form-control" name="person_name" id="person_name" placeholder="Enter Contact" required=""  value="<?php if(!empty($vendor_details['person_name'])){ echo $vendor_details['person_name'];} ?>"/>
					</div>
					<div class="clearfix"></div><br>
					
					<label class="col-md-3 col-md-offset-1">Contact</label>
					<div class="col-md-5">
						<input type="text" class="form-control" name="vendor_contact" id="vendor_contact" placeholder="Enter Contact" required=""  value="<?php if(!empty($vendor_details['vendor_contact'])){ echo $vendor_details['vendor_contact'];} ?>"/>
					</div>
					<div class="clearfix"></div><br>
					
					<label class="col-md-3 col-md-offset-1">Email</label>
					<div class="col-md-5">
						<input type="text" class="form-control" name="vendor_email" id="vendor_email" placeholder="Enter Email" required=""  value="<?php if(!empty($vendor_details['vendor_email'])){ echo $vendor_details['vendor_email'];} ?>"/>
					</div>
					<div class="clearfix"></div><br>
					
					<label class="col-md-3 col-md-offset-1">GST</label>
					<div class="col-md-5">
						<input type="text" class="form-control IsGst" name="vendor_gst" id="vendor_gst" placeholder="Enter GST No" value="<?php if(!empty($vendor_details['vendor_gst'])){ echo $vendor_details['vendor_gst'];} ?>"/>
					</div>
					<div class="clearfix"></div><br>

					<label class="col-md-3 col-md-offset-1">Vendor Address</label>
					<div class="col-md-5">
						<textarea type="text" class="form-control" name="vendor_address" id="vendor_address"  placeholder="Enter Address" required=""><?php if(!empty($vendor_details['vendor_address'])){ echo $vendor_details['vendor_address'];} ?></textarea>
					</div>
					<div class="clearfix"></div><br>
					<label class="col-md-3 col-md-offset-1">PinCode</label>
					<div class="col-md-5">
						<input type="text" class="form-control" placeholder="Enter Pincode" name="pincode" id="pincode" required="" value="<?php if(!empty($vendor_details['pincode'])){ echo $vendor_details['pincode'];} ?>" />
					</div>
					<div class="clearfix"></div><br>
					<label class="col-md-3 col-md-offset-1">State</label>
					<div class="col-md-5">
						<input type="text" class="form-control" readonly="" name="state" id="state" placeholder="State"  value="<?php if(!empty($vendor_details['state'])){ echo $vendor_details['state'];} ?>"/>
					</div>
					<div class="clearfix"></div><br>
					<label class="col-md-3 col-md-offset-1">District</label>
					<div class="col-md-5">
						<input type="text" class="form-control" id="district" readonly="" name="district" placeholder="District"  value="<?php if(!empty($vendor_details['district'])){ echo $vendor_details['district'];} ?>"/>
					</div>
					<div class="clearfix"></div><br>
					<label class="col-md-3 col-md-offset-1">City</label>
					<div class="col-md-5">
						<input type="text" class="form-control" id="city" readonly="" name="city" placeholder="City"  value="<?php if(!empty($vendor_details['city'])){ echo $vendor_details['city'];} ?>"/>
					</div>
					
					<div class="clearfix"></div><br>

					<label class="col-md-3 col-md-offset-1">Status</label>
					<div class="col-md-5">
						<select class="select form-control" name="active" id="active">
							<option value="1" <?php if(!empty($vendor_details) && !empty($vendor_details['active'])){ if($vendor_details['active']==1) {echo 'selected';}}  ?>>Active</option>
							<option value="1" <?php if(!empty($vendor_details) && !empty($vendor_details['active'])){ if($vendor_details['active']==0) {echo 'selected';}}  ?>>In Active</option>

						</select>
					</div>

					<div class="clearfix"></div><hr>
					<input type="hidden" value="<?php if(!empty($vendor_details['id_vendor'])){ echo $vendor_details['id_vendor'];} ?>"  id="id_vendor" name="id_vendor"> 

					<a class="btn btn-warning waves-effect simple-tooltip" href="<?php echo base_url('vendor_create_interior') ?>">Cancel</a>
					<button type="submit" class="pull-right btn btn-info waves-effect" id="btn-submit">Save</button>
					<div class="clearfix"></div>
				</div>
			</form>
		</div>
	</div>
<?php } ?>
<div class="clearfix"></div><br>
<div>
	<div class="col-md-9">
		<div id="count_1" class="text-info"></div>
	</div>
	<div class="col-md-2">
		<button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('branch_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
	</div>
	<a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" href="<?php echo base_url().'vendor_create_interior/NEW';?>"></a>
	<div class="clearfix"></div>
</div>
<table id="branch_data" class="branch_data table table-condensed table-full-width table-bordered table-hover display">
	<thead id="header" style="background: #2dbbc1;">
		<th>Vendor ID</th>
		<th>Vendor Name</th>
		<th>Address</th>
		<th>Email</th>
		<th>Contact</th>
		<th>Gst</th>
		<th>Status</th>
		<th style="width:15%;">Edit </th>
	</thead>

	<tbody class="data_1">
		<?php $i = 1;
		foreach ($vendor_data as $vendor) { ?>
			<tr>
				<td><?php echo $vendor['id_vendor']; ?></td>
				<td><?php echo $vendor['vendor_name']; ?></td>
				<td><?php echo $vendor['vendor_address']; ?></td>
				<td><?php echo $vendor['vendor_email']; ?></td>
				<td><?php echo $vendor['vendor_contact']; ?></td>
				<td><?php echo $vendor['vendor_gst']; ?></td>      
				<td><?php if ($vendor['active'] == 1) {
					echo 'Active';
				} else {
					echo 'In Active';
				} ?></td>
				<td style="text-align: center;">
					<a class="thumbnail btn-link waves-effect edit-btn" href="<?php echo base_url('vendor_create_interior/'.$vendor['id_vendor']); ?>" style="margin: 0" >
						<span class="mdi mdi-pen text-danger fa-lg"></span>
					</a>

				</td>
			</tr>
		<?php } ?>
	</tbody>

</table>
<script type="text/javascript">
	var base_url='<?php echo base_url();?>';
	// window.onscroll = function() {myFunction()};

	// var header = document.getElementById("header");
	// var sticky = header.offsetTop;
	// function myFunction() {
	// 	if (window.pageYOffset > sticky) {
	// 		header.classList.add("sticky");
	// 	} else {
	// 		header.classList.remove("sticky");
	// 	}
	// }
	$(document).ready(function () {
		$('#branch_data').DataTable();
		$(document).on("change", "#pincode", function (event) {
			
			var par = $(this).parent().parent();
			var pincode = $(this).val();
			$.ajax({
				url: "https://api.postalpincode.in/pincode/"+pincode,
				success:function(data)
				{
					$(data).each(function (index, item) {
						var result = item.Status;
						if(result == 'Success'){
							var postoffice = item.PostOffice;
							var post = postoffice[postoffice.length-1];
							$(par).find('#state').val(post.State);
							$(par).find('#district').val(post.District);
							$(par).find('#city').val(post.Block);
						}else{
							alert ('Invalid Pincode');
						}
					});
				}
			});
		});

		$("#vendor_create_form").validate({
			errorElement : 'span',
			submitHandler: function(form) {
				var formData = new FormData($('#vendor_create_form')[0]);
				formData.append('vendor_type',2);
				console.log(formData);
				$('#btn-submit').attr('disabled', true).html('Loading');
				$.ajax({
					url:base_url + 'vendor_create_store',
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
								window.location.href = base_url + 'vendor_create_interior';
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

	});
</script>
<?php include __DIR__ . '../../footer.php'; ?>