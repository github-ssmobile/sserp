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
            <div class="col-md-2">Date To</div>
            <div class="col-md-4"><input type="text" class="form-control input-sm" placeholder="Select Date to" data-provide="datepicker" onfocus="blur()" autocomplete="off" name="date_to" /></div>
            <div class="clearfix"></div><br>
            <div class="col-md-2">Discontinue Scheme</div>
            <div class="col-md-4">
                <select data-placeholder="Select Scheme" class="chosen-select form-control input-sm" required="" id="discon_scheme" name="discon_scheme">
                    <option value="">Select Scheme</option>
                    <?php foreach ($all_schemes as $all){ ?>
                    <option value="<?php echo $all->id_scheme ?>"><?php echo $all->scheme_code.'-'.$all->scheme_name.' ('.$all->scheme_type.')' ?></option>
                    <?php } ?>
                </select>
            </div>
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
            <div class="col-md-2">Model Variant</div>
            <div class="col-md-4">
                <div class="model_block">
                    <select class="chosen-select form-control" data-placeholder="Select Variant" name="selmodel" id="selmodel" required=""><option value="">Select Variant</option></select>
                </div>
            </div>
            <div class="clearfix"></div><br>
            <div class="thumbnail p-0" id="model_table" style="display: none; margin-top: 10px; padding: 0">
                <table class="table table-condensed table-striped p-0">
                    <thead>
                        <th>Id</th>
                        <th>Model Variant</th>
                        <!--<th>Stock Qty</th>-->
                        <th>Remove</th>
                    </thead>
                    <tbody id="selected_model"></tbody>
                </table>
            </div>
            <div class="clearfix"></div><hr>
            <button type="submit" id="create_scheme_btn" class="btn btn-primary waves-block waves-effect pull-right" formmethod="POST" formaction="<?php echo base_url('Scheme/create_discontinue_scheme') ?>">Create</button>
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
        <th>From</th>
        <th>To</th>
        <th>Discontinue Scheme</th>
        <th>Scheme Code - Name</th>
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
            <td><?php echo $scheme->dis_scheme_type ?></td>
            <td><?php echo $scheme->dis_scheme_code.'-'.$scheme->dis_scheme_name ?></td>
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
        var gst_selected_type = $('#gst_selected_type').val();
        if ($('#selmodel').val()) {
            if (variants.includes(selmodel) === false){
                variants.push(selmodel);
                $.ajax({
                    url: "<?php echo base_url() ?>Scheme/get_variant_byid_for_model_discontinue",
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
    $(document).on('click', '.remove_btn', function() {
        var parent = $(this).closest('td').parent('tr');
        var idvariant = parent.find(".idvariant").val();
//        alert(idvariant);
        swal({
            title: "😕 Want to Remove Product?",
            text: '',
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#E84848',
            confirmButtonText: 'Yes, Remove it!',
            closeOnConfirm: false,
        },
        function(){
            swal("Removed!", "Product removed from this list!", "success");
            variants = jQuery.grep(variants, function(value) { return value !== idvariant; });
            $(parent).remove();
        });
    });
    $(document).on('click', '#create_scheme_btn', function() {
        if($("#idbrand").val() == ''){
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