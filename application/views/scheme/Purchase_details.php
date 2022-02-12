<?php include __DIR__.'../../header.php'; ?>
<div class="col-md-10"><center><span style="color:#1b6caa;font-family: Kurale;font-size: 22px"><i class="<?php echo $schemetype->font ?>"></i> <?php echo str_replace('_', ' ', $schemetype->scheme_type) ?> Scheme</span></center></div><div class="clearfix"></div><hr>
<div class="col-md-10 col-md-offset-1">
    <form id="generate_scheme_form">
        <div class="thumbnail" style="font-size: 14px;">
            <div class="col-md-3" style="color: #0056b3">
                <center><h4><?php echo $scheme->brand_name ?></h4></center>
                <input type="hidden" name="idbrand" value="<?php echo $scheme->idbrand ?>" />
            </div>
            <div class="col-md-9" style="border-left: 2px solid #0056b3">
                <span class="col-md-4">Scheme Name</span>
                <span class="col-md-8"><?php echo $scheme->scheme_name ?></span>
                <div class="clearfix"></div>
                <span class="col-md-4">Scheme Period</span>
                <span class="col-md-8"><?php echo $scheme->date_from .' To '. $scheme->date_to ?></span>
                <input type="hidden" name="date_from" value="<?php echo $scheme->date_from ?>" />
                <input type="hidden" name="date_to" value="<?php echo $scheme->date_to ?>" />
            </div><div class="clearfix"></div><hr>
            <span class="col-md-12">Vendor: <?php echo $scheme->vendor_name ?></span>
            <input type="hidden" name="idvendor" value="<?php echo $scheme->idvendor ?>" />
            <div class="clearfix"></div><hr>
            <span class="col-md-3">Scheme ID: <b><?php echo $scheme->id_scheme ?></b></span>
            <input type="hidden" name="idscheme" id="idscheme" value="<?php echo $scheme->id_scheme ?>" />
            <input type="hidden" name="idscheme_type" id="idscheme_type" value="<?php echo $scheme->idscheme_type ?>" />
            <input type="hidden" name="settlement_type" id="settlement_type" value="<?php echo $scheme->settlement_type ?>" />
            <span class="col-md-3">Settlement Type: <b><?php if($scheme->settlement_type == 0){ echo "FOC"; }elseif($scheme->settlement_type == 1){ echo "Payout"; }else{ echo 'Percentage'; } ?></b></span>
            <span class="col-md-3">Claim Target: <b><?php echo empty($scheme->claim_target)? "Qty" : "Overall Value"; ?></b></span>
            <span class="col-md-3">Has Slabs: <b><?php echo empty($scheme->has_slabs) ? "No" : "Yes"; ?></b></span><div class="clearfix"></div><hr>
            <input type="hidden" name="has_slabs" id="has_slabs" value="<?php echo $scheme->has_slabs ?>" />
            <span class="col-md-12"><h5><?php echo str_replace('_', ' ', $schemetype->scheme_type) ?> Scheme. Details as mentioned below,</h5></span>
            <table class="table table-bordered table-condensed" style="margin: 0">
                <thead>
                    <th class="col-md-4">Sellout Model</th>
                    <th>Min</th>
                    <th>Max</th>     
                    <?php if($scheme->claim_target == 1){ ?>
                        <th>Purchase Price</th>    
                    <?php  }else{ ?>
                        <th>Purchase Qty</th>    
                    <?php } ?>
                    <?php if($scheme->settlement_type == 0){ ?>
                    <th>FOC Model</th>
                    <th>FOC Settlement</th>
                    <?php }elseif($scheme->settlement_type == 1){ ?>
                    <th>Payout/Qty</th>
                    <th>Achivement</th>
                    <?php }else{ ?>
                    <th>Percentage%</th>
                    <th>Achivement</th>
                    <?php } ?> 
                </thead>
                <tbody>
                    <?php 
