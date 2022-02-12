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
			<th style="text-align:center;">Owner Name</th>
			<th style="text-align:center;">Address</th>
			<th style="text-align:center;">Contact</th>
			<th style="text-align:center;">Email</th>
	 		<th style="text-align:center;">Deposit Amount</th>
			<th style="width:15%;text-align: center;">Edit </th>
		</thead>

		<tbody class="data_1">
			<?php
			$i = 1;
			foreach ($branch_rent_details as $rentow) { 

				?>
				<tr>
					<td style="text-align:center;"><?php echo $rentow['id']; ?></td>
					<td><?php echo $rentow['owner_name']; ?></td>
					<td><?php echo $rentow['owner_address']; ?></td>
					<td><?php echo $rentow['owner_contact']; ?></td>
					<td><?php echo $rentow['owner_email']; ?></td>      
					<td><?php echo $rentow['deposit_amt']; ?></td>                        

					<td style="text-align: center;">
						<a class="thumbnail btn-link waves-effect edit-btn" href="<?php echo base_url('branch_rent_details/'.$rentow['branch_id'].'/'.$rentow['id']); ?>" style="margin: 0" >
							<span class="mdi mdi-pen text-danger fa-lg"></span>
						</a>
					</td>
				</tr>
			<?php } 

			?>
		</tbody>

	</table>
