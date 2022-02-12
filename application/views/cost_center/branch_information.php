<?php include __DIR__ . '../../header.php'; ?>
<center><h3 style="margin-top: 0"><span class="mdi mdi-sitemap fa-lg"></span> Branch Information </h3></center><div class="clearfix"></div><hr>
<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
	<span class="ripple pinkBg"></span>
</a>

<div class="clearfix"></div><br>
<div>
	<div class="col-md-11">
		<div id="count_1" class="text-info"></div>
	</div>
	<a class="btn btn-warning waves-effect simple-tooltip" href="<?php echo base_url('branch_basic_details') ?>">Back</a>
	<div class="clearfix"></div>
</div>
<?php if(!empty($branch_details)){?>
	<h3 style="margin-top: 0"> Branch Details </h3><div class="clearfix"></div><hr>
	<table id="branch_data" class="branch_data table table-condensed table-full-width table-bordered table-responsive table-hover display">
		<thead>
			<th>Branch ID</th>
			<th>Branch Name</th>
			<th>Address</th>
			<th>Pincode</th>
			

		</thead>
		<tbody class="data_1">
			<tr>
				<td><?php echo $branch_details['branch_id']; ?></td>
                                <td><?php echo ucwords($branch_details['branch_name']); ?></td>
				<td><?php echo $branch_details['branch_address']; ?></td>
				<td><?php echo $branch_details['branch_pincode']; ?></td>
		  
			</tr>
		</tbody>
	</table>
<?php } ?>

<?php if(!empty($rent_details)){?>
	<h3 style="margin-top: 0"> Branch Rent Details </h3><div class="clearfix"></div><hr>
	<table id="branch_data" class="branch_data table table-condensed table-full-width table-bordered table-responsive table-hover display">
		<thead>
			
			<th>Owner Name</th>
			<th>Owner Age</th>
			<th>Owner Pan/Adhar</th>
			<th>Owner Email</th>
			<th>Owner Address</th>
			<th>Deposit Amount</th>
			<th>Rent Amount</th>
			<th>Rent Tenure</th>
			<th>Rent Start Date</th>
			<th>Rent End Date</th>
			<th>Owner Bank Name</th>
			<th>Account No</th>
			<th>IFSC Code</th>
                        
			<th>View Document</th>

		</thead>
		<tbody class="data_1">
			<?php foreach ($rent_details as $rent) { ?>
			<tr>
				<td><?php echo $rent['owner_name']; ?></td>
				<td><?php echo $rent['owner_age']; ?></td>
				<td><?php echo $rent['owner_pan'].'/'.$rent['owner_adhar']; ?></td>
				<td><?php echo $rent['owner_email']; ?></td>
				<td><?php echo $rent['owner_address']; ?></td>
				<td><?php echo $rent['deposit_amt']; ?></td>      
				<td><?php echo $rent['rent_amount']; ?></td>                        
				<td><?php echo $rent['rent_tenure']; ?></td>  
				<td><?php echo $rent['rent_start_date']; ?></td>  
				<td><?php echo $rent['rent_end_date']; ?></td>  
				<td><?php echo $rent['owner_bank_name']; ?></td>  
				<td><?php echo $rent['owner_bank_accno']; ?></td>  
				<td><?php echo $rent['owner_bank_ifsc']; ?></td> 
                                <?php if(!empty($rent['rent_doc'])){?>
				<td><a class="btn btn-sm btn-warning waves-effect simple-tooltip" href="<?php echo base_url().$rent['rent_doc']; ?>" target="_blank">View Agreement</a></td>  
                                <?php }else{ ?>
                                <td><a class="btn btn-sm btn-warning waves-effect simple-tooltip" href="#">Not Uploaded</a></td>  
                                <?php } ?>
			</tr>
		<?php } ?>
		</tbody>
	</table>
        <h3 style="margin-top: 0"> Branch Rent Deposit Details </h3><div class="clearfix"></div><hr>
	<table id="branch_data" class="branch_data table table-condensed table-full-width table-bordered table-responsive table-hover display">
		<thead>
			
			<th>Deposit Amount</th>
			<th>Deposit Paid Amount</th>
			<th>Deposit Paid Date</th>
			<th>Deposit Transaction Id</th>
			<th>Deposit Remark</th>
			<th>Deposit Status</th>
		</thead>
		<tbody class="data_1">
			
			<tr>

				<td><?php echo $rent_details[0]['deposit_amt']; ?></td>      
				<td><?php echo $rent_details[0]['deposit_paid_amt']; ?></td>      
				<td><?php echo $rent_details[0]['deposit_paid_date']; ?></td>  
				<td><?php echo $rent_details[0]['trans_id']; ?></td>  
				<td><?php echo $rent_details[0]['remark']; ?></td>  
				<?php if(($rent_details[0]['deposit_status']==1)){?>
					<td class="btn btn-success">Deposit Paid</td>  
				<?php }else{ ?>
					<td class="btn btn-danger">Deposit Not Paid</td>  
				<?php } ?>
			</tr>
			
		</tbody>
	</table>
<?php } ?>
        
