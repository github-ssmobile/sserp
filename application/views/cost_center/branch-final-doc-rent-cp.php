<?php include __DIR__ . '../../header.php'; ?>
<center><h3 style="margin-top: 0"><span class="mdi mdi-sitemap fa-lg"></span> Branch Final Documents Upload </h3></center><div class="clearfix"></div><hr>

<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
</a>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 800px; overflow: auto">
	<div id="purchase" style="padding: 20px 10px; margin: 0">
		<form id="branch_shopact_gst_form" name="branch_rent_form" onsubmit="return CommonFunction(branch_rent_form)">
			<div class="col-md-10 thumbnail col-md-offset-1">
				<label class="col-md-2">Branch</label>
				<div class="col-md-2">
					<select class=" form-control" name="branch_name" id="branch_name" required="" <?php if(!empty($branch_details['branch_name'])){ echo 'readonly';}?>>
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
					<div class="clearfix"></div><br><hr>
					<h3 style="margin-top: 0"><span></span> Upload Documents</h3><hr>
					<div class="clearfix"></div><br>
					<label class="col-md-2">Rent Agreement</label>
					<div class="col-md-3">
						<input type="file" class="form-control" placeholder="Rent Document" name="rent_doc" id="rent_doc" />
					</div>
					<?php  
					if(!empty($branch_rent_details['rent_doc'])){
						?>
						<label class="col-md-1">
							<a href="<?php echo base_url().$branch_rent_details['rent_doc']; ?>" target="_blank"><button  type="button">View</button></a>
						</label>
					<?php }
					?>
					<label class="col-md-2">Channel Partner Agreement</label>
					<div class="col-md-3">
						<input type="file" class="form-control" placeholder="Rent Document" name="agreement_doc" id="agreement_doc" />
					</div>
					<?php 
					if(!empty($branch_cp_details['agreement_doc'])){
						?>
						<label class="col-md-1"><a href="<?php echo base_url().$branch_cp_details['agreement_doc']; ?>" target="_blank"><button type="button">View</button></a></label>
					<?php } 
					?>
					<div class="clearfix"></div><hr>
					
					<input type="hidden" value="<?php if(!empty($branch_details['branch_id'])){ echo $branch_details['branch_id'];} ?>"  id="branch_id" name="branch_id"> 
					
					<a class="btn btn-warning waves-effect simple-tooltip" href="<?php echo base_url('branch-shopact-gst') ?>">Cancel</a>
					<button type="submit" class="pull-right btn btn-info waves-effect">
						<?php if(!empty($branch_details['branch_partener_type'])){
							if($branch_details['branch_partener_type']=='1'){
								echo 'Save and Create Branch';
							}else{echo 'Save';} }else{echo 'Save';}?>
						</button>
						<div class="clearfix"></div>

					</div>
				</form>
			</div>
		</div>
		<script type="text/javascript">
			$(document).ready(function () {
				$("#branch_shopact_gst_form").validate({
					errorElement : 'span',
					submitHandler: function(form) {
						var formData = new FormData($('#branch_shopact_gst_form')[0]);
						$('#btn-submit').attr('disabled', true).html('Loading');
						$.ajax({
							url:base_url + 'branch-final-doc-rent-cp-store',
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
										window.location.href = base_url + 'branch-final-doc-rent-cp';
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
					window.location.href = base_url + 'branch-shopact-gst/'+$(this).val();
				});
			});
		</script>
		<?php include __DIR__ . '../../footer.php'; ?>