<?php } 
if((!empty($rentow_details)) && ($this->session->userdata('role_name')=='Legal')){ ?>
	<div class="thumbnail" style="padding: 0; margin: 0; min-height: 800px; overflow: auto">
		<div id="purchase" style="padding: 20px 10px; margin: 0">
			<form id="branch_rent_form" name="branch_rent_form" onsubmit="return CommonFunction(branch_rent_form)">
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


						<h3 style="margin-top: 0"><span></span> Finance Details</h3><hr>
						<div class="clearfix"></div><br>
						<?php if($this->session->userdata('role_name')=='Legal'){ ?>
							<label class="col-md-2">Rent Document</label>
							<div class="col-md-4">
								<input type="file" class="form-control" placeholder="Rent Document" name="rent_doc" id="rent_doc" />

							</div>
							<?php if(!empty($rentow_details['rent_doc'])){
								?>
								<label class="col-md-2">View Document</label>
								<div class="col-md-3">

									<a href="<?php echo base_url().$rentow_details['rent_doc']; ?>" target="_blank"><button  type="button">View</button></a>


								</div>
							<?php } ?>

						<?php } ?>
                                                                <div class="clearfix"></div><br>
						<?php if($this->session->userdata('role_name')=='Legal'){ ?>
							<label class="col-md-2">Amenity Document</label>
							<div class="col-md-4">
								<input type="file" class="form-control" placeholder="Rent Document" name="aminiti_doc" id="aminiti_doc" />

							</div>
							<?php if(!empty($rentow_details['aminiti_doc'])){
								?>
								<label class="col-md-2">View Document</label>
								<div class="col-md-3">

									<a href="<?php echo base_url().$rentow_details['aminiti_doc']; ?>" target="_blank"><button  type="button">View</button></a>


								</div>
							<?php } ?>

						<?php } ?>
						<div class="clearfix"></div><br><hr>
						<h3 style="margin-top: 0"><span></span> Uploaded Documents</h3><hr>
						<div class="clearfix"></div><br>
						<label class="col-md-2">Pan Card</label>
						<div class="col-md-3">
							<!--<input type="file" class="form-control" placeholder="Rent Document" name="pan_doc" id="pan_doc" />-->
						</div>
						<?php if($this->session->userdata('role_name')=='Legal'){ 
							if(!empty($rentow_details['pan_doc'])){
								?>
								<label class="col-md-1">
									<a href="<?php echo base_url().$rentow_details['pan_doc']; ?>" target="_blank"><button  type="button">View</button></a>
								</label>
							<?php }
						} ?>
						<label class="col-md-2">Adhar Card</label>
						<div class="col-md-3">
							<!--<input type="file" class="form-control" placeholder="Rent Document" name="adhar_doc" id="adhar_doc" />-->
						</div>
						<?php if($this->session->userdata('role_name')=='Legal'){ 
							if(!empty($rentow_details['adhar_doc'])){
								?>
								<label class="col-md-1"><a href="<?php echo base_url().$rentow_details['adhar_doc']; ?>" target="_blank"><button type="button">View</button></a></label>
							<?php } 
						} ?>
						<div class="clearfix"></div><hr>
						<label class="col-md-2">Property Documents</label>
						<div class="col-md-3">
							<!--<input type="file" class="form-control" placeholder="Rent Document" name="property_doc" id="property_doc" />-->
						</div>
						<?php if($this->session->userdata('role_name')=='Legal'){
							if(!empty($rentow_details['property_doc'])){
								?>
								<label class="col-md-1"><a href="<?php echo base_url().$rentow_details['property_doc']; ?>" target="_blank"><button type="button">View</button></a></label>
							<?php } 
						}?>
						<label class="col-md-2">Electricity Bill</label>
						<div class="col-md-3">
							<!--<input type="file" class="form-control" placeholder="Rent Document" name="electricity_doc" id="electricity_doc" />-->
						</div>
						<?php if($this->session->userdata('role_name')=='Legal'){
							if(!empty($rentow_details['electricity_doc'])){
								?>
								<label class="col-md-1"><a href="<?php echo base_url().$rentow_details['electricity_doc']; ?>" target="_blank"><button type="button">View</button></a></label>
							<?php }
						} ?>
						<div class="clearfix"></div><hr>
                                                <label class="col-md-2">Other Documents</label>
									<div class="col-md-3">
										<!-- <input type="file" class="form-control" placeholder="Oher Document" name="other_doc" id="other_doc" /> -->
									</div>
									<?php if($this->session->userdata('role_name')=='Legal'){
										if(!empty($rentow_details['other_doc'])){
											?>
											<label class="col-md-1"><a href="<?php echo base_url().$rentow_details['other_doc']; ?>" target="_blank"><button type="button">View</button></a></label>
										<?php } 
									}?>
								<div class="clearfix"></div><hr>
						<input type="hidden" value="<?php if(!empty($branch_details['branch_id'])){ echo $branch_details['branch_id'];} ?>"  id="branch_id" name="branch_id"> 
						<input type="hidden" value="<?php if(!empty($rentow_details['id'])){ echo $rentow_details['id'];} ?>"  id="id" name="id"> 
                                                <input type="hidden" value="1"  id="legal_approve" name="legal_approve"> 

						<a class="btn btn-warning waves-effect simple-tooltip" href="<?php echo base_url('branch_rent_details/'.$this->uri->segment(2)) ?>">Cancel</a>
						<button type="submit" class="pull-right btn btn-info waves-effect">
							<?php if(!empty($branch_details['branch_partener_type'])){
								if($branch_details['branch_partener_type']=='1'){
									echo 'Approve and Create Branch';
								}else{echo 'Approve';} }else{echo 'Approve';}?>
							</button>
							<div class="clearfix"></div>

						</div>
					</form>
				</div>
			</div>
		<?php }else if($this->session->userdata('role_name')!='Legal'){ ?>


			<div class="thumbnail" style="padding: 0; margin: 0; min-height: 800px; overflow: auto">
                         <?php if(!empty($branch_details['branch_id'])){ ?>
					<a href="<?php echo base_url()."branch_rent_details/".$branch_details['branch_id'].'/NEW';?>" class="btn btn-sm btn-info" style="float: right;margin-top: 4px;margin-right: 5px;">Add Rent Owner</a>
				<?php } ?>
				<div id="purchase" style="padding: 20px 10px; margin: 0">
					<form id="branch_rent_form" name="branch_rent_form" onsubmit="return CommonFunction(branch_rent_form)">
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
                                                                <?php if(!empty($rentow_details)){?>
								<h3 style="margin-top: 0"><span></span> Owner KYC Details</h3><hr>
								<div class="clearfix"></div><br>

								<label class="col-md-2">Name Of Shop Owner</label>
								<div class="col-md-4">
									<input type="text" class="form-control" placeholder="Name Of Shop Owner" name="owner_name" id="owner_name" required="" value="<?php if(!empty($rentow_details['owner_name'])){ echo $rentow_details['owner_name'];} ?>" />
								</div>

								<label class="col-md-2">Age Of Shop Owner</label>
								<div class="col-md-4">
									<input type="text" class="form-control NumberOnly" placeholder="Age Of Shop Owner" name="owner_age" id="owner_age" required="" value="<?php if(!empty($rentow_details['owner_age'])){ echo $rentow_details['owner_age'];} ?>" />
								</div>
								<div class="clearfix"></div><br>
								<label class="col-md-2">Occupation of Shop Owner</label>
								<div class="col-md-4">
									<input type="text" class="form-control CharOnly" placeholder="Occupation of Shop Owner" name="owner_occupation" id="owner_occupation" required="" value="<?php if(!empty($rentow_details['owner_occupation'])){ echo $rentow_details['owner_occupation'];} ?>" />
								</div>

								<label class="col-md-2">Contact No Shop Owner</label>
								<div class="col-md-4">
									<input type="text" class="form-control IsContact" placeholder="Contact No Shop Owner" name="owner_contact" id="owner_contact" required="" value="<?php if(!empty($rentow_details['owner_contact'])){ echo $rentow_details['owner_contact'];} ?>" />
								</div>

								<div class="clearfix"></div><br>
								<label class="col-md-2">PAN No Shop Owner</label>
								<div class="col-md-4">
									<input type="text" class="form-control IsPan" placeholder="AAAAA0000A" name="owner_pan" id="owner_pan" required value="<?php if(!empty($rentow_details['owner_pan'])){ echo $rentow_details['owner_pan'];} ?>" />
								</div>

								<label class="col-md-2">Adhar No Shop Owner</label>
								<div class="col-md-4">
									<input type="text" class="form-control IsAdhar" placeholder="123412341234" name="owner_adhar" id="owner_adhar" required value="<?php if(!empty($rentow_details['owner_adhar'])){ echo $rentow_details['owner_adhar'];} ?>" />
								</div>
								<div class="clearfix"></div><br>
								<label class="col-md-2">Gst No (if any)</label>
								<div class="col-md-4">
									<input type="text" class="form-control IsGst" placeholder="11AAAAA0000A1Z5" name="owner_gst" id="owner_gst" value="<?php if(!empty($rentow_details['owner_gst'])){ echo $rentow_details['owner_gst'];} ?>" />
								</div>
								<label class="col-md-2">Email of Shop Owner</label>
								<div class="col-md-4">
									<input type="email" class="form-control" placeholder="Email of Shop Owner" name="owner_email" id="owner_email" required="" value="<?php if(!empty($rentow_details['owner_email'])){ echo $rentow_details['owner_email'];} ?>" />
								</div>
								<div class="clearfix"></div><br>			
								<label class="col-md-2">Address of Shop Owner</label>
								<div class="col-md-4">
									<textarea type="text" class="form-control" placeholder="Address of Shop Owner" name="owner_address" id="owner_address" required=""><?php if(!empty($rentow_details['owner_address'])){ echo $rentow_details['owner_address'];} ?></textarea>
								</div>
								<label class="col-md-2">Address of Shop</label>
								<div class="col-md-4">
									<textarea type="text" class="form-control" placeholder="Address of Shop" name="shop_address" id="shop_address" required="" readonly ><?php if(!empty($branch_details['branch_address'])){ echo $branch_details['branch_address'];}?></textarea>
								</div>
								<div class="clearfix"></div><br><hr>
								<h3 style="margin-top: 0"><span></span> Shop Agreement Details</h3><hr>
								<div class="clearfix"></div><br>

								<div id="shop-rent-details" class=""> 
									<label class="col-md-2">Shop Measurement (in sq.ft.)</label>
									<div class="col-md-4">
										<input type="text" class="form-control" placeholder="Shop Measurement" name="shop_measurement" id="shop_measurement" required="" value="<?php if(!empty($branch_rent_details[0]['shop_measurement'])){ echo $branch_rent_details[0]['shop_measurement'];} ?>"  <?php if(!empty($branch_rent_details[0]['shop_measurement'])){ echo 'readonly'; } ?>/>
									</div>
									<label class="col-md-2">Deposit Amount</label>
									<div class="col-md-4">
										<input type="text" class="form-control" placeholder="Deposit Amount" name="deposit_amt" id="deposit_amt" required="" value="<?php if(isset($branch_rent_details[0]['deposit_amt'])){ echo $branch_rent_details[0]['deposit_amt'];} ?>"  <?php if(!empty($branch_rent_details[0]['shop_measurement'])){ echo 'readonly'; } ?> />
									</div>
									<div class="clearfix"></div><br>	
									<label class="col-md-2">Rent Amount (in rs)</label>
									<div class="col-md-4">
										<input type="text" class="form-control" placeholder="Rent Amount" name="rent_amount" id="rent_amount"  required="" value="<?php if(isset($branch_rent_details[0]['rent_amount'])){ echo $branch_rent_details[0]['rent_amount'];} ?>"  <?php if(!empty($branch_rent_details[0]['shop_measurement'])){ echo 'readonly'; } ?> />
									</div>

									<label class="col-md-2">Rent Tenure (in Years)</label>
									<div class="col-md-4">
										<input type="text" class="form-control" placeholder="Rent Tenure" name="rent_tenure" id="rent_tenure" required="" value="<?php if(!empty($branch_rent_details[0]['rent_tenure'])){ echo $branch_rent_details[0]['rent_tenure'];} ?>"  <?php if(!empty($branch_rent_details[0]['shop_measurement'])){ echo 'readonly'; } ?>/>
									</div>
									<div class="clearfix"></div><br>
									<label class="col-md-2">Rent Increment Ratio</label>
									<div class="col-md-4" id="titles">
										<div id="titleAdd"></div>
                                                                               
										<!-- 	<input type="text" class="form-control" placeholder="Rent Increment Ratio" name="rent_incr_ratio" id="rent_incr_ratio" required="" value="<?php //if(!empty($rentow_details['rent_incr_ratio'])){ echo $rentow_details['rent_incr_ratio'];} ?>" /> -->
									</div>
									<label class="col-md-2">Rent Free Period (In days)</label>
									<div class="col-md-4">
										<input type="text" class="form-control" placeholder="Rent Free Period" name="rent_free_period" name="rent_free_period"  value="<?php if(isset($branch_rent_details[0]['rent_free_period'])){ echo $branch_rent_details[0]['rent_free_period'];} ?>"  <?php if(!empty($branch_rent_details[0]['shop_measurement'])){ echo 'readonly'; } ?>/>
									</div>
									<div class="clearfix"></div><br>
									<label class="col-md-2">Rent Free Period Start Date</label>
									<div class="col-md-4">
										<input type="date" class="form-control" name="rent_free_start_date" id="rent_free_start_date"  value="<?php if(!empty($branch_rent_details[0]['rent_free_start_date'])){ echo $branch_rent_details[0]['rent_free_start_date'];} ?>"  <?php if(!empty($branch_rent_details[0]['shop_measurement'])){ echo 'readonly'; } ?>/>
									</div>
									<label class="col-md-2">Rent Free Period End Date</label>
									<div class="col-md-4">
										<input type="date" class="form-control" name="rent_free_end_date" id="rent_free_end_date"  value="<?php if(!empty($branch_rent_details[0]['rent_free_end_date'])){ echo $branch_rent_details[0]['rent_free_end_date'];} ?>"  <?php if(!empty($branch_rent_details[0]['shop_measurement'])){ echo 'readonly'; } ?>/>
									</div>
									<div class="clearfix"></div><br>
									<label class="col-md-2">Rent Start Date</label>
									<div class="col-md-4">
										<input type="date" class="form-control" name="rent_start_date" id="rent_start_date"  value="<?php if(!empty($branch_rent_details[0]['rent_start_date'])){ echo $branch_rent_details[0]['rent_start_date'];} ?>"  <?php if(!empty($branch_rent_details[0]['shop_measurement'])){ echo 'readonly'; } ?>/>
									</div>
									<label class="col-md-2">Rent End Date</label>
									<div class="col-md-4">
										<input type="date" class="form-control" name="rent_end_date" id="rent_end_date"  value="<?php if(!empty($branch_rent_details[0]['rent_end_date'])){ echo $branch_rent_details[0]['rent_end_date'];} ?>" readonly />
									</div>
									<div class="clearfix"></div><br>
									<label class="col-md-2">Lock In Period (In Months)</label>
									<div class="col-md-4">
										<input type="text" class="form-control" placeholder="Lock In Period" name="lock_in_period" id="lock_in_period"  value="<?php if(isset($branch_rent_details[0]['lock_in_period'])){ echo $branch_rent_details[0]['lock_in_period'];} ?>"  <?php if(!empty($branch_rent_details[0]['shop_measurement'])){ echo 'readonly'; } ?>/>
									</div>
									<label class="col-md-2">Termination Notice Period (In Months)</label>
									<div class="col-md-4">
										<input type="text" class="form-control" placeholder="Termination Notice Period" name="termination_notice_period" id="termination_notice_period"  value="<?php if(!empty($branch_rent_details[0]['termination_notice_period'])){ echo $branch_rent_details[0]['termination_notice_period'];} ?>"  <?php if(!empty($branch_rent_details[0]['shop_measurement'])){ echo 'readonly'; } ?>/>
									</div>


									<div class="clearfix"></div><br><hr>

								</div>
								<h3 style="margin-top: 0"><span></span> Finance Details</h3><hr>
								<div class="clearfix"></div><br>
								<label class="col-md-2">Bank Name</label>
								<div class="col-md-4">
									<input type="text" class="form-control" placeholder="Bank Name" name="owner_bank_name" id="owner_bank_name" required="" value="<?php if(!empty($branch_rent_details[0]['owner_bank_name'])){ echo $branch_rent_details[0]['owner_bank_name'];} ?>" />
								</div>
								<label class="col-md-2">Bank Account No</label>
								<div class="col-md-4">
									<input type="text" class="form-control" placeholder="Bank Account No" name="owner_bank_accno" id="owner_bank_accno" required="" value="<?php if(!empty($branch_rent_details[0]['owner_bank_accno'])){ echo $branch_rent_details[0]['owner_bank_accno'];} ?>" />
								</div>
								<div class="clearfix"></div><br>
								<label class="col-md-2">Bank Ifsc Code</label>
								<div class="col-md-4">
									<input type="text" class="form-control" placeholder="Bank Ifsc Code" name="owner_bank_ifsc" id="owner_bank_ifsc" required="" value="<?php if(!empty($branch_rent_details[0]['owner_bank_ifsc'])){ echo $branch_rent_details[0]['owner_bank_ifsc'];} ?>" />
								</div>
								<div class="clearfix"></div><br>
                                                               
									<label class="col-md-2">Cheque Document</label>
									<div class="col-md-4">
										<input type="file" class="form-control" placeholder="Cheque Document" name="cheque_doc" id="cheque_doc" />
									</div>
									<?php if($this->session->userdata('role_name')=='Legal'){ 
										if(!empty($rentow_details['cheque_doc'])){
											?>
											<label class="col-md-1">
												<a href="<?php echo base_url().$rentow_details['cheque_doc']; ?>" target="_blank"><button  type="button">View</button></a>
											</label>
										<?php }
									} ?>
									
									<label class="col-md-2">Passbook Document</label>
									<div class="col-md-4">
										<input type="file" class="form-control" placeholder="Passbook Document" name="passbook_doc" id="passbook_doc" />
									</div>
									<?php if($this->session->userdata('role_name')=='Legal'){ 
										if(!empty($rentow_details['passbook_doc'])){
											?>
											<label class="col-md-1">
												<a href="<?php echo base_url().$rentow_details['passbook_doc']; ?>" target="_blank"><button  type="button">View</button></a>
											</label>
										<?php }
									} ?>
									</div>
									
									
								<div class="clearfix"></div><br><hr>
								<h3 style="margin-top: 0"><span></span> Electricity Details</h3><hr>
								<div class="clearfix"></div><br>
								<label class="col-md-2">Provider Name</label>
								<div class="col-md-4">
									<input type="text" class="form-control" placeholder="Enter Name" name="ele_provider" id="ele_provider" required="" value="<?php if(!empty($branch_ele_details['ele_provider'])){ echo $branch_ele_details['ele_provider'];} ?>"  <?php if(!empty($branch_ele_details['ele_provider'])){ echo 'readonly'; } ?>/>
								</div>
								<label class="col-md-2">Consumer No</label>
								<div class="col-md-4">
									<input type="text" class="form-control NumberOnly" placeholder="Enter Customer No" name="ele_custno" id="ele_custno" required="" value="<?php if(!empty($branch_ele_details['ele_custno'])){ echo $branch_ele_details['ele_custno'];} ?>" <?php if(!empty($branch_ele_details['ele_provider'])){ echo 'readonly'; } ?>/>
								</div>
								<div class="clearfix"></div><hr>

								<label class="col-md-2">Billing Unit</label>
								<div class="col-md-4">
									<input type="text" class="form-control NumberOnly" placeholder="Enter Billing Unit" name="ele_billingunit" id="ele_billingunit" required="" value="<?php if(!empty($branch_ele_details['ele_billingunit'])){ echo $branch_ele_details['ele_billingunit'];} ?>" <?php if(!empty($branch_ele_details['ele_provider'])){ echo 'readonly'; } ?> />
								</div>

								<label class="col-md-2">Meter Number</label>
								<div class="col-md-4">
									<input type="text" class="form-control NumberOnly" placeholder="Meter No" name="ele_meterno" id="ele_meterno" required="" value="<?php if(!empty($branch_ele_details['ele_meterno'])){ echo $branch_ele_details['ele_meterno'];} ?>" <?php if(!empty($branch_ele_details['ele_provider'])){ echo 'readonly'; } ?>/>
								</div>
								<div class="clearfix"></div><hr>

								<label class="col-md-2">Last Billing Count</label>
								<div class="col-md-4">
									<input type="text" class="form-control NumberOnly" placeholder="Last Billing Unit" name="ele_last_billing_unit" id="ele_last_billing_unit" required="" value="<?php if(!empty($branch_ele_details['ele_last_billing_unit'])){ echo $branch_ele_details['ele_last_billing_unit'];} ?>" <?php if(!empty($branch_ele_details['ele_provider'])){ echo 'readonly'; } ?>/>
								</div>

								<label class="col-md-2">Last Billing Month</label>
								<div class="col-md-4">
									<input type="date" class="form-control" placeholder="Last Billing Month" name="ele_las_billing_month" id="ele_las_billing_month" value="<?php if(!empty($branch_ele_details['ele_las_billing_month'])){ echo $branch_ele_details['ele_las_billing_month'];} ?>" <?php if(!empty($branch_ele_details['ele_provider'])){ echo 'readonly'; } ?> />
								</div>

								<div class="clearfix"></div><br><hr>
								<h3 style="margin-top: 0"><span></span> Upload Documents</h3><hr>
								<div class="clearfix"></div><br>
								<label class="col-md-2">Pan Card</label>
								<div class="col-md-3">
									<input type="file" class="form-control" placeholder="Rent Document" name="pan_doc" id="pan_doc" />
								</div>
								<?php if($this->session->userdata('role_name')=='Legal'){ 
									if(!empty($rentow_details['pan_doc'])){
										?>
										<label class="col-md-1">
											<a href="<?php echo base_url().$rentow_details['pan_doc']; ?>" target="_blank"><button  type="button">View</button></a>
										</label>
									<?php }
								} ?>
								<label class="col-md-2">Adhar Card</label>
								<div class="col-md-3">
									<input type="file" class="form-control" placeholder="Rent Document" name="adhar_doc" id="adhar_doc" />
								</div>
								<?php if($this->session->userdata('role_name')=='Legal'){ 
									if(!empty($rentow_details['adhar_doc'])){
										?>
										<label class="col-md-1"><a href="<?php echo base_url().$rentow_details['adhar_doc']; ?>" target="_blank"><button type="button">View</button></a></label>
									<?php } 
								} ?>
								<div class="clearfix"></div><hr>
								<label class="col-md-2">Property Documents</label>
								<div class="col-md-3">
									<input type="file" class="form-control" placeholder="Rent Document" name="property_doc" id="property_doc" />
								</div>
								<?php if($this->session->userdata('role_name')=='Legal'){
									if(!empty($rentow_details['property_doc'])){
										?>
										<label class="col-md-1"><a href="<?php echo base_url().$rentow_details['property_doc']; ?>" target="_blank"><button type="button">View</button></a></label>
									<?php } 
								}?>
								<label class="col-md-2">Electricity Bill</label>
								<div class="col-md-3">
									<input type="file" class="form-control" placeholder="Rent Document" name="electricity_doc" id="electricity_doc" />
								</div>
								<?php if($this->session->userdata('role_name')=='Legal'){
									if(!empty($rentow_details['electricity_doc'])){
										?>
										<label class="col-md-1"><a href="<?php echo base_url().$rentow_details['electricity_doc']; ?>" target="_blank"><button type="button">View</button></a></label>
									<?php }
								} ?>
								<div class="clearfix"></div><hr>
                                                                <label class="col-md-2">Other Documents</label>
									<div class="col-md-3">
										<input type="file" class="form-control" placeholder="Oher Document" name="other_doc" id="other_doc" />
									</div>
									<?php if($this->session->userdata('role_name')=='Legal'){
										if(!empty($rentow_details['other_doc'])){
											?>
											<label class="col-md-1"><a href="<?php echo base_url().$rentow_details['other_doc']; ?>" target="_blank"><button type="button">View</button></a></label>
										<?php } 
									}?>
                                                                <label class="col-md-2">Rent Document</label>
							
							<?php if(!empty($rentow_details['rent_doc'])){
								?>
								
								<div class="col-md-3">

									<a href="<?php echo base_url().$rentow_details['rent_doc']; ?>" target="_blank"><button  type="button">View</button></a>


								</div>
							<?php } ?>

						
                                                                <div class="clearfix"></div><br>
								<input type="hidden" value="<?php if(!empty($branch_details['branch_id'])){ echo $branch_details['branch_id'];} ?>"  id="branch_id" name="branch_id"> 
								<input type="hidden" value="<?php if(!empty($rentow_details['id'])){ echo $rentow_details['id'];} ?>"  id="id" name="id"> 
                                                                <?php } ?>
                                                                <a class="btn btn-warning waves-effect simple-tooltip" href="<?php echo base_url('branch_rent_details') ?>">Cancel</a>
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
				<?php }
				?>
				<script type="text/javascript">
                                    base_url='<?php echo base_url();?>';
					$(document).ready(function () {

						$("#branch_rent_form").validate({
							errorElement : 'span',
							submitHandler: function(form) {
                                                            var user_role='<?php echo $this->session->userdata('role_name');?>';
                                                            
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
                                                                                        if(user_role=='Legal'){
                                                                                            setTimeout(function(){
												window.location.href = base_url + 'branch_rent_details_legal';
											},2000);
                                                                                        }else{
											setTimeout(function(){
												window.location.href = base_url + 'branch_rent_details';
											},2000);
                                                                                }
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

var inc_rat='<?php if(isset($branch_rent_details[0]['rent_incr_ratio'])){ echo $branch_rent_details[0]['rent_incr_ratio'];} ?>';
if(inc_rat!=''){
     
       	var container = $('<div class="controls" id="asd">');
    var option=inc_rat.split(',');
  
    for(i=0;i<option.length;i++) 
							{
                                                             

								container.append('<input style="display: block;" class="form-control" type=text id="rent_incr_ratio" class="span3 input-left-top-margins" name="rent_incr_ratio[]" id="Description' + (i+1) +'"' + 'value="' + option[i] + '" readonly/>');
							}
							$('#titleAdd').after(container);

    }
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