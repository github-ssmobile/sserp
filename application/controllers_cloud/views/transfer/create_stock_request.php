<?php include __DIR__ . '../../header.php'; ?>

<script>

    $(document).ready(function () {
        
        $(document).on('change', '#brand', function () {
            if ($('#brand').val()) {
                var product_category = 0;
                var brand = +$('#brand').val();
                $.ajax({
                    url: "<?php echo base_url() ?>Stock_allocation/ajax_variants_by_brand",
                    method: "POST",
                    data: {brand: brand, product_category: product_category},
                    success: function (data)
                    {
                        $(".variant").html(data);                        
                        $(".chosen-select").chosen({search_contains: true});
                    }
                });
            }
        });
         $(document).on('change', '#branch', function () {
                var flag=0;
                $("#variant_data .delete_row").each(function () {                    
                    flag += 1;      
                });
                if(flag > 0){
                        $("#branch").prop('selectedValue', $("#request_to").val());
                        alert("Sorry, You cant chnage the branch after adding products!");                          
                        return false;
                }else{
                    if ($('#branch').val()) {                                
                        $("#request_to").val($(this).val());                        
                    }
               }        
        });
        
        $(document).on("click", ".delete_row", function (event) {
        
                if (confirm('Do you want to Delete this product!!')) {
                    var variant = $(this).attr("variant"); 
                    var idgodown = $(this).attr("godown"); 
                    element=$(this);
                    $.ajax({
                    url: "<?php echo base_url() ?>Stock_allocation/remove_variant",
                    method: "POST",                
                    data: {variant: variant,idgodown:idgodown},                
                    dataType:'json',
                    success: function (data)
                    {
                       if(data.data === 'success'){
                            $(element).parent().parent().fadeOut();   
                            $(element).parent().parent().remove();
                        }else if(data.data === "fail"){
                            alert("Fail to remove!! ");
                        }
                    }
                });
            }
        });
        
        $(document).on("click", ".requestform", function (event) {
            event.preventDefault();
            
            var bool=true;
            $("#variant_data .qtt").each(function () {
                    var get_textbox_value = $(this).val();                    
                    if ($.isNumeric(get_textbox_value) && get_textbox_value > 0) {
                            bool=true;
                       }else{                            
                            bool=false;  
                            return false;
                       }                  
                });             
            if(bool===true){
                if (confirm('Do you want to Submit the Stock Request!!')) {                
                var serialized = $('.request_form').serialize();
                $.ajax({
                    url: "<?php echo base_url() ?>Transfer/save_stock_request",
                    method: "POST",
                    data: serialized,
                    dataType: 'json',
                    success: function (data)
                    {
                        if (data.data === 'success') {
                            alert("Request submitted successfully!!");
                            location.reload();
                        } else if (data.data === "fail") {
                            alert("Fail to save stock request!! ");
                        } else {
                            alert("Select at least one model !! ");
                        }
                    }
                });
                }
            }else{
                alert("Please enter valid quantity!!");
                return false;
            }
        });
        $(document).on('change', '#model', function () {
            var variant_id = +$('#model').val();      
            var brand = +$('#brand').val();
            var idgodown = +$('#idgodown').val();
            var godown_name = $('#idgodown option:selected').text();
            var branch = +$('#branch').val();
            var request_from = +$('#request_from').val();
            if (!idgodown) {
                alert("Please select godown!");
                return false;
            }
            if (!branch) {
                alert("Please select branch!");
                return false;
            }
                    $.ajax({
                        url: "<?php echo base_url() ?>Transfer/ajax_variants_by_id",
                        method: "POST",
                        data: {variant_id: variant_id, brand: brand, idgodown: idgodown,request_to:branch,godown_name:godown_name,request_from:request_from},
                        success: function (data)
                        {
                            var idexist=data.replace(/\s/g, '');
                            if(idexist==="<exist>"){                                 
                             alert("Model already added!");   
                            }else{
                             $("#variant_data").append(data);                            
                             $('.requestform').show();
                            }
                        }
                    });
                }); 
             $("#variant_data").on('input', '.qtt', function () {    
                var w_ty = +$(this).val(); 
                var stock=$(this).parent().parent().find('.bstcok').val();                
                         if (w_ty && w_ty > 0) {
                             /*if (stock < w_ty) {
                                 alert("Sorry, Branch dose not have enough quantity!!");
                                 $(this).val("0");
                                 $(this).removeAttr('style');

                             } else {*/
                                 $(this).attr('style', "background: #caffca;");
                            /* }*/
                         } else {
                             $(this).removeAttr('style');

                         }
                     });
                
            });
</script>
<div class="col-md-11">
    <center>
        <h3 style="margin-top: 0"><span class="mdi mdi-home-variant fa-lg"></span> Stock Request</h3>
    </center>
</div><div class="clearfix"></div>
<div class="fixedelement hovereffect1" style="padding: 5px; margin-bottom: 10px"><br>        
    <div class="col-md-2  col-xs-6 col-sm-4">
        <select class="chosen-select form-control input-sm" name="branch" id="branch">
            <option value="0">Select Branch</option>
            <?php foreach ($branch_data as $branch) { ?>
                <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
            <?php } ?>
        </select>
        <div class="chosen-container chosen-container-single branch_lable" style="display:none">
            
        </div>
    </div>    
    <div class="col-md-2 col-xs-6 col-sm-4">
        <select class="chosen-select form-control input-sm" name="idgodown"  id="idgodown">            
            <?php foreach ($active_godown as $godown) { ?>
                <option value="<?php echo $godown->id_godown ?>"><?php echo $godown->godown_name ?></option>
            <?php } ?>
        </select>
    </div>    
    <div class="col-md-2  col-xs-6 col-sm-4">
        <select class="chosen-select form-control input-sm" name="brand" id="brand">
            <option value="0">Select Brand</option>
            <?php foreach ($brand_data as $brand) { ?>
                <option value="<?php echo $brand->id_brand; ?>"><?php echo $brand->brand_name; ?></option>
            <?php } ?>
        </select>
    </div>   
    <div class="col-md-4  col-xs-12 col-sm-6 variant">        
        <select class="chosen-select form-control" name="model" id="model" required="">
            <option value="">Select Model</option>                
        </select>        
    </div>    
    <div class="clearfix"></div>   
    <br>  
</div>    
   

<form class="request_form">    
    <input type="hidden" name="request_to" id="request_to" value="0" />
    <input type="hidden" name="request_type" id="request_type" value="<?php echo $request_type; ?>" />
    <input type="hidden" name="request_from" id="request_from" value="<?php echo $request_from ?>" />
    <div class="thumbnail" id="search_block" style="margin-bottom: 0; padding: 0;">                
        <center>
            <table id="variant_data" class="table-condensed table-bordered table-striped table-responsive table-hover" style="font-size: 13px;width: 100%">
            <thead class='fixedelement' style='text-align: center;position: none !important;'>
            <th>Model Name</th><th>Godown</th>
            <!--<th>Branch Stock</th>-->
            <th>Branch Sale</th><th>My Stock</th><th>My Sale</th><th>Requested Qty</th><th>Remove</th></thead>                
            
            </table>
        </center>        
    </div>
    <br><br>    
    <div class="col-md-2 pull-right">
        <button type="button" class="requestform btn btn-primary" style="display: none; margin: 0; right: 30px; bottom: 20px">Submit</button>
    </div>
</form>


<?php include __DIR__ . '../../footer.php'; ?>