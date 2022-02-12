<?php include __DIR__.'../../header.php'; ?>

<script>
$(document).ready(function(){
$(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
    $(document).on('change', '#model', function() {  
        var model = +$('#model').val();    
        var days = +$('#days').val();  
        $.ajax({
            url:"<?php echo base_url() ?>Outward/ajax_model_variants_allocation_data",
            method:"POST",
             data:{variant : model,days : days,idgodown:1},
            success:function(data)
            {
//                      $("#model_data").html(data);  
                        $(".search_label").hide();
//                      $("#variant_data").html(data);   
                        $('.allocationform').show();
                        $('.exp_btn').show();
                        var splitted = data.split("|"); // RESULT
                        $("#variant_data").html(splitted[0]);                      
                        $('#variant_data .top_row').html(splitted[1]); 
            }
        });
    });
    $(document).on("click", ".allocationform", function(event) {   
        event.preventDefault();
        if (confirm('Do you want to Submit the allocation!!')) {
        var serialized = $('.allocation_form').serialize();
        $.ajax({
        url: "<?php echo base_url() ?>Outward/save_transfer_balance",
                method: "POST",
                data: serialized,
                dataType: 'json',
                success: function (data)
                {
                    if (data.data === 'success') {
                        alert("Allocation submitted successfully!!");
                        location.reload();
                    } else if (data.data === "fail") {
                        alert("Fail to save allocation!! ")
                    } else {
                        alert("Select at least one model !! ")
                    }
                }
             });
         }
    });
});
</script>
<style>
    .fixheader {
        background-color: #e1f0ff;
        position: sticky;
        top: 0;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 999;
    }
    .fixheader1 {
        background-color: #e1f0ff;
        position: sticky;
        top: 67px;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 999;
    }
    .fixheader2 {
        background-color: #e1f0ff;
        position: sticky;
        top: 115px;
        box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
        z-index: 999;
    }
/*    .fixleft{
      position: sticky;
      left:0px;
      box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    }
    .fixleft1{
      position: sticky;
      left:47px;
      box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    }
    .fixleft2{
      position: sticky;
      left:133px;
      box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    }*/
</style>
    <div class="col-md-11">
        <center>
            <h3 style="margin-top: 0"><span class="mdi mdi-dropbox fa-lg"></span> Model Allocation</h3>
        </center>
    </div><div class="clearfix"></div><hr>
    <div class="fixedelement hovereffect1" style="padding: 5px; margin-bottom: 10px">
        <div class="col-md-1" style="padding: 6px 0px 0px 21px;">Days Sale</div>
        <div class="col-md-1" style="padding: 0px 16px 0px 0px;">
            <input type="text" class="form-control input-sm" name="days" value="30" id="days" required="" />
            <input type="hidden" name="iduser" value="<?php echo $this->session->userdata('id_users') ?>" />
        </div>
        <div class="col-md-6 col-sm-6" style="padding: 0 5px">
            <div style="padding: 5px 0">
                <select class="chosen-select form-control input-sm" name="model" id="model">
                    <option value="">Select Product</option>
                    <?php foreach ($model_variant as $variant) { ?>
                        <option value="<?php echo $variant->id_variant; ?>"><?php echo $variant->product_category_name.' '.$variant->full_name; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="col-md-1 col-sm-2 pull-right hidden exp_btn" style="display: none;">
            <button class="btn btn-primary btn-sm gradient2 export" onclick="javascript:xport.toCSV('variant_data');" style="margin-top: 6px;line-height: unset; "><span class="fa fa-file-excel-o"></span> Export</button>
        </div>
        <div class="clearfix"></div><div class="clearfix"></div>
    </div>
    <form class="allocation_form">
        <input type="hidden" name="allocation_type" value="1" />
        <div class="thumbnail" id="search_block" style="margin-bottom: 0; padding: 0;">
            <center><div class="search_label"><h3>Select From Above Filter</h3></div></center>
<!--            <center><table id="model_data" class="table-condensed table-bordered table-striped table-responsive table-hover" style="font-size: 13px;"></table></center><hr>        -->
            <table id="variant_data" class="table table-condensed table-bordered table-responsive table-hover" style="font-size: 13px;"></table>
        </div>
        <br><br>    
        <div class="col-md-2 pull-right">
            <button type="button" class="allocationform btn btn-primary" style="display: none; margin: 0; right: 30px; bottom: 20px">Submit</button>
        </div>
    </form>
<?php include __DIR__.'../../footer.php'; ?>