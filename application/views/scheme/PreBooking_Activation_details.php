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
            <b><?php echo $scheme->scheme_name ?></b>
            <div class="clearfix"></div>
            <center>
                <input type="hidden" name="bookdate_from" value="<?php echo $scheme->date_from ?>" />
                <input type="hidden" name="bookdate_to" value="<?php echo $scheme->date_to ?>" />
                <input type="hidden" name="actdate_from" value="<?php echo $scheme->activate_date_from ?>" />
                <input type="hidden" name="actdate_to" value="<?php echo $scheme->activate_date_to ?>" />
                <span class="col-md-6 thumbnail p-1">PreBooking: <?php echo date('d-M-Y', strtotime($scheme->date_from)) .' To '. date('d-M-Y', strtotime($scheme->date_to)) ?></span>
                <span class="col-md-6 thumbnail p-1">Activation: <?php echo date('d-M-Y', strtotime($scheme->activate_date_from)) .' To '. date('d-M-Y', strtotime($scheme->activate_date_to)) ?></span>
            </center>
        </div><div class="clearfix"></div><hr>
        <span class="col-md-12">Vendor: <?php echo $scheme->vendor_name ?></span>
        <input type="hidden" name="idvendor" value="<?php echo $scheme->idvendor ?>" />
        <div class="clearfix"></div><hr>
        <input type="hidden" name="idscheme" id="idscheme" value="<?php echo $scheme->id_scheme ?>" />
        <input type="hidden" name="idscheme_type" id="idscheme_type" value="<?php echo $scheme->idscheme_type ?>" />
        <span class="col-md-4">Scheme ID: <?php echo $scheme->id_scheme ?></span>
        <span class="col-md-4">Settlement Type: <?php if($scheme->settlement_type == 0){ echo "FOC"; }elseif($scheme->settlement_type == 1){ echo "Payout"; }else{ echo 'Percentage'; } ?></span>
        <span class="col-md-4">Claim Target: <?php echo empty($sd->claim_target) ? "Qty" : "Value"; ?></span><div class="clearfix"></div><hr>
        <span class="col-md-12"><h5><?php echo str_replace('_', ' ', $schemetype->scheme_type) ?> Scheme. Details as mentioned below,</h5></span>
        <?php if($scheme->min_val_per_for_booking){ ?><center>Minimun Booking value in percentage <b><?php echo $scheme->min_val_per_for_booking; ?>%</b></center><?php } ?>
        <table class="table table-bordered table-condensed" style="margin: 0;">
            <thead>
                <th>Model</th>
                <th colspan="2"><center>Target</center></th>
                <th>Incentive</th>
                <th colspan="3"><center>Achievement Count</center></th>
            </thead>
            <thead>
                <th></th>
                <th>Min Booking value%</th>
                <th>Min</th>
                <th>Max</th>
                <th>Per Unit</th>
                <th>Booking</th>
                <th>Activation</th>
                <th style="background-color: #cdfbee">Valid Count</th>
            </thead>
            <tbody>
                <?php foreach ($scheme_data as $sd){ ?>
                <tr>
                    <td><?php echo $sd->full_name; ?>
                        <input type="hidden" name="idvariant[]" value="<?php echo $sd->idvariant ?>" />
                        <input type="hidden" name="min_target[]" value="<?php echo $sd->min_target ?>" />
                        <input type="hidden" name="min_target[]" value="<?php echo $sd->min_target ?>" />
                        <input type="hidden" name="max_target[]" value="<?php echo $sd->max_target ?>" />
                        <input type="hidden" name="price[<?php echo $sd->idvariant ?>]" value="<?php echo $sd->price ?>" />
                        <input type="hidden" name="min_val_per_for_booking[]" value="<?php echo $sd->min_val_per_for_booking ?>" />
                    </td>
                    <td><?php echo $sd->min_val_per_for_booking; ?>%</td>
                    <td><?php echo $sd->min_target; ?></td>
                    <td><?php echo $sd->max_target; ?></td>
                    <td><?php echo $sd->price; ?></td>
                    <td><?php echo $prebook_count[$sd->idvariant][0]->count_pre; ?></td>
                    <td><?php echo $sale_count[$sd->idvariant][0]->sum_qty; ?></td>
                    <td style="background-color: #cdfbee"><?php echo min($prebook_count[$sd->idvariant][0]->count_pre,$sale_count[$sd->idvariant][0]->sum_qty); ?></td>
                </tr>
                <?php } ?>
            </tbody>
        </table><br>
        <div id="footer_action" style="color: #0056b3">
            <?php if($scheme->claim_status !=1){ ?>
            <a class="btn btn-primary pull-right" id="generate_scheme">Generate Claim Data</a>
            <input type="hidden" name="regen" value="0" />
            <?php }else{ ?>
            <center><?php echo str_replace("_", " ", $schemetype->scheme_type) ?> Claim generated. Last generated on <?php echo date('d-m-Y h:i a', strtotime($scheme->generated_on)) ?></center>
            <a class="btn btn-sm btn-primary" id="view_claimed_report"><i class="mdi mdi-note"></i> View Report</a>
            <a class="btn btn-sm pull-right" id="generate_scheme"><i class="mdi mdi-history"></i> Re-Generate Claim Data</a>
            <input type="hidden" name="regen" value="1" />
            <?php } ?>
        </div>
        <div class="clearfix"></div>
    </div>
    </form>
</div>
<div class="clearfix"></div>
<button class="btn btn-primary btn-sm pull-right" id="btn_export_report" onclick="javascript:xport.toCSV('PreBooking_Activation_Scheme');" style="margin: 0; display: none"><span class="fa fa-file-excel-o"></span> Export</button><div class="clearfix"></div><br>
<div class="" id="generated_report"></div>
<div class="clearfix"></div>
<script>
$(document).ready(function(){
    $(document).on("click", "#generate_scheme", function (event) {
        event.preventDefault();
        if(confirm('Do you want to generate cailm data')){
            var serialized = $('#generate_scheme_form').serialize();
            $.ajax({
                url: "<?php echo base_url('Scheme/generate_prebooking_claim') ?>",
                method: "POST",
                data: serialized,
                dataType: 'json',
                success: function (data)
                {
                    if(data.result == 'Success'){
                        swal('Success', 'Claim generated. '+data.count+' Products found', 'success');
                        $('#generate_scheme').hide();
//                        $('#footer_action').html('<?php echo str_replace("_", " ", $schemetype->scheme_type) ?> Claim generated successfully <center><a class="btn btn-primary" id="view_claimed_report">View Report</a></center>');
                        var leb = '<center>PreBooking Activation drop claim generated. Last generated on <?php echo date('d-m-Y h:i a') ?></center>\n\
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
        var scheme_type = 'PreBooking_Activation_Scheme';
        $.ajax({
            url: "<?php echo base_url('Scheme/view_prebooking_claim') ?>",
            method: "POST",
            data: {idscheme:idscheme, idtype: idtype,scheme_type:scheme_type},
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