<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
        $('.btnreport').click(function (){
            var from = $('#from').val();
            var to = $('#to').val();
            var idbranch = $('#idbranch').val();
            var branches = $('#branches').val();
            if(from !='' && to !='' && idbranch !=''){
                $.ajax({
                    url:"<?php echo base_url() ?>Sale/ajax_get_invoice_search",
                    method:"POST",
                    data:{from: from, to: to, idbranch: idbranch, branches: branches},
                    success:function(data)
                    {   
                        $('.salehide').remove();
                        $('#sale_data').html(data);
                    }
                });
            }else{
                alert('Select date range..');
            }
        });
        
          $(document).on('keydown', 'input[id=imeino]', function(e) {
            var keyCode = e.keyCode || e.which ; 
            if (keyCode === 13 ) {
                var imei = $('#imeino').val();
                if(imei != ''){
                    $.ajax({
                        url:"<?php echo base_url() ?>Sale/ajax_get_invoice_search_byimeino",
                        method:"POST",
                        data:{imei: imei},
                        success:function(data)
                        {   
                            $('.salehide').remove();
                            $('#sale_data').html(data);
                        }
                    });
                }else{
                    alert("Enter IMEI Number");
                    return false;
                }
                
            }
        });
        $(document).on('keydown', 'input[id=contact_no]', function(e) {
            var keyCode = e.keyCode || e.which ; 
            if (keyCode === 13 ) {
                var contact_no = $('#contact_no').val();
                if(contact_no != ''){
                    $.ajax({
                        url:"<?php echo base_url() ?>Sale/ajax_get_invoice_search_bycontact",
                        method:"POST",
                        data:{contact_no: contact_no},
                        success:function(data)
                        {   
                            $('.salehide').remove();
                            $('#sale_data').html(data);
                        }
                    });
                }else{
                    alert("Enter Customer Contact Number");
                    return false;
                }
                
            }
        });
        $(document).on('keydown', 'input[id=invoice_no]', function(e) {
            var keyCode = e.keyCode || e.which ; 
            if (keyCode === 13 ) {
                var invoice_no = $('#invoice_no').val();
                if(invoice_no != ''){
                    $.ajax({
                        url:"<?php echo base_url() ?>Sale/ajax_get_invoice_search_byinvoiceno",
                        method:"POST",
                        data:{invoice_no: invoice_no},
                        success:function(data)
                        {   
                            $('.salehide').remove();
                            $('#sale_data').html(data);
                        }
                    });
                }else{
                    alert("Enter Invoice Number");
                    return false;
                }
            }
        });
        
    });
</script>
<style>

.fixedelementtop {
  background-color: #fbf7c0;
  position: sticky;
  top: 0;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 9;
}

</style>
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-magnify fa-lg"></span> Invoice Search</h3></center></div><div class="clearfix"></div><hr>
<div class="col-md-1"><b>From</b></div>
<div class="col-md-2">
    <input type="text" class="form-control" data-provide="datepicker" id="from" name="from" required="" placeholder="Date From">
</div>
<div class="col-md-1"><b>To</b></div>
<div class="col-md-2">
    <input type="text" class="form-control" data-provide="datepicker" id="to" name="to" required="" placeholder="Date To">
</div>
<?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
    <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
<?php } else { ?>
    <div class="col-md-1"><b>Branch</b></div>
    <div class="col-md-3">
        <select class="form-control chosen-select" name="idbranch" id="idbranch">
            <option value="0">All Branch</option>
            <?php foreach($branch_data as $branch){ ?>
                <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
            <?php $branches[] = $branch->id_branch; } ?>
        </select>
    </div>
    <input type="hidden" name="branches" id="branches" value="<?php echo implode($branches,',') ?>">
<?php } ?>
 <div class="col-md-2">
    <button class="btn btn-info btn-sm btnreport">Filter</button> &nbsp; <span style="font-size: larger"><b>OR</b></span>
</div>
<div class="clearfix"></div><br>
<div class="col-md-1"> <b>Imei No</b></div>
<div class="col-md-2">
    <input type="text" name="imeino" class="form-control " id="imeino" placeholder="Enter Imei Number">
</div>
<div class="col-md-1"> <b>Contact No</b></div>
<div class="col-md-2">
    <input type="text" name="contact_no" class="form-control " id="contact_no" placeholder="Enter Contact Number">
</div>
<div class="col-md-1"><b>Invoice Number</b></div>
<div class="col-md-3">
    <input type="text" name="invoice_no" class="form-control " id="invoice_no" placeholder="Enter Invoice Number">
</div>
<div class="clearfix"></div><br>
    
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
    <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('sale_data_report');"><span class="fa fa-file-excel-o"></span> Export</button>
</div><div class="clearfix"></div><br>
<table id="sale_data_report" class="table table-bordered table-striped table-condensed table-info salehide">
    <thead class="fixedelementtop">
        <th>Date</th>
        <th>Invoice No</th>
        <th>Branch</th>
        <th>Customer</th>
        <th>Contact</th>
        <th>GSTIN</th>
        <th>Sale Promotor</th>
        <th>Total Basic</th>
        <th>Total Discount</th>
        <th>Total Amount</th>
        <th>Info</th>
        <th>Print</th>
    </thead>
    <tbody class="data_1sale_data_report">
        <?php foreach ($sale_data as $sale) { ?>
        <tr>
            <td><?php echo $sale->entry_time ?></td>
            <td><?php echo $sale->inv_no ?></td>
            <td><?php echo $sale->branch_name ?></td>
            <td><?php echo $sale->customer_fname.' '.$sale->customer_lname ?></td>
            <td><?php echo $sale->customer_contact ?></td>
            <td><?php echo $sale->customer_gst ?></td>
            <td><?php echo $sale->user_name ?></td>
            <td><?php echo $sale->basic_total ?></td>
            <td><?php echo $sale->discount_total ?></td>
            <td><?php echo $sale->final_total ?></td>
            <td><a href="<?php echo base_url('Sale/sale_details/'.$sale->id_sale) ?>" class="btn btn-primary btn-floating waves-effect"><i class="fa fa-info"></i></a></td>
            <td><a href="<?php echo base_url('Sale/invoice_print/'.$sale->id_sale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>
        </tr> 
        <?php } ?>
    </tbody>
</table>
<div id="sale_data"></div>
<?php include __DIR__.'../../footer.php'; ?>