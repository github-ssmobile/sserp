<div class="col-md-10 col-md-offset-1">
    <div class="thumbnail"><br>
        <form>
            <input type="hidden" name="idscheme_type" value="<?php echo $schemetype->id_scheme_type ?>" />
            <input type="hidden" name="create_by" value="<?php echo $this->session->userdata('id_users') ?>" />
            <div class="col-md-2">Scheme Code</div>
            <div class="col-md-4"><input type="text" class="form-control input-sm" placeholder="Enter Scheme code" required="" name="scheme_code" /></div>
            <div class="col-md-2">Scheme Name</div>
            <div class="col-md-4"><input type="text" class="form-control input-sm" placeholder="Enter Scheme Name" required="" name="scheme_name" /></div>
            <div class="clearfix"></div><br>
            <div class="col-md-2">Prebooking Date</div>
            <div class="col-md-4">
                <div class="input-group">
                    <div class="input-group-btn">
                        <input type="text" name="bdate_from" id="bdate_from" class="form-control input-sm" data-provide="datepicker" placeholder="From Date" onfocus="blur()" autocomplete="off" required="">
                    </div>
                    <div class="input-group-btn">
                        <input type="text" name="bdate_to" id="bdate_to" class="form-control input-sm" data-provide="datepicker" placeholder="To Date" onfocus="blur()" autocomplete="off" required="">
                    </div>
                </div>
            </div>
            <div class="col-md-2">Activation Date</div>
            <div class="col-md-4">
                <div class="input-group">
                    <div class="input-group-btn">
                        <input type="text" name="adate_from" id="bdate_from" class="form-control input-sm" data-provide="datepicker" placeholder="From Date" onfocus="blur()" autocomplete="off" required="">
                    </div>
                    <div class="input-group-btn">
                        <input type="text" name="adate_to" id="bdate_to" class="form-control input-sm" data-provide="datepicker" placeholder="To Date" onfocus="blur()" autocomplete="off" required="">
                    </div>
                </div>
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
<!--            <div class="col-md-2">Booking (Min value%)</div>
            <div class="col-md-4">
                <input type="text" name="min_val_per" id="min_val_per" class="form-control input-sm" placeholder="Advanced booking percentage">
            </div>-->
            <div class="clearfix"></div><br>
            <div>
                <div class="thumbnail p-0" id="model_table" style="display: none; margin-top: 10px; padding: 0">
                    <table class="table table-condensed table-striped p-0">
                        <thead>
                            <th>Id</th>
                            <th>Model Variant</th>
                            <th>All Color Variant</th>
                            <th>Booking Value%</th>
                            <th>Min Target</th>
                            <th>Max Target</th>
                            <th>Per Unit Incentive</th>
                            <th>Remove</th>
                        </thead>
                        <tbody id="selected_model"></tbody>
                    </table>
                </div>
            </div>
            <div class="clearfix"></div>
            <button type="submit" id="create_scheme_btn" class="btn btn-primary waves-block waves-effect pull-right" formmethod="POST" formaction="<?php echo base_url('Scheme/create_pre_booking') ?>">Create</button>
        </form>
        <div class="clearfix"></div>
    </div>
</div><div class="clearfix"></div><br>
<center><span style="color:#1b6caa;font-family: Kurale;font-size: 22px"><i class="<?php echo $schemetype->font ?>"></i> <?php echo str_replace('_', ' ', $schemetype->scheme_type) ?> Schemes</span></center><hr>
<table class="table table-condensed">
    <thead>
        <th>Id</th>
        <th>Brand</th>
        <th>Code</th>
        <th>Name</th>
        <th>Vendor</th>
        <th>Booking From</th>
        <th>Booking To</th>
        <th>Activation From</th>
        <th>Activation To</th>
        <th>Details</th>
    </thead>
    <tbody>
        <?php foreach ($schemes as $scheme) { ?>
        <tr>
            <td><?php echo $scheme->id_scheme ?></td>
            <td><?php echo $scheme->brand_name ?></td>
            <td><?php echo $scheme->scheme_code ?></td>
            <td><?php echo $scheme->scheme_name ?></td>
            <td><?php echo $scheme->vendor_name ?></td>
            <td><?php echo $scheme->date_from ?></td>
            <td><?php echo $scheme->date_to ?></td>
            <td><?php echo $scheme->activate_date_from ?></td>
            <td><?php echo $scheme->activate_date_to ?></td>
            <td><a class="btn btn-sm btn-primary" href="<?php echo base_url('Scheme/scheme_details/'.$scheme->idscheme_type.'/'.$scheme->id_scheme) ?>"><i class="fa fa-info"></i></a></td>
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
                }
            });
        }
    });
    $(document).on('change', '#selmodel', function () {
        var selmodel = $(this).val();
        if ($('#selmodel').val()) {
            if (variants.includes(selmodel) === false){
                variants.push(selmodel);
                $.ajax({
                    url: "<?php echo base_url() ?>Scheme/get_variant_byid_for_prebooking",
                    method: "POST",
                    data:{id : selmodel},
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
});
</script>