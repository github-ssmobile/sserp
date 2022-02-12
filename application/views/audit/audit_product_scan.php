<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function (){
       var barcodes = [];
        $(document).on('keydown', 'input[id=scan_barcode]', function(e) {
            var keyCode = e.keyCode || e.which ; 
            if (keyCode === 13 ) {
                var scan_barcode = $('#scan_barcode').val();
                var idbranch = $('#idbranch').val();
                var idcat = $('#idcat').val();
                var idbrand = $('#idbrand').val();
                var audit_start = $('#audit_start').val();
                
                if(scan_barcode != '' && idbranch != '' && idcat != '' && idbrand){
                    if(barcodes.includes(scan_barcode) === false){
                        barcodes.push(scan_barcode);
                        $.ajax({
                            url:"<?php echo base_url() ?>Audit/ajax_scan_barcode",
                            method:"POST",
                            data:{idbranch: idbranch, idcat: idcat, idbrand: idbrand, scan_barcode: scan_barcode, audit_start: audit_start},
                            success:function(data)
                            {
                                //alert(data);
                                $("#barcode_data").append(data);
                                $('#scan_barcode').val('');
                                var totalmatch = 0;
                                var totalunmatch = 0;
                                var totalmissing = 0;

                                $('tr').each(function (){
                                    $(this).find('.mtch_cnt').each(function (){
                                        var tmatchcnt = $(this).val();
                                        if(!isNaN(tmatchcnt) && tmatchcnt.length !== 0){
                                             totalmatch += parseFloat(tmatchcnt);
                                        }
                                    });
                                    $(this).find('.unmtch_cnt').each(function (){
                                        var t_unmatchcnt = $(this).val();
                                        if(!isNaN(t_unmatchcnt) && t_unmatchcnt.length !== 0){
                                             totalunmatch += parseFloat(t_unmatchcnt);
                                        }
                                    });
    //                                    totalmissing = sys_cnt - totalmatch;
                                });
                                
                                 
                                $('.matched_count').html(totalmatch);
                                $('.unmatched_count').html(totalunmatch);
                            }
                        });
                    }else{
                        alert("Barcode Alreday Entered!.. ");
                        $('#scan_barcode').val('');
                        return false;
                    }
                }else{
                    alert("😡 Please Enter Barcode");
                    return false;
                }
            } 
        });
        
        $('#idmodel').change(function (){
            var idstock = $('#idmodel').val();
            $('#qty').show();
            if(idstock !=''){

            }else{
                alert("Please Select Model");
                $('#qty').hide();
                return false;
            }
        });
        
        var qty_idstock = [];
        $(document).on('keydown', 'input[id=qty]', function(e) {
            var keyCode = e.keyCode || e.which ; 
            if (keyCode === 13 ) {
                var idvariant = $('#idmodel').val();
                var idbranch = $('#idbranch').val();
                var idcat = $('#idcat').val();
                var idbrand = $('#idbrand').val();
                var idgodown = $('#idgodown').val();
                var audit_start = $('#audit_start').val();
                var qty = $('#qty').val();
                if(idvariant != '' && qty != 0){
                    if(qty_idstock.includes(idvariant) === false){
                        qty_idstock.push(idvariant);
                        
                        $.ajax({
                            url:"<?php echo base_url() ?>Audit/ajax_qty_scan_barcode",
                            method:"POST",
                            data:{idbranch: idbranch, idcat: idcat, idbrand: idbrand, idvariant: idvariant, qty: qty, idgodown: idgodown, audit_start: audit_start},
                            success:function(data)
                            {
                                $("#barcode_data").append(data);
                                $('#qty').val('');
                                
                                var totalmatch = 0;
                                var totalunmatch = 0;
                                var totalmissing = 0;

                                $('tr').each(function (){
                                    $(this).find('.mtch_cnt').each(function (){
                                        var tmatchcnt = $(this).val();
                                        if(!isNaN(tmatchcnt) && tmatchcnt.length !== 0){
                                             totalmatch += parseFloat(tmatchcnt);
                                        }
                                    });
                                    $(this).find('.unmtch_cnt').each(function (){
                                        var t_unmatchcnt = $(this).val();
                                        if(!isNaN(t_unmatchcnt) && t_unmatchcnt.length !== 0){
                                             totalunmatch += parseFloat(t_unmatchcnt);
                                        }
                                    });
    //                                    totalmissing = sys_cnt - totalmatch;
                                });
                                 
                                 
                                $('.matched_count').html(totalmatch);
                                $('.unmatched_count').html(totalunmatch);
                            }
                        });
                    }else{
                        alert('Model Already Exists');
                        return false;
                    }
                }else{
                    alert('Enter Qty Greater Than 0');
                    return false;
                }
                
            }
        });
        
        $('#procced').click(function (){
            var idbranch = $('#idbranch').val();
            var idcat = $('#idcat').val();
            var idbrand = $('#idbrand').val();
            
            var all_variants = $('#all_variants').val();
            
             $.ajax({
                url:"<?php echo base_url() ?>Audit/ajax_check_idvarian_audit_temp_data",
                method:"POST",
                data:{idbranch: idbranch, idcat: idcat, idbrand: idbrand},
                success:function(data)
                {
                    if(all_variants > 0){
                        if(data < all_variants ){
                            if(!confirm("You have not done the audit of qty products. DO YOU WANT TO SUBMIT AUDIT?")){
                                return false;
                            }else{
                                $('#submit_audit').show();
                                $('#procced').hide();
                            }
                        }
                    }else{
                        $('#submit_audit').show();
                        $('#procced').hide();
                    }
                    
                }
            });
        });
        
        $('#submit_audit').click(function (){
            if(!confirm("Do You Want To Submit Audit")){
                return false;
            }
        });
        
        
    });
