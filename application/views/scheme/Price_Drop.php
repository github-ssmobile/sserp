<div class="col-md-10 col-md-offset-1">
    <div class="thumbnail"><br>
        <form>
            <input type="hidden" name="idscheme_type" value="<?php echo $schemetype->id_scheme_type ?>" />
            <div class="col-md-2">Scheme Code</div>
            <div class="col-md-4"><input type="text" class="form-control input-sm" placeholder="Enter Scheme code" required="" name="scheme_code" /></div>
            <div class="col-md-2">Scheme Name</div>
            <div class="col-md-4"><input type="text" class="form-control input-sm" placeholder="Enter Scheme Name" required="" name="scheme_name" /></div>
            <div class="clearfix"></div><br>
            <div class="col-md-2">Date From</div>
            <div class="col-md-4"><input type="text" class="form-control input-sm" placeholder="Select Date from" data-provide="datepicker" onfocus="blur()" autocomplete="off" required=""  name="date_from" /></div>
            <!--<div class="col-md-2">Date To</div>-->
            <!--<div class="col-md-4"><input type="text" class="form-control input-sm" placeholder="Select Date to" data-provide="datepicker" onfocus="blur()" autocomplete="off" name="date_to" /></div>-->
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
            <div class="col-md-2">Vendor</div>
            <div class="col-md-4">
                <select data-placeholder="Select Vendor" class="chosen-select form-control input-sm" required="" id="idvendor" name="idvendor">
                    <?php foreach ($vendor_data as $vendor){ ?>
                    <option value="<?php echo $vendor->id_vendor ?>"><?php echo $vendor->vendor_name ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="clearfix"></div><br>
            <div class="col-md-2">Model Variant</div>
            <div class="col-md-4">
                <div class="model_block">
                    <select class="chosen-select form-control" name="selmodel" id="selmodel" required=""><option value="">First Select Brand</option></select>
                </div>
            </div>
            <div class="col-md-2">
                Price Drop
                <input type="hidden" id="gst_selected_type" value="1" name="gst_selected_type" />
            </div>
            <div class="col-md-4 text-center" id="gst_select_block">
                <label class="col-md-6 p-1" for="including">
                    <input class="form-check-input gstprice" checked="" type="radio" name="gstprice" id="including" value="1" onclick="$('#gst_selected_type').val($(this).val())" />
                    Including GST<div class="clearfix"></div>
                </label>
                <label class="col-md-6 p-1" for="excluding">
                    <input class="form-check-input gstprice" type="radio" name="gstprice" id="excluding" value="0" onclick="$('#gst_selected_type').val($(this).val())" />
                    Excluding GST
                </label>
            </div>
            <div class="clearfix"></div><br>
            <div>
                <div class="thumbnail p-0" id="model_table" style="display: none; margin-top: 10px; padding: 0">
                    <table class="table table-condensed table-striped p-0">
                        <thead>
                            <th>Id</th>
                            <th>Model Variant</th>
                            <th>All Color Variants</th>
                            <!--<th>StockQty</th>-->
                            <th class="col-md-2">Last purchase price</th>
                            <th class="col-md-2">New price</th>
                            <th class="col-md-2">Claim amount</th> 
                            <th>Price Excluding</th>
                            <th>Remove</th>
                        </thead>
                        <tbody id="selected_model"></tbody>
                    </table>
                </div>
            </div>
            <div class="clearfix"></div><hr>
            <button type="submit" id="create_scheme_btn" class="btn btn-primary waves-block waves-effect pull-right" formmethod="POST" formaction="<?php echo base_url('Scheme/create_price_drop') ?>">Create</button>
        </form>
        <div class="clearfix"></div>
    </div>
</div><div class="clearfix"></div><br>
<center><span style="color:#1b6caa;font-family: Kurale;font-size: 22px"><i class="<?php echo $schemetype->font ?>"></i> <?php echo str_replace('_', ' ', $schemetype->scheme_type) ?> Schemes</span></center><hr>
<div class="col-md-4">
    <div class="input-group">
        <div class="input-group-btn">
            <a class="btn-sm">
                <i class="fa fa-search"></i> Search
            </a>
        </div>
        <input type="text" name="search" id="filter_1" class="form-control input-sm" placeholder="Search from table">
    </div>
</div><div class="clearfix"></div><br>
        
<table class="table table-condensed table-bordered">
    <thead>
        <th>Id</th>
        <th>Brand</th>
        <th>Code</th>
        <th>Name</th>
        <th>Vendor</th>
        <th>From</th>
        <th>To</th>
        <th>GST</th>
        <th>Details</th>
        <!--<th>Report</th>-->
    </thead>
    <tbody class="data_1">
        <?php foreach ($schemes as $scheme) { ?>
        <tr>
            <td><?php echo $scheme->id_scheme ?></td>
            <!--<td><?php // echo $scheme->scheme_type ?></td>-->
            <td><?php echo $scheme->brand_name ?></td>
            <td><?php echo $scheme->scheme_code ?></td>
            <td><?php echo $scheme->scheme_name ?></td>
            <td><?php echo $scheme->vendor_name ?></td>
            <td><?php echo $scheme->date_from ?></td>
            <td><?php echo $scheme->date_to ?></td>
            <td><?php echo empty($scheme->is_gst_include) ? "Excluding" : "Including"; ?></td>
            <td><a class="btn btn-sm btn-primary" href="<?php echo base_url('Scheme/scheme_details/'.$scheme->idscheme_type.'/'.$scheme->id_scheme) ?>"><i class="fa fa-info"></i></a></td>
            <!--<td><?php // echo empty($scheme->claim_status) ? '' : '<a class="btn btn-sm btn-info" href="'.base_url('Scheme/scheme_report/'.$scheme->idscheme_type.'/'.$scheme->id_scheme).'"><i class="fa fa-table"></i></a>'; ?></td>-->
        </tr>
        <?php } ?>
    </tbody>
</table>

<script>
var variants = [];
$(document).ready(function(){
    $(document).on('change', '#idbrand', function () {
        if ($('#idbrand').val()) {
            var brand = +$('#idbrand').val();
            $.ajax({
                url: "<?php echo base_url() ?>Scheme/ajax_variants_by_brand",
                method: "POST",
                data: {brand: brand},
                success: function (data)
                {
                    $(".model_block").html(data);                        
                    $(".chosen-select").chosen({search_contains: true});
                    $('#selected_model').html('');
                    variants = [];
                    $('#gst_select_block').attr("style", "pointer-events: auto;");
                }
            });
        }
    });
    $(document).on('change', '#selmodel', function () {
        var selmodel = $(this).val();
        var gst_selected_type = $('#gst_selected_type').val();
        if ($('#selmodel').val()) {
            if (variants.includes(selmodel) === false){
                variants.push(selmodel);
                $.ajax({
                    url: "<?php echo base_url() ?>Scheme/get_variant_byid_for_price_drop",
                    method: "POST",
                    data:{id : selmodel, gst_selected_type:gst_selected_type},
                    success: function (data)
                    {
                        $('#model_table').show();
                        $('#selected_model').append(data);
//                      $('#idbrand_chosen').attr("style", "pointer-events: none;");
                    }
                });
            }else{
                swal("😠 Warning!", "Duplicate Product selected!");
                return false;
            }
        }
    });
    $(document).on('keyup', '.price_drop', function() {
        var parent = $($(this)).closest('td').parent('tr');
        var last_purchase_price = +parent.find(".last_purchase_price").val();
        var price_drop = +parent.find(".price_drop").val();
        var igst = +parent.find(".igst").val();
        if($('#gst_selected_type').val() === "1"){ // including
            var new_price = Math.round(last_purchase_price - price_drop);
            var igstamt = 0;
            var taxable = Math.round(price_drop);
        }else{ // excluding
            var cal = (igst + 100) / 100;
            var taxable = price_drop / cal;            
            var igstamt = price_drop - taxable;
            var price = last_purchase_price - taxable;
            new_price = Math.round(price);
        }
        parent.find(".new_price").val(new_price);
        parent.find(".excluding_gst_amt").val(Math.round(igstamt));
        parent.find(".spexcluding_gst_amt").html(Math.round(igstamt));
        parent.find(".taxable_gst_price").val(Math.round(taxable));
        parent.find(".sptaxable_gst_price").html(Math.round(taxable));
        $('#gst_select_block').attr("style", "pointer-events: none;");
    });
    $(document).on('keyup', '.last_purchase_price, .new_price', function() {
        var parent = $($(this)).closest('td').parent('tr');
        var last_purchase_price = +parent.find(".last_purchase_price").val();
        var new_price = +parent.find(".new_price").val();
        var igst = +parent.find(".igst").val();
        var price_drop=0;
        if($('#gst_selected_type').val() === "1"){ // including
            price_drop = Math.round(last_purchase_price - new_price);
            var igstamt = 0;
            var taxable = Math.round(price_drop);
        }else{ // excluding
            var taxable = Math.round(last_purchase_price - new_price);
            var igstamt= ((taxable/(igst+100))*100);            
             var price = taxable + igstamt;
//            var cal = (igst + 100) / 100;
//            var taxable = price_drop / cal;
//            var igstamt = price_drop - taxable;
//            var price = last_purchase_price - taxable;
//            price_drop=price+' - '+taxable+' - '+igstamt;
            price_drop = Math.round(igstamt);
        }        
        parent.find(".price_drop").val(price_drop);
        parent.find(".excluding_gst_amt").val(Math.round(igstamt));
        parent.find(".spexcluding_gst_amt").html(Math.round(igstamt));
        parent.find(".taxable_gst_price").val(Math.round(taxable));
        parent.find(".sptaxable_gst_price").html(Math.round(taxable));
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
});
</script>