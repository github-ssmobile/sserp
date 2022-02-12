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
<center><h3 style="margin-top: 0"><span class="mdi mdi-sitemap fa-lg"></span> Branch Details </h3></center><div class="clearfix"></div><hr>
<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
</a>
<?php
if(!empty($branch_details)){ 
// print_r($this->session->userdata());die();
	?>
	<div class="thumbnail" style="padding: 0; margin: 0; min-height: 600px; overflow: auto">

		<div id="purchase" style="padding: 20px 10px; margin: 0">
			<form id="branch_basic_form" method="post" enctype="multipart/formdata">

				<div class="col-md-8 thumbnail col-md-offset-2">
					<label class="col-md-3 col-md-offset-1">Branch Category</label>
					<div class="col-md-6">
						<select class=" form-control" name="branch_category" id="branch_category" required="">
							<option value="">Select Category</option>
							<?php foreach ($branch_category_data as $branch_category) {
								if(!empty($branch_details['branch_category'])){
									if ($branch_category->id_branch_category == $branch_details['branch_category']) { ?>
										<option selected="" value="<?php echo $branch_category->id_branch_category; ?>"><?php echo $branch_category->branch_category_name; ?></option>
									<?php }
								} else { ?>
									<option value="<?php echo $branch_category->id_branch_category; ?>"><?php echo $branch_category->branch_category_name; ?></option>
								<?php } ?>
							<?php } ?>
						</select> 
					</div>
					<div class="clearfix"></div><br>
					<label class="col-md-3 col-md-offset-1">Branch Type</label>
					<div class="col-md-6">
						<select class="form-control" name="branch_partener_type" id="branch_partener_type">

							<?php foreach ($partner_type_data as $ptype) {
								if(!empty($branch_details['branch_partener_type'])){
									if($ptype->id_partner_type == $branch_details['branch_partener_type']){ ?>
										<option selected="" value="<?php echo $ptype->id_partner_type; ?>"><?php echo $ptype->partner_type; ?></option>
									<?php }
								}else{  ?>
									<option value="<?php echo $ptype->id_partner_type; ?>"><?php echo $ptype->partner_type; ?></option>
								<?php  }
							} ?>
						</select>
					</div>
					<div class="clearfix"></div><br>
					
					<label class="col-md-3 col-md-offset-1">Branch Name</label>
					<div class="col-md-6">
						<input type="text" class="form-control" placeholder="Enter Branch Name" name="branch_name" id="branch_name" required="" value="<?php if(!empty($branch_details['branch_name'])){ echo $branch_details['branch_name'];} ?>"  style="text-transform:uppercase"/>
					</div>
					<div class="clearfix"></div><br>
					<label class="col-md-3 col-md-offset-1">Branch Address</label>
					<div class="col-md-6">
						<textarea type="text" class="form-control" name="branch_address" id="branch_address"  placeholder="Enter Address" required=""><?php if(!empty($branch_details['branch_address'])){ echo $branch_details['branch_address'];} ?></textarea>
					</div>

					<div class="clearfix"></div><br>
					<label class="col-md-3 col-md-offset-1">Branch Landmark</label>
					<div class="col-md-6">
						<textarea type="text" class="form-control" name="branch_landmark" id="branch_landmark"  placeholder="Enter Landmark"><?php if(!empty($branch_details['branch_landmark'])){ echo $branch_details['branch_landmark'];} ?></textarea>
					</div>
					<div class="clearfix"></div><br>
					<label class="col-md-3 col-md-offset-1">PinCode</label>
					<div class="col-md-6">
						<input type="text" class="form-control" placeholder="Enter Pincode" name="branch_pincode" id="branch_pincode" required="" value="<?php if(!empty($branch_details['branch_pincode'])){ echo $branch_details['branch_pincode'];} ?>" />
					</div>
					<div class="clearfix"></div><br>
					<label class="col-md-3 col-md-offset-1">State</label>
					<div class="col-md-6">
						<input type="text" class="form-control" readonly="" name="branch_state" id="branch_state" placeholder="State"  value="<?php if(!empty($branch_details['branch_state'])){ echo $branch_details['branch_state'];} ?>"/>
					</div>
					<div class="clearfix"></div><br>
					<label class="col-md-3 col-md-offset-1">District</label>
					<div class="col-md-6">
						<input type="text" class="form-control" id="branch_district" readonly="" name="branch_district" placeholder="District"  value="<?php if(!empty($branch_details['branch_district'])){ echo $branch_details['branch_district'];} ?>"/>
					</div>
					<div class="clearfix"></div><br>
					<label class="col-md-3 col-md-offset-1">City</label>
					<div class="col-md-6">
						<input type="text" class="form-control" id="branch_city" readonly="" name="branch_city" placeholder="City"  value="<?php if(!empty($branch_details['branch_city'])){ echo $branch_details['branch_city'];} ?>"/>
					</div>
					<div class="clearfix"></div><br>
					<label class="col-md-3 col-md-offset-1">ABM</label>
					<div class="col-md-6">
						<select class=" form-control" name="branch_contact_person" id="branch_contact_person" required="">
							<option value="">Select Account Business Manager</option>
							<?php 
							foreach ($abm_data as $abm) {
								if(!empty($branch_details['branch_contact_person'])){
									if ($abm['id_users'] == $branch_details['branch_contact_person']) { ?>
										<option selected="" value="<?php echo $abm['id_users']; ?>"><?php echo $abm['user_name']; ?></option>
									<?php }
								} ?>
								
								<option value="<?php echo $abm['id_users']; ?>"><?php echo $abm['user_name']; ?></option>
							<?php } ?>
						</select> 
						
					</div>
					
					<div class="clearfix"></div><br>
					<label class="col-md-3 col-md-offset-1">Interior Vendor</label>
					<div class="col-md-6">
						<select class=" form-control" name="interior_vendor" id="interior_vendor" required="">
							<option value="">Select Vendor</option>
							<?php foreach ($vendor_data as $vendor) {
								if(!empty($branch_details['interior_vendor'])){
									if ($vendor['id_vendor'] == $branch_details['interior_vendor']) { ?>
										<option selected="" value="<?php echo $vendor['id_vendor']; ?>"><?php echo $vendor['vendor_name']; ?></option>
									<?php }
								} else { ?>
									<option value="<?php echo $vendor['id_vendor']; ?>"><?php echo $vendor['vendor_name']; ?></option>
								<?php } ?>
							<?php } ?>
						</select> 
					</div>
					<div class="clearfix"></div><br>
					<?php 
					if($this->session->userdata('level')==1){?>
						<label class="col-md-3 col-md-offset-1" style="display: none;">Status</label>
						<div class="col-md-6" style="display: none;">
							<select class="select form-control" name="branch_status" id="branch_status">
								<option value="1" <?php if(!empty($branch_details) && !empty($branch_details['branch_status'])){ if($branch_details['branch_status']==1) {echo 'selected';}}  ?>>Active</option>

							</select>
						</div>
					<?php } ?>
					<div class="clearfix"></div><hr>
					<input type="hidden" value="<?php if(!empty($branch_details['branch_id'])){ echo $branch_details['branch_id'];} ?>"  id="branch_id" name="branch_id"> 
					<input type="hidden" value="<?php if(!empty($branch_details['original_branch_id'])){ echo $branch_details['original_branch_id'];} ?>"  id="original_branch_id" name="original_branch_id"> 
					<a class="btn btn-warning waves-effect simple-tooltip" href="<?php echo base_url('branch_basic_details') ?>">Cancel</a>
					
					<button type="submit" class="pull-right btn btn-info waves-effect" id="btn-submit">	Save </button>
					
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
	<?php if($this->session->userdata('role_name')!='Legal'){?>
		<!--<a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" href="<?php //echo base_url().'branch_basic_details/NEW';?>"></a>-->
	<?php } ?>
	<div class="clearfix"></div>
</div>
<table id="branch_data" class="branch_data table table-condensed table-full-width table-bordered table-hover display">
	<thead  style="background: #2dbbc1;">
		<th>Branch ID</th>
		<th>Branch Name</th>
		<th>Address</th>
		<th>Pincode</th>
		<th>Contact Person</th>
		<th>Contact</th>
		<th>Branch Category</th>
		<th>Partner Type</th>
		<th>Status</th>
		
		<th style="width:15%;text-align: center;">Edit / View</th>
	</thead>

	<tbody class="data_1">
		<?php $i = 1;
		foreach ($branch_data as $branch) { ?>
			<tr>
				<td><?php echo $branch['branch_id']; ?></td>
				<td><?php echo $branch['branch_name']; ?></td>
				<td><?php echo $branch['branch_address']; ?></td>
				<td><?php echo $branch['branch_pincode']; ?></td>
				<td><?php echo $branch['branch_contact_person']; ?></td>
				<td><?php echo $branch['branch_contact']; ?></td>      
				<td><?php echo $branch['branch_category']; ?></td>                        
				<td><?php echo $branch['branch_category']; ?></td>  
				<td><?php if ($branch['branch_status'] == 1) {
					echo 'Active';
				} else {
					echo 'In Active';
				} ?></td>
				
				
				
				<td style="text-align: center;">
					<?php if($this->session->userdata('role_name')=='Admin'){ ?>
					<a class="thumbnail btn-link waves-effect edit-btn" href="<?php echo base_url('branch_basic_details/'.$branch['branch_id']); ?>" style="margin: 0" >
						<span class="mdi mdi-pen text-danger fa-lg"></span>
					</a>
				        <?php } ?>
					<a class="thumbnail btn-link waves-effect edit-btn" href="<?php echo base_url('branch_information/'.$branch['branch_id']); ?>" style="margin: 0" >
						<span> view</span>
					</a>

				</td>
			</tr>
		<?php } ?>
	</tbody>

</table>
<script type="text/javascript">
	var base_url='<?php echo base_url();?>';
	
	$(document).ready(function () {
		var table=$('#branch_data').DataTable();
		table
    .order( [0, 'desc' ] )
    .draw();
                
		$(document).on("change", "#branch_pincode", function (event) {
			var par = $(this).parent().parent();
			var branch_pincode = $(this).val();
			$.ajax({
				url: "https://api.postalpincode.in/pincode/"+branch_pincode,
				success:function(data)
				{
					$(data).each(function (index, item) {
						var result = item.Status;
						if(result == 'Success'){
							var postoffice = item.PostOffice;
							var post = postoffice[postoffice.length-1];
							$(par).find('#branch_state').val(post.State);
							$(par).find('#branch_district').val(post.District);
							$(par).find('#branch_city').val(post.Block);
						}else{
							alert ('Invalid Pincode');
						}
					});
				}
			});
		});

		$("#branch_basic_form").validate({
			errorElement : 'span',
			submitHandler: function(form) {
				var formData = new FormData($('#branch_basic_form')[0]);
				console.log(formData);
				$('#btn-submit').attr('disabled', true).html('Loading');
				$.ajax({
					url:base_url + 'branch_basic_details_store',
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
								window.location.href = base_url + 'branch_basic_details';
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