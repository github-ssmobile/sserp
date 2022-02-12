<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function (){
       $('.btnreport').click(function (){
            var from = $('#datefrom').val() ;
            var to = $('#dateto').val() ;
            var idvendor = $('#idvendor').val() ;
            var idbrand = $('#idbrand').val() ;
            var idmodel = $('#idmodel').val() ;
            
            var vendors = $('#vendors').val();
                    
            if(from !='' && to !='' && idvendor !='' && idbrand !='' && idmodel !=''){
                $.ajax({
                    url:"<?php echo base_url() ?>Purchase/ajax_get_purchase_report",
                    method:"POST",
                    data:{from : from, to : to, idvendor: idvendor, idbrand: idbrand, idmodel: idmodel, vendors: vendors },
                    success:function(data)
                    {
                        $("#inward_data").hide();
                        $("#purchasedata").html(data);
                    }
                });
            }else{
                alert("Select Filter");
                return false;
            }
       }); 
    });
</script>
<style>
      table {
  text-align: left;
  position: relative;
  border-collapse: collapse; 
 
}
.fixedelementtop {
  background-color: #fbf7c0;
  position: sticky;
  top: 0;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 9;
}
.fixedelement1 {
  background-color: #fbf7c0;
  position: sticky;
  top: 30px;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 999;
}
</style>
<div align="center" class="col-md-8 col-md-offset-1text-center">
    <span class="mdi mdi-cart fa-2x"> Inward</span>
</div><div class="clearfix"></div><hr>
<div class="thumbnail" style="font-family: K2D; min-height: 650px"><br>
    <div class="col-md-4">
        <div class="input-group">
            <div class="input-group-btn">
                <input type="text" name="search" id="datefrom" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
            </div>
            <div class="input-group-btn">
                <input type="text" name="search" id="dateto" class="form-control input-sm" data-provide="datepicker" placeholder="To Date">
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <select class="form-control input-sm" name="idvendor" id="idvendor">
            <option value="0">All Vendor</option>
            <?php foreach($vendor_data as $vdata){ ?>
            <option value="<?php echo $vdata->id_vendor ?>"><?php echo $vdata->vendor_name; ?></option>
            <?php $vendors[] = $vdata->id_vendor; } ?>
        </select>
    </div>
    <div class="col-md-1">
        <input type="hidden" name="vendors" id="vendors" value="<?php echo implode($vendors,',') ?>">
        <button class="btn btn-primary btnreport" type="">Search</button>
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
    <div class="col-md-5"><div id="count_1" class="text-info"></div></div>
    <div class="col-md-2">
        <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('inward_data');"><span class="fa fa-file-excel-o"></span> Export to Excel</button>
    </div><div class="clearfix"></div><br>
    <div id="purchasedata">    </div>
    <table id="inward_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px">
        <thead class="bg-info fixedelementtop">
            <th>Inward Id</th>
            <th>Date Time</th>
            <th>Vendor Inv No</th>
            <th>Vendor</th>
            <th>Vendor GST</th>
            <th>Vendor Invoice Date</th>
            <th>Basic</th>
            <th>Charges</th>
            <th>Discount</th>
            <th>Taxable</th>
            <th>Tax</th>
            <th>Total</th>
            <th>Created by</th>
            <th>Remark</th>
            <th>Info</th>
        </thead>
        <tbody class="data_1">
            <?php foreach($inward_data as $inward){ ?>
            <tr>
                <td><?php echo $inward->financial_year.'/'.$inward->id_inward ?></td>
                <td><?php echo date('d/m/Y h:i a', strtotime($inward->entry_time)); ?></td>
                <td><?php echo $inward->supplier_invoice_no ?></td>
                <td><?php echo $inward->vendor_name ?></td>
                <td><?php echo $inward->vendor_gst ?></td>
                <td><?php echo $inward->vendor_invoice_date ?></td>
                <td><?php echo $inward->total_basic_amt ?></td>
                <td><?php echo $inward->total_charges_amt ?></td>
                <td><?php echo $inward->total_discount_amt ?></td>
                <td><?php echo $inward->total_taxable_amt ?></td>
                <td><?php echo $inward->total_tax ?></td>
                <td><?php echo $inward->gross_amount ?></td>
                <td><?php echo $inward->user_name ?></td>
                <td><?php echo $inward->remark ?></td>
                <td><center><a href="<?php echo base_url('Purchase/inward_details/'.$inward->id_inward) ?>" class="btn btn-sm btn-primary gradient_info waves-block waves-effect waves-ripple" style="margin: 0"><i class="fa fa-info fa-2x"></i></a></center></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php include __DIR__ . '../../footer.php'; ?>