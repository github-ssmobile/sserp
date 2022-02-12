<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function(){
        $('#hasattribute').change(function(){
            if($(this).prop("checked") == true){
                $('#attribute_block').show();
                $.ajax({
                    url:"<?php echo base_url('Master/ajax_get_payment_attributes') ?>",
                    success:function(data){
                        $('#attribute_block').html(data);
                        $(".chosen-select").chosen({ search_contains: true });
                    }
                });
            }else{
                $(this).val('');
                $('#sel_attribute').val('');
                $('#attribute_block').hide();
            }
        });
    });
</script>
    <div class="col-md-10">
        <center><h3><span class="mdi mdi-currency-inr fa-lg"></span> Payment Head</h3></center>
    </div>
    <div class="col-md-1">
        <a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>
    </div><div class="clearfix"></div><br>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px; background-color: #fff">
    <div id="purchase" style="min-height: 450px; padding: 20px 10px; margin: 0">
        <form id="pay" class="collapse">
            <div class="col-md-4 col-md-offset-1">
                <div class="thumbnail" style="border: none"><br>
                    <img src="<?php echo base_url() ?>assets/images/payment_head.png" style="width: 100%" />
                </div>
            </div>
            <div class="col-md-6"><br>
                <div class="thumbnail">
                    <center><h4><span class="mdi mdi-flattr"></span> Add Payment Head</h4></center><hr>
                    <label class="col-md-4">Payment Head</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="payment_head" placeholder="Enter Payment Head" required=""/>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-4">Amount Name</label>
                    <div class="col-md-8">
                        <input type="text" class="form-control" name="amount_name" placeholder="Enter Amount Name (Space not allowed)" required="" pattern="[A-Za-z0-9]+"/>
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-4">Corporate Sale</label>
                    <div class="col-md-8">
                        <select name="corporate_sale" id="corporate_sale" class="form-control">
                            <option value="0">InActive</option>
                            <option value="1">Active</option>
                        </select>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-4">Credit Type </label>
                    <div class="col-md-8">
                        <select name="credit_type" id="credit_type" class="form-control">
                            <option value="0">sale_payment, Payment reconcilation Entry</option>
                            <option value="1">sale payment Entry</option>
                        </select>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-5">Reconciliation</label>
                    <div class="col-md-7">
                        <div class="material-switch">                                            
                            <input id="isrecon" name="isrecon" type="checkbox"/> 
                            &nbsp; &nbsp; &nbsp; &nbsp; <label for="isrecon" class="label-primary"></label> Is direct reconciliation done?
                        </div>
                    </div>
                    <div class="clearfix"></div><br>
                     <label class="col-md-5">Multiple Rows</label>
                    <div class="col-md-7">
                        <div class="material-switch">                                            
                            <input id="multiple_rows" name="multiple_rows" type="checkbox"/> 
                            &nbsp; &nbsp; &nbsp; &nbsp; <label for="multiple_rows" class="label-primary"></label> 
                        </div>
                    </div><div class="clearfix"></div><br>
                     <label class="col-md-5">Payment Reconciloation</label>
                    <div class="col-md-7">
                        <div class="material-switch">                                            
                            <input id="pay_rec" name="pay_rec" type="checkbox"/> 
                            &nbsp; &nbsp; &nbsp; &nbsp; <label for="pay_rec" class="label-primary"></label> 
                        </div>
                    </div><div class="clearfix"></div><br>
                     <label class="col-md-5">Bank Reconciloation</label>
                    <div class="col-md-7">
                        <div class="material-switch">                                            
                            <input id="bank_rec" name="bank_rec" type="checkbox"/> 
                            &nbsp; &nbsp; &nbsp; &nbsp; <label for="bank_rec" class="label-primary"></label> 
                        </div>
                    </div><div class="clearfix"></div><br>
                     <label class="col-md-5">Branch Receivable</label>
                    <div class="col-md-7">
                        <div class="material-switch">                                            
                            <input id="receive_payment_mode" name="receive_payment_mode" type="checkbox"/> 
                            &nbsp; &nbsp; &nbsp; &nbsp; <label for="receive_payment_mode" class="label-primary"></label> 
                        </div>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-5">Has Attributes</label>
                    <div class="col-md-7">
                        <div class="material-switch">                                            
                            <input id="hasattribute" name="hasattribute" type="checkbox"/> 
                            &nbsp; &nbsp; &nbsp; &nbsp; <label for="hasattribute" class="label-primary"></label> 
                        </div>
                    </div><div class="clearfix"></div>
                    <div id="attribute_block"></div>
                    <div class="clearfix"></div><hr>
                    <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                    <button type="submit" formmethod="POST" formaction="<?php echo base_url('Master/save_payment_head') ?>" class="pull-right btn btn-info waves-effect">Save</button>
                    <div class="clearfix"></div>
                </div><div class="clearfix"></div>
            </div><div class="clearfix"></div><hr>
        </form>
        <div class="col-md-5">
            <div class="input-group">
                <div class="input-group-btn">
                    <a class="btn-sm" >
                        <i class="fa fa-search"></i> Search
                    </a>
                </div>
                <input type="text" name="search" id="filter_1" class="form-control" placeholder="Search from table">
            </div>
        </div>
        <div class="col-md-4">
            <div id="count_1" class="text-info"></div>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('payment_mode_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
        </div>
        <!--<a class="pull-right arrow-down thumbnail waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Branch" style="margin-bottom: 2px"></a>-->
        <div class="clearfix"></div><br>
        <table id="payment_mode_data" class="table table-condensed table-bordered table-hover">
            <thead>
                <th>Sr</th>
                <th>Payment Head</th>
                <th>Amount Name</th>
                <th>Attributes</th>
                <th>Credit Type</th>
                <th>Multiple Rows</th>
                <th>Corporate Sale</th>
                <th>Branch Receivable</th>
                <th>Credit Receive By</th>
                <th>Payment Reconciloation</th>
                <th>Bank Reconciloation</th>
                <th>Status</th>
                <th>Edit</th>
            </thead>
            <tbody class="data_1">
                <?php $i=1; foreach ($payment_head as $head){ ?>
                <tr>
                    <td><?php echo $i++;?></td>
                    <td><?php echo $head->payment_head; ?></td>
                    <td><?php echo $head->amount_name; ?></td>
                    <td><?php foreach ($payment_head_has_attributes as $has_attributes){ if($has_attributes->idpayment_head == $head->id_paymenthead){ echo '<div class="thumbnail" style="margin-bottom:2px; padding:5px 10px">'.$has_attributes->attribute_name.'</div>'; }} ?></td>
                    <td><?php if($head->credit_type == 0){ echo 'sale_payment, Payment recon Entry'; } else{ echo 'sale payment Entry'; } ?></td>
                    <td><?php if($head->multiple_rows == 1){ echo 'Yes'; } else{ echo 'No'; } ?></td>
                    <td><?php if($head->corporate_sale == 0){ echo 'InActive'; } else{ echo 'Active'; } ?></td>
                    <td><?php if($head->valid_for_creadit_receive == 0){ echo 'Not Receivable'; } else{ echo 'Receivable'; } ?></td> 
                    <td><?php if($head->credit_receive_type == 1){ echo 'Required'; } else{ echo 'Not Required'; } ?></td>
                    <td><?php if($head->payment_reconciliation == 1){ echo 'Allowed'; } else{ echo 'Not Allowed'; } ?></td>
                    <td><?php if($head->bank_reconciliation == 1){ echo 'Allowed'; } else{ echo 'Not Allowed'; } ?></td>
                    <td><?php if($head->active == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></td>
                    <td>
                        <a class="thumbnail btn-sm btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                            <span class="mdi mdi-pen text-danger fa-lg"></span>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="col-md-3">
        </div><div class="clearfix"></div>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>