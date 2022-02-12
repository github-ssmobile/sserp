<?php include __DIR__ . '../../header.php'; ?>
<center><h3 style="margin-top: 0"><span class="mdi mdi-sitemap fa-lg"></span> Insurence Details </h3></center><div class="clearfix"></div><hr>

<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
</a>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 800px; overflow: auto">
	<div id="purchase" style="padding: 20px 10px; margin: 0">
		<form id="branch_insurence_form">
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
						<select class="form-control" name="branch_partener_type" id="branch_partener_type" readonly>

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
					<label class="col-md-2">Type Of Insurence</label>
					<div class="col-md-2">
						<select class="form-control" name="insurence_type" id="insurence_type">
							<option value="1">Office Insurence</option>
					<!-- 	<option value="2" >Vehicle Insurence</option>
						<option value="3">Godown Insurence</option>
						<option value="4">Employee Insurence</option> -->
					</select>
				</div>
				
				<div class="clearfix"></div><br>
				<label class="col-md-2">Start Date</label>
				<div class="col-md-2">
					<input type="date" class="form-control" placeholder="Enter Start Date" name="insurence_start_date" id="insurence_start_date" required="" value="<?php if(!empty($branch_insurence_details['insurence_start_date'])){ echo $branch_insurence_details['insurence_start_date'];} ?>" />
				</div>
				<label class="col-md-2">End Date</label>
				<div class="col-md-2">
					<input type="date" class="form-control" placeholder="Enter End Date" name="insurence_end_date" id="insurence_end_date" required="" value="<?php if(!empty($branch_insurence_details['insurence_end_date'])){ echo $branch_insurence_details['insurence_end_date'];} ?>" />
				</div>
				<label class="col-md-2">Policy Number</label>
				<div class="col-md-2">
					<input type="text" class="form-control" placeholder="Enter Policy Number" name="policy_no" id="policy_no" required="" value="<?php if(!empty($branch_insurence_details['policy_no'])){ echo $branch_insurence_details['policy_no'];} ?>" />
				</div>
				<div class="clearfix"></div><br>
				<label class="col-md-2">Furniture & Fixuters</label>
				<div class="col-md-2">
					<input type="text" class="form-control" placeholder="Enter Amount" name="furni_fixtures_amt" id="furni_fixtures_amt" required="" value="<?php if(!empty($branch_insurence_details['furni_fixtures_amt'])){ echo $branch_insurence_details['furni_fixtures_amt'];} ?>" />
				</div>
				<label class="col-md-2">Stock</label>
				<div class="col-md-2">
					<input type="text" class="form-control" placeholder="Enter Amount" name="stock_amt" id="stock_amt" required="" value="<?php if(!empty($branch_insurence_details['stock_amt'])){ echo $branch_insurence_details['stock_amt'];} ?>" />
				</div> 
				<label class="col-md-2">Electric Equiptment</label>
				<div class="col-md-2">
					<input type="text" class="form-control" placeholder="Enter Amount" name="elec_equip_amt" id="elec_equip_amt" required="" value="<?php if(!empty($branch_insurence_details['elec_equip_amt'])){ echo $branch_insurence_details['elec_equip_amt'];} ?>" />
				</div>
				<div class="clearfix"></div><br>
				<label class="col-md-2">Section 3A(Money in transit)</label>
				<div class="col-md-2">
					<input type="text" class="form-control" placeholder="Enter Amount" name="section_3A_amt" id="section_3A_amt" required="" value="<?php if(!empty($branch_insurence_details['section_3A_amt'])){ echo $branch_insurence_details['section_3A_amt'];} ?>" />
				</div>
				<label class="col-md-2">Section 3B (Money in till or counter during business hours)</label>
				<div class="col-md-2">
					<input type="text" class="form-control" placeholder="Enter Amount" name="section_3B_amt" id="section_3B_amt" required="" value="<?php if(!empty($branch_insurence_details['section_3B_amt'])){ echo $branch_insurence_details['section_3B_amt'];} ?>" />
				</div>
				
				<label class="col-md-2">Plate Glass</label>
				<div class="col-md-2">
					<input type="text" class="form-control" placeholder="Enter Amount" name="plate_glass_amt" id="plate_glass_amt" required="" value="<?php if(!empty($branch_insurence_details['plate_glass_amt'])){ echo $branch_insurence_details['plate_glass_amt'];} ?>" />
				</div>
				<div class="clearfix"></div><br>
				<label class="col-md-2">Neon & Glow Sign</label>
				<div class="col-md-2">
					<input type="text" class="form-control" placeholder="Enter Amount" name="neon_glowsign_amt" id="neon_glowsign_amt" required="" value="<?php if(!empty($branch_insurence_details['neon_glowsign_amt'])){ echo $branch_insurence_details['neon_glowsign_amt'];} ?>" />
				</div>
				<label class="col-md-2">Baggage Insurance</label>
				<div class="col-md-2">
					<input type="text" class="form-control" placeholder="Enter Amount" name="baggage_amt" id="baggage_amt" required="" value="<?php if(!empty($branch_insurence_details['baggage_amt'])){ echo $branch_insurence_details['baggage_amt'];} ?>" />
				</div>
				<label class="col-md-2">Sec.10A - Public Liablity</label>
				<div class="col-md-2">
					<input type="text" class="form-control" placeholder="Enter Amount" name="section_10A_amt" id="section_10A_amt" required="" value="<?php if(!empty($branch_insurence_details['section_10A_amt'])){ echo $branch_insurence_details['section_10A_amt'];} ?>" />
				</div>
				<div class="clearfix"></div><br>
				
				<label class="col-md-2">Total sum Insured</label>
				<div class="col-md-2">
					<input type="text" class="form-control" placeholder="Enter Amount" name="total_sum_insured" id="total_sum_insured" required="" value="<?php if(!empty($branch_insurence_details['total_sum_insured'])){ echo $branch_insurence_details['total_sum_insured'];} ?>" readonly/>
				</div>
				<label class="col-md-2">Premium </label>
				<div class="col-md-2">
					<input type="text" class="form-control" placeholder="Enter Amount" name="premium_amt" id="premium_amt" required="" value="<?php if(!empty($branch_insurence_details['premium_amt'])){ echo $branch_insurence_details['premium_amt'];} ?>" />
				</div>
				<label class="col-md-2">GST (Rs.)</label>
				<div class="col-md-2">
					<input type="text" class="form-control" placeholder="Enter Amount" name="gst_amt" id="gst_amt" required="" value="<?php if(!empty($branch_insurence_details['gst_amt'])){ echo $branch_insurence_details['gst_amt'];} ?>" />
				</div>
				<div class="clearfix"></div><br>
				<label class="col-md-2">Total Prem.(Rs.)</label>
				<div class="col-md-2">
					<input type="text" class="form-control" placeholder="Enter Amount" name="total_premium_amt" id="total_premium_amt" required="" value="<?php if(!empty($branch_insurence_details['total_premium_amt'])){ echo $branch_insurence_details['total_premium_amt'];} ?>" readonly />
				</div>
				<div class="clearfix"></div><br>

				<label class="col-md-2">Insurance Co.Name</label>

				<div class="col-md-2">
					<select class=" form-control" name="insurence_co_name" id="insurence_co_name" required="">
						<option value="">Select Vendor</option>
						<?php foreach ($vendor_data as $vendor) {
							if(!empty($branch_insurence_details['insurence_co_name'])){
								if ($vendor['id_vendor'] == $branch_insurence_details['insurence_co_name']) { ?>
									<option selected="" value="<?php echo $vendor['id_vendor']; ?>"><?php echo $vendor['vendor_name']; ?></option>
								<?php }
							} else { ?>
								<option value="<?php echo $vendor['id_vendor']; ?>"><?php echo $vendor['vendor_name']; ?></option>
							<?php } ?>
						<?php } ?>
					</select> 
				</div>

				<label class="col-md-2">Hypothicated With</label>
				<div class="col-md-2">
					
					<select class=" form-control" name="hypothicated_with" id="hypothicated_with" required="">
						<option value="">Select Bank</option>
						<?php foreach ($bank_data as $bank) {
							if(!empty($branch_insurence_details['hypothicated_with'])){
								if ($bank['id_bank'] == $branch_insurence_details['hypothicated_with']) { ?>
									<option selected="" value="<?php echo $bank['id_bank']; ?>"><?php echo $bank['bank_name']; ?></option>
								<?php }
							} else { ?>
								<option value="<?php echo $bank['id_bank']; ?>"><?php echo $bank['bank_name']; ?></option>
							<?php } ?>
						<?php } ?>
					</select> 
				
				</div>
				<label class="col-md-2">Insurence Document</label>
				<div class="col-md-2">
					<input type="file" class="form-control" placeholder="Select Document" name="insurence_doc" id="insurence_doc" />
				</div>
				<div class="clearfix"></div><br>
				
				
				<div class="clearfix"></div><hr>
				<input type="hidden" value="<?php if(!empty($branch_details['branch_id'])){ echo $branch_details['branch_id'];} ?>"  id="branch_id" name="branch_id"> 
				<a class="btn btn-warning waves-effect simple-tooltip" href="<?php echo base_url('branch_insurence_details') ?>">Cancel</a>
				<button type="buton" class="pull-right btn btn-info waves-effect">Save</button>
				<div class="clearfix"></div>

			</div>
		</form>
	</div>
</div>
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