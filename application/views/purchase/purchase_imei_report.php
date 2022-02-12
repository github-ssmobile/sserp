<?php include __DIR__ . '../../header.php'; ?>
<script>
$(document).ready(function (){
   $('.btnreport').click(function (){
        var from = $('#datefrom').val() ;
        var to = $('#dateto').val() ;
        var idvendor = $('#idvendor').val() ;
        var idbrand = $('#idbrand').val() ;
        var idmodel = $('#idmodel').val() ;
        var idpcat = $('#idpcat').val() ;
        var allpcats = $('#allpcats').val() ;
//        var vendors = $('#vendors').val();
        if(from !='' && to !='' && idvendor !='' && idbrand !='' && idmodel !='' && idpcat !=''){
            $.ajax({
                url:"<?php echo base_url() ?>Purchase/ajax_get_purchase_imei_report",
                method:"POST",
                data:{from : from, to : to, idvendor: idvendor, idbrand: idbrand, idmodel: idmodel, idpcat: idpcat, allpcats: allpcats},
                success:function(data)
                {
//                    $("#inward_data").hide();
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
.fixedelement {
  background-color: #d9edf7;
  position: sticky;
  top: 0;
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
  z-index: 9;
}
</style>
<div align="center" class="col-md-8 col-md-offset-1text-center">
    <span class="mdi mdi-cart fa-2x"> Inward</span>
</div><div class="clearfix"></div><hr>
<div class="thumbnail" style="font-family: K2D; min-height: 650px"><br>
    <div class="col-md-3">
        <div class="input-group">
            <div class="input-group-btn">
                <input type="text" name="search" id="datefrom" class="form-control input-sm" data-provide="datepicker" placeholder="From Date">
            </div>
            <div class="input-group-btn">
                <input type="text" name="search" id="dateto" class="form-control input-sm" data-provide="datepicker" placeholder="To Date">
            </div>
        </div>
    </div>
  
    <div class="col-md-2">
        <select class="form-control input-sm" name="idpcat" id="idpcat">
            <option value="0">All Ctegorys</option>
            <?php foreach($product_category as $pcat){ ?>
            <option value="<?php echo $pcat->id_product_category ?>"><?php echo $pcat->product_category_name; ?></option>
            <?php 
              $productcat[] = $pcat->id_product_category;   } ?>
             
        </select>
        <input type="hidden" name="allpcats" id="allpcats" value="<?php echo implode($productcat,',') ?>">
    </div>
    <div class="col-md-2">
        <select class="form-control input-sm" name="idvendor" id="idvendor">
            <option value="0">All Vendor</option>
            <?php foreach($vendor_data as $vdata){ ?>
            <option value="<?php echo $vdata->id_vendor ?>"><?php echo $vdata->vendor_name; ?></option>
            <?php // $vendors[] = $vdata->id_vendor;
                } ?>
        </select>
    </div>
    
    <div class="col-md-1">
        <!--<input type="hidden" name="vendors" id="vendors" value="<?php // echo implode($vendors,',') ?>">-->
        <button class="btn btn-primary btnreport btn-sm"><i class="fa fa-filter"></i> Filter</button>
    </div>
    <!--<div class="clearfissx"></div><br>-->
    <div class="col-md-2">
        <div class="input-group">
            <div class="input-group-btn">
                <a class="btn-sm" >
                    <i class="fa fa-search"></i> Search
                </a>
            </div>
            <input type="text" name="search" id="filter_1" class="form-control input-sm" placeholder="Search from table">
        </div>
    </div>
    <!--<div class="col-md-1"><div id="count_1" class="text-info"></div></div>-->
    <div class="col-md-1">
        <button class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('inward_data');"><span class="fa fa-file-excel-o"></span> Export</button>
    </div><div class="clearfix"></div><br>
    <div style="overflow-x: auto;height: 650px;" class="">
        <div id="purchasedata">
            <table id="inward_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 12px">
                <thead class="bg-info fixedelement">
                    <th>Inward Id</th>
                    <th>Date</th>
                    <th>Vendor Inv No</th>
                    <th>Vendor Invoice Date</th>
                    <th>Vendor</th>
                    <th>Vendor GST</th>
                    <th>Imei</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Basic</th>
                    <th>(+)Charges</th>
                    <th>(-)Discount</th>
                    <th>Taxable</th>
                    <th>(+)Tax</th>
                    <th>Total</th>
                    <th>Created by</th>
                    <th>Remark</th>
                    <th>Info</th>
                    <th>Print</th>
                </thead>
                <tbody class="data_1">
                    <?php foreach($inward_data as $inward){ ?>
                    <tr>
                        <td><?php echo $inward->financial_year.'/'.$inward->id_inward ?></td>
                        <td><?php echo $inward->date; ?></td>
                        <td><?php echo $inward->supplier_invoice_no ?></td>
                        <td><?php echo $inward->vendor_invoice_date ?></td>
                        <td><?php echo $inward->vendor_name ?></td>
                        <td><?php echo $inward->vendor_gst ?></td>
                        <td><?php echo $inward->imei_no ?></td>
                        <td><?php echo $inward->product_name ?></td>
                        <td><?php echo $inward->qty ?></td>
                        <td><?php echo $inward->price ?></td>
                        <td><?php echo $inward->basic ?></td>
                        <td><?php echo $inward->charges_amt ?></td>
                        <td><?php echo $inward->discount_amt ?></td>
                        <td><?php echo $inward->taxable_amt ?></td>
                        <td><?php echo $inward->tax ?></td>
                        <td><?php echo $inward->total_amount ?></td>
                        <td><?php echo $inward->user_name ?></td>
                        <td><?php echo $inward->remark ?></td>
                        <td><a target="_blank" href="<?php echo base_url('Purchase/inward_details/'.$inward->id_inward) ?>" class="btn btn-sm btn-info waves-block waves-effect waves-ripple" style="margin: 0"><i class="fa fa-info fa-2x"></i></a></td>
                        <td><a target="_blank" href="<?php echo base_url('Purchase/purchase_print/'.$inward->id_inward) ?>" class="btn btn-sm btn-primary waves-block waves-effect waves-ripple" style="margin: 0"><i class="fa fa-print fa-2x"></i></a></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include __DIR__ . '../../footer.php'; ?>