<?php include __DIR__ . '../../header.php'; ?>
<center><h3 style="margin-top: 0"><span class="mdi mdi-sitemap fa-lg"></span> Channel Partner Details </h3></center><div class="clearfix"></div><hr>

<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
</a>
<?php if(!empty($branch_cp_details)){ ?>
	<table id="branch_data" class="branch_data table table-condensed table-full-width table-bordered table-hover display">
		<thead id="header" style="background: #2dbbc1;">
			<th style="text-align:center;">Channel Partner ID</th>
			<th style="text-align:center;">Channel Partner Name</th>
			<th style="text-align:center;">Address</th>
			<th style="text-align:center;">Contact</th>
			<th style="text-align:center;">Email</th>
			<th style="text-align:center;">Deposit Amount</th>
			<th style="width:15%;text-align: center;">Edit </th>
		</thead>

		<tbody class="data_1">
			<?php 
			
				$i = 1;
				foreach ($branch_cp_details as $branch) { ?>
					<tr>
						<td style="text-align:center;"><?php echo $branch['id']; ?></td>
						<td><?php echo $branch['owner_name']; ?></td>
						<td><?php echo $branch['owner_address']; ?></td>
						<td><?php echo $branch['owner_occupation']; ?></td>
						<td><?php echo $branch['owner_email']; ?></td>      
						<td><?php echo $branch['deposit_amt']; ?></td>                        

						<td style="text-align: center;">
							<a class="thumbnail btn-link waves-effect edit-btn" href="<?php echo base_url('branch_cp_details/'.$branch['branch_id'].'/'.$branch['id']); ?>" style="margin: 0" >
								<span class="mdi mdi-pen text-danger fa-lg"></span>
							</a>
						</td>
					</tr>
				<?php } 
			
			?>
		</tbody>

	</table>
	<?php } ?>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 800px; overflow: auto">
    <?php if(!empty($branch_details['branch_id'])){ ?>
		<a href="<?php echo base_url()."branch_cp_details/".$branch_details['branch_id'].'/NEW';?>" class="btn btn-sm btn-info" style="float: right;margin-top: 4px;margin-right: 5px;">Add Channel Partner</a>
	<?php } ?>
	<div id="purchase" style="padding: 20px 10px; margin: 0">
		<form id="branch_rent_form" name="branch_rent_form" onsubmit="return CommonFunction(branch_rent_form)">
			<div class="col-md-10 thumbnail col-md-offset-1">
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
						<select class=" form-control" 	name="branch_partener_type" id="branch_partener_type" readonly>

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
                                        <?php if(!empty($cp_details)){?>
					<h3 style="margin-top: 0"><span></span> Channel Partner KYC Details</h3><hr>
					<div class="clearfix"></div><br>

					<label class="col-md-2">Name Of Channel Partner</label>
					<div class="col-md-4">
						<input type="text" class="form-control" placeholder="Name Of Channel Partner" name="owner_name" id="owner_name" required="" value="<?php if(!empty($cp_details[0]['owner_name'])){ echo $cp_details[0]['owner_name'];} ?>" />
					</div>
					<label class="col-md-2">Age Of Channel Partner</label>
					<div class="col-md-4">
						<input type="text" class="form-control NumberOnly" placeholder="Age Of Channel Partner" name="owner_age" id="owner_age" required="" value="<?php if(!empty($cp_details[0]['owner_age'])){ echo $cp_details[0]['owner_age'];} ?>" />
					</div>
					<div class="clearfix"></div><br>
					<label class="col-md-2">Occupation of Channel Partner</label>
					<div class="col-md-4">
						<input type="text" class="form-control" placeholder="Occupation of Channel Partner" name="owner_occupation" id="owner_occupation" required="" value="<?php if(!empty($cp_details[0]['owner_occupation'])){ echo $cp_details[0]['owner_occupation'];} ?>" />
					</div>
					<label class="col-md-2">Contact of Channel Partner</label>
					<div class="col-md-4">
						<input type="text" class="form-control IsContact" placeholder="Contact of Channel Partner" name="owner_occupation" id="owner_occupation" required="" value="<?php if(!empty($cp_details[0]['owner_occupation'])){ echo $cp_details[0]['owner_occupation'];} ?>" />
					</div>

					<div class="clearfix"></div><br>			

					<label class="col-md-2">PAN No Channel Partner</label>
					<div class="col-md-4">
						<input type="text" class="form-control IsPan" placeholder="PAN No Channel Partner" name="owner_pan" id="owner_pan" required="" value="<?php if(!empty($cp_details[0]['owner_pan'])){ echo $cp_details[0]['owner_pan'];} ?>" />
					</div>
					<label class="col-md-2">Adhar No Channel Partner</label>
					<div class="col-md-4">
						<input type="text" class="form-control IsAdhar" placeholder="Adhar No Channel Partner" name="owner_adhar" id="owner_adhar" required="" value="<?php if(!empty($cp_details[0]['owner_adhar'])){ echo $cp_details[0]['owner_adhar'];} ?>" />
					</div>
					<div class="clearfix"></div><br>			
					<label class="col-md-2">Email of Channel Partner</label>
					<div class="col-md-4">
						<input type="email" class="form-control" placeholder="Email of Channel Partner" name="owner_email" id="owner_email" required="" value="<?php if(!empty($cp_details[0]['owner_email'])){ echo $cp_details[0]['owner_email'];} ?>" />
					</div>
					<label class="col-md-2">Gst No (if any)</label>
					<div class="col-md-4">
						<input type="text" class="form-control IsGst" placeholder="Gst No" name="owner_gst" id="owner_gst" value="<?php if(!empty($cp_details[0]['owner_gst'])){ echo $cp_details[0]['owner_gst'];} ?>" />
					</div>

					<div class="clearfix"></div><br>
					<label class="col-md-2">Address of Channel Partner</label>
					<div class="col-md-4">
						<textarea type="text" class="form-control" placeholder="Address of Channel Partner" name="owner_address" id="owner_address" required=""><?php if(!empty($cp_details[0]['owner_address'])){ echo $cp_details[0]['owner_address'];} ?></textarea>
					</div>

					<div class="clearfix"></div><br><hr>
					<h3 style="margin-top: 0"><span></span> Agreement Details</h3><hr>
					<div class="clearfix"></div><br>
					<label class="col-md-2">Deposit Amount</label>
					<div class="col-md-4">
						<input type="text" class="form-control NumberOnly" placeholder="Deposit Amount" name="deposit_amt" id="deposit_amt" required="" value="<?php if(!empty($branch_cp_details[0]['deposit_amt'])){ echo $branch_cp_details[0]['deposit_amt'];} ?>" />
					</div>
					<label class="col-md-2">Agreement Document</label>
					<div class="col-md-4">
						<input type="file" class="form-control" placeholder="Rent Document" name="agreement_doc" name="agreement_doc" />
					</div>
                                        <div class="clearfix"></div><br><hr>
						<h3 style="margin-top: 0"><span></span> Upload Documents</h3><hr>
						<div class="clearfix"></div><br>
						<label class="col-md-2">Pan Card</label>
						<div class="col-md-3">
							<input type="file" class="form-control" placeholder="Rent Document" name="pan_doc" id="pan_doc" />
						</div>
						<?php  
							if(!empty($cp_details[0]['pan_doc'])){
								?>
								<label class="col-md-1">
									<a href="<?php echo base_url().$cp_details[0]['pan_doc']; ?>" target="_blank"><button  type="button">View</button></a>
								</label>
							<?php }
						 ?>
						<label class="col-md-2">Adhar Card</label>
						<div class="col-md-3">
							<input type="file" class="form-control" placeholder="Rent Document" name="adhar_doc" id="adhar_doc" />
						</div>
						<?php 
							if(!empty($cp_details[0]['adhar_doc'])){
								?>
								<label class="col-md-1"><a href="<?php echo base_url().$cp_details[0]['adhar_doc']; ?>" target="_blank"><button type="button">View</button></a></label>
							<?php } 
						 ?>
					<div class="clearfix"></div><br><hr>
					<h3 style="margin-top: 0"><span></span> Finance Details</h3><hr>
					<div class="clearfix"></div><br>
					<label class="col-md-2">Bank Name</label>
					<div class="col-md-4">
						<input type="text" class="form-control" placeholder="Bank Name" name="owner_bank_name" id="owner_bank_name" required="" value="<?php if(!empty($cp_details[0]['owner_bank_name'])){ echo $cp_details[0]['owner_bank_name'];} ?>" />
					</div>

					<label class="col-md-2">Bank Account No</label>
					<div class="col-md-4">
						<input type="text" class="form-control NumberOnly" placeholder="Bank Account No" name="owner_bank_accno" id="owner_bank_accno" required="" value="<?php if(!empty($cp_details[0]['owner_bank_accno'])){ echo $cp_details[0]['owner_bank_accno'];} ?>" />
					</div>
					<div class="clearfix"></div><br>
					<label class="col-md-2">Bank Ifsc Code</label>
					<div class="col-md-4">
						<input type="text" class="form-control" placeholder="Bank Ifsc Code" name="owner_bank_ifsc" id="owner_bank_ifsc" required="" value="<?php if(!empty($cp_details[0]['owner_bank_ifsc'])){ echo $cp_details[0]['owner_bank_ifsc'];} ?>" />
					</div>
					<div class="clearfix"></div><br>
					<div class="clearfix"></div><br><hr>
					<h3 style="margin-top: 0"><span></span> Deposit Details</h3><hr>
					<div class="clearfix"></div><br>
					<label class="col-md-2">Deposit Receive Amount</label>
					<div class="col-md-4">
						<input type="text" class="form-control" placeholder="Deposit Receive Amount" name="deposit_rec_amt" id="deposit_rec_amt" required="" value="<?php if(!empty($branch_cp_details[0]['deposit_rec_amt'])){ echo $branch_cp_details[0]['deposit_rec_amt'];} ?>" />
					</div>

					<label class="col-md-2">Deposit Receive Date</label>
					<div class="col-md-4">
						<input type="date" class="form-control" placeholder="Deposit Receive Date" name="deposit_rec_date" id="deposit_rec_date" required="" value="<?php if(!empty($branch_cp_details[0]['deposit_rec_date'])){ echo $branch_cp_details[0]['deposit_rec_date'];} ?>" />
					</div>
					<div class="clearfix"></div><br>
					<label class="col-md-2">Transaction Id</label>
					<div class="col-md-4">
						<input type="text" class="form-control" placeholder="Transaction Id" name="trans_id" id="trans_id" required="" value="<?php if(!empty($branch_cp_details[0]['trans_id'])){ echo $branch_cp_details[0]['trans_id'];} ?>" />
					</div>
					<div class="clearfix"></div><hr>
					<input type="hidden" value="<?php if(!empty($branch_details['branch_id'])){ echo $branch_details['branch_id'];} ?>"  id="branch_id" name="branch_id"> 
					<input type="hidden" value="<?php if(!empty($cp_details[0]['id'])){ echo $cp_details[0]['id'];} ?>"  id="id" name="id"> 
<?php } ?>
                                        <a class="btn btn-warning waves-effect simple-tooltip" href="<?php echo base_url('branch_cp_details') ?>">Cancel</a>
					<button type="buton" class="pull-right btn btn-info waves-effect">Save</button>
					<div class="clearfix"></div>

				</div>
			</form>
		</div>
	</div>
	
	<script type="text/javascript">
		$(document).ready(function () {

			$("#branch_rent_form").validate({
				errorElement : 'span',
				submitHandler: function(form) {
					var formData = new FormData($('#branch_rent_form')[0]);
					console.log(formData);
					$('#btn-submit').attr('disabled', true).html('Loading');
					$.ajax({
						url:base_url + 'branch_cp_details_store',
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
									window.location.href = base_url + 'branch_cp_details';
								},1000);
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
				window.location.href = base_url + 'branch_cp_details/'+$(this).val();
			});
		});
	</script>
	<?php include __DIR__ . '../../footer.php'; ?>