//                    echo '<pre>'.print_r($settlement_data,1).'</pre>';
                    $tsum_qty=0;$r=0;
                    foreach ($settlement_data as $sds){ 
                        $sd=$sds[0];
                        $id = $sd['id_scheme_data']; 
//                        $sum_qty = $sale_count[$sd['idvariant']][0]->sum_qty;
//                        $tsum_qty += $sum_qty; ?>
                    <tr>
                        <td><?php  $array = explode(',', $sd['full_name']);$i=1;foreach ($array as $name){  echo '<b>'.$i.')</b> '.$name.'<br>'; $i++; } ?>                            
                            <input type="hidden" name="id_scheme_data[]" value="<?php echo $sd['id_scheme_data'] ?>" />
                        </td>                            
                        <td><?php echo $sd['min']; ?></td>
                        <td><?php echo $sd['max']; ?></td>
                         <?php if($scheme->claim_target == 1){ ?>
                            <td><?php if($scheme->has_slabs == 0){ echo $sd['ach_'.$id][0]->basic;}else{ echo $sd['total_ach_'.$scheme->id_scheme][0]->basic; }  ?></td>
                        <?php  }else{ ?>
                            <td><?php if($scheme->has_slabs == 0){ echo $sd['ach_'.$id][0]->ach;}else{ echo $sd['total_ach_'.$scheme->id_scheme][0]->ach; }  ?></td>
                        <?php } ?> 
                        
                        <?php if($scheme->settlement_type == 0){ ?>                          
                            <td class="p-2">
                                <?php $i=1; //echo print_r($sd[$id]); 
                                    foreach ($sd[$id] as $sett){ ?>
                                    <input type="hidden" name="foc_model[<?php echo $sd['idvariant'] ?>][]" value="<?php echo $sett->foc_model_name ?>" />
                                    <input type="hidden" name="foc_unit[<?php echo $sd['idvariant'] ?>][]" value="<?php echo $sett->foc_units ?>" />
                                    <div class="">
                                        <!--<center>-->
                                        <?php echo $sett->foc_model_name; ?><br>
                                            <div class="col-md-6 thumbnail btn-sm" style="background-color: #ffecde; color: #000; margin-bottom: 0"><i class="mdi mdi-clock-fast pull-left" style="color:#ea8236"><small> Units</small> </i><span class="pull-right"><?php echo $sett->foc_units ?></span></div>
                                            <!--<div class="col-md-4 pull-right thumbnail btn-sm" style="background-color: #cdfbee; color: #000; margin-bottom: 0"><i class="mdi mdi-check-all pull-left" style="color:#01a478"><small> Claimed</small> </i><span class="pull-right"> <?php echo $sett->foc_unit * $sale_count[$sd['idvariant']][0]->sum_qty ?></span></div>-->
                                            <div class="clearfix"></div>
                                        <!--</center>-->
                                    </div>
                                <?php echo ($i != count($sd[$id])) ? '<hr>' : ''; ?>
                                <?php $i++; } ?>
                            </td> 
                            <td class="p-2">
                            <?php $i=1; //echo print_r($sd[$id]); 
                                    foreach ($sd[$id] as $sett){ ?>                                   
                                    <div class="">
                                        <?php echo $sett->foc_model_name; ?><br>
                                            <div class="col-md-6 thumbnail btn-sm" style="background-color: #c3ffae; color: #000; margin-bottom: 0"><i class="mdi mdi-check pull-left" style="color:#ea8236"><small> Settlement</small> </i><span class="pull-right"><?php echo $sett->foc_settlement ?></span></div>                                            
                                            <div class="clearfix"></div>                                   
                                    </div>
                                <?php echo ($i != count($sd[$id])) ? '<hr>' : ''; ?>
                                <?php $i++; } ?>    
                            </td>
                            <?php }elseif($scheme->settlement_type == 1){
                                    if($scheme->claim_target == 0){ ?> 
                                                           
                                    <td class="p-2"><?php echo $sd['payout_value']; ?></td>                            
                                    <td class="p-2"><div class="col-md-6 thumbnail btn-sm" style="background-color: #c3ffae; color: #000; margin-bottom: 0">
                                        <?php  
                                         if($scheme->has_slabs == 0){ 
                                             echo (($sd['ach_'.$id][0]->ach)*($sd['payout_value']));                                  
                                         }else{ 
                                             $payout=0;
                                             $sale_count= $sd['total_ach_'.$scheme->id_scheme][0]->ach;
                                             
                                             if($sale_count>0){                                                                                     
                                                if($sale_count >= $sd['min'] && $sale_count <= $sd['max']){
                                                    $payout= $sale_count*$sd['payout_value'];                                                                                                        
                                                }elseif($sd['max']==$sd['min'] && $sale_count >= $sd['max']){
                                                   $payout= $sale_count*$sd['payout_value'];                                                   
                                                    
                                                } 
                                             }
                                             echo $payout; 
                                             
                                         }
                                         ?>
                                        </div></td>                            
                                    <?php }elseif($scheme->claim_target == 1){ ?>
                                        <td class="p-2"><?php echo $sd['payout_value']; ?></td>
                                        <td class="p-2"><div class="col-md-6 thumbnail btn-sm" style="background-color: #c3ffae; color: #000; margin-bottom: 0">
                                        <?php  
                                         if($scheme->has_slabs == 0){ 
                                                                             
                                         }else{
                                             $payout=0; 
                                             $sale_count= $sd['total_ach_'.$scheme->id_scheme][0]->basic;
                                             if($sale_count>0){                                                                                  
                                                if($sd['max']==$sd['min'] && $sale_count >= $sd['max']){
                                                   $payout= $sd['payout_value'];
                                                }else if($sale_count>=$sd['min'] && $sale_count <= $sd['max']){
                                                    $payout= $sd['payout_value'] ;
                                                } 
                                             }
                                             echo $payout;  
                                         }
                                         ?>
                                        </div></td>
                                    <?php }else{ ?>
                                        
                                   <?php  } 
                                    
                                    }else{   
                                        if($scheme->claim_target == 1){ ?>
                                        <td class="p-2"><?php echo $sd['payout_per']; ?></td>
                                        <td class="p-2"><div class="col-md-6 thumbnail btn-sm" style="background-color: #c3ffae; color: #000; margin-bottom: 0">
                                        <?php  
                                        $claim_sum=0;
                                        $payout_per=0;
                                        $sale_count= $sd['total_ach_'.$scheme->id_scheme][0]->basic;
                                        if($sd['max']==$sd['min'] && $sale_count >= $sd['max']){
                                            $payout_per= $sd['payout_per'];
                                        }else if($sale_count>=$sd['min'] && $sale_count<=$sd['max']){
                                            $payout_per= $sd['payout_per'];
                                        } 
                                         if($scheme->has_slabs == 0){ 
                                                                             
                                         }else{  
                                             $claim_sum = ($sale_count * $payout_per)/100;                                                
                                         }
                                          echo $claim_sum; 
                                         ?>
                                        </div></td>
                                    <?php }else{ ?>
                                        <td class="p-2"><?php echo $sd['payout_per']; ?>%</td>
                                        <td class="p-2"><div class="col-md-6 thumbnail btn-sm" style="background-color: #c3ffae; color: #000; margin-bottom: 0">
                                        <?php  
                                        $claim_sum=0;
                                        $payout_per=0;
                                        $sale_qty= $sd['total_ach_'.$scheme->id_scheme][0]->ach;
                                        if($sd['max']==$sd['min'] && $sale_qty >= $sd['max']){
                                            $payout_per= $sd['payout_per'];
                                        }else if($sale_qty>=$sd['min'] && $sale_qty<=$sd['max']){
                                            $payout_per= $sd['payout_per'];
                                        } 
                                         if($scheme->has_slabs == 0){ 
                                             $claim_sum = (($sd['ach_'.$id][0]->basic) * $sd['payout_per'])/100;                                                                                
                                         }else{  
                                             $claim_sum = (($sd['total_ach_'.$scheme->id_scheme][0]->basic) * $payout_per)/100;                                                
                                         }
                                          echo $claim_sum; 
                                         ?>
                                        </div></td>
                                   <?php  }  ?>
                            
                        <?php } ?>
                    </tr>
                    <?php $r++; } ?>
                </tbody>
            </table><br>
            <div id="footer_action" style="color: #0056b3">
                <?php if($scheme->claim_status !=1){ ?>
                <a class="btn btn-primary pull-right" id="generate_scheme">Generate Claim Data</a>
                <input type="hidden" name="regen" value="0" />
                <?php }else{ ?>
                <center><?php echo str_replace("_", " ", $schemetype->scheme_type) ?> Claim generated. Last generated on <?php echo date('d-m-Y h:i a', strtotime($scheme->generated_on)) ?></center>
                <a class="btn btn-sm btn-primary" id="view_claimed_report"><i class="mdi mdi-note"></i> View Report</a>
                <a class="btn btn-sm pull-right" id="generate_scheme"><i class="mdi mdi-history mdi-spin"></i> Re-Generate Claim Data</a>
                <input type="hidden" name="regen" value="1" />
                <?php } ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </form>
