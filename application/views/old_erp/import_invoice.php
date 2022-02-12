<?php include __DIR__.'../../header.php'; ?>
<center><h3><span class="mdi mdi-steam fa-lg"></span> Import Invoice</h3></center>
<?php $attributes = array('id' => 'myform'); echo form_open('Old_erp/save_import_invoice',$attributes) ?>            
            <div class="col-md-6 thumbnail col-md-offset-3">                    
<!--                        <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Attribute</h4></center><hr>-->
                        <div class="col-md-12">
                        <label class="col-md-3 col-md-offset-1">Invoice No</label>
                        <div class="col-md-7">
                            <input type="text" required class="form-control"  placeholder="Enter Invoice No" name="invoice_no"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Invoice Date</label>
                        <div class="col-md-7">
                            <input type="text" required class="form-control" data-provide="datepicker" placeholder="Enter Invoice Date" name="invoice_date"  />
                        </div><div class="clearfix"></div><br>
<!--                        <label class="col-md-3 col-md-offset-1">Billing Type</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control"  placeholder="Enter Billing Type" name="billing_type"  />
                        </div><div class="clearfix"></div><br>-->
                        <label class="col-md-3 col-md-offset-1">Zone</label>
                        <div class="col-md-7">
                            <input type="text" required class="form-control"  placeholder="Enter Zone" name="zone"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Promoter Name</label>
                        <div class="col-md-7">
                            <input type="text" required class="form-control"  placeholder="Enter Promoter Name" name="promoter_name" />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Customer Name</label>
                        <div class="col-md-7">
                            <input type="text" required class="form-control"  placeholder="Enter Customer Name" name="customer_name"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Customer Mobile</label>
                        <div class="col-md-7">
                            <input type="text" required class="form-control"  placeholder="Enter Customer Mobile" name="customer_mobile"  />
                        </div><div class="clearfix"></div><br>
                         <label class="col-md-3 col-md-offset-1">Customer Gst No</label>
                        <div class="col-md-7">
                        <input type="text"  class="form-control"  placeholder="Enter Customer Gst No" name="customer_gst_no"  />
                        </div><div class="clearfix"></div><br>
                         <label class="col-md-3 col-md-offset-1">Branch</label>
                        <div class="col-md-7">
                            <select class="chosen-select form-control input-sm branch_val"  name="branch" id="branch" required="">
                        <option value="-1">Select Branch</option>
                        <?php foreach ($branch_data as $branch) { ?>
                        <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                        <?php } ?>
                        </select>
                        </div>
                         <input type="hidden" class="branch_name" name="branch_name" value="">
                         <div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">City</label>
                        <div class="col-md-7">
                        <input type="text" required class="form-control"  placeholder="Enter City" name="city"  />
                        </div><div class="clearfix"></div><br>
                         <label class="col-md-3 col-md-offset-1">Pincode</label>
                        <div class="col-md-7">
                        <input type="number" required class="form-control"  placeholder="Enter Pincode" name="pincode"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Route</label>
                        <div class="col-md-7">
                        <input type="text"  required class="form-control"  placeholder="Enter Route" name="route"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Category</label>
                        <div class="col-md-7">
                        <input type="text" required class="form-control"  placeholder="Enter Category" name="category"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Sub Category</label>
                        <div class="col-md-7">
                        <input type="text" required class="form-control"  placeholder="Enter Sub Category" name="sub_category"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Brand</label>
                        <div class="col-md-7">
                        <input type="text" required class="form-control"  placeholder="Enter Brand" name="brand"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Product Name</label>
                        <div class="col-md-7">
                        <input type="text" required class="form-control"  placeholder="Enter Product Name" name="product_name"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Product Code</label>
                        <div class="col-md-7">
                        <input type="text" required class="form-control"  placeholder="Enter Product Code" name="product_code"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Product Id</label>
                        <div class="col-md-7">
                        <input type="text" required class="form-control"  placeholder="Enter Product Id" name="product_id"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Hsn Code</label>
                        <div class="col-md-7">
                        <input type="text"  class="form-control"  placeholder="Enter Hsn Code" name="hsn_code"  />
                        </div><div class="clearfix"></div><br>
                         <label class="col-md-3 col-md-offset-1">IMEI 1 No</label>
                        <div class="col-md-7">
                        <input type="text"  class="form-control"  placeholder="Enter IMEI 1 No" name="imei_1_no"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">IMEI 2 No</label>
                        <div class="col-md-7">
                        <input type="text"  class="form-control"  placeholder="Enter IMEI 2 No" name="imei_2_no" />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Serial No</label>
                        <div class="col-md-7">
                        <input type="text"  class="form-control"  placeholder="Enter Serial No" name="serial_no"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Gst Rate</label>
                        <div class="col-md-7">
                        <input type="number" required class="form-control"  placeholder="Enter Gst Rate" name="gst_rate"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Base Price</label>
                        <div class="col-md-7">
                        <input type="number" required class="form-control"  placeholder="Enter Base Price" name="base_price"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Igst</label>
                        <div class="col-md-7">
                        <input type="number"  class="form-control"  placeholder="Enter Igst" name="igst"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Sgst</label>
                        <div class="col-md-7">
                        <input type="number"  class="form-control"  placeholder="Enter Sgst" name="sgst" />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Cgst</label>
                        <div class="col-md-7">
                        <input type="number"  class="form-control"  placeholder="Enter Cgst" name="cgst"  />
                        </div><div class="clearfix"></div><br>
                         <label class="col-md-3 col-md-offset-1">Total Amount Per Qty</label>
                        <div class="col-md-7">
                        <input type="number" required class="form-control"  placeholder="Enter Total Amount Per Qty" name="total_amount_per_qty"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Hidden Discount</label>
                        <div class="col-md-7">
                        <input type="number" required class="form-control"  placeholder="Enter Hidden Discount" name="hidden_discount"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Settlement Amount</label>
                        <div class="col-md-7">
                        <input type="number" required class="form-control"  placeholder="Enter Settlement Amount" name="settlement_amount"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Cash Amount</label>
                        <div class="col-md-7">
                        <input type="number" required class="form-control"  placeholder="Enter Cash Amount" name="cash_amount"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Manager Price</label>
                        <div class="col-md-7">
                        <input type="number" required class="form-control"  placeholder="Enter Manager Price" name="manager_price"  />
                        </div><div class="clearfix"></div><br>
                        <label class="col-md-3 col-md-offset-1">Salesman Price</label>
                        <div class="col-md-7">
                        <input type="number" required class="form-control"  placeholder="Enter Salesman Price" name="salesman_price"  />
                        </div><div class="clearfix"></div><br>
                         <label class="col-md-3 col-md-offset-1">Customer Price</label>
                        <div class="col-md-7">
                        <input type="number" required class="form-control"  placeholder="Enter Customer Price" name="customer_price"  />
                        </div><div class="clearfix"></div><br>
                        <button type="submit" onclick="return validation_fn(event)" class="pull-right btn btn-info waves-effect">Save</button>
                        <div class="clearfix"></div>
                        </div>
                    <div class="clearfix"></div>
                </div><div class="clearfix"></div><hr>
            <?php echo form_close(); ?>
<script>
    
$('.branch_val').change(function() {    
    
    var branchname = $(this).find("option:selected").text();
    $('.branch_name').val(branchname);
   
});    


function validation_fn(e){
    
    var branchval = $(".branch_val").val()
    
    if(branchval == '-1'){
    e.preventDefault();
    alert('Please Select Branch');
    return false;
   }else{
       $('form#myForm').submit();
   }
}    
</script>                
<?php include __DIR__.'../../footer.php'; ?>