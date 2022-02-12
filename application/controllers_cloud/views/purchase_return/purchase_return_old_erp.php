<?php include __DIR__ . '../../header.php'; ?>
<script>
    $(document).ready(function() {
        $("#sidebar").addClass("active");
        $(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
        $('#idvendor').change(function(){
            var idbranch = $('#idbranch').val();
            var idvendor = $('#idvendor').val();
            swal({
                title: "Want to Add Vendor?",
                text: $("#idvendor option:selected").text(),
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#4BC97F',
                confirmButtonText: 'Yes, Add it!',
                cancelButtonText: "No, cancel plx!",
                closeOnConfirm: false,
                closeOnCancel: true
            },
            function(isConfirm){
                if (isConfirm){
                    swal("Added!", "Vendor selected!", "success");
                    $('.vendor_block').hide();
                    $.ajax({
                        url: "<?php echo base_url() ?>Purchase_return/ajax_get_vendor_has_brands_old_erp",
                        method: "POST",
                        data:{idvendor: idvendor, idbranch: idbranch},
                        success: function (data)
                        {
                            $('#vendor_details').html(data);
                            $(".chosen-select").chosen({ search_contains: true });
                        }
                    });
                } else {
                    $('#idvendor').val('');
                    return false;
                }
            });
//            }
        });
    });
</script>
<div class="col-md-10"><center><h3 style="margin-top: 0"><span class="mdi mdi-repeat fa-lg"></span> Purchase Return</h3></center></div><div class="clearfix"></div><hr>
<div class="thumbnail" style="margin: 0;overflow: auto; min-height: 800px">
    <form>
        <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch'] ?>"/>
        <input type="hidden" id="idbranch" name="iduser" value="<?php echo $_SESSION['id_users'] ?>"/>
        <input type="hidden" id="id_stocks" name="id_stocks" />
        <input type="hidden" name="financial_year" id="financial_year" value="PR/<?php echo $y.'-'.$y2.'/'.$_SESSION['branch_code'].'/'; ?>" />
        <div class="vendor_block">
            <div class="col-md-2">Select Vendor</div>
            <div class="col-md-4">
                <select class="chosen-select form-control" id="idvendor" name="idvendor" required="">
                    <option value="">Select Vendor</option>
                    <?php foreach ($vendor_data as $vendor) { ?>
                        <option value="<?php echo $vendor->id_vendor; ?>"><?php echo $vendor->vendor_name; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div><div class="clearfix"></div>
        <div class="" id="vendor_details"></div><div class="clearfix"></div>
        <div id="product_box" style="display: none;">
            <div class="thumbnail" style="padding: 0">
                <table class="table table-striped table-condensed table-bordered" style="margin-bottom: 0">
                    <thead class="bg-info">
                        <th class="col-md-1 col-lg-1 col-xs-1">Id</th>
                        <th>Product</th>
                        <th>Godown</th>
                        <th>IMEI</th>
                        <th>Qty</th>
                        <!--<th class="col-md-1 col-lg-1 col-xs-1">Return Qty</th>-->
                        <th class="col-md-1 col-lg-1 col-xs-1">Basic</th>
                        <th class="col-md-1 col-lg-1 col-xs-1">Discount</th>
                        <th class="col-md-1 col-lg-1 col-xs-1">Taxable</th>
                        <th class="col-md-1 col-lg-1 col-xs-1">cgst per</th>
                        <th class="col-md-1 col-lg-1 col-xs-1">cgst amt</th>
                        <th class="col-md-1 col-lg-1 col-xs-1">sgst per</th>
                        <th class="col-md-1 col-lg-1 col-xs-1">sgst amt</th>
                        <th class="col-md-1 col-lg-1 col-xs-1">igst per</th>
                        <th class="col-md-1 col-lg-1 col-xs-1">igst amt</th>
                        <th class="col-md-1 col-lg-1 col-xs-1">Total</th>
                        <th class="col-md-1 col-lg-1 col-xs-1">Rem</th>
                    </thead>
                    <tbody id="return_block"></tbody>
                </table>
            </div><br>
            <div class="col-md-2">Approved By</div>
            <div class="col-md-3">
                <input type="text" class="form-control input-sm" name="approved_by" placeholder="Enter Approver Name" required="">
            </div>
            <div class="col-md-2">Return Reason</div>
            <div class="col-md-3">
                <input type="text" class="form-control input-sm" name="reason" placeholder="Enter Return Reason" required="">
            </div>
            <div class="col-md-2">
                <button formmethod="POST" formaction="<?php echo base_url('Purchase_return/save_purchase_return_old') ?>" class="btn btn-primary gradient2 waves-effect waves-light btn-sub">Submit</button>
            </div><div class="clearfix"></div>
        </div>
    </form>
</div>
<?php include __DIR__ . '../../footer.php'; ?>