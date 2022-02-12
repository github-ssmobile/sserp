<?php include __DIR__ . '../../header.php'; ?>
<center><h3 style="margin-top: 0"><span class="mdi mdi-sitemap fa-lg"></span> Electricity Details </h3></center><div class="clearfix"></div><hr>

<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
</a>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 800px; overflow: auto">
	<div id="purchase" style="padding: 20px 10px; margin: 0">
		<form id="branch_electricity_form">
			<div class="col-md-12 thumbnail">
				<label class="col-md-2">Branch</label>
				<div class="col-md-2">
					<input type="text" class="form-control" placeholder="Enter Branch Name" name="branch_name" required="" value="<?php if(!empty($branch_details['branch_name'])){ echo $branch_details['branch_name'];} ?>" readonly />
				</div>
				<label class="col-md-2">Branch category</label>
				<div class="col-md-2">
					<select class=" form-control" name="branch_category" id="branch_category" required="" readonly>
						<?php foreach ($branch_category_data as $branch_category) {
							if(!empty($branch_details['branch_category'])){
								if ($branch_category->id_branch_category == $branch_details['branch_category']) { ?>
									<option selected="" value="<?php echo $branch_category->id_branch_category; ?>"><?php echo $branch_category->branch_category_name; ?></option>
								<?php }
							} ?>

						<?php } ?>
					</select>
				</div>
				<label class="col-md-2">Type Of Branch</label>
				<div class="col-md-2">
					<select name="branch_partener_type" id="branch_partener_type" readonly>

						<?php foreach ($partner_type_data as $ptype) {
							if(!empty($branch_details['branch_partener_type'])){
								if($ptype->id_partner_type == $branch_details['branch_partener_type']){ ?>
									<option selected="" value="<?php echo $ptype->id_partner_type; ?>"><?php echo $ptype->partner_type; ?></option>
								<?php }
							} 
						} ?>
					</select>
				</div>
				<div class="clearfix"></div><br>
				<label class="col-md-3 col-md-offset-1">Provider Name</label>
				<div class="col-md-7">
					<input type="text" class="form-control" placeholder="Enter Name" name="ele_provider" required="" value="<?php if(!empty($branch_ele_details['ele_provider'])){ echo $branch_ele_details['ele_provider'];} ?>" />
				</div>
				<div class="clearfix"></div><hr>
				<label class="col-md-3 col-md-offset-1">Customer No</label>
				<div class="col-md-7">
					<input type="text" class="form-control" placeholder="Enter Customer No" name="ele_custno" required="" value="<?php if(!empty($branch_ele_details['ele_custno'])){ echo $branch_ele_details['ele_custno'];} ?>" />
				</div>
				<div class="clearfix"></div><hr>
				<label class="col-md-3 col-md-offset-1">Owner Address</label>
				<div class="col-md-7">
					<textarea class="form-control" placeholder="Enter Address" name="ele_custadd" required=""><?php if(!empty($branch_ele_details['ele_custadd'])){ echo $branch_ele_details['ele_custadd'];} ?></textarea>
				</div>
				<div class="clearfix"></div><hr>
				<label class="col-md-3 col-md-offset-1">Contact No</label>
				<div class="col-md-7">
					<input type="text" class="form-control" placeholder="Enter Contact" name="ele_telno" required="" value="<?php if(!empty($branch_ele_details['ele_telno'])){ echo $branch_ele_details['ele_telno'];} ?>" />
				</div>
				<div class="clearfix"></div><hr>
				<label class="col-md-3 col-md-offset-1">Billing Unit</label>
				<div class="col-md-7">
					<input type="text" class="form-control" placeholder="Enter Billing Unit" name="ele_billingunit" required="" value="<?php if(!empty($branch_ele_details['ele_billingunit'])){ echo $branch_ele_details['ele_billingunit'];} ?>" />
				</div>
				<div class="clearfix"></div><hr>
				<label class="col-md-3 col-md-offset-1">Meter Number</label>
				<div class="col-md-7">
					<input type="text" class="form-control" placeholder="Meter No" name="ele_meterno" required="" value="<?php if(!empty($branch_ele_details['ele_meterno'])){ echo $branch_ele_details['ele_meterno'];} ?>" />
				</div>
				<div class="clearfix"></div><hr> 
				<label class="col-md-3 col-md-offset-1">Provider GST No</label>
				<div class="col-md-7">
					<input type="text" class="form-control" placeholder="GST No" name="ele_gstno" required="" value="<?php if(!empty($branch_ele_details['ele_gstno'])){ echo $branch_ele_details['ele_gstno'];} ?>" />
				</div>
				<div class="clearfix"></div><hr>
				<input type="hidden" value="<?php if(!empty($branch_details['branch_id'])){ echo $branch_details['branch_id'];} ?>"  id="branch_id" name="branch_id"> 
				<a class="btn btn-warning waves-effect simple-tooltip" href="<?php echo base_url('branch_basic_details') ?>">Cancel</a>
				<button type="submit" id="btn-submit" class="pull-right btn btn-info waves-effect">Save</button>
				<div class="clearfix"></div>
				<div class="clearfix"></div><hr>
				
			</div>
		</form>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		$("#branch_electricity_form").validate({
			errorElement : 'span',
			submitHandler: function(form) {
				var formData = new FormData($('#branch_electricity_form')[0]);
				console.log(formData);
				$('#btn-submit').attr('disabled', true).html('Loading');
				$.ajax({
					url:base_url + 'branch_mbb_details_store',
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