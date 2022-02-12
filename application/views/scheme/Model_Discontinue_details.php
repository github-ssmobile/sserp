<?php include __DIR__.'../../header.php'; ?>
<div class="col-md-10"><center><span style="color:#1b6caa;font-family: Kurale;font-size: 22px"><i class="<?php echo $schemetype->font ?>"></i> <?php echo str_replace('_', ' ', $schemetype->scheme_type) ?> Scheme</span></center></div><div class="clearfix"></div><hr>
<div class="col-md-10 col-md-offset-1">
    <form id="generate_scheme_form">
        <div class="thumbnail" style="font-size: 14px;">
            <div class="col-md-3" style="color: #0056b3">
                <center><h4><?php echo $scheme->brand_name; ?></h4></center>
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
            <span class="col-md-4">Scheme ID: <?php echo $scheme->id_scheme ?></span>
            <input type="hidden" name="idscheme" id="idscheme" value="<?php echo $scheme->id_scheme ?>" />
            <input type="hidden" name="iddiscon" id="iddiscon" value="<?php echo $scheme->discontinue_scheme_id ?>" />
            <input type="hidden" name="idscheme_type" id="idscheme_type" value="<?php echo $scheme->idscheme_type ?>" />
            <span class="col-md-12"><h5><?php echo str_replace('_', ' ', $schemetype->scheme_type) ?> Scheme. Details as mentioned below,</h5></span>
            <table class="table table-bordered table-condensed" style="margin: 0">
                <thead>
                    <th>Model</th>
                </thead>
                <tbody>
                    <?php foreach ($scheme_data as $sd){ ?>
                    <tr>
                        <td>
                            <?php echo $sd->full_name; ?>
                            <input type="hidden" name="idvariant[]" value="<?php echo $sd->idvariant ?>" />
                        </td>
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
                <a class="btn btn-sm pull-right waves-effect waves-teal" id="generate_scheme" style="border: 1px solid #01a478"><i class="mdi mdi-history mdi-spin"></i> Re-Generate Claim Data</a>
                <input type="hidden" name="regen" value="1" />
                <?php } ?>
            </div>
            <div class="clearfix"></div>
        </div>
    </form>
</div><div class="clearfix"></div><hr>
<button class="btn btn-primary btn-sm pull-right" id="btn_export_report" onclick="javascript:xport.toCSV('Model_Discontinue_Scheme');" style="margin: 0; display: none"><span class="fa fa-file-excel-o"></span> Export</button><div class="clearfix"></div><br>
<div class="" id="generated_report"></div>
<div class="clearfix"></div>
<script>
$(document).ready(function(){
    $(document).on("click", "#generate_scheme", function (event) {
        event.preventDefault();
        if(confirm('Do you want to generate cailm data')){
            var serialized = $('#generate_scheme_form').serialize();
            $.ajax({
                url: "<?php echo base_url('Scheme/generate_model_discontinue_claim') ?>",
                method: "POST",
                data: serialized,
                dataType: 'json',
                success: function (data)
                {
                    if(data.result == 'Success'){
                        swal('Success', 'Claim generated. '+data.count+' Products found', 'success');
                        $('#generate_scheme').hide();
//                        $('#footer_action').html('<?php echo str_replace("_", " ", $schemetype->scheme_type) ?> Claim generated successfully <center><a class="btn btn-primary" id="view_claimed_report">View Report</a></center>');
                        var leb = '<center>Model Discontinue claim generated. Last generated on <?php echo date('d-m-Y h:i a') ?></center>\n\
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
        var scheme_type = 'Model_Discontinue_Scheme';
        $.ajax({
            url: "<?php echo base_url('Scheme/view_model_discontinue_claim') ?>",
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