</div><div class="clearfix"></div>
<button class="btn btn-primary btn-sm pull-right" id="btn_export_report" onclick="javascript:xport.toCSV('Purchase_Scheme');" style="margin: 0; display: none"><span class="fa fa-file-excel-o"></span> Export</button><div class="clearfix"></div><br>
<div class="" id="generated_report"></div>
<div class="clearfix"></div>
<script>
$(document).ready(function(){
    $(document).on("click", "#generate_scheme", function (event) {
        event.preventDefault();
        if(confirm('Do you want to generate cailm data')){
            var serialized = $('#generate_scheme_form').serialize();
            $.ajax({
                url: "<?php echo base_url('Scheme/generate_purchase_claim') ?>",
                method: "POST",
                data: serialized,
                dataType: 'json',
                success: function (data)
                {
                    if(data.result == 'Success'){
                        swal('Success', 'Claim generated. '+data.count+' Products found', 'success');
                        $('#generate_scheme').hide();
                        var leb = '<center>Purchase Claim generated. Last generated on <?php echo date('d-m-Y h:i a') ?></center>\n\
                                    <a class="btn btn-sm btn-primary" id="view_claimed_report"><i class="mdi mdi-note"></i> View Report</a>\n\
                                    <a class="btn btn-sm pull-right" id="generate_scheme"><i class="mdi mdi-history"></i> Re-Generate Claim Data</a>\n\
                                    <input type="hidden" name="regen" value="1" />';
                        $('#footer_action').html(leb);
                    }else{
                        swal('Alert', 'Failed to generated claim. '+data.count+' Products found', 'warning');
                    }
                }
            });
        };
    });
    $(document).on("click", "#view_claimed_report", function (event) {
        var idtype = $('#idscheme_type').val();
        var idscheme = $('#idscheme').val();
        var settlement_type = $('#settlement_type').val();
        var has_slabs = $('#has_slabs').val();
        var scheme_type = 'Purchase_Scheme';
        $.ajax({
            url: "<?php echo base_url('Scheme/view_purchase_claim') ?>",
            method: "POST",
            data: {idscheme:idscheme, idtype: idtype,scheme_type:scheme_type,settlement_type:settlement_type,has_slabs:has_slabs},
            success: function (data)
            {
                if(data != '0'){
                    $('#generated_report').html(data);
                    $('#btn_export_report').css('display','block');
                }else{
                    swal('Alert', 'Failed to view claim. Products found!', 'warning');
                }
            }
        });
    });
});
</script>
<?php include __DIR__.'../../footer.php'; ?>