</script>
<style>
.fix {
    position: fixed;
    bottom: 80px;
    right: 20px; 
}
</style>
<center><h3><span class="mdi mdi-barcode-scan fa-lg"></span> Stock Audit </h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <?php  if(count($audit_done_data) > 0){?>
        <center><h3>Already Done Audit For <span style="color: #3270c6"><?php if($idbrand != 'all'){ echo $audit_done_data[0]->brand_name; } else{ echo  'ALL';}?></span> Brand. </h3></center>
        <a class="btn btn-info pull-right" href="<?php echo base_url()?>Audit/stock_audit" style="margin-right: 10px;">Back To Audit</a>
    <?php } else { ?>
        <div  style="padding: 20px 10px; margin: 0" >
            <div style="font-size: 14px;">
                 <input type="hidden" name="audit_start" id="audit_start" value="<?php echo $audit_start; ?>">
                <div class="col-md-1">Branch</div>
                <div class="col-md-2" style="color: #ff0033"><b><?php echo strtoupper($branch_data->branch_name); ?></b></div>
                <div class="col-md-1">Category</div>
                <div class="col-md-2" style="color: #ff0033"><b><?php echo strtoupper($cat_data[0]->product_category_name); ?></b></div>
                <div class="col-md-1">Brand</div>
                <div class="col-md-2" style="color: #ff0033"><b><?php if($this->input->get('idbrand') != 'all'){ echo strtoupper($brand_data->brand_name); }else{ echo ' All Brands';  } ?></b></div>
                <div class="col-md-1">Godown</div>
                <div class="col-md-2" style="color: #ff0033"><b><?php echo strtoupper($godown_data->godown_name); ?></b></div>

                <div class="clearfix"></div><br>
                <div class="col-md-1">Barcode</div>
                <div class="col-md-3">
                    <input type="text" class="form-control" id="scan_barcode" name="scan_barcode" >
                </div>
                <?php if(count($qty_stock_data) > 0){  ?>
                <div class="col-md-1"> Model</div>
                <div class="col-md-3">
                    <select class="form-control" name="idmodel" id="idmodel">
                        <option value="">Select Model</option>
                        <?php foreach ($stock_data as $qty_stock){
                            array_push($_SESSION['all_variants'], $qty_stock->idvariant); ?>
                        <option value="<?php echo $qty_stock->idvariant?>"><?php echo $qty_stock->full_name; ?></option>
                        <?php } ?>
                    </select>
    <!--                <select class="form-control" name="idmodel" id="idmodel">
                        <option value="">Select Model</option>
                        <?php foreach ($qty_stock_data as $qty_stock){
                            array_push($_SESSION['all_variants'], $qty_stock->id_variant); ?>
                        <option value="<?php echo $qty_stock->id_variant?>"><?php echo $qty_stock->full_name ?></option>
                        <?php } ?>
                    </select>-->

                </div>
                <div class="col-md-1" >Qty</div>
                <div class="col-md-2"><input type="number"  style="display:none " class="form-control" min="1" name="qty" id="qty" value="1"></div>
                <?php } ?>
                <div class="clearfix"></div><br><hr>

                <?php $match = 0;  $unmatch =0;
                if(count($audit_temp_data) > 0){ 
                    foreach($audit_temp_data as $audit_temp){
                        if($audit_temp->status == 'matched'){
    //                    if($audit_temp->status == 'matched' || $audit_temp->status == 'match in'){
                           $match = $match + $audit_temp->qty;
                        }
                        if($audit_temp->status == 'unmatched'){
                           $unmatch = $unmatch + $audit_temp->qty;
                        }

                    }
                } ?>
                <div class="col-md-2"><b>System Count</b></div>
                <div class="col-md-2">
                    <b>
                        <?php 
                            $sys_cnt=0; foreach($stock_data as $stock_d){
                            $sys_cnt = $sys_cnt + $stock_d->qty;
                        } 
                        echo $sys_cnt; 
                        ?>
                    </b>
                </div>
               <div class="col-md-2"><b>Intransit Count</b></div>
                <div class="col-md-2">
                    <b>
                        <?php 
                            $intra_cnt=0; foreach($intransit_stock as $intransit){
                            $intra_cnt = $intra_cnt + $intransit->qty;
                        } 
                        echo $intra_cnt; 
                        ?>
                    </b>
                </div>
                <div class="col-md-2"><b>Transfer Count</b></div>
                <div class="col-md-2">
                    <b>
                        <?php 
                            $transfer_cnt=0; foreach($trnasfer_stock as $transfer){
                            $transfer_cnt = $transfer_cnt + $transfer->qty;
                        } 
                        echo $transfer_cnt; 
                        ?>
                    </b>
                </div>
                <div class="clearfix"></div><br>
                 <div class="col-md-2"><b>Matched Count</b></div>
                <div class="col-md-2 matched_count"><?php echo $match; ?></div>
                <div class="col-md-2"><b>Unmatched Count</b></div>
                <div class="col-md-2 unmatched_count"><?php echo $unmatch; ?></div>
            </div>
            <div class="clearfix"></div><br>
             <div class="col-md-4"></div>
    <!--        <div class="col-md-2" ><b>Previous Matched </b></div>
            <div class="col-md-2 "><?php echo $match ?></div>
            <div class="col-md-2"><b>Previous Unmatched </b></div>
            <div class="col-md-2"><?php echo $unmatch ?></div>-->
            <div class="clearfix"></div><br>
                <table class="table table-bordered table-condensed">
                    <thead style="background-color: #99ccff">
                        <th><b>Barcode</b></th>
                        <th><b>Category</b></th>
                        <th><b>Brand</b></th>
                        <th><b>Product</b></th>
                        <th><b>Qty</b></th>
                        <th><b>Status</b></th>
                        <th><b>Remark</b></th>
                    </thead>
                    <tbody id="barcode_data"> 
                        <?php if($audit_temp_data){ 
                            foreach($audit_temp_data as $audit_temp){

                                if($audit_temp->idskutype == 4){
                                    array_push($_SESSION['qty_barcode'], $audit_temp->idvariant);
                                }else{
                                    array_push($_SESSION['temp_barcode'], $audit_temp->imei_no);
                                }

                                ?>
                                <tr>
                                    <td><?php echo $audit_temp->imei_no; ?></td>
                                    <td><?php echo $audit_temp->product_category_name; ?></td>
                                    <td><?php echo $audit_temp->brand_name; ?></td>
                                    <td><?php echo $audit_temp->product_name; ?></td>
                                    <td><?php echo $audit_temp->qty; ?></td>
                                    <td><?php echo $audit_temp->status;
                                    if($audit_temp->status == 'matched'){ ?>
                                    <!--if($audit_temp->status == 'matched' || $audit_temp->status == 'match in'){--> 
                                        <input type="hidden"  class="mtch_cnt" value="<?php echo $audit_temp->qty; ?>">
                                            <?php }
                                            if($audit_temp->status == 'unmatched'){?>
                                        <input type="hidden"  class="unmtch_cnt" value="<?php echo $audit_temp->qty; ?>">
                                            <?php }
                                            ?></td>
                                    <td><?php echo $audit_temp->remark?></td>
                                </tr>
                        <?php } } ?>
                    </tbody>
                </table>
                <?php if(count($qty_stock_data) > 0){ ?> 
            <a class="btn btn-primary fix" style="<?php  if(count($qty_stock_data) <= 0){ ?> display: none <?php } ?>" id="procced">Proceed</a>
                <?php }  ?>
                <form>
                   <div> 
                       <input type="hidden" id="all_variants" value="<?php echo count($_SESSION['all_variants']); ?>">
                       <!--<input type="text" id="scaned_variants" value="<?php echo count($_SESSION['qty_barcode']);?>">-->
                       <input type="hidden" class="form-control" id="idbranch" name="idbranch" value="<?php echo $idbranch; ?>">
                       <input type="hidden" class="form-control" id="idcat" name="idcat" value="<?php echo $idcat; ?>" >
                       <input type="hidden" class="form-control" id="idbrand" name="idbrand" value="<?php echo $idbrand; ?>">
                       <input type="hidden" class="form-control" id="idgodown" name="idgodown" value="<?php echo $idgodown; ?>">
                       <button class="btn btn-primary pull-right fix" style="<?php  if(count($qty_stock_data) > 0){ ?> display: none <?php } ?>"   id="submit_audit" formaction="<?php echo base_url()?>Audit/save_scan_barcodes" formmethod="POST">Submit</button>
                   </div>
               </form>
        </div>
    <?php }?>
</div>
<?php include __DIR__.'../../footer.php'; ?>