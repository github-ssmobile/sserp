<script>
function call_by_foc(ele){
    var focmodel = $(ele).val();    
    if(focmodel){
        var focmodel_name = $("option:selected", $(ele)).text();    
        var slabname = $("option:selected", $(ele)).attr("slabname");    
        var idvariant = +$(ele).val();
//      $("#modes_block").find(".fochead").show();
        if(slabname){
            
        }else{ slabname=""; }
        parent=$(ele).parent().parent();
        $(parent).find(".fochead").show();
        var appdata = '<div class="" style="border-top: 1px solid #ddd">\n\
                            <div class="col-md-7" style="padding: 2px">\n\
                                <input type="hidden" class="foc_model" name="'+slabname+'_foc_model['+idvariant+'][]" value="'+focmodel_name+'" />\n\
                                '+focmodel_name+'\n\
                            </div>\n\
                            <div class="col-md-3" style="padding: 2px"><input type="number" class="form-control input-sm foc_unit" name="'+slabname+'_foc_unit['+idvariant+'][]" placeholder="FOC Units" min="1" required="" /></div>\n\
                            <div class="col-md-2" style="padding: 2px;text-align: center;"><a class="btn btn-sm btn-warning remove_foc_btn" id="remove_foc_btn"><i class="fa fa-trash-o fa-lg"></i></a></div>\n\
                            <div class="clearfix"></div>\n\
                      </div>';
    $(parent).find(".fochead").append(appdata);
//        $("#modes_block").find('.focdata').append(appdata);
    }
}
var variants = [];
var cnt=0;
$(document).ready(function(){
    $(document).on('change', '.claim_tar', function () {           
           $('#claim_target_val').val($(this).val());
           if($(this).val() == 1){
               $("#overall_value").css('display','block');
               $("#overall_volume").css('display','none');
               $("#overall_volume").val('');
//               if($('.hasslabs').val()=='1'){
//               }else{
//                   $('.has_slabs').trigger('click');
//                    $('.hasslabs').val('1');
//                }
           }else if($(this).val() == 2){
               $("#overall_volume").css('display','block');
               $("#overall_value").css('display','none');
               $("#overall_value").val('');
           }else{
               $("#overall_value").css('display','none');
               $("#overall_value").val('');
               $("#overall_volume").css('display','none');
               $("#overall_volume").val('');               
//               $('.has_slabs').trigger('click');
//               $('.hasslabs').val('0');
           }
       });
    $(document).on('change', '#idbrand', function () {
        if ($('#idbrand').val()) {
            var brand = +$('#idbrand').val();
            $.ajax({
                url: "<?php echo base_url() ?>Scheme/ajax_variants_by_brand_multiselect",
                method: "POST",
                data: {brand: brand},
                success: function (data)
                {
                    $(".model_block").html(data);                        
                    $(".chosen-select").chosen({search_contains: true});
                    $('#selected_model').html('');
                    variants = [];
                }
            });
        }
    });
     $(document).on('click', '#add_scheme_btn', function () {
        
        var brand = $('#idbrand').val();
        var settlement_type_val = $("#settlement_type_val").val();
        var selmodel = $("#selmodel").val();  
        var has_slabs = $('.has_slabs').val(); 
        if(selmodel[0]==''){return false;}
        var flag=true;
        if ($('#selmodel').val()) {
                    selmodel.forEach(function (item, index) {
                        if (variants.includes(item) === false){
                            flag=true;
                            variants.push(item);
                        }else{
                            flag=false;
                            return false
                        }
                    });
            if (flag){
//                variants.push(selmodel);                
                $.ajax({
                    url: "<?php echo base_url() ?>Scheme/get_variants_byid_for_foc",
                    method: "POST",
                    data:{variant_ids : selmodel, brand:brand, settlement_type_val:settlement_type_val,has_slabs:has_slabs},
                    success: function (data)
                    {
                        if(has_slabs==1){
                            $('#foc_model_table').show();
                            $('#foc_selected_model').append(data);
                            $('#add_slabs_btn').trigger('click');
                        }else{                            
                            $('#model_table').show();
                            $('#selected_model').append(data);                            
                            $("#add_slabs_btn").css('display','none');
                        }
                        $(".chosen-select").chosen({search_contains: true});
                        if(settlement_type_val == 0)
                            $("#sett_type_lb").html('FOC');
                        else if(settlement_type_val == 1) 
                            $("#sett_type_lb").html('Payout');
                        else 
                            $("#sett_type_lb").html('Percentage');
                        
                        $('#selmodel').val('').trigger('chosen:updated');
                    }
                });
            }else{
                swal("😠 Warning!", "Duplicate Product selected!");
                return false;
            }
        }
    });
     $(document).on('click', '#add_slabs_btn', function () {        
        var brand = $('#idbrand').val();
        var has_slabs = $('.has_slabs').val(); 
        var settlement_type_val = $("#settlement_type_val").val();   
        cnt++;
        $.ajax({
                    url: "<?php echo base_url() ?>Scheme/get_slabs",
                    method: "POST",
                    data:{  brand:brand, settlement_type_val:settlement_type_val,variants:variants,cnt:cnt,has_slabs:has_slabs},
                    success: function (data)
                    {
                        $('.slabs').append(data);
                    }
                });
       });
       
       $(document).on('click', '.has_slabs', function () {         
            if((variants.length)>0){
                $('.has_slabs').trigger('click');           
            }else{
//                 if($('#claim_target_val').val() === '1'){             
//                    $('.has_slabs').trigger('click');  
//                }else{
                    if ($(this).is(":checked")){
                        $(this).val('1');
                        $('.hasslabs').val('1');                
                    }else{
                        $(this).val('0');
                        $('.hasslabs').val('0');
                    }
//                }
            }
       });
       
       $(document).on('click', '.all_variants', function () {             
            pr=$(this).parent();
            if ($(this).is(":checked")){
                $(pr).find('.allvariants').val('1');                
            }else{
                $(pr).find('.allvariants').val('0');
            } 
       });
    /*
    $(document).on('change', '#selmodel', function () {
        var selmodel = $(this).val();
        var brand = $('#idbrand').val();
        var settlement_type_val = $("#settlement_type_val").val();
        if ($('#selmodel').val()) {
            if (variants.includes(selmodel) === false){
                variants.push(selmodel);
                $.ajax({
                    url: "<?php echo base_url() ?>Scheme/get_variant_byid_for_foc",
                    method: "POST",
                    data:{id : selmodel, brand:brand, settlement_type_val:settlement_type_val},
                    success: function (data)
                    {
                        $('#model_table').show();
                        $('#selected_model').append(data);
                        $(".chosen-select").chosen({search_contains: true});
                        if(settlement_type_val == 0)
                            $("#sett_type_lb").html('FOC');
                        else if(settlement_type_val == 1) 
                            $("#sett_type_lb").html('Payout');
                        else 
                            $("#sett_type_lb").html('Percentage');
//                      $('#idbrand_chosen').attr("style", "pointer-events: none;");
                    }
                });
            }else{
                swal("😠 Warning!", "Duplicate Product selected!");
                return false;
            }
        }
    });
    */
//    $(document).on('keyup', 'input[id=cust_mobile]', function(e) {
    $(document).on('click', 'input[id=remove_foc_btn]', function () {
        alert('hi');
//        var focmodel = $(this).val();
//        var focmodel_name = $("option:selected", this).text();
//        var parent = $($(this)).closest('td').parent('tr');
//        var idvariant = +parent.find(".idvariant").val();
//        var appdata = '<div>\n\
//                            <div class="col-md-8">\n\
//                                <input type="hidden" class="idvariant" name="idvariant[]" value="'+focmodel+'" />\n\
//                                '+focmodel_name+'\n\
//                            </div>\n\
//                            <div class="col-md-4"><input type="number" class="form-control input-sm foc_unit" name="foc_unit['+idvariant+'][]" placeholder="FOC Units" min="1" required="" style="width: 150px" /></div>\n\
//                      </div>';
//        parent.find('#focdata').append(appdata);
    });
    $(document).on('keyup', '.last_purchase_price, .price_drop', function() {
        var parent = $($(this)).closest('td').parent('tr');
        var last_purchase_price = +parent.find(".last_purchase_price").val();
        var price_drop = +parent.find(".price_drop").val();
        var igst = +parent.find(".igst").val();
        if($('#gst_selected_type').val() === "1"){ // including
            var new_price = last_purchase_price - price_drop;
            var igstamt = 0;
            var taxable = price_drop;
        }else{ // excluding
            var cal = (igst + 100) / 100;
            var taxable = price_drop / cal;
            var igstamt = price_drop - taxable;
            var price = last_purchase_price - taxable;
            new_price = price.toFixed(2);
        }
        parent.find(".new_price").val(new_price);
        parent.find(".excluding_gst_amt").val(igstamt.toFixed(2));
        parent.find(".spexcluding_gst_amt").html(igstamt.toFixed(2));
        parent.find(".taxable_gst_price").val(taxable.toFixed(2));
        parent.find(".sptaxable_gst_price").html(taxable.toFixed(2));
        $('#gst_select_block').attr("style", "pointer-events: none;");
    });
    $(document).on('click', '.remove_btn', function() {
        var parent = $($(this)).closest('td').parent('tr');
        var idvariant = parent.find(".idvariant").val();
        var product_name = parent.find(".product_name").val();
        swal({
            title: "😕 Want to Remove Product?",
            text: product_name,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#E84848',
            confirmButtonText: 'Yes, Remove it!',
            closeOnConfirm: false,
        },
        function(){
            swal("Removed!", product_name+" Product removed from this list!", "success");
            variants = jQuery.grep(variants, function(value) { return value !== idvariant; });
            $(parent).remove();
            if(variants.length == 0){
                $('#gst_select_block').attr("style", "pointer-events: auto;");
            }
        });
    });
    $(document).on('click', '#create_scheme_btn', function() {
        if($("#idvendor").val() == '' || $("#idbrand").val() == ''){
            swal('Alert!','All fields are mandatory!!','warning');
            return false;
        }
        if(variants.length == 0){
            swal('Alert!','Select at least one product!!','warning');
            return false;
        }
    });
     $(document).on('click', '.remove_foc_btn', function () {        
        $(this).closest('div').parent().remove();
    });
    $(document).on('click', '.remove_slab_btn', function () {                
        $(this).parent().parent().remove();
    });
});
</script>
<form>
    <div class="col-md-10 col-md-offset-1">
        <div class="thumbnail"><br>
            <input type="hidden" name="idscheme_type" value="<?php echo $schemetype->id_scheme_type ?>" />
            <input type="hidden" name="create_by" value="<?php echo $this->session->userdata('id_users') ?>" />
            <div class="col-md-2">Scheme Code</div>
            <div class="col-md-4"><input type="text" class="form-control input-sm" placeholder="Enter Scheme code" required="" name="scheme_code" /></div>
            <div class="col-md-2">Scheme Name</div>
            <div class="col-md-4"><input type="text" class="form-control input-sm" placeholder="Enter Scheme Name" required="" name="scheme_name" /></div>
            <div class="clearfix"></div><br>
            <div class="col-md-2">Offer Date</div>
            <div class="col-md-4">
                <div class="input-group">
                    <div class="input-group-btn">
                        <input type="text" name="date_from" id="date_from" class="form-control input-sm" data-provide="datepicker" placeholder="From Date" onfocus="blur()" autocomplete="off" required="">
                    </div>
                    <div class="input-group-btn">
                        <input type="text" name="date_to" id="date_to" class="form-control input-sm" data-provide="datepicker" placeholder="To Date" onfocus="blur()" autocomplete="off" required="">
                    </div>
                </div>
            </div>
            <div class="col-md-2">Vendor</div>
            <div class="col-md-4">
                <select data-placeholder="Select Vendor" class="chosen-select form-control input-sm" required="" id="idvendor" name="idvendor">
                    <?php foreach ($vendor_data as $vendor){ ?>
                    <option value="<?php echo $vendor->id_vendor ?>"><?php echo $vendor->vendor_name ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="clearfix"></div><br>
            <div class="col-md-2">Brand</div>
            <div class="col-md-4">
                <select data-placeholder="Select Brand" class="chosen-select form-control input-sm" required="" id="idbrand" name="idbrand">
                    <option value="">Select Brand</option>
                    <?php foreach ($brand_data as $branch){ ?>
                    <option value="<?php echo $branch->id_brand ?>"><?php echo $branch->brand_name ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="clearfix"></div><br>
            <div class="col-md-2">Claim Target</div>
            <div class="col-md-6">
                <input type="hidden" name="claim_target_val" id="claim_target_val" value="0" />
                <div class="col-md-4 p-1">
                    <label class="btn btn-block btn-sm btn-default" for="qty_target">
                        <input class="form-check-input claim_tar" checked="" type="radio" name="claim_tar" id="qty_target" value="0" />
                        Quantity<div class="clearfix"></div>
                    </label>
                </div>
                <div class="col-md-4 p-1">
                    <label class="btn btn-block btn-sm btn-default" for="overall_valsettle">
                        <input class="form-check-input claim_tar" type="radio" name="claim_tar" id="overall_valsettle" value="1" />
                        Oevrall Value <div class="clearfix"></div>
                    </label>
                </div>
<!--                <div class="col-md-4 p-1">
                    <label class="btn btn-block btn-sm btn-default" for="overall_volsettle">
                        <input class="form-check-input claim_tar" type="radio" name="claim_tar" id="overall_volsettle" value="2" onclick="$('#claim_target_val').val($(this).val())" />
                        Oevrall Volume
                    </label>
                </div>-->
                <!--<div class="col-md-4 p-1"></div>-->
                <!--<div class="col-md-4 p-1">-->
                <input type="hidden" name="overall_value" id="overall_value" class="form-control input-sm"  value="0" style="display: none" />
                <!--</div>-->
                <!--<div class="col-md-4 p-1">-->
                <input type="hidden" name="overall_volume" id="overall_volume" class="form-control input-sm" value="0" placeholder="Enter Overall Volume" style="display: none" />
                <!--</div>-->
            </div>
            <div class="clearfix"></div><br>
            <div class="col-md-2">Settelment Type</div>
            <div class="col-md-6">
                <div class="" style="margin-bottom: 0px;">
                    <input type="hidden" name="settlement_type_val" class="settlement_type_val" id="settlement_type_val" value="0" />
                    <div class="col-md-4 p-1">
                        <label class="btn btn-block btn-sm btn-default" for="foc" style="margin-bottom: 0px;">
                            &nbsp; &nbsp;
                            <input class="form-check-input settlement_type" checked="" type="radio" name="settlement_type" id="foc" value="0" onclick="$('#settlement_type_val').val($(this).val())" />
                            FOC <div class="clearfix"></div>
                        </label>
                    </div>
                    <div class="col-md-4 p-1">
                        <label class="btn btn-block btn-sm btn-default" for="value" style="margin-bottom: 0px;">
                            &nbsp; &nbsp;
                            <input class="form-check-input settlement_type" type="radio" name="settlement_type" id="value" value="1" onclick="$('#settlement_type_val').val($(this).val())" />
                            Payout
                        </label>
                    </div>
                    <div class="col-md-4 p-1">
                        <label class="btn btn-block btn-sm btn-default" for="percentage" style="margin-bottom: 0px;">
                            &nbsp; &nbsp;
                            <input class="form-check-input settlement_type" type="radio" name="settlement_type" id="percentage" value="2" onclick="$('#settlement_type_val').val($(this).val())" />
                            Percentage
                        </label><div class="clearfix"></div>
                    </div>
                </div>
            </div>
             <div class="col-md-2 col-sm-2" style="font-family: Kurale; font-size: 15px; padding: 2px 5px;">
                <label class="material-switch  " for="has_slabs" style="font-weight: 100; padding: 7px 12px;">
                    <input class="has_slabs" id="has_slabs" name="has_slabs" type="checkbox" unchecked value="0">                    
                    <label for="has_slabs" class="label-primary" style="margin-bottom: 10px"></label>
                    <span>Has Slabs</span>
                </label>
                <input class="hasslabs" id="hasslabs" name="hasslabs" type="hidden" value="0">
            </div>
            <div class="clearfix"></div><br>
            <div class="col-md-2">Model Variant</div>
            <div class="col-md-10">
                <div class="model_block">
                    <select class="chosen-select form-control" name="selmodel" id="selmodel" required=""><option value="">First Select Brand</option></select>
                </div>
            </div>
            <div class="clearfix"></div><br>
            <button type="button" id="add_scheme_btn" class="btn btn-primary waves-block waves-effect pull-right" >Add</button>
<!--            <div class="col-md-2 text-muted">Payout Percentage</div>
            <div class="col-md-4 text-muted">
                <input type="text" name="min_val_per" id="min_val_per" class="form-control input-sm" placeholder="Payout Percentage">
            </div>-->
            <div class="clearfix"></div><br>
        </div>
    </div><div class="clearfix"></div>
    
    <div class="col-md-12">
    <div class="thumbnail p-0" id="foc_model_table" style="display: none;">
        <table class="table table-condensed table-striped p-0 table-bordered">
            <thead>
                <th>Id</th>
                <th>Model Variant</th>    
                <th>All Color Variants</th>                
                <th>Remove</th>
            </thead>
            <tbody id="foc_selected_model"></tbody>
        </table>            
        <div class="clearfix"></div><br>
        <button type="button" id="add_slabs_btn"  class="btn btn-primary waves-block waves-effect pull-right">Add Slabs</button>
        <div class="clearfix"></div><br>
        <div class="slabs"></div>
          
        <div class="clearfix"></div>
        <!--<button type="submit" id="create_scheme_btn" class="btn btn-primary waves-block waves-effect pull-right" formmethod="POST" formaction="<?php echo base_url('Scheme/create_sell_out_scheme') ?>">Create</button>-->    
        </div><div class="clearfix"></div>    
    </div>
    <div class="clearfix"></div>
    <div class="col-md-12">
        <div class="thumbnail p-0" id="model_table" style="display: none;">
            <table class="table table-condensed table-striped p-0 table-bordered">
                <thead>
                    <th>Id</th>
                    <th>Model Variant</th>
                    <th>All Color Variants</th>
                    <th>Min Target</th>
                    <th>Max Target</th>
                    <th id="sett_type_lb">FOC Model</th>
                    <th>Remove</th>
                </thead>
                <tbody id="selected_model"></tbody>
            </table>
            <!--<button type="submit" id="create_scheme_btn" class="btn btn-primary waves-block waves-effect pull-right" formmethod="POST" formaction="<?php echo base_url('Scheme/create_sell_out_scheme') ?>">Create</button>-->
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="clearfix"></div><br>
    <button type="submit" id="create_scheme_btn" class="btn btn-primary waves-block waves-effect pull-right" formmethod="POST" formaction="<?php echo base_url('Scheme/create_sell_out_scheme') ?>">Create</button>    
    <div class="clearfix"></div><br>
</form>
<center><span style="color:#1b6caa;font-family: Kurale;font-size: 22px"><i class="<?php echo $schemetype->font ?>"></i> <?php echo str_replace('_', ' ', $schemetype->scheme_type) ?> Schemes</span></center><hr>
<table class="table table-condensed">
    <thead>
        <th>Id</th>
        <th>Brand</th>
        <th>Code</th>
        <th>Name</th>
        <th>From</th>
        <th>To</th>
        <th>Vendor</th>
        <th>Claim Target</th>
        <th>Settlement Type</th>
        <th>Details</th>
    </thead>
    <tbody>
        <?php foreach ($schemes as $scheme) { ?>
        <tr>
            <td><?php echo $scheme->id_scheme ?></td>
            <td><?php echo $scheme->brand_name ?></td>
            <td><?php echo $scheme->scheme_code ?></td>
            <td><?php echo $scheme->scheme_name ?></td>
            <td><?php echo $scheme->date_from ?></td>
            <td><?php echo $scheme->date_to ?></td>
            <td><?php echo $scheme->vendor_name ?></td>
            <td><?php echo empty($scheme->claim_target) ? "Qty" : "Overall Value"; ?></td>
            <td><?php if($scheme->settlement_type == 0){ echo "FOC"; }elseif($scheme->settlement_type == 1){ echo "Payout"; }else{ echo 'Percentage'; } ?></td>
            <td><a class="btn btn-sm btn-primary" href="<?php echo base_url('Scheme/scheme_details/'.$scheme->idscheme_type.'/'.$scheme->id_scheme) ?>"><i class="fa fa-info"></i></a></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