<?php if(!empty($branch_details['shopact_doc']) || !empty($branch_details['gstcert_doc'])){?>
	<h3 style="margin-top: 0"> Branch Shopact and Gst Certificate Details </h3><div class="clearfix"></div><hr>
	<table id="branch_data" class="branch_data table table-condensed table-full-width table-bordered table-responsive table-hover display">
		<thead>
			
			<th>Branch Name</th>
			<th style="text-align: center;">Shopact Document</th>
			<th style="text-align: center;">Gst Certificate Document</th>
		</thead>
		<tbody class="data_1">
			<tr>
				<td><?php echo $branch_details['branch_name']; ?></td>
				<td style="text-align: center;"><a class="btn btn-sm btn-warning waves-effect simple-tooltip" href="<?php echo base_url().$branch_details['shopact_doc']; ?>" target="_blank">View Agreement</a></td>  
				<td style="text-align: center;"><a class="btn btn-sm btn-warning waves-effect simple-tooltip" href="<?php echo base_url().$branch_details['gstcert_doc']; ?>" target="_blank">View Agreement</a></td>  

			</tr>
		</tbody>
	</table>
<?php } ?>        

<?php if(!empty($branch_cp_details)){?>
	<h3 style="margin-top: 0"> Branch Channel Partner Details </h3><div class="clearfix"></div><hr>
	<table id="branch_data" class="branch_data table table-condensed table-full-width table-bordered table-responsive table-hover display">
		<thead>
			<th>Owner Name</th>
			<th>Owner Age</th>
			<th>Owner Pan/Adhar</th>
			<th>Owner Email</th>
			<th>Owner Address</th>
			<th>Deposit Amount</th>
			<th>Owner Bank Name</th>
			<th>Account No</th>
			<th>IFSC Code</th>
                        <th>Deposit Status</th>
			<th>View Document</th>

		</thead>
		<tbody class="data_1">
			<?php foreach ($branch_cp_details as $value) { ?>
				<tr>
					<td><?php echo $value['owner_name']; ?></td>
					<td><?php echo $value['owner_age']; ?></td>
					<td><?php echo $value['owner_pan'].'/'.$value['owner_adhar']; ?></td>
					<td><?php echo $value['owner_email']; ?></td>
					<td><?php echo $value['owner_address']; ?></td>
					<td><?php echo $value['deposit_amt']; ?></td>      
					<td><?php echo $value['owner_bank_name']; ?></td>  
					<td><?php echo $value['owner_bank_accno']; ?></td>  
					<td><?php echo $value['owner_bank_ifsc']; ?></td>  
                                        <td><?php if($value['receive_status']==1){ echo 'Approve';}else{ echo "Pending";} ?></td> 
                                        <?php if($value['agreement_doc']){?>
					<td><a class="btn btn-sm btn-warning waves-effect simple-tooltip" href="<?php echo base_url().$value['agreement_doc']; ?>" target="_blank">View Agreement</a></td>  
                                         <?php }else{ ?>
                                <td><a class="btn btn-sm btn-warning waves-effect simple-tooltip" href="#">Not Uploaded</a></td>  
                                <?php } ?>
				</tr>
			<?php } ?>
		</tbody>
	</table>
     <h3 style="margin-top: 0"> Branch Channel Partner Deposit Details </h3><div class="clearfix"></div><hr>
	<table id="branch_data" class="branch_data table table-condensed table-full-width table-bordered table-responsive table-hover display">
		<thead>
			
			<th>Deposit Amount</th>
			<th>Deposit Receive Amount</th>
			<th>Deposit Receive Date</th>
			<th>Deposit Transaction Id</th>
			<th>Deposit Remark</th>
			<th>Deposit Status</th>
		</thead>
		<tbody class="data_1">
			
			<tr>

				<td><?php echo $branch_cp_details[0]['deposit_amt']; ?></td>      
				<td><?php echo $branch_cp_details[0]['deposit_rec_amt']; ?></td>      
				<td><?php echo $branch_cp_details[0]['deposit_rec_date']; ?></td>  
				<td><?php echo $branch_cp_details[0]['trans_id']; ?></td>  
				<td><?php echo $branch_cp_details[0]['remark']; ?></td>  
				<?php if(($branch_cp_details[0]['receive_status']==1)){?>
					<td class="btn btn-success">Deposit Received</td>  
				<?php }else{ ?>
					<td class="btn btn-danger">Deposit Not Received</td>  
				<?php } ?>
			</tr>
			
		</tbody>
	</table>
<?php } ?>

