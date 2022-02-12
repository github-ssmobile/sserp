<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase_return extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Purchase_model');
        $this->load->model('Stock_model');
//        $this->load->model('Inward_model');
        $this->load->model('General_model');
    }
    public function index() {
        $q['tab_active'] = 'Purchase return';
        $q['purchase_order'] = $this->Purchase_model->get_purchase_return();
        $q['vendor_data'] = $this->General_model->get_active_vendor_data();
//        $q['model_variant'] = $this->General_model->ajax_get_model_variant_byidskutype(4);
        $this->load->view('purchase_return/generate_purchase_return',$q);
    }
    public function purchase_return_old_erp() {
        $q['tab_active'] = 'Purchase return';
        $q['purchase_order'] = $this->Purchase_model->get_purchase_return();
        $q['vendor_data'] = $this->General_model->get_active_vendor_data();
//        $q['model_variant'] = $this->General_model->ajax_get_model_variant_byidskutype(4);
        $this->load->view('purchase_return/purchase_return_old_erp',$q);
    }
    public function purchase_return_report() {
        $q['tab_active'] = 'Purchase return';
        $q['purchase_return'] = $this->Purchase_model->get_purchase_return();
        $q['vendor_data'] = $this->General_model->get_active_vendor_data();
        $this->load->view('purchase_return/purchase_return_report',$q);
    }
    public function ajax_get_purchase_return_report(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idvendor = $this->input->post('idvendor');
        
        $purchases_return = $this->Purchase_model->get_purchase_return_byfilter($from, $to, $idvendor);
//        die(print_r($purchases_return));
        if(count($purchases_return) > 0){ ?>
            <table class="table table-bordered table-condensed" id="branch_data">
                <thead class="bg-info">
                    <th>Sr</th>
                    <th>Return ID</th>
                    <th>Date Time</th>
                    <th>Warehouse</th>
                    <th>Vendor</th>
                    <th>GSTIN</th>
                    <th>Info</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach ($purchases_return as $pr){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $pr->financial_year.$pr->id_purchasereturn ?></td>
                        <td><?php echo date('d-m-Y h:i a', strtotime($pr->entry_time)) ?></td>
                        <td><?php echo $pr->branch_name ?></td>
                        <td><?php echo $pr->vendor_name ?></td>
                        <td><?php echo $pr->vendor_gst ?></td>
                        <td><center><a target="_blank" href="<?php echo base_url('Purchase_return/purchase_return_details/'.$pr->id_purchasereturn) ?>" class="btn btn-sm btn-warning gradient_info waves-effect waves-light"><i class="fa fa-info fa-lg"></i></a></td>
                    </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
        <?php }else{ ?>
            <script>
                $(document).ready(function (){
                   alert("Data Not Found") ;
                });
            </script>
        <?php }
    }

    public function purchase_return_details($prid) {
        $q['tab_active'] = 'Purchase return';
        $q['purchase_return'] = $this->Purchase_model->get_purchase_return_byid($prid);
        $q['purchase_return_product'] = $this->Purchase_model->get_purchase_return_product_byid($prid);
        $this->load->view('purchase_return/purchase_return_details',$q);
    }
    public function ajax_get_vendor_byid() {
        $idbranch = $this->input->post('idbranch');
        $idvendor = $this->input->post('idvendor'); // id warehouse
        $q['vendor_data'] = $this->General_model->get_vendor_byid($idvendor);
        $q['branch_data'] = $this->General_model->get_branch_byid($idbranch);
        if(count($q['vendor_data'])){
            $q['result'] = 'Success';
            $q['msg'] = '';
        }else{
            $q['result'] = 'Failed';
            $q['msg'] = 'vendor not verified.';
        }
        echo json_encode($q);
    }
    public function ajax_get_imei_details() {
        $imei = $this->input->post('imei');
        $idbranch = $this->input->post('idbranch'); // id warehouse
        $idvendor = $this->input->post('idvendor'); // id vendor
//        $q['purchase_return_data_byimei'] = $this->Purchase_model->ajax_purchase_return_data_byimei($imei, $idbranch, $idvendor);
        $q['purchase_return_data_byimei'] = $this->Purchase_model->ajax_purchase_return_data_byimei_without_vendor($imei, $idbranch);
        if(count($q['purchase_return_data_byimei'])){
            $q['result'] = 'Success';
            $q['msg'] = '';
        }else{
            $q['result'] = 'Failed';
            $q['msg'] = 'IMEI/SRNO not found in branch or selected vendor is wrong.';
        }
        echo json_encode($q);
    }
    public function ajax_get_imei_details_old_erp() {
        $imei = $this->input->post('imei');
        $idbranch = $this->input->post('idbranch'); // id warehouse
        $idvendor = $this->input->post('idvendor'); // id vendor
//        $q['purchase_return_data_byimei'] = $this->Purchase_model->ajax_purchase_return_data_byimei($imei, $idbranch, $idvendor);
        $q['purchase_return_data_byimei'] = $this->Purchase_model->ajax_purchase_return_data_byimei_without_vendor($imei, $idbranch);
        if(count($q['purchase_return_data_byimei'])){
            $q['result'] = 'Success';
            $q['msg'] = '';
        }else{
            $q['result'] = 'Failed';
            $q['msg'] = 'IMEI/SRNO not found in branch or selected vendor is wrong.';
        }
        echo json_encode($q);
    }
    public function ajax_get_variant_byid_branch_vendor() {
        $variant = $this->input->post('variant');
        $idbranch = $this->input->post('idbranch'); // id warehouse
//        $idvendor = $this->input->post('idvendor'); // id vendor
        $idgodown = 1; // id godown
        $q['purchase_return_data'] = $this->Purchase_model->ajax_get_variant_byid_branch_godown($variant, $idbranch, $idgodown);
        if(count($q['purchase_return_data'])){
            $q['result'] = 'Success';
            $q['msg'] = '';
        }else{
            $q['result'] = 'Failed';
            $q['msg'] = 'IMEI/SRNO not found in branch or selected vendor is wrong.';
        }
        echo json_encode($q);
    }
    public function ajax_get_vendor_has_brands() {
        $idvendor = $this->input->post('idvendor');
        $idbranch = $this->input->post('idbranch');
        $vendor = $this->General_model->get_vendor_byid($idvendor);
        $branch = $this->General_model->get_branch_byid($idbranch);
        $model_variant = $this->General_model->ajax_get_vendor_has_brand_bysku($idvendor, 4); ?>
        <div class="col-md-6">
            To,<div class="clearfix"></div>
            <div class="col-md-3 col-xs-4 text-muted">Vendor Name</div>
            <div class="col-md-9 col-xs-8"><?php echo $vendor[0]->vendor_name ?></div>
            <div class="col-md-3 col-xs-4 text-muted">Contact No</div>
            <div class="col-md-9 col-xs-8"><?php echo $vendor[0]->vendor_contact ?></div>
            <div class="col-md-3 col-xs-4 text-muted">Vendor Address</div>
            <div class="col-md-9 col-xs-8"><?php echo $vendor[0]->vendor_address ?></div>
            <div class="col-md-3 col-xs-4 text-muted">Vendor State</div>
            <div class="col-md-9 col-xs-8"><?php echo $vendor[0]->state ?></div>
            <div class="col-md-3 col-xs-4 text-muted">GSTIN</div>
            <div class="col-md-9 col-xs-8"><?php echo $vendor[0]->vendor_gst ?></div>
        </div>
        <div class="col-md-6">
            From,<div class="clearfix"></div>
            <div class="col-md-3 col-xs-4 text-muted">Warehouse</div>
            <div class="col-md-9 col-xs-8"><?php echo $branch->branch_name ?></div>
            <div class="col-md-3 col-xs-4 text-muted">Warehouse Address</div>
            <div class="col-md-9 col-xs-8"><?php echo $branch->branch_address ?></div>
            <div class="col-md-3 col-xs-4 text-muted">Warehouse State</div>
            <div class="col-md-9 col-xs-8"><?php echo $branch->branch_state_name ?></div>
        </div><div class="clearfix"></div><hr>
        <div class="col-md-2">Scan IMEI/SRNO</div>
        <div class="col-md-4">
            <input type="text" class="form-control" placeholder="Scan IMEI/SRNO" id="imei"/>
        </div>
        <div class="col-md-2">Select Product</div>
        <div class="col-md-4">
            <select class="chosen-select form-control" name="variant" id="variant">
                <option value="">Select Model</option>
                <?php foreach ($model_variant as $variant) { ?>
                    <option value="<?php echo $variant->id_variant; ?>"><?php echo $variant->product_category_name.' '.$variant->full_name; ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="clearfix"></div><br>
        <script>
            $(document).ready(function() {
            var idstock = [];
                $(document).on('keydown', 'input[id=imei]', function(e) {
                    var keyCode = e.keyCode || e.which; 
                    var imei = $(this).val();
                    if (keyCode === 13 && imei !== '') {
                        var idbranch = $('#idbranch').val();
                        var idvendor = $('#idvendor').val();
                        if(idvendor == ''){
                            swal("😠 Vendor not selected!");
                            return false;
                        }else{
                            $.ajax({
                                url: "<?php echo base_url() ?>Purchase_return/ajax_get_imei_details",
                                method: "POST",
                                data:{imei : imei,idvendor: idvendor, idbranch: idbranch},
                                dataType: 'json',
                                success: function (data)
                                {
                                    if(data.result == 'Success'){
                                        $(data.purchase_return_data_byimei).each(function (index, product) {
                                            if (idstock.includes(product.id_stock) === false){
                                                idstock.push(product.id_stock); 
                                            }else{
                                                alert("😠 Duplicate imei scanned");
                                                return false;
                                            }
                                            $('#id_stocks').val(idstock);
                                            var prodrow = '<tr>\n\
                                                                <td><input type="hidden" name="idvariant[]" class="idvariant" value="'+product.idvariant+'" />'+product.idvariant+'</td>\n\
                                                                <td><input type="hidden" name="product_name[]" value="'+product.product_name+'" />'+product.product_name+'</td>\n\
                                                                <td>'+product.godown_name+'</td>\n\
                                                                <td><input type="hidden" name="imei_no[]" value="'+product.imei_no+'" />'+product.imei_no+'</td>\n\
                                                                <td><input type="hidden" name="avail_qty[]" value="1" />1</td>\n\
                                                                <td><input type="text" class="form-control input-sm" readonly name="qty[]" value="1" /></td>\n\
                                                                <td><input type="hidden" id="id_stock" name="id_stock[]" value="'+product.id_stock+'" />\n\
                                                                <input type="hidden" id="sku_type" name="sku_type[]" value="'+product.idskutype+'" />\n\
                                                                <input type="hidden" id="idproductcategory" name="idproductcategory[]" value="'+product.idproductcategory+'" />\n\
                                                                <input type="hidden" id="idcategory" name="idcategory[]" value="'+product.idcategory+'" />\n\
                                                                <input type="hidden" id="idmodel" name="idmodel[]" value="'+product.idmodel+'" />\n\
                                                                <input type="hidden" id="idbrand" name="idbrand[]" value="'+product.idbrand+'" />\n\
                                                                <center><a class="btn btn-sm btn-danger gradient1 waves-effect waves-light remove" name="remove[]" id="remove"><i class="fa fa-trash-o fa-lg"></i></a></center></td>\n\
                                                            </tr>';
                                            $('#return_block').append(prodrow);
                                        });
                                        $('#product_box').show();
                                        $('#imei').val('');
                                    }else{
                                        alert("😠 "+ data.msg);
                                    }
                                }
                            });
                        }
                    }
                });

                $('#variant').change(function(){
                    var variant = $(this).val();
                    var idbranch = $('#idbranch').val();
                    var idvendor = $('#idvendor').val();
                    if(idvendor == ''){
                        swal("😠 Vendor not selected!");
                        return false;
                    }else{
                        if(variant != ''){
                            $.ajax({
                                url: "<?php echo base_url() ?>Purchase_return/ajax_get_variant_byid_branch_vendor",
                                method: "POST",
                                data:{variant : variant, idbranch: idbranch, idvendor: idvendor},
                                dataType: 'json',
                                success: function (data)
                                {
                                    if(data.result == 'Success'){
                                        $(data.purchase_return_data).each(function (index, qtyproduct) {
                                            if (idstock.includes(qtyproduct.id_stock) === false){
                                                idstock.push(qtyproduct.id_stock); 
                                            }else{
                                                alert("😠 Duplicate product selected");
                                                return false;
                                            }
                                            $('#id_stocks').val(idstock);
                                            var prodrow = '<tr>\n\
                                                                <td><input type="hidden" name="idvariant[]" class="idvariant" value="'+qtyproduct.idvariant+'" />'+qtyproduct.idvariant+'</td>\n\
                                                                <td><input type="hidden" name="product_name[]" value="'+qtyproduct.product_name+'" />'+qtyproduct.product_name+'</td>\n\
                                                                <td>New Godown</td>\n\
                                                                <td><input type="hidden" name="imei_no[]" value="" /></td>\n\
                                                                <td><input type="hidden" name="avail_qty[]" value="1" />'+qtyproduct.qty+'</td>\n\
                                                                <td><input type="number" class="form-control input-sm" name="qty[]" value="1" min="1" max="'+qtyproduct.qty+'" /></td>\n\
                                                                <td><input type="hidden" id="sku_type" name="sku_type[]" value="'+qtyproduct.idskutype+'" />\n\
                                                                <input type="hidden" id="idproductcategory" name="idproductcategory[]" value="'+qtyproduct.idproductcategory+'" />\n\
                                                                <input type="hidden" id="idcategory" name="idcategory[]" value="'+qtyproduct.idcategory+'" />\n\
                                                                <input type="hidden" id="idmodel" name="idmodel[]" value="'+qtyproduct.idmodel+'" />\n\
                                                                <input type="hidden" id="idbrand" name="idbrand[]" value="'+qtyproduct.idbrand+'" />\n\
                                                                <input type="hidden" id="id_stock" name="id_stock[]" value="'+qtyproduct.id_stock+'" />\n\
                                                                <center><a class="btn btn-sm btn-danger gradient1 waves-effect waves-light remove" name="remove[]" id="remove"><i class="fa fa-trash-o fa-lg"></i></a></center></td>\n\
                                                            </tr>';
                                            $('#return_block').append(prodrow);
                                        });
                                        $('#product_box').show();
                                    }else{
                                        alert(data.msg);
                                    }
                                }
                            });
                        }
                    }
                });
            });
        </script>
    <?php    
    }
    public function ajax_get_vendor_has_brands_old_erp() {
        $idvendor = $this->input->post('idvendor');
        $idbranch = $this->input->post('idbranch');
        $vendor = $this->General_model->get_vendor_byid($idvendor);
        $branch = $this->General_model->get_branch_byid($idbranch);
        $model_variant = $this->General_model->ajax_get_vendor_has_brand_bysku($idvendor, 4); ?>
        <div class="col-md-6">
            To,<div class="clearfix"></div>
            <div class="col-md-3 col-xs-4 text-muted">Vendor Name</div>
            <div class="col-md-9 col-xs-8"><?php echo $vendor[0]->vendor_name ?></div>
            <div class="col-md-3 col-xs-4 text-muted">Contact No</div>
            <div class="col-md-9 col-xs-8"><?php echo $vendor[0]->vendor_contact ?></div>
            <div class="col-md-3 col-xs-4 text-muted">Vendor Address</div>
            <div class="col-md-9 col-xs-8"><?php echo $vendor[0]->vendor_address ?></div>
            <div class="col-md-3 col-xs-4 text-muted">Vendor State</div>
            <div class="col-md-9 col-xs-8"><?php echo $vendor[0]->state ?></div>
            <div class="col-md-3 col-xs-4 text-muted">GSTIN</div>
            <div class="col-md-9 col-xs-8"><?php echo $vendor[0]->vendor_gst ?></div>
        </div>
        <div class="col-md-6">
            From,<div class="clearfix"></div>
            <div class="col-md-3 col-xs-4 text-muted">Warehouse</div>
            <div class="col-md-9 col-xs-8"><?php echo $branch->branch_name ?></div>
            <div class="col-md-3 col-xs-4 text-muted">Warehouse Address</div>
            <div class="col-md-9 col-xs-8"><?php echo $branch->branch_address ?></div>
            <div class="col-md-3 col-xs-4 text-muted">Warehouse State</div>
            <div class="col-md-9 col-xs-8"><?php echo $branch->branch_state_name ?></div>
        </div><div class="clearfix"></div><hr>
        <div class="col-md-2">Scan IMEI/SRNO</div>
        <div class="col-md-4">
            <input type="text" class="form-control" placeholder="Scan IMEI/SRNO" id="imei"/>
        </div>
<!--        <div class="col-md-2">Select Product</div>
        <div class="col-md-4">
            <select class="chosen-select form-control" name="variant" id="variant">
                <option value="">Select Model</option>
                <?php foreach ($model_variant as $variant) { ?>
                    <option value="<?php echo $variant->id_variant; ?>"><?php echo $variant->product_category_name.' '.$variant->full_name; ?></option>
                <?php } ?>
            </select>
        </div>-->
        <div class="clearfix"></div><br>
        <script>
            $(document).ready(function() {
            var idstock = [];
                $(document).on('keydown', 'input[id=imei]', function(e) {
                    var keyCode = e.keyCode || e.which; 
                    var imei = $(this).val();
                    if (keyCode === 13 && imei !== '') {
                        var idbranch = $('#idbranch').val();
                        var idvendor = $('#idvendor').val();
                        if(idvendor == ''){
                            swal("😠 Vendor not selected!");
                            return false;
                        }else{
                            $.ajax({
                                url: "<?php echo base_url() ?>Purchase_return/ajax_get_imei_details_old_erp",
                                method: "POST",
                                data:{imei : imei,idvendor: idvendor, idbranch: idbranch},
                                dataType: 'json',
                                success: function (data)
                                {
                                    if(data.result == 'Success'){
                                        $(data.purchase_return_data_byimei).each(function (index, product) {
                                            if (idstock.includes(product.id_stock) === false){
                                                idstock.push(product.id_stock); 
                                            }else{
                                                alert("😠 Duplicate imei scanned");
                                                return false;
                                            }
                                            $('#id_stocks').val(idstock);
                                            var prodrow = '<tr>\n\
                                                    <td><input type="hidden" name="idvariant[]" class="idvariant" value="'+product.idvariant+'" />'+product.idvariant+'</td>\n\
                                                    <td><input type="hidden" class="product_name" name="product_name[]" value="'+product.product_name+'" />'+product.product_name+'</td>\n\
                                                    <td>'+product.godown_name+'</td>\n\
                                                    <td><input type="hidden" name="imei_no[]" value="'+product.imei_no+'" />'+product.imei_no+'</td>\n\
                                                    <td><input type="hidden" name="avail_qty[]" value="1" />1 <input type="hidden" class="form-control input-sm" readonly name="qty[]" value="1" /></td>\n\
                                                    <td><input type="number" step="0.0001" class="form-control input-sm basic" name="basic[]" required="" value="0" style="width: 90px" /></td>\n\
                                                    <td><input type="number" step="0.0001" class="form-control input-sm discount" name="discount[]" required="" value="0" style="width: 90px" /></td>\n\
                                                    <td><input type="hidden" class="taxable" name="taxable[]" required="" value="0" /><span class="taxable_sp">0</span></td>\n\
                                                    <td><input type="number" step="0.0001" class="form-control input-sm cgst_per" name="cgst_per[]" required="" value="0" style="width: 70px" /></td>\n\
                                                    <td><input type="hidden" class="cgst_amt" name="cgst_amt[]"value="0" /><span class="cgst_amt_sp">0</span></td>\n\
                                                    <td><input type="number" step="0.0001" class="form-control input-sm sgst_per" name="sgst_per[]" required="" value="0" style="width: 70px" /></td>\n\
                                                    <td><input type="hidden" class="sgst_amt" name="sgst_amt[]" value="0" /><span class="sgst_amt_sp">0</span></td>\n\
                                                    <td><input type="number" step="0.0001" class="form-control input-sm igst_per" name="igst_per[]" required="" value="0" style="width: 70px" /></td>\n\
                                                    <td><input type="hidden" class="igst_amt" name="igst_amt[]" value="0" /><span class="igst_amt_sp">0</span></td>\n\
                                                    <td><input type="hidden" class="total" name="total[]" value="0" style="width: 90px" /><span class="total_sp">0</span></td>\n\
                                                    <td><input type="hidden" class="id_stock" id="id_stock" name="id_stock[]" value="'+product.id_stock+'" />\n\
                                                    <input type="hidden" id="sku_type" name="sku_type[]" value="'+product.idskutype+'" />\n\
                                                    <input type="hidden" id="idproductcategory" name="idproductcategory[]" value="'+product.idproductcategory+'" />\n\
                                                    <input type="hidden" id="idcategory" name="idcategory[]" value="'+product.idcategory+'" />\n\
                                                    <input type="hidden" id="idmodel" name="idmodel[]" value="'+product.idmodel+'" />\n\
                                                    <input type="hidden" id="idbrand" name="idbrand[]" value="'+product.idbrand+'" />\n\
                                                    <a class="btn btn-sm btn-danger gradient1 waves-effect waves-light remove" name="remove[]" id="remove"><i class="fa fa-trash-o fa-lg"></i></a></td>\n\
                                                </tr>';
                                            $('#return_block').append(prodrow);
                                        });
                                        $('#product_box').show();
                                        $('#imei').val('');
                                    }else{
                                        alert("😠 "+ data.msg);
                                    }
                                }
                            });
                        }
                    }
                });
                $(document).on('keyup', '.basic, .discount, .cgst_per, .sgst_per, .igst_per', function() {
                    var parent_tr = $(this).closest('td').parent('tr');
                    var basic = isNaN(parent_tr.find('.basic').val()) ? 0 : parent_tr.find('.basic').val();
                    var discount = isNaN(parent_tr.find('.discount').val()) ? 0 : parent_tr.find('.discount').val();
                    var cgst_per = isNaN(parent_tr.find('.cgst_per').val()) ? 0 : parent_tr.find('.cgst_per').val();
                    var sgst_per = isNaN(parent_tr.find('.sgst_per').val()) ? 0 : parent_tr.find('.sgst_per').val();
                    var igst_per = isNaN(parent_tr.find('.igst_per').val()) ? 0 : parent_tr.find('.igst_per').val();
                    var taxable = basic - discount;
                    var cgst_amt = (taxable * cgst_per)/100;
                    var sgst_amt = (taxable * sgst_per)/100;
                    var igst_amt = (taxable * igst_per)/100;
                    parent_tr.find('.taxable').val(taxable);
                    parent_tr.find('.taxable_sp').html(taxable);
                    parent_tr.find('.cgst_amt').val(cgst_amt);
                    parent_tr.find('.cgst_amt_sp').html(cgst_amt);
                    parent_tr.find('.sgst_amt').val(sgst_amt);
                    parent_tr.find('.sgst_amt_sp').html(sgst_amt);
                    parent_tr.find('.igst_amt').val(igst_amt);
                    parent_tr.find('.igst_amt_sp').html(igst_amt);
                    var total = taxable + cgst_amt + sgst_amt + igst_amt;
                    parent_tr.find('.total').val(total);
                    parent_tr.find('.total_sp').html(total);
                    
                    
                });
                $(document).on('click', 'a[id=remove]', function() {
                    var product_name = $(this).closest('td').parent('tr').find('.product_name').val();
                    var id_stock = $(this).closest('td').find('.id_stock').val();
                    if (confirm('Are you sure? You want to remove product: '+product_name)) {
                        idstock = jQuery.grep(idstock, function(value) { return value !== id_stock; });
                        $('#id_stocks').val(idstock);
                        $(this).closest('td').parent('tr').remove();
                    }
                });
                $('#variant').change(function(){
                    var variant = $(this).val();
                    var idbranch = $('#idbranch').val();
                    var idvendor = $('#idvendor').val();
                    if(idvendor == ''){
                        swal("😠 Vendor not selected!");
                        return false;
                    }else{
                        if(variant != ''){
                            $.ajax({
                                url: "<?php echo base_url() ?>Purchase_return/ajax_get_variant_byid_branch_vendor",
                                method: "POST",
                                data:{variant : variant, idbranch: idbranch, idvendor: idvendor},
                                dataType: 'json',
                                success: function (data)
                                {
                                    if(data.result == 'Success'){
                                        $(data.purchase_return_data).each(function (index, qtyproduct) {
                                            if (idstock.includes(qtyproduct.id_stock) === false){
                                                idstock.push(qtyproduct.id_stock); 
                                            }else{
                                                alert("😠 Duplicate product selected");
                                                return false;
                                            }
                                            $('#id_stocks').val(idstock);
                                            var prodrow = '<tr>\n\
                                                                <td><input type="hidden" name="idvariant[]" class="idvariant" value="'+qtyproduct.idvariant+'" />'+qtyproduct.idvariant+'</td>\n\
                                                                <td><input type="hidden" name="product_name[]" value="'+qtyproduct.product_name+'" />'+qtyproduct.product_name+'</td>\n\
                                                                <td>New Godown</td>\n\
                                                                <td><input type="hidden" name="imei_no[]" value="" /></td>\n\
                                                                <td><input type="hidden" name="avail_qty[]" value="1" />'+qtyproduct.qty+'</td>\n\
                                                                <td><input type="number" class="form-control input-sm" name="qty[]" value="1" min="1" max="'+qtyproduct.qty+'" /></td>\n\
                                                                <td><input type="hidden" id="sku_type" name="sku_type[]" value="'+qtyproduct.idskutype+'" />\n\
                                                                <input type="hidden" id="idproductcategory" name="idproductcategory[]" value="'+qtyproduct.idproductcategory+'" />\n\
                                                                <input type="hidden" id="idcategory" name="idcategory[]" value="'+qtyproduct.idcategory+'" />\n\
                                                                <input type="hidden" id="idmodel" name="idmodel[]" value="'+qtyproduct.idmodel+'" />\n\
                                                                <input type="hidden" id="idbrand" name="idbrand[]" value="'+qtyproduct.idbrand+'" />\n\
                                                                <input type="hidden" id="id_stock" name="id_stock[]" value="'+qtyproduct.id_stock+'" />\n\
                                                                <center><a class="btn btn-sm btn-danger gradient1 waves-effect waves-light remove" name="remove[]" id="remove"><i class="fa fa-trash-o fa-lg"></i></a></center></td>\n\
                                                            </tr>';
                                            $('#return_block').append(prodrow);
                                        });
                                        $('#product_box').show();
                                    }else{
                                        alert(data.msg);
                                    }
                                }
                            });
                        }
                    }
                });
            });
        </script>
    <?php    
    }
    
    public function save_purchase_return() {
//        die('<pre>'.print_r($_POST, 1).'</pre>');
        $this->db->trans_begin();
        $date = date('Y-m-d');
        $entry_time = date('Y-m-d H:i:s');
        $idvendor = $this->input->post('idvendor');
        $idbranch = $this->input->post('idbranch');
        $iduser = $this->input->post('iduser');
        $finyear = $this->input->post('financial_year');
        $id_stock = $this->input->post('id_stock');
        $sku_type = $this->input->post('sku_type');
        $imei_no = $this->input->post('imei_no');
        $idvariant = $this->input->post('idvariant');
        $qty = $this->input->post('qty');
        $product_name = $this->input->post('product_name');
        $basic_arr=array();$discount_amt_arr=array();$taxable_amt_arr=array();$total_amount_arr=array();
        $cgst_amt_arr=array();$sgst_amt_arr=array();$igst_amt_arr=array();$tax_arr=array();$overall_total_amt=0;
        for($i=0;$i<count($id_stock);$i++){
            $basic=0;$discount_amt=0;$taxable_amt=0;$cgst_amt=0;$sgst_amt=0;$igst_amt=0;$tax=0;$total_amount=0;
            if($sku_type[$i] == 4){
                $purchase_product_qty = $this->Purchase_model->get_purchase_product_byidvraiant_vendor($idvariant[$i], $idvendor);
//                die('<pre>'.print_r($purchase_product_qty, 1).'</pre>');
                $basic = $qty[$i] * $purchase_product_qty->price;
                array_push($basic_arr,$basic);
                $discount_amt = ($purchase_product_qty->discount_amt / $purchase_product_qty->qty) * $qty[$i];
                array_push($discount_amt_arr,$discount_amt);
                $taxable_amt = ($purchase_product_qty->taxable_amt / $purchase_product_qty->qty) * $qty[$i];
                array_push($taxable_amt_arr,$taxable_amt);
                $cgst_amt = ($purchase_product_qty->cgst_amt / $purchase_product_qty->qty) * $qty[$i];
                array_push($cgst_amt_arr,$cgst_amt);
                $sgst_amt = ($purchase_product_qty->sgst_amt / $purchase_product_qty->qty) * $qty[$i];
                array_push($sgst_amt_arr,$sgst_amt);
                $igst_amt = ($purchase_product_qty->igst_amt / $purchase_product_qty->qty) * $qty[$i];
                array_push($igst_amt_arr,$igst_amt);
                $tax = $cgst_amt + $sgst_amt + $igst_amt;
                array_push($tax_arr,$tax);
                $total_amount = ($purchase_product_qty->total_amount / $purchase_product_qty->qty) * $qty[$i];
                array_push($total_amount_arr,$total_amount);
            }else{
                $purchase_product = $this->Purchase_model->get_purchase_product_byimei($imei_no[$i]);
                $discount_amt = $purchase_product->discount_amt;
                $taxable_amt = $purchase_product->taxable_amt;
                $basic = $purchase_product->basic;
                $cgst_per = $purchase_product->cgst_per;
                $sgst_per = $purchase_product->sgst_per;
                $igst_per = $purchase_product->igst_per;
                $cgst_amt = $purchase_product->cgst_amt;
                $sgst_amt = $purchase_product->sgst_amt;
                $igst_amt = $purchase_product->igst_amt;
                $tax = $purchase_product->tax;
                $total_amount = $purchase_product->total_amount;
                array_push($discount_amt_arr,$discount_amt);
                array_push($taxable_amt_arr,$taxable_amt);
                array_push($basic_arr,$basic);
                array_push($cgst_amt_arr,$cgst_amt);
                array_push($sgst_amt_arr,$sgst_amt);
                array_push($igst_amt_arr,$igst_amt);
                array_push($tax_arr,$tax);
                array_push($total_amount_arr,$total_amount);
            }
        }
        $overall_total_amt = array_sum($total_amount_arr);
//        die('<pre>'.print_r($basic_arr, 1).'</pre>'.array_sum($basic_arr));
        $data = array(
            'date' => $date,
            'idvendor' => $idvendor,
            'idbranch' => $idbranch,
            'financial_year' => $finyear,
            'total_basic_amt' => array_sum($basic_arr),
            'total_discount_amt' => array_sum($discount_amt_arr),
            'total_taxable_amt' => array_sum($taxable_amt_arr),
            'total_cgst_amt' => array_sum($cgst_amt_arr),
            'total_sgst_amt' => array_sum($sgst_amt_arr),
            'total_igst_amt' => array_sum($igst_amt_arr),
            'total_tax' => array_sum($tax_arr),
            'gross_amount' => $overall_total_amt,
            'final_amount' => $overall_total_amt,
            'entry_time' => $entry_time,
            'purchase_return_by' => $iduser,
            'purchase_return_approved_by' => $this->input->post('approved_by'),
            'purchase_return_reason' => $this->input->post('reason'),
        );
        $idpurchase_return = $this->Purchase_model->save_purchase_return($data);
        $nestarray[] = array('nest'=>array());
        $imei_history[] = array('nest'=>array());
        for($i=0;$i<count($id_stock);$i++){
            $basic=0;$discount_amt=0;$taxable_amt=0;$cgst_amt=0;$sgst_amt=0;$igst_amt=0;$tax=0;$total_amount=0;
            if($sku_type[$i] == 4){
                $purchase_product_qty = $this->Purchase_model->get_purchase_product_byidvraiant_vendor($idvariant[$i], $idvendor);
                $price = $purchase_product_qty->price;
                $cgst_per = $purchase_product_qty->cgst_per;
                $sgst_per = $purchase_product_qty->sgst_per;
                $igst_per = $purchase_product_qty->igst_per;
                $nestarray['nest'][] = array(
                    'idbranch' => $idbranch,
                    'idpurchase_return' => $idpurchase_return,
                    'date' => $date,
                    'idvendor' => $idvendor,
                    'product_name' => $product_name[$i],
                    'imei_no' => $imei_no[$i],
                    'idproductcategory' => $this->input->post('idproductcategory['.$i.']'),
                    'idcategory' => $this->input->post('idcategory['.$i.']'),
                    'idmodel' => $this->input->post('idmodel['.$i.']'),
                    'idbrand' => $this->input->post('idbrand['.$i.']'),
                    'idskutype' => 4,
                    'idvariant' => $idvariant[$i],
                    'qty' => $this->input->post('qty['.$i.']'),
                    'idgodown' => 1, // New godown
                    'price' => $price,
                    'discount_amt' => $discount_amt_arr[$i],
                    'taxable_amt' => $taxable_amt_arr[$i],
                    'basic' => $basic_arr[$i],
                    'cgst_per' => $cgst_per,
                    'sgst_per' => $sgst_per,
                    'igst_per' => $igst_per,
                    'cgst_amt' => $cgst_amt_arr[$i],
                    'sgst_amt' => $sgst_amt_arr[$i],
                    'igst_amt' => $igst_amt_arr[$i],
                    'tax' => $tax_arr[$i],
                    'total_amount' => $total_amount_arr[$i],
                );
                $this->Purchase_model->update_skustock_byidstock($id_stock[$i], $this->input->post('qty['.$i.']'));
            }else{
                $purchase_product = $this->Purchase_model->get_purchase_product_byimei($imei_no[$i]);
                $nestarray['nest'][] = array(
                    'idbranch' => $idbranch,
                    'idpurchase_return' => $idpurchase_return,
                    'date' => $date,
                    'idvendor' => $idvendor,
                    'product_name' => $product_name[$i],
                    'imei_no' => $imei_no[$i],
                    'idproductcategory' => $this->input->post('idproductcategory['.$i.']'),
                    'idcategory' => $this->input->post('idcategory['.$i.']'),
                    'idmodel' => $this->input->post('idmodel['.$i.']'),
                    'idbrand' => $this->input->post('idbrand['.$i.']'),
                    'idvariant' => $idvariant[$i],
                    'qty' => 1,
                    'idgodown' => 1, // New godown
                    'price' => $purchase_product->price,
                    'idskutype' => $sku_type[$i],
                    'discount_amt' => $purchase_product->discount_amt,
                    'taxable_amt' => $purchase_product->taxable_amt,
                    'basic' => $purchase_product->basic,
                    'cgst_per' => $purchase_product->cgst_per,
                    'sgst_per' => $purchase_product->sgst_per,
                    'igst_per' => $purchase_product->igst_per,
                    'cgst_amt' => $purchase_product->cgst_amt,
                    'sgst_amt' => $purchase_product->sgst_amt,
                    'igst_amt' => $purchase_product->igst_amt,
                    'tax' => $purchase_product->tax,
                    'total_amount' => $purchase_product->total_amount,
                );
                $this->Purchase_model->delete_stock_byidstock($id_stock[$i]);
                $imei_history['nest'][]=array(
                    'imei_no' => $imei_no[$i],
                    'entry_type' => 'Purchase Return',
                    'entry_time' => $entry_time,
                    'date' => $date,
                    'idbranch' => $idbranch,
                    'idgodown' => 1,
                    'idvariant' => $idvariant[$i],
//                    'model_variant_full_name' => $product_name[$i],
                    'idimei_details_link' => 3, // Purchase return from imei_details_link table
                    'idlink' => $idpurchase_return,
                    'iduser' => $iduser,
                );
            }
        }
        $this->Purchase_model->save_purchase_return_product($nestarray['nest']);
        if(count($imei_history['nest']) > 0){
            $this->General_model->save_batch_imei_history($imei_history['nest']);
        }
        $prpayment = array(
            'idpurchase_return' => $idpurchase_return,
            'purchase_return_invid' => $finyear.$idpurchase_return,
            'entry_time' => $entry_time,
            'date' => $date,
            'idbranch' => $idbranch,
            'idpayment_mode' => 12, // credit
            'amount' => $overall_total_amt,
            'idvendor' => $idvendor,
        );
        $this->Purchase_model->save_purchase_return_payment($prpayment);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Product inward is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Product Inwarded');
        }
        return redirect('Purchase_return/purchase_return_details/'.$idpurchase_return);
    }
    public function save_purchase_return_old() {
//        die('<pre>'.print_r($_POST, 1).'</pre>');
        $this->db->trans_begin();
        $date = date('Y-m-d');
        $entry_time = date('Y-m-d H:i:s');
        $idvendor = $this->input->post('idvendor');
        $idbranch = $this->input->post('idbranch');
        $iduser = $this->input->post('iduser');
        $finyear = $this->input->post('financial_year');
        $id_stock = $this->input->post('id_stock');
        $sku_type = $this->input->post('sku_type');
        $imei_no = $this->input->post('imei_no');
        $idvariant = $this->input->post('idvariant');
        $qty = $this->input->post('qty');
        $product_name = $this->input->post('product_name');
        $basic_arr=array();$discount_amt_arr=array();$taxable_amt_arr=array();$total_amount_arr=array();
        $cgst_amt_arr=array();$sgst_amt_arr=array();$igst_amt_arr=array();$tax_arr=array();$overall_total_amt=0;
        for($i=0;$i<count($id_stock);$i++){
            $basic=0;$discount_amt=0;$taxable_amt=0;$cgst_amt=0;$sgst_amt=0;$igst_amt=0;$tax=0;$total_amount=0;
//            if($sku_type[$i] == 4){
//                $purchase_product_qty = $this->Purchase_model->get_purchase_product_byidvraiant_vendor($idvariant[$i], $idvendor);
////                die('<pre>'.print_r($purchase_product_qty, 1).'</pre>');
//                $basic = $qty[$i] * $purchase_product_qty->price;
//                array_push($basic_arr,$basic);
//                $discount_amt = ($purchase_product_qty->discount_amt / $purchase_product_qty->qty) * $qty[$i];
//                array_push($discount_amt_arr,$discount_amt);
//                $taxable_amt = ($purchase_product_qty->taxable_amt / $purchase_product_qty->qty) * $qty[$i];
//                array_push($taxable_amt_arr,$taxable_amt);
//                $cgst_amt = ($purchase_product_qty->cgst_amt / $purchase_product_qty->qty) * $qty[$i];
//                array_push($cgst_amt_arr,$cgst_amt);
//                $sgst_amt = ($purchase_product_qty->sgst_amt / $purchase_product_qty->qty) * $qty[$i];
//                array_push($sgst_amt_arr,$sgst_amt);
//                $igst_amt = ($purchase_product_qty->igst_amt / $purchase_product_qty->qty) * $qty[$i];
//                array_push($igst_amt_arr,$igst_amt);
//                $tax = $cgst_amt + $sgst_amt + $igst_amt;
//                array_push($tax_arr,$tax);
//                $total_amount = ($purchase_product_qty->total_amount / $purchase_product_qty->qty) * $qty[$i];
//                array_push($total_amount_arr,$total_amount);
//            }else{
//                $purchase_product = $this->Purchase_model->get_purchase_product_byimei($imei_no[$i]);
                $discount_amt = $this->input->post('discount['.$i.']');
                $basic = $this->input->post('basic['.$i.']');
                $taxable_amt = $basic - $discount_amt;
                $cgst_per = $this->input->post('cgst_per['.$i.']');
                $sgst_per = $this->input->post('sgst_per['.$i.']');
                $igst_per = $this->input->post('igst_per['.$i.']');
                $cgst_amt = $this->input->post('cgst_amt['.$i.']');
                $sgst_amt = $this->input->post('sgst_amt['.$i.']');
                $igst_amt = $this->input->post('igst_amt['.$i.']');
                $tax = $cgst_amt + $sgst_amt + $igst_amt;
                $total_amount = $this->input->post('total['.$i.']');
                array_push($discount_amt_arr,$discount_amt);
                array_push($taxable_amt_arr,$taxable_amt);
                array_push($basic_arr,$basic);
                array_push($cgst_amt_arr,$cgst_amt);
                array_push($sgst_amt_arr,$sgst_amt);
                array_push($igst_amt_arr,$igst_amt);
                array_push($tax_arr,$tax);
                array_push($total_amount_arr,$total_amount);
//            }
        }
        $overall_total_amt = array_sum($total_amount_arr);
//        die('<pre>'.print_r($basic_arr, 1).'</pre>'.array_sum($basic_arr));
        $data = array(
            'date' => $date,
            'idvendor' => $idvendor,
            'idbranch' => $idbranch,
            'financial_year' => $finyear,
            'total_basic_amt' => array_sum($basic_arr),
            'total_discount_amt' => array_sum($discount_amt_arr),
            'total_taxable_amt' => array_sum($taxable_amt_arr),
            'total_cgst_amt' => array_sum($cgst_amt_arr),
            'total_sgst_amt' => array_sum($sgst_amt_arr),
            'total_igst_amt' => array_sum($igst_amt_arr),
            'total_tax' => array_sum($tax_arr),
            'gross_amount' => $overall_total_amt,
            'final_amount' => $overall_total_amt,
            'entry_time' => $entry_time,
            'purchase_return_by' => $iduser,
            'purchase_return_approved_by' => $this->input->post('approved_by'),
            'purchase_return_reason' => $this->input->post('reason'),
        );
        $idpurchase_return = $this->Purchase_model->save_purchase_return($data);
        $nestarray[] = array('nest'=>array());
        $imei_history[] = array('nest'=>array());
        for($i=0;$i<count($id_stock);$i++){
            $basic=0;$discount_amt=0;$taxable_amt=0;$cgst_amt=0;$sgst_amt=0;$igst_amt=0;$tax=0;$total_amount=0;
//            if($sku_type[$i] == 4){
////                $purchase_product_qty = $this->Purchase_model->get_purchase_product_byidvraiant_vendor($idvariant[$i], $idvendor);
//                $price = $purchase_product_qty->price;
//                $cgst_per = $purchase_product_qty->cgst_per;
//                $sgst_per = $purchase_product_qty->sgst_per;
//                $igst_per = $purchase_product_qty->igst_per;
//                $nestarray['nest'][] = array(
//                    'idbranch' => $idbranch,
//                    'idpurchase_return' => $idpurchase_return,
//                    'date' => $date,
//                    'idvendor' => $idvendor,
//                    'product_name' => $product_name[$i],
//                    'imei_no' => $imei_no[$i],
//                    'idproductcategory' => $this->input->post('idproductcategory['.$i.']'),
//                    'idcategory' => $this->input->post('idcategory['.$i.']'),
//                    'idmodel' => $this->input->post('idmodel['.$i.']'),
//                    'idbrand' => $this->input->post('idbrand['.$i.']'),
//                    'idskutype' => 4,
//                    'idvariant' => $idvariant[$i],
//                    'qty' => $this->input->post('qty['.$i.']'),
//                    'idgodown' => 1, // New godown
//                    'price' => $price,
//                    'discount_amt' => $discount_amt_arr[$i],
//                    'taxable_amt' => $taxable_amt_arr[$i],
//                    'basic' => $basic_arr[$i],
//                    'cgst_per' => $cgst_per,
//                    'sgst_per' => $sgst_per,
//                    'igst_per' => $igst_per,
//                    'cgst_amt' => $cgst_amt_arr[$i],
//                    'sgst_amt' => $sgst_amt_arr[$i],
//                    'igst_amt' => $igst_amt_arr[$i],
//                    'tax' => $tax_arr[$i],
//                    'total_amount' => $total_amount_arr[$i],
//                );
//                $this->Purchase_model->update_skustock_byidstock($id_stock[$i], $this->input->post('qty['.$i.']'));
//            }else{
//                $purchase_product = $this->Purchase_model->get_purchase_product_byimei($imei_no[$i]);
                $nestarray['nest'][] = array(
                    'idbranch' => $idbranch,
                    'idpurchase_return' => $idpurchase_return,
                    'date' => $date,
                    'idvendor' => $idvendor,
                    'product_name' => $product_name[$i],
                    'imei_no' => $imei_no[$i],
                    'idproductcategory' => $this->input->post('idproductcategory['.$i.']'),
                    'idcategory' => $this->input->post('idcategory['.$i.']'),
                    'idmodel' => $this->input->post('idmodel['.$i.']'),
                    'idbrand' => $this->input->post('idbrand['.$i.']'),
                    'idvariant' => $idvariant[$i],
                    'qty' => 1,
                    'idgodown' => 1, // New godown
                    'price' => $this->input->post('basic['.$i.']'),
                    'idskutype' => $sku_type[$i],
                    'discount_amt' => $this->input->post('discount['.$i.']'),
                    'taxable_amt' => $this->input->post('basic['.$i.']') - $this->input->post('discount['.$i.']'),
                    'basic' => $this->input->post('basic['.$i.']'),
                    'cgst_per' => $this->input->post('cgst_per['.$i.']'),
                    'sgst_per' => $this->input->post('sgst_per['.$i.']'),
                    'igst_per' => $this->input->post('igst_per['.$i.']'),
                    'cgst_amt' => $this->input->post('cgst_amt['.$i.']'),
                    'sgst_amt' => $this->input->post('sgst_amt['.$i.']'),
                    'igst_amt' => $this->input->post('igst_amt['.$i.']'),
                    'tax' => $this->input->post('cgst_amt['.$i.']') + $this->input->post('sgst_amt['.$i.']') + $this->input->post('igst_amt['.$i.']'),
                    'total_amount' => $this->input->post('total['.$i.']'),
                );
                $this->Purchase_model->delete_stock_byidstock($id_stock[$i]);
                $imei_history['nest'][]=array(
                    'imei_no' => $imei_no[$i],
                    'entry_type' => 'Purchase Return',
                    'entry_time' => $entry_time,
                    'date' => $date,
                    'idbranch' => $idbranch,
                    'idgodown' => 1,
                    'idvariant' => $idvariant[$i],
//                    'model_variant_full_name' => $product_name[$i],
                    'idimei_details_link' => 3, // Purchase return from imei_details_link table
                    'idlink' => $idpurchase_return,
                    'iduser' => $iduser,
                );
//            }
        }
        $this->Purchase_model->save_purchase_return_product($nestarray['nest']);
        if(count($imei_history['nest']) > 0){
            $this->General_model->save_batch_imei_history($imei_history['nest']);
        }
        $prpayment = array(
            'idpurchase_return' => $idpurchase_return,
            'purchase_return_invid' => $finyear.$idpurchase_return,
            'entry_time' => $entry_time,
            'date' => $date,
            'idbranch' => $idbranch,
            'idpayment_mode' => 12, // credit
            'amount' => $overall_total_amt,
            'idvendor' => $idvendor,
        );
        $this->Purchase_model->save_purchase_return_payment($prpayment);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Product inward is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Product Inwarded');
        }
        return redirect('Purchase_return/purchase_return_details/'.$idpurchase_return);
    }
}