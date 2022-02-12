<?php include __DIR__ . '../../header.php'; ?>
<center><h3 style="margin-top: 0"><span class="mdi mdi-sitemap fa-lg"></span> Mobile And Broadband Details </h3></center><div class="clearfix"></div><hr>

<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
</a>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 800px; overflow: auto">
	<div id="purchase" style="padding: 20px 10px; margin: 0">
		<form id="branch_mbb_form">
			<div class="col-md-12 thumbnail">
				<label class="col-md-2">Branch</label>
				<div class="col-md-2">
					<select class=" form-control" name="branch_name" id="branch_name" required=""  <?php if(!empty($branch_details['branch_name'])){ echo 'readonly';}?>>
						<option value="">Select Branch</option>
						<?php foreach ($branch_data as $branch_rent) {
							if(!empty($branch_details['branch_id'])){
								if ($branch_rent['branch_id'] == $branch_details['branch_id']) { ?>
									<option selected="" value="<?php echo $branch_rent['branch_id']; ?>"><?php echo $branch_rent['branch_name']; ?></option>
								<?php }
							}else{ ?>
								<option value="<?php echo $branch_rent['branch_id']; ?>"><?php echo $branch_rent['branch_name']; ?></option>
							<?php } } ?>
						</select>
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
						<select class=" form-control" name="branch_partener_type" id="branch_partener_type" readonly>

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
					<label class="col-md-3 col-md-offset-1">Vendor Name</label>
					<div class="col-md-7">
						<input type="text" class="form-control" placeholder="Enter Name" name="mbb_provider" required="" value="<?php if(!empty($branch_mbb_details['mbb_provider'])){ echo $branch_mbb_details['mbb_provider'];} ?>" />
					</div>
					<div class="clearfix"></div><hr>
					<label class="col-md-3 col-md-offset-1">Consumer No</label>
					<div class="col-md-7">
						<input type="text" class="form-control" placeholder="Enter Consumer No" name="mbb_acname" required="" value="<?php if(!empty($branch_mbb_details['mbb_acname'])){ echo $branch_mbb_details['mbb_acname'];} ?>" />
					</div>
					<div class="clearfix"></div><hr>
					<label class="col-md-3 col-md-offset-1">Address</label>
					<div class="col-md-7">
						<textarea class="form-control" placeholder="Enter Address" name="mbb_accadd" required=""><?php if(!empty($branch_mbb_details['mbb_accadd'])){ echo $branch_mbb_details['mbb_accadd'];} ?></textarea>
					</div>
					<div class="clearfix"></div><hr>
					<label class="col-md-3 col-md-offset-1">Contact No</label>
					<div class="col-md-7">
						<input type="text" class="form-control" placeholder="Enter Contact" name="mbb_telno" required="" value="<?php if(!empty($branch_mbb_details['mbb_telno'])){ echo $branch_mbb_details['mbb_telno'];} ?>" />
					</div>
					<div class="clearfix"></div><hr>
					<label class="col-md-3 col-md-offset-1">Plan Validity</label>
					<div class="col-md-7">
						<select class="form-control" name="mbb_plandetails" id='mbb_plandetails'>
							<option value='Monthly' selected >Monthly</option>
							<option value='Quartraly'>Quartraly</option>
							<option value='Yearly'>Yearly</option>
						</select>
					</div>
					<div class="clearfix"></div><hr>
					<label class="col-md-3 col-md-offset-1">Plan Amount</label>
					<div class="col-md-7">
						<input type="text" class="form-control" placeholder="Plan Amount" name="mbb_planamount" id='mbb_planamount' required="" value="<?php if(!empty($branch_mbb_details['mbb_planamount'])){ echo $branch_mbb_details['mbb_planamount'];} ?>" />
					</div>
					<div class="clearfix"></div><hr>
					<label class="col-md-3 col-md-offset-1">Plan Amount Per Month</label>
					<div class="col-md-7">
						<input type="text" class="form-control" placeholder="Plan Amount Per Month" name="mbb_permonthamt" id='mbb_permonthamt' required="" value="<?php if(!empty($branch_mbb_details['mbb_permonthamt'])){ echo $branch_mbb_details['mbb_permonthamt'];} ?>" readonly/>
					</div>

					<div class="clearfix"></div><hr>
					<input type="hidden" value="<?php if(!empty($branch_details['branch_id'])){ echo $branch_details['branch_id'];} ?>"  id="branch_id" name="branch_id"> 
					<a class="btn btn-warning waves-effect simple-tooltip" href="<?php echo base_url('branch_mbb_details') ?>">Cancel</a>
					<button type="submit" id="btn-submit" class="pull-right btn btn-info waves-effect">Save</button>
					<div class="clearfix"></div>
					<div class="clearfix"></div><hr>

				</div>
			</form>
		</div>
	</div>
	<script type="text/javascript">
		$(document).ready(function () {
			$("#branch_mbb_form").validate({
				errorElement : 'span',
				submitHandler: function(form) {
					var formData = new FormData($('#branch_mbb_form')[0]);
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
									window.location.href = base_url + 'branch_mbb_details';
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
				window.location.href = base_url + 'branch_mbb_details/'+$(this).val();
			});
			$(document).on("focusout", "#mbb_planamount", function (event) {

				var permonamt=0;
				if($('#mbb_plandetails').val()=='Monthly'){
					permonamt=($('#mbb_planamount').val())/1;
				}else if($('#mbb_plandetails').val()=='Quartraly'){
					permonamt=($('#mbb_planamount').val())/3;
				}else if($('#mbb_plandetails').val()=='Yearly'){
					permonamt=($('#mbb_planamount').val())/12;
				}
				$('#mbb_permonthamt').val(((permonamt)).toFixed(2));
			});


		});
	</script>
	<?php include __DIR__ . '../../footer.php'; ?>