<?php if(!empty($branch_insurence_details)){?>
	<h3 style="margin-top: 0"> Branch Insurence Details </h3><div class="clearfix"></div><hr>
	<table id="branch_data" class="branch_data table table-condensed table-full-width table-bordered table-responsive table-hover display">
		<thead>
			<th>Start Date</th>
			<th>End Date</th>
			<th>Policy No</th>
			<th>Total Sum Insured</th>
			<th>Premium Amount</th>
			<th>GST Amount</th>
			<th>Total Premium Amount</th>
			<th>Company Name</th>
			<th>Hypothicated With</th>
			<th>View Document</th>
		</thead>
		<tbody class="data_1">
			<tr>
				<td><?php echo $branch_insurence_details['insurence_start_date']; ?></td>
				<td><?php echo $branch_insurence_details['insurence_end_date']; ?></td>
				<td><?php echo $branch_insurence_details['policy_no']; ?></td>
				<td><?php echo $branch_insurence_details['total_sum_insured']; ?></td>
				<td><?php echo $branch_insurence_details['premium_amt']; ?></td>
				<td><?php echo $branch_insurence_details['gst_amt']; ?></td>      
				<td><?php echo $branch_insurence_details['total_premium_amt']; ?></td>                        
				<td><?php echo $branch_insurence_details['insurence_co_name']; ?></td>  
				<td><?php echo $branch_insurence_details['hypothicated_with']; ?></td>  
				<td><a class="btn btn-sm btn-warning waves-effect simple-tooltip" href="<?php echo base_url().$branch_insurence_details['insurence_doc']; ?>" target="_blank">View Document</a></td> 
			</tr>
		</tbody>
	</table>
<?php } ?>

<?php if(!empty($branch_mbb_ele_details)){?>
	<h3 style="margin-top: 0"> Branch Mobile And Broadband Details </h3><div class="clearfix"></div><hr>
	<table id="branch_data" class="branch_data table table-condensed table-full-width table-bordered table-responsive table-hover display">
		<thead>
			<th>Provider Name</th>
			<th>Account No</th>
			<th>Account Name</th>
			<th>Address</th>
			<th>Contact</th>
			<th>GST No</th>
			<th>Plan details</th>
		</thead>
		<tbody class="data_1">
			<tr>
				<td><?php echo $branch_mbb_ele_details['mbb_provider']; ?></td>
				<td><?php echo $branch_mbb_ele_details['mbb_accno']; ?></td>
				<td><?php echo $branch_mbb_ele_details['mbb_acname']; ?></td>
				<td><?php echo $branch_mbb_ele_details['mbb_accadd']; ?></td>
				<td><?php echo $branch_mbb_ele_details['mbb_telno']; ?></td>
				<td><?php echo $branch_mbb_ele_details['mbb_gstno']; ?></td>      
				<td><?php echo $branch_mbb_ele_details['mbb_plandetails']; ?></td>                        
			</tr>
		</tbody>
	</table>

	<h3 style="margin-top: 0"> Branch Electricity Details </h3><div class="clearfix"></div><hr>
	<table id="branch_data" class="branch_data table table-condensed table-full-width table-bordered table-responsive table-hover display">
		<thead>
			<th>Provider Name</th>
			<th>Customer Address</th>
			<th>Customer No</th>
			<th>Billing Unit</th>
			<th>Contact</th>
			<th>Meter No</th>
			<th>GST No</th>

		</thead>
		<tbody class="data_1">
			<tr>
				<td><?php echo $branch_mbb_ele_details['ele_provider']; ?></td>
				<td><?php echo $branch_mbb_ele_details['ele_custadd']; ?></td>
				<td><?php echo $branch_mbb_ele_details['ele_custno']; ?></td>
				<td><?php echo $branch_mbb_ele_details['ele_billingunit']; ?></td>
				<td><?php echo $branch_mbb_ele_details['ele_telno']; ?></td>
				<td><?php echo $branch_mbb_ele_details['ele_meterno']; ?></td>      
				<td><?php echo $branch_mbb_ele_details['ele_gstno']; ?></td>                        


			</tr>
		</tbody>
	</table>
<?php } ?>

<?php include __DIR__ . '../../footer.php'; ?>