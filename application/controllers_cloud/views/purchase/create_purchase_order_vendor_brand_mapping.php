<?php include __DIR__ . '../../header.php'; ?>
<div class="col-md-10"><center><h3><span class="mdi mdi-cart-outline fa-lg"></span> Create Purchase Order</center></div><div class="clearfix"></div><hr><br>
<script>
    $(document).ready(function () {
        $('#idvendor').change(function(){
            var idvendor = $(this).val();
            $.ajax({
                url: "<?php echo base_url() ?>Purchase/ajax_get_vendor_has_brands",
                method: "POST",
                data:{idvendor : idvendor},
                success: function (data)
                {
                    $('#model_block').html(data);
                    $(".chosen-select").chosen({ search_contains: true });
                }
            });
        });
    });
</script>
<form id="pay">
    <div class="col-md-10 col-md-offset-1">
        <div class="" style="font-size: 13px; padding: 10px;border-radius: 1rem;background: #fbfbff;border: 1px solid #e3e3e3;box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.15);">
            <div style="background-image: linear-gradient(to right top, #051937, #113c63, #176391, #168ebf, #12bceb);box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5);border-radius: 5px; margin-top: -30px">
                <div class="col-md-12 col-lg-12" style="padding: 5px">
                    <div class="" style="font-size: 17px; padding: 3px; margin: 0px; color: #fff">
                        <center><i class="pe pe-7s-news-paper fa-lg"></i> Create Purchase Order</center>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div><div class="clearfix"></div><br>
            <input type="hidden" name="iduser" value="<?php echo $_SESSION['id_users'] ?>" />
            <div class="col-md-5">
                <div class="col-md-3 text-muted">Date</div>
                <div class="col-md-9"><?php echo $date ?>
                    <input type="hidden" name="date" value="<?php echo $now ?>" />
                    <!--<input type="hidden" name="godown_name" id="godown_name" />-->
                    <?php if($_SESSION['idrole'] == 10){ ?>
                    <input type="hidden" name="status" value="1" />
                    <?php }else{ ?>
                    <input type="hidden" name="status" value="0" />
                    <?php } ?>
                </div><div class="clearfix"></div><br>
                <div class="col-md-3 text-muted">Warehouse</div>
                <div class="col-md-9">
                    <select class="form-control input-sm" required="" name="idwarehouse" id="idwarehouse">
                        <option value="">Select Warehouse</option>
                        <?php foreach ($warehouse_data as $warehouse){ ?>
                            <option value="<?php echo $warehouse->id_branch ?>" wcode="<?php echo $warehouse->branch_code ?>"><?php echo $warehouse->branch_name ?></option>
                        <?php } ?>
                    </select>
                </div><div class="clearfix"></div><br>
            </div>
            <div class="col-md-7">
                <div class="col-md-3 text-muted">Vendor</div>
                <div class="col-md-9" id="vendor_name_block" style="display: none">
                    <div class="" id="vendor_name"></div>
                </div>
                <div class="col-md-9" id="idvendor_block">
                    <select class="chosen-select form-control input-sm" required="" name="idvendor" id="idvendor" style="width: 100%;">
                        <option value="">Select Vendor</option>
                        <?php foreach ($vendor_data as $vendor) { ?>
                            <option value="<?php echo $vendor->id_vendor ?>"><?php echo $vendor->vendor_name ?></option>
                        <?php } ?>
                    </select>
                </div><div class="clearfix"></div><br>
                <div class="col-md-3 text-muted">Remark</div>
                <div class="col-md-9"><input type="text" class="form-control input-sm" name="remark" placeholder="Enter Remark" /></div>
            </div><div class="clearfix"></div>
            <div class="col-md-12" id="model_block"></div><div class="clearfix"></div>
            <div id="model_table" style="display: none; margin-top: 10px; padding: 0">
                <table class="table table-condensed table-striped table-bordered">
                    <thead>
                    <th>Model</th>
                    <th>Warehouse Qty</th>
                    <th>Intransit Qty</th>
                    <th>Branch Qty</th>
                    <th>Branch Sale Qty</th>
                    <th class="col-md-2">Qty</th>
                    <th>Remove</th>
                    </thead>
                    <tbody id="selected_model"></tbody>
                </table>
            </div><hr>
            <a class="btn btn-warning waves-effect simple-tooltip gradient1" data-toggle="collapse" data-target="#pay">Close</a>
            <button type="submit" formmethod="POST" formaction="<?php echo base_url('Purchase/save_purchase_order') ?>" class="pull-right btn btn-info gradient2 waves-effect">Save</button>
            <div class="clearfix"></div>
        </div>
    </div><div class="clearfix"></div>
</form>
<?php include __DIR__ . '../../footer.php'; ?>