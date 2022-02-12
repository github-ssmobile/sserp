<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Purchase extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Purchase_model');
        $this->load->model('Stock_model');
        $this->load->model('Inward_model');
        $this->load->model('Transfer_model');
    }
    public function purchase_order() {
        $q['tab_active'] = 'Purchase';
        $datefrom=''; $dateto=''; $status=0;
        $q['vendor_data'] = $this->General_model->get_active_vendor_data();
        $q['purchase_order'] = $this->Purchase_model->ajax_get_purchase_order_data($status, $datefrom, $dateto);
        $q['godown_data'] = $this->General_model->get_active_godown_data();
        $q['warehouse_data'] = $this->General_model->get_active_warehouse_data();
        $this->load->view('purchase/purchase_order',$q);
    }
    public function create_purchase_order() {
        $q['tab_active'] = 'Purchase';
        $q['vendor_data'] = $this->General_model->get_active_vendor_data();
        $q['godown_data'] = $this->General_model->get_active_godown_data();
        $q['warehouse_data'] = $this->General_model->get_active_warehouse_data();
        if($_SESSION['idrole'] == 45){
            $sale_type = array(1);
            $q['model_variant'] = $this->General_model->get_product_by_sale_type($sale_type);
        }else{
            $q['model_variant'] = $this->General_model->get_model_variant_data();
        }
        $this->load->view('purchase/create_purchase_order',$q);
    }
    public function purchase_order_list() {
        $q['tab_active'] = '';
        $q['vendor_data'] = $this->General_model->get_active_vendor_data();
        $q['godown_data'] = $this->General_model->get_active_godown_data();
        $datefrom=''; $dateto=''; $status=0;
        $q['purchase_order'] = $this->Purchase_model->ajax_get_purchase_order_data($status, $datefrom, $dateto);
        $q['purchase_order_product'] = $this->Purchase_model->get_purchase_order_product();
        $q['warehouse_data'] = $this->General_model->get_active_warehouse_data();
        $this->load->view('purchase/purchase_order_list',$q);
    }
    public function purchase_order_details($idpo) {
        $q['tab_active'] = '';
        $q['purchase_order'] = $this->Purchase_model->get_purchase_order_byid($idpo);
        $q['purchase_order_product'] = $this->Purchase_model->get_purchase_order_product_byid($idpo);
        $this->load->view('purchase/purchase_order_details',$q);
    }
    public function purchase_order_details_print($idpo) {
        $q['tab_active'] = '';
        $q['purchase_order'] = $this->Purchase_model->get_purchase_order_byid($idpo);
        $q['purchase_order_product'] = $this->Purchase_model->get_purchase_order_product_byid($idpo);
        $this->load->view('purchase/purchase_order_details_print',$q);
    }
    public function purchase_direct_inward_details($idpo) {
        $q['tab_active'] = '';
        $q['purchase_order'] = $this->Purchase_model->get_purchase_direct_inward_byid($idpo);
        $q['purchase_order_product'] = $this->Purchase_model->get_purchase_direct_inward_product_byid($idpo);
        $this->load->view('purchase/purchase_direct_inward_details',$q);
    }
    public function ready_to_intake_po() {
        $q['tab_active'] = '';
        $q['purchase_order'] = $this->Purchase_model->ready_to_intake_po_list(1, $_SESSION['idbranch']); // approved
//        $q['purchase_order_product'] = $this->Purchase_model->get_purchase_order_product();
        $this->load->view('purchase/ready_to_intake_po',$q);
    }
    public function inward_details($id){
        $q['tab_active'] = 'Inward';
        $q['inward_data'] = $this->Inward_model->get_inward_byid($id);
        $q['inward_product'] = $this->Inward_model->get_inward_product_byid($id);
        $this->load->view('purchase/inward_details', $q);
    }
    public function purchase_print($id){
        $q['tab_active'] = 'Inward';
        $q['inward_data'] = $this->Inward_model->get_inward_byid($id);
        $q['inward_product'] = $this->Inward_model->get_inward_product_byid($id);
        $this->load->view('purchase/purchase_print', $q);
    }
    public function purchase_scanned_report() {
        $q['tab_active'] = '';
        $q['inward_data'] = $this->Inward_model->get_inward_data();
        $q['vendor_data'] = $this->General_model->get_active_vendor_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $q['model_variants_data'] = $this->General_model->get_model_variant_data();
        $this->load->view('purchase/purchase_scanned_report',$q);
    }
   
    public function ajax_get_purchase_report(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idvendor = $this->input->post('idvendor');
        $idbrand = $this->input->post('idbrand');
        $idmodel = $this->input->post('idmodel');
        
        $vendor_arr = $this->input->post('vendors');
        $inward_data = $this->Inward_model->ajax_get_inward_data_byfilter($from, $to, $idvendor, $vendor_arr);
        if(count($inward_data) > 0){ ?>
            <table id="inward_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px">
                <thead class="bg-info fixedelementtop">
                    <th>Inward Id</th>
                    <th>Date Time</th>
                    <th>Vendor Inv No</th>
                    <th>Vendor</th>
                    <th>Vendor GST</th>
                    <th>Vendor Invoice Date</th>
                    <th>Basic</th>
                    <th>Charges</th>
                    <th>Discount</th>
                    <th>Taxable</th>
                    <th>Tax</th>
                    <th>Total</th>
                    <th>Created by</th>
                    <th>Remark</th>
                    <th>Info</th>
                </thead>
                <tbody class="data_1">
                    <?php foreach($inward_data as $inward){ ?>
                    <tr>
                        <td><?php echo $inward->financial_year.'/'.$inward->id_inward ?></td>
                        <td><?php echo date('d/m/Y h:i a', strtotime($inward->entry_time)); ?></td>
                        <td><?php echo $inward->supplier_invoice_no ?></td>
                        <td><?php echo $inward->vendor_name ?></td>
                        <td><?php echo $inward->vendor_gst ?></td>
                        <td><?php echo $inward->vendor_invoice_date ?></td>
                        <td><?php echo $inward->total_basic_amt ?></td>
                        <td><?php echo $inward->total_charges_amt ?></td>
                        <td><?php echo $inward->total_discount_amt ?></td>
                        <td><?php echo $inward->total_taxable_amt ?></td>
                        <td><?php echo $inward->total_tax ?></td>
                        <td><?php echo $inward->gross_amount ?></td>
                        <td><?php echo $inward->user_name ?></td>
                        <td><?php echo $inward->remark ?></td>
                        <td><center><a href="<?php echo base_url('purchase/inward_details/'.$inward->id_inward) ?>" class="btn btn-sm btn-primary gradient_info waves-block waves-effect waves-ripple" style="margin: 0"><i class="fa fa-info fa-2x"></i></a></center></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php }else{ ?>
            <script>
                $(document).ready(function (){
                    alert("Data Not Found");
                    return false;
                });
            </script>
        <?php }
        
    }
    
    public function purchase_imei_report() {
        $q['tab_active'] = '';
        $q['inward_data'] = $this->Inward_model->get_inward_product_data();
        $q['vendor_data'] = $this->General_model->get_active_vendor_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $q['model_variants_data'] = $this->General_model->get_model_variant_data();
        $q['product_category'] = $this->General_model->get_product_category_data();
//        die('<pre>'.print_r( $q['inward_data'],1).'</pre>');
        $this->load->view('purchase/purchase_imei_report',$q);
    }
    public function ajax_get_purchase_imei_report(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idvendor = $this->input->post('idvendor');
        $idbrand = $this->input->post('idbrand');
        $idmodel = $this->input->post('idmodel');
        $idpcat = $this->input->post('idpcat');
        $allpcats = $this->input->post('allpcats');
        
//        die(print_r($_POST));
//        $vendor_arr = $this->input->post('vendors');
        $inward_data = $this->Inward_model->ajax_get_inward_product_data($from, $to, $idvendor, $idpcat, $allpcats);
        if(count($inward_data) > 0){ ?>
        <div style="overflow-x: auto;">
            <table id="inward_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover" style="font-size: 13px">
                <thead class="bg-info fixedelement">
                   <th>Inward Id</th>
                    <th>Date</th>
                    <th>Vendor Inv No</th>
                    <th>Vendor Invoice Date</th>
                    <th>Vendor</th>
                    <th>Vendor GST</th>
                    <th>Imei</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Basic</th>
                    <th>(+)Charges</th>
                    <th>(-)Discount</th>
                    <th>Taxable</th>
                    <th>(+)Tax</th>
                    <th>Total</th>
                    <th>CGST(%)</th>
                    <th>SGST(%)</th>
                    <th>IGST(%)</th>
                    <th>Created by</th>
                    <th>Remark</th>
                    <th>Info</th>
                </thead>
                <tbody class="data_1">
                    <?php foreach($inward_data as $inward){ ?>
                    <tr>
                         <td><?php echo $inward->financial_year.'/'.$inward->id_inward ?></td>
                    <td><?php echo $inward->date; ?></td>
                    <td><?php echo $inward->supplier_invoice_no ?></td>
                    <td><?php echo $inward->vendor_invoice_date ?></td>
                    <td><?php echo $inward->vendor_name ?></td>
                    <td><?php echo $inward->vendor_gst ?></td>
                    <td><?php echo $inward->imei_no ?></td>
                    <td><?php echo $inward->product_name ?></td>
                    <td><?php echo $inward->qty ?></td>
                    <td><?php echo $inward->price ?></td>
                    <td><?php echo $inward->basic ?></td>
                    <td><?php echo $inward->charges_amt ?></td>
                    <td><?php echo $inward->discount_amt ?></td>
                    <td><?php echo $inward->taxable_amt ?></td>
                    <td><?php echo $inward->tax ?></td>
                    <td><?php echo $inward->total_amount ?></td>
                    <td><?php echo 'Input CGST '. $inward->cgst_per.'%'?></td>
                    <td><?php echo 'Input SGST '. $inward->sgst_per.'%'?></td>
                    <td><?php if($inward->cgst_per == 0){ echo 'Input IGST '. $inward->igst_per.'%' ; }else{ echo 'Input IGST @ 0%' ; }?></td>
                    <td><?php echo $inward->user_name ?></td>
                    <td><?php echo $inward->remark ?></td>
                    <td><center><a href="<?php echo base_url('purchase/inward_details/'.$inward->id_inward) ?>" class="btn btn-sm btn-primary gradient_info waves-block waves-effect waves-ripple" style="margin: 0"><i class="fa fa-info fa-2x"></i></a></center></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
                </div>
        <?php }else{ ?>
            <script>
                $(document).ready(function (){
                    alert("Data Not Found");
                    return false;
                });
            </script>
        <?php }
        
    }

    public function purchase_inward($idpo, $idvendor) {
        $q['tab_active'] = '';
        $q['model_variant'] = $this->General_model->ajax_get_vendor_has_brand_products($idvendor);
        $q['godown_data'] = $this->General_model->get_active_godown_data();
        $q['purchase_order'] = $this->Purchase_model->get_purchase_order_byid($idpo);
        $q['purchase_order_product'] = $this->Purchase_model->get_purchase_order_product_byid($idpo);
        $this->load->view('purchase/purchase_inward',$q);
    }
    public function purchase_direct_inward($idpo) {
        $q['tab_active'] = '';
//        $q['model_variant'] = $this->General_model->get_model_variant_data();
        $q['godown_data'] = $this->General_model->get_active_godown_data();
        $q['purchase_order'] = $this->Purchase_model->get_purchase_direct_inward_byid($idpo);
        $q['purchase_order_product'] = $this->Purchase_model->get_purchase_direct_inward_product_byid($idpo);
        $this->load->view('purchase/purchase_direct_inward',$q);
    }
    public function direct_inward_list() {
        $q['tab_active'] = '';
        $q['purchase_direct_inward'] = $this->Purchase_model->get_purchase_direct_inward_data();
        $q['branch_data_list'] = $this->General_model->get_active_branch_data();
        $this->load->view('purchase/direct_inward_list',$q);
    }
    public function direct_inward() {
        $q['tab_active'] = '';
        $idbranch = $this->session->userdata('idbranch');
        $q['branch_data'] = $this->General_model->get_branch_byid($idbranch);
        $q['vendor_data'] = $this->General_model->get_active_vendor_data();
        $q['godown_data'] = $this->General_model->get_active_godown_data();
        $q['warehouse_data'] = $this->General_model->get_active_warehouse_data();
        $q['purchase_direct_inward'] = $this->Purchase_model->get_purchase_direct_inward_data_byidbranch($idbranch);
        $q['model_variant'] = $this->General_model->get_model_variant_data();
        $this->load->view('purchase/direct_inward',$q);
    }
    public function direct_inward1() {
        $q['tab_active'] = '';
        $q['model_variant'] = $this->General_model->get_model_variant_data();
        $q['vendor_data'] = $this->General_model->get_active_vendor_data();
//        $q['purchase_order'] = $this->Purchase_model->get_purchase_order_byid();
//        $q['purchase_order_product'] = $this->Purchase_model->get_purchase_order_product_byid();
        $this->load->view('purchase/direct_inward',$q);
    }
    public function ajax_get_purchase_direct_inward_data_byidbranch() {
        $idbranch = $this->input->post('idbranch');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $status = $this->input->post('status');
        $purchase_direct_inward = $this->Purchase_model->ajax_get_purchase_direct_inward_data_byidbranch($status,$idbranch,$datefrom,$dateto);
        ?>
        <thead>
            <th>Sr</th>
            <th>PO ID</th>
            <th>Date</th>
            <th>Warehouse</th>
            <th>Vendor</th>
            <th>Status</th>
            <th>Action</th>
        </thead>
        <tbody class="data_1">
            <?php $i=1; foreach ($purchase_direct_inward as $po){ ?>
            <tr>
                <td><?php echo $i ?></td>
                <td><?php echo $po->financial_year.$po->id_purchase_direct_inward ?></td>
                <td><?php echo $po->date ?></td>
                <td><?php echo $po->branch_name ?></td>
                <td><?php echo $po->vendor_name ?></td>
                <?php if($po->status == 3){ ?>
                <td>Inwarded</td>
                <td><a target="_blank" href="<?php echo base_url('purchase/inward_details/'.$po->id_purchase_direct_inward) ?>" class="btn btn-sm btn-warning gradient_info waves-effect waves-light"><i class="fa fa-info fa-lg"></i></a></td>
                <?php }else{ ?>
                <td><?php if($po->status == 0){ echo 'Pending'; }elseif($po->status == 1){ echo 'Approved'; }elseif($po->status == 2){ echo 'Rejected'; } ?></td>
                <td><a target="_blank" href="<?php echo base_url('purchase/purchase_direct_inward_details/'.$po->id_purchase_direct_inward) ?>" class="btn btn-sm btn-warning gradient_info waves-effect waves-light"><i class="fa fa-info fa-lg"></i></a></td>
                <?php } ?>
            </tr>
            <?php $i++; } ?>
        </tbody>
    <?php 
    }
    public function get_variant_by_id() {
        $id = $this->input->post('id');
        $idwarehouse = $this->input->post('idwarehouse');
//        $idgodown = $this->input->post('idgodown');
//        $godown_name = $this->input->post('godown_name');
        $variant = $this->Purchase_model->get_variant_by_id($id);
//        die(print_r($_POST));
        $variant_stock = $this->Stock_model->get_stock_by_variant_branch($id, $idwarehouse);
        $branch_stock = $this->Stock_model->get_all_branch_stock_by_variant($id);
        $intransit_stock = $this->Stock_model->get_all_intransit_stock_by_variant($id);
        $branch_sale = $this->Stock_model->get_all_branch_sale_qty_by_variant($id);
//        die(print_r($variant_stock));
        $variant_qty = 0;$branch_qty=0;$intransit_qty=0;$branch_sale_qty=0;
        if($variant_stock->sum_qty != ''){
            $variant_qty = $variant_stock->sum_qty;
        }
        if($branch_stock->sum_qty != ''){
            $branch_qty = $branch_stock->sum_qty;
        }
        if($intransit_stock->sum_qty != ''){
            $intransit_qty = $intransit_stock->sum_qty;
        }
        if($branch_sale->sum_qty != ''){
            $branch_sale_qty = $branch_sale->sum_qty;
        }
//        echo '<pre>'.print_r($variant,1).'</pre>';
        ?>
        <tr>
            <!--<td>-->
                <?php // echo $variant->id_variant; ?>
                <!--<input type="hidden" class="sel_idgodown" name="sel_idgodown[]" value="<?php // echo $idgodown; ?>" />-->
            <!--</td>-->
            <td>
                <input type="hidden" class="idvariant" name="idvariant[]" value="<?php echo $variant->id_variant; ?>" />
                <input type="hidden" class="idsku_type" name="idsku_type[]" value="<?php echo $variant->idsku_type; ?>" />
                <input type="hidden" class="product_name" name="product_name[]" value="<?php echo $variant->product_category_name.' - '.$variant->full_name; ?>" />
                <input type="hidden" class="idsku_type" name="idsku_type[]" value="<?php echo $variant->idsku_type; ?>" />
                <input type="hidden" class="idproductcategory" name="idproductcategory[]" value="<?php echo $variant->idproductcategory; ?>" />
                <input type="hidden" class="idcategory" name="idcategory[]" value="<?php echo $variant->idcategory; ?>" />
                <input type="hidden" class="idmodel" name="idmodel[]" value="<?php echo $variant->idmodel; ?>" />
                <input type="hidden" class="idbrand" name="idbrand[]" value="<?php echo $variant->idbrand; ?>" />
                <input type="hidden" class="sale_type" name="sale_type[]" value="<?php echo $variant->sale_type; ?>" />
                <?php echo $variant->product_category_name.' - '.$variant->full_name; ?>
            </td>
            <!--<td><?php // echo $godown_name; ?></td>-->
            <td><?php echo $variant_qty; ?></td>
            <td><?php echo $intransit_qty; ?></td>
            <td><?php echo $branch_qty; ?></td>
            <td><?php echo $branch_sale_qty; ?></td>
            <td><input type="number" class="form-control input-sm qty" name="qty[]" placeholder="Enter Quantity" min="1" required="" max="" style="width: 150px" /></td>
            <td><a class="btn btn-sm btn-warning waves-effect waves-light remove_btn" id="remove_btn"><i class="fa fa-trash-o fa-lg"></i></a></td>
        </tr>
        <?php 
    }
    public function get_inward_variant_by_id() {
        $id = $this->input->post('id');
        $variant = $this->Purchase_model->get_variant_by_id($id);
        ?>
        <tr>
            <td><?php echo $variant->id_variant; ?>
                <input type="hidden" class="idvariant" name="idvariant[]" value="<?php echo $variant->id_variant; ?>" />
                <input type="hidden" class="idsku_type" name="idsku_type[]" value="<?php echo $variant->idsku_type; ?>" />
            </td>
            <td><?php echo $variant->full_name; ?></td>
            <td><input type="number" class="form-control input-sm" name="qty[]" placeholder="Qty" min="1" required="" style="width: 100px" /></td>
            <td><input type="text" class="form-control input-sm scan_imei" style="width: 130px" placeholder="Scan IMEI" /></td>
            <td>
                <input type="text" class="form-control input-sm scanned_imei" />
            </td>
            <td><center><a class="btn btn-sm btn-warning waves-effect waves-light remove_btn gradient1" id="remove_btn"><i class="fa fa-times fa-lg"></i></a></center></td>
        </tr>
        <?php 
    }
    public function save_purchase_order() {
        $idbranch = $this->input->post('idwarehouse');
        $branch_data = $this->General_model->get_branch_byid($idbranch);
        $y = date('y', mktime(0, 0, 0, 3 + date('m')));
        $y1 = $y + 1;
        $financial_year = "PO/".$y."-".$y1."/".$branch_data->branch_code."/";
        $poapproval = 0;
        if($this->input->post('status') == 1 || $branch_data->po_approval == 0){ 
            $poapproval = 1;
        }
        $po = array(
            'date' => $this->input->post('date'),
            'idwarehouse' => $idbranch,
            'idvendor' => $this->input->post('idvendor'),
            'created_by' => $this->input->post('iduser'),
            'financial_year' => $financial_year,
            'remark' => $this->input->post('remark'),
            'required_approval' => $branch_data->po_approval,
            'status' => $poapproval,
            'entry_time' => date('Y-m-d H:i:s'),
        );
        $idpo = $this->Purchase_model->save_purchase_order($po);
        $first_nestarray[] = array('nest'=>array());
        $idvariant = $this->input->post('idvariant');
        for($i=0;$i<count($idvariant);$i++){
            $first_nestarray['nest'][] = array(
                'idpurchase_order' => $idpo,
                'idmodelvariant' => $this->input->post('idvariant['.$i.']'),
                'qty' => $this->input->post('qty['.$i.']'),
                'idsku_type' => $this->input->post('idsku_type['.$i.']'),
            );
            
            // save for shield
            if($this->input->post('idsku_type['.$i.']') == 4){
                $hostock = $this->Transfer_model->get_branchstock_byidmodel_skutype_godown($this->input->post('idvariant['.$i.']'),4,$idbranch,1);
                if(count($hostock) === 0){
                    if($this->input->post('sale_type['.$i.']') == 1){
                        $inward_stock_sku = array(
                            'date' => $this->input->post('date'),
                            'idgodown' => 1,
                            'idskutype' => 4,
                            'is_gst' => 1,
                            'idbranch' => $idbranch,
                            'idvariant' => $this->input->post('idvariant['.$i.']'),
                            'created_by' => $this->input->post('iduser'),
                            'idvendor' => $this->input->post('idvendor'),
                            'qty' => $this->input->post('qty['.$i.']'),
                            'product_name' => $this->input->post('product_name['.$i.']'),
                            'idproductcategory' => $this->input->post('idproductcategory['.$i.']'),
                            'idcategory' => $this->input->post('idcategory['.$i.']'),
                            'idmodel' => $this->input->post('idmodel['.$i.']'),
                            'idbrand' => $this->input->post('idbrand['.$i.']'),
                        );
                        $this->Inward_model->save_stock($inward_stock_sku);
                    }
                }else{
                    foreach ($hostock as $hstock){
                        $qty = $hstock->qty + $this->input->post('qty['.$i.']');
                        $this->Inward_model->update_stock_byid($hstock->id_stock,$qty);
                    }
                }
            }
        }
        $this->Purchase_model->save_purchase_order_products($first_nestarray['nest']);
//        if($popproval){ 
        if($poapproval == 0){ 
            $serverpath = 'http://117.247.90.162:8800/erp'; 
            $purchase_order = $this->Purchase_model->get_purchase_order_byid($idpo);
            $purchase_order_product = $this->Purchase_model->get_purchase_order_product_byid($idpo);
            $logopath = $serverpath.'assets/images/sslogo.jpg';
            $msg =  '<div style="border: 1px solid #999999">
                    <div style="border-bottom: 1px solid #999999">
                        <img src="'.$logopath.'" height="60" />
                        <div style="font-size: 22px; float: right; padding: 10px; color: #929292"> PURCHASE ORDER </div>
                    </div><br>
                    <div style="width: 60%; float: left">
                        <div style="padding: 5px;font-size: 18px;">
                            SS Communication & Services Pvt Ltd
                        </div>
                    </div>
                    <div style="width: 35%; float:right">
                        <div>PO Date: '.date('d-m-Y',  strtotime($purchase_order->date)).'</div>
                        <div>PO ID: '.$purchase_order->financial_year.'-'.$purchase_order->id_purchase_order.'</div><div class="clearfix"></div>
                    </div><br>
                    <div style="width: 48%; float: left">
                        <div style="padding: 10px;background-color: #d9edf7;font-size: 16px">Vendor</div>'
                            .$purchase_order->vendor_name.'<br>'
                            .$purchase_order->vendor_contact.'<br>'
                            .$purchase_order->state.'<br>'
                            .$purchase_order->vendor_gst.'<br>
                    </div>
                    <div style="width: 48%; float:right">
                        <div style="padding: 10px;background-color: #d9edf7;font-size: 16px">Ship To</div>
                        <div>Warehouse</div>
                        <div>'.$purchase_order->branch_name.'</div>
                    </div>
                    <div class="thumbnail" style="padding: 0">
                        <table id="branch_data" class="table table-condensed table-striped table-bordered" style="width:100%">
                            <thead>
                                <th style="border: 1px solid #929292">Product</th>
                                <th style="border: 1px solid #929292">Qty</th>
                            </thead>
                            <tbody class="data_1">';
                            foreach ($purchase_order_product as $product){
                            $msg .=  '<tr>
                                    <td style="border: 1px solid #929292">'.$product->full_name.'</td>
                                    <td style="border: 1px solid #929292">'.$product->qty.'</td>
                                </tr>';
                            }
                           $msg .= '</tbody></table></div><div class="clearfix"></div></div><div class="clearfix"></div><br>';
//            sendEmail('vcg.gonjari@gmail.com', 'Purchase Order', $msg, 'vcg.gonjari@gmail.com');
        }
        $this->session->set_flashdata('save_data', 'Purchase Order Generated Successfully');
        return redirect($this->session->userdata('dashboard'));
    }
    public function approve_po() {
//        die('<pre>' . print_r($_POST, 1) . '</pre>');
        $id_po = $this->input->post('id_po_order');
        $data = array(
          'status' => $this->input->post('status')
        );
        $this->Purchase_model->update_purchase_order($id_po,$data);
        if($this->input->post('status') == 10){
//        if($this->input->post('status') == 1){
            $purchase_order = $this->Purchase_model->get_purchase_order_byid($id_po);
            $purchase_order_product = $this->Purchase_model->get_purchase_order_product_byid($id_po);
            $serverpath = 'http://117.247.90.162:8800/erp';
            $logopath = $serverpath.'assets/images/sslogo.jpg';
            $msg =  '<div class="thumbnail">
                    <div class="col-xs-4 col-md-4">
                        <img src="'.$logopath.'" height="60" />
                    </div>
                    <div class="col-xs-8 col-md-8">
                        <h3 class="pull-right blue-grey-text"> PURCHASE ORDER</h3><div class="clearfix"></div>
                    </div><div class="clearfix"></div><hr>
                    <div class="col-xs-6 col-md-6">
                        <div class="col-xs-3 col-md-3 text-muted">PO Date</div>
                        <div class="col-xs-9 col-md-9">'.date('d-m-Y',  strtotime($purchase_order->date)).'</div>
                        <div class="col-xs-3 col-md-3 text-muted">PO ID</div>
                        <div class="col-xs-9 col-md-9">'.$purchase_order->financial_year.'-'.$purchase_order->id_purchase_order.'</div><div class="clearfix"></div>
                        <div class="col-xs-3 col-md-3 text-muted">Warehouse</div>
                        <div class="col-xs-9 col-md-9">'.$purchase_order->branch_name.'</div><div class="clearfix"></div>
                    </div>
                    <div class="col-xs-6 col-md-6">
                        <div class="col-xs-2 col-md-2 text-muted">Vendor</div>
                        <div class="col-xs-10 col-md-10">'.$purchase_order->vendor_name.'</div>
                        <div class="col-xs-2 col-md-2 text-muted">Contact</div>
                        <div class="col-xs-10 col-md-10">'.$purchase_order->vendor_contact.'</div><div class="clearfix"></div>
                        <div class="col-xs-2 col-md-2 text-muted">State</div>
                        <div class="col-xs-10 col-md-10">'.$purchase_order->state.'</div><div class="clearfix"></div>
                        <div class="col-xs-2 col-md-2 text-muted">GSTIN</div>
                        <div class="col-xs-10 col-md-10">'.$purchase_order->vendor_gst.'</div><div class="clearfix"></div>
                    </div><div class="clearfix"></div><br>
                    <div class="thumbnail" style="padding: 0">
                        <table id="branch_data" class="table table-condensed table-striped table-bordered" style="margin-bottom: 0">
                            <thead>
                                <th class="col-xs-1 col-md-1">Id</th>
                                <th class="col-xs-4 col-md-4">Product</th>
                                <th class="col-xs-1 col-md-1">Godown</th>
                                <th class="col-xs-1 col-md-1">Qty</th>
                            </thead>
                            <tbody class="data_1">';
                            foreach ($purchase_order_product as $product){
                            $msg .=  '<tr>
                                    <td>'.$product->id_variant.'</td>
                                    <td>'.$product->full_name.'</td>
                                    <td>'.$product->godown_name.'</td>
                                    <td>'.$product->qty.'</td>
                                </tr>';
                            }
                            '</tbody>
                        </table>
                    </div>
                    <div class="clearfix"></div>
                </div><div class="clearfix"></div>';
            sendEmail('vcg.gonjari@gmail.com', 'Purchase Order', $msg, 'vcg.gonjari@gmail.com');
        }
        $this->session->set_flashdata('save_data', 'Purchase Order Updated Successfully');
        return redirect('purchase/purchase_order_list');
    }
    public function approve_direct_inward_bymanager() {
//        die('<pre>' . print_r($_POST, 1) . '</pre>');
        $id_po = $this->input->post('id_purchase_direct_inward');
        $data = array( 'status' => $this->input->post('status') );
        $this->Purchase_model->update_purchase_direct_inward($id_po,$data);
        $this->session->set_flashdata('save_data', 'Direct Inward Updated Successfully');
//        return redirect('purchase/purchase_direct_inward_details/'.$id_po);
        return redirect('purchase/direct_inward_list');
    }
    public function save_purchase_direct_inward() {
//        die('<pre>' . print_r($_POST, 1) . '</pre>');
        $idbranch = $this->input->post('idwarehouse');
        $po = array(
            'date' => $this->input->post('date'),
            'idwarehouse' => $idbranch,
            'idvendor' => $this->input->post('idvendor'),
            'created_by' => $this->input->post('iduser'),
            'financial_year' => $this->input->post('financial_year'),
            'remark' => $this->input->post('remark'),
            'status' => $this->input->post('status'),
            'entry_time' => date('Y-m-d H:i:s'),
        );
        $idpo = $this->Purchase_model->save_purchase_direct_inward($po);
        $first_nestarray[] = array('nest'=>array());
        $idvariant = $this->input->post('idvariant');
        for($i=0;$i<count($idvariant);$i++){
            $first_nestarray['nest'][] = array(
                'idpurchase_direct_inward' => $idpo,
                'idgodown' => $this->input->post('sel_idgodown['.$i.']'),
                'idmodelvariant' => $this->input->post('idvariant['.$i.']'),
                'qty' => $this->input->post('qty['.$i.']'),
                'idsku_type' => $this->input->post('idsku_type['.$i.']'),
            );
        }
        $this->Purchase_model->save_purchase_direct_inward_products($first_nestarray['nest']);
        $this->session->set_flashdata('save_data', 'Purchase Order Generated Successfully');
        return redirect('purchase/direct_inward');
    }
    public function ajax_get_product_byid() {
        $id = $this->input->post('id');
        $gstradio = $this->input->post('gstradio');
        $sel_idgodown = $this->input->post('sel_idgodown');
        $sel_godown_text = $this->input->post('sel_godown_text');
        $models = $this->Purchase_model->get_model_variant_by_id($id);
//        die ('<pre>' . print_r($models, 1) . '</pre>');?>
        <?php foreach ($models as $model){ 
            if($gstradio && $model->cgst == 0){ ?>
            <tr class="fadeout_nongst"><td colspan="12"><h4 style="color: #cc0033"><i class="mdi mdi-alert"></i> Please Setup GST Rates for <?php // echo $model->type.' '.$model->category_name.' '.$model->brand_name.' '.$model->model_name; ?>...</h4></td></tr>
            <?php }else{ ?>
            <tr id="m<?php echo $model->id_variant?>">
                <!--<td><?php // echo $model->id_variant ?></td>-->
                <td>
                    <p style="font-size: 14px"><?php echo $model->full_name; ?></p>
                </td>
                <td class="col-md-1">
                    <input type="hidden" id="idgodown" class="idgodown" name="idgodown[]" value="<?php echo $sel_idgodown; ?>" />
                    <input type="hidden" id="id_purchase_order_product" class="id_purchase_order_product" name="id_purchase_order_product[]" value="0" />
                    <?php echo $sel_godown_text; ?>
                </td>
                <td class="col-md-1">
                    <input type="hidden" id="idtype" class="form-control idtype" name="idtype[]" value="<?php echo $model->idproductcategory ?>" />
                    <input type="hidden" id="idcategory" class="form-control idcategory" name="idcategory[]" value="<?php echo $model->id_category ?>" />
                    <input type="hidden" id="idbrand" class="form-control idbrand" name="idbrand[]" value="<?php echo $model->id_brand ?>" />
                    <input type="hidden" id="idmodel" class="form-control idmodel" name="idmodel[]" value="<?php echo $model->id_variant ?>" />
                    <input type="hidden" id="idmainmodel" class="form-control idmainmodel" name="idmainmodel[]" value="<?php echo $model->idmodel ?>" />
                    <input type="hidden" id="skutype" class="form-control skutype" name="skutype[]" value="<?php echo $model->idsku_type ?>" />
                    <input type="hidden" id="skulenght" class="form-control skulenght" name="skulenght[]" value="<?php echo $model->sku_lenght ?>" />
                    <input type="hidden" id="sale_type" class="sale_type" name="sale_type[]" value="<?php echo $model->sale_type; ?>" />
                    <input type="hidden" id="product_name" class="form-control product_name" name="product_name[]" value="<?php echo $model->category_name.' '.$model->brand_name.' '.$model->model_name; ?>" />
                    <input type="number" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" value="1" min="1"/>
                </td>
                <td class="col-md-1">
                    <input type="text" id="mrp" name="mrp[]" class="form-control input-sm mrp" required="" placeholder="MRP" min="1" style="width: 80px"/>
                </td>
                <td class="col-md-1">
                    <input type="text" id="price" name="price[]" class="form-control input-sm price" required="" placeholder="Price" min="1" style="width: 80px"/>
                </td>
                <td class="col-md-1">
                    <input type="hidden" id="basic" name="basic[]" class="basic" placeholder="Basic" readonly="" value="0" min="0"/>
                    <input type="hidden" id="basic_percent" name="basic_percent[]" class="basic_percent"/>
                    <span class="input-sm spbasic" id="spbasic" name="spbasic[]">0</span>               
                    <input type="hidden" id="chrgs_amt" name="chrgs_amt[]" class="form-control chrgs_amt input-sm" readonly="" placeholder="Amount" value="0"/>
                    <span class="input-sm spchrgs_amt hidden" id="spchrgs_amt" name="spchrgs_amt[]">0</span>
                </td>
                <td class="col-md-1">
                    <input type="hidden" id="discount_per" name="discount_per[]" class="form-control discount_per input-sm" placeholder="Percentage" value="0" />
                    <input type="text" id="discount_amt" name="discount_amt[]" class="form-control discount_amt input-sm" placeholder="Amount" value="0" readonly=""  style="width: 80px" />
                </td>
                <?php if($model->idsku_type == 4){ ?>
                <td>
                    <center>SKU Type -> Quantity</center>
                    <input type="text" id="qty1<?php echo $model->id_variant?>" name="qty1[]" class="form-control input-sm qty1" placeholder="Qty1" readonly="" style="margin: 0; width: 70px; display: none" value="0" />
                    <input type="text" id="qty2<?php echo $model->id_variant?>" name="qty2[]" class="form-control input-sm qty2" readonly="" style="display: none" value="0" />
                </td>
                <td><textarea class="form-control input-sm scanned" id="scanned" name="scanned[]" rows="2" placeholder="Scanned IMEI" style="display: none"></textarea></td>
                <?php }else{ ?>
                <td>
                    <div id="mn<?php echo $model->id_variant?>" style="width: 190px">
                        <input type="hidden" id="idmodel" class="form-control idmodel" value="<?php echo $model->id_variant ?>" />
                        <div class="col-md-3" style="padding: 0; margin: 0;">
                            <input type="text" id="qty1<?php echo $model->id_variant?>" name="qty1[]" class="form-control input-sm qty1" placeholder="Qty1" readonly="" style="margin: 0; width: 60px;" value="1" />
                            <input type="hidden" id="qty2<?php echo $model->id_variant?>" name="qty2[]" class="form-control input-sm qty2" placeholder="Qty1" value="1"/>
                        </div>
                        <div class="col-md-9" style="padding: 0; margin: 0;">
                            <input type="text" id="barcode" name="barcode[]" class="form-control input-sm barcode"  value="" placeholder="Scan IMEI" style="margin: 0; width: 130px"/>
                        </div>
                    </div>
                </td>
                <td>
                    <textarea class="form-control input-sm scanned" id="scanned" name="scanned[]" rows="2" placeholder="Scanned IMEI" style="display: none"></textarea>
                    <div class="form-control input-sm scanned1" id="scanned1" style="min-height: 30px; height: auto; overflow: auto; padding: 2px"></div>
                </td>
                <?php } ?>
                <td>
                    <input type="hidden" id="taxable" name="taxable[]" class="taxable" placeholder="Taxable" readonly="" value="0"/>
                    <span class="input-sm sptaxable" id="sptaxable" name="sptaxable[]">0</span>
                    <?php if($gstradio){ ?>
                        <div>
                            <input type="hidden" id="cgst" name="cgst[]" class="form-control input-sm cgst" value="<?php echo $model->cgst; ?>" readonly=""/>
                            <input type="hidden" id="cgst_amt" name="cgst_amt[]" class="form-control input-sm cgst_amt" value="" placeholder="CGST <?php echo $model->cgst; ?>%" readonly=""/>
                            <span class="input-sm spcgst_amt hidden" id="spcgst_amt" name="spcgst_amt[]"><?php echo $model->cgst; ?>%</span>
                        </div>
                        <div>
                            <input type="hidden" id="sgst" name="sgst[]" class="form-control input-sm sgst" value="<?php echo $model->sgst; ?>" readonly=""/>
                            <input type="hidden" id="sgst_amt" name="sgst_amt[]" class="form-control input-sm sgst_amt" value="" placeholder="SGST <?php echo $model->sgst; ?>%" readonly=""/>
                            <span class="input-sm spsgst_amt hidden" id="spsgst_amt" name="spsgst_amt[]"><?php echo $model->sgst; ?>%</span>
                        </div>
                        <div>
                            <input type="hidden" id="igst" name="igst[]" class="form-control input-sm igst" value="<?php echo $model->igst; ?>" readonly=""/>
                            <input type="hidden" id="igst_amt" name="igst_amt[]" class="form-control input-sm igst_amt" value="" placeholder="IGST <?php echo $model->igst; ?>%" readonly=""/>
                            <span class="input-sm spigst_amt hidden" id="spigst_amt" name="spigst_amt[]"><?php echo $model->igst; ?>%</span>
                            <input type="hidden" class="form-control input-sm tax" id="tax" name="tax[]" value="" placeholder="Tax" readonly=""/>
                        </div>
                    <?php }else{ ?>
                        <div>
                            <input type="hidden" id="cgst" name="cgst[]" class="form-control input-sm cgst" value="0" readonly=""/>
                            <input type="hidden" id="cgst_amt" name="cgst_amt[]" class="form-control input-sm cgst_amt" value="" placeholder="CGST 0%" readonly=""/>
                            <span class="input-sm spcgst_amt hidden" id="spcgst_amt" name="spcgst_amt[]">0%</span>
                        </div>
                        <div>
                            <input type="hidden" id="sgst" name="sgst[]" class="form-control input-sm sgst" value="0" readonly=""/>
                            <input type="hidden" id="sgst_amt" name="sgst_amt[]" class="form-control input-sm sgst_amt" value="" placeholder="SGST 0%" readonly=""/>
                            <span class="input-sm spsgst_amt hidden" id="spsgst_amt" name="spsgst_amt[]">0%</span>
                        </div>
                        <div>
                            <input type="hidden" id="igst" name="igst[]" class="form-control input-sm igst" value="0" readonly=""/>
                            <input type="hidden" id="igst_amt" name="igst_amt[]" class="form-control input-sm igst_amt" value="" placeholder="IGST 0%" readonly=""/>
                            <span class="input-sm spigst_amt hidden" id="spigst_amt" name="spigst_amt[]">0%</span>
                            <input type="hidden" class="form-control input-sm tax" id="tax" name="tax[]" value="" placeholder="Tax" readonly=""/>
                        </div>
                    <?php } ?>
                </td>
                <td>
                    <input type="hidden" class="total" id="total" name="total[]" placeholder="Total Amount" value="0"/>
                    <span class="input-sm sptotal" id="sptotal" name="sptotal[]">0</span>
                </td>
                <td><center><a class="btn btn-sm btn-danger gradient1 waves-effect waves-light remove" name="remove[]" id="remove"><?php echo $model->id_variant; ?></a></center></td>
                <!--<td><a class="btn btn-warning btn-sm gradient1 remove" name="remove[]" id="remove"><?php // echo $model->id_variant; ?></button></td>-->
            </tr>
    <?php }} ?>
<?php }
    
    public function ajax_get_vendor_has_brands() {
        $idvendor = $this->input->post('idvendor');
        $model_variant = $this->General_model->ajax_get_vendor_has_brand_products($idvendor); ?>
        <hr>
        <div class="col-md-1 text-muted">Model</div>
        <div class="col-md-6">
            <select class="chosen-select form-control" name="idmodelvariant" id="idmodelvariant" required="">
                <option value="">Select Model</option>
                <?php foreach ($model_variant as $variant) { ?>
                    <option value="<?php echo $variant->id_variant; ?>"><?php echo $variant->product_category_name . ' ' . $variant->full_name; ?></option>
                <?php } ?>
            </select>
        </div>
        <script>
            var variants = [];
            $(document).ready(function () {
                $('#idmodelvariant').change(function(){
                    var idmodel = $(this).val();
                    var idwarehouse = $('#idwarehouse').val();
                    if(idwarehouse == ''){
                        swal("😠 Warning!","First Select Warehouse!");
                        return false;
                    }else{
                        if (variants.includes(idmodel) === false){
                            variants.push(idmodel);
                            $.ajax({
                                url: "<?php echo base_url() ?>purchase/get_variant_by_id",
                                method: "POST",
                                data:{id : idmodel, idwarehouse: idwarehouse},
                                success: function (data)
                                {
                                    $('#model_table').show();
                                    $('#selected_model').append(data);
                                    $('#idwarehouse').attr("style", "pointer-events: none;");
                                    $('#vendor_name_block').show();
                                    $('#vendor_name').html($("#idvendor option:selected").text());
                                    $('#idvendor_block').attr("style", "display: none;");
                                }
                            });
                        }else{
                            swal("😠 Warning!", "Duplicate Product selected!");
                            return false;
                        }
                    }
                });
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
                });
            });
        </script>
    <?php    
    }
    
    public function ajax_get_vendor_has_brands_for_direct_inward() {
        $idvendor = $this->input->post('idvendor');
        $model_variant = $this->General_model->ajax_get_vendor_has_brand_products($idvendor); ?>
        <div class="col-md-12">
            <div class="col-md-1 text-muted">Model</div>
            <div class="col-md-8">
                <select class="chosen-select form-control" name="idmodelvariant" id="idmodelvariant" required="">
                    <option value="">Select Model</option>
                    <?php foreach ($model_variant as $variant) { ?>
                        <option value="<?php echo $variant->id_variant; ?>"><?php echo $variant->product_category_name . ' ' . $variant->full_name; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <script>
            var variants = [];
            $(document).ready(function () {
                $('#idmodelvariant').change(function(){
                    var idmodel = $(this).val();
                    var idwarehouse = $('#idwarehouse').val();
                    var idgodown = $('#idgodown').val();
                    var godown_name = $('#godown_name').val();
                    if(idmodel != ''){
                        if (variants.includes(idmodel) === false){
                            variants.push(idmodel);
                            $.ajax({
                                url: "<?php echo base_url() ?>purchase/get_variant_by_id",
                                method: "POST",
                                data:{id : idmodel, idwarehouse: idwarehouse, idgodown: idgodown, godown_name: godown_name},
                                success: function (data)
                                {
                                    $('#model_table').show();
                                    $('#selected_model').append(data);
                                    $('#idwarehouse').attr("style", "pointer-events: none;");
                                }
                            });
                        }else{
                            alert('duplicate product selected');
                            return false;
                        }
                    }
                });
            });
            $(document).on('click', '.remove_btn', function() {
                var parent = $($(this)).closest('td').parent('tr');
                var idvariant = parent.find(".idvariant").val();
                variants = jQuery.grep(variants, function(value) { return value !== idvariant; });
                $(parent).remove();
            });
        </script>
    <?php    
    }
    
    public function save_purchase_inward() {
//      $filename=$_FILES["uploadfile"]["tmp_name"];
//      $filename=$this->input->post('csvfile');
//        die($filename);
//      die($filename);
        $this->db->trans_begin();
        $date = $this->input->post('date');
        $entry_time = date('Y-m-d H:i:s');
        $created_by = $this->input->post('created_by');
        $idvendor = $this->input->post('idvendor');
        $id_po = $this->input->post('id_purchase_order');
        $id_direct_inward = $this->input->post('id_purchase_direct_inward');
        $idbranch = $this->input->post('idbranch');
        $y = date(date('Y', strtotime($date)), mktime(0, 0, 0, 3 + date('m', strtotime($date))));
        $y1 = $y + 1;
        $y2 = substr($y1,-2);
        $financial_year = 'IN/'.$y.'-'.$y2.'/'.$this->input->post('branch_code');
        $data = array(
            'date' => $date,
            'idvendor' => $idvendor,
            'idpurchase_order' => $id_po,
            'idpurchase_direct_inward' => $id_direct_inward,
            'vendor_state' => $this->input->post('state'),
            'supplier_invoice_no' => $this->input->post('supplier_inv'),
            'vendor_invoice_date' => $this->input->post('inv_date'),
            'financial_year' => $financial_year,
            'idbranch' => $idbranch,
            'direct_inward' => $this->input->post('direct_inward'),
            'remark' => $this->input->post('remark'),
            'total_basic_amt' => $this->input->post('total_basic_amt'),
            'total_taxable_amt' => $this->input->post('total_taxable_amt'),
            'total_cgst_amt' => $this->input->post('total_cgst_amt'),
            'total_sgst_amt' => $this->input->post('total_sgst_amt'),
            'total_igst_amt' => $this->input->post('total_igst_amt'),
            'total_tax' => $this->input->post('total_tax'),
            'total_charges_amt' => $this->input->post('total_charges'),
            'total_discount_amt' => $this->input->post('total_discount'),
            'gross_amount' => $this->input->post('gross_total'),
            'overall_discount' => $this->input->post('overall_discount'),
            'final_amount' => $this->input->post('final_total'),
            'tcs_amount' => $this->input->post('tcs_amount'),
            'overall_amount' => $this->input->post('overall_amount'),
            'created_by' => $created_by,
            'entry_time' => $entry_time,
            'status' => 3,
        );
        $idinward = $this->Inward_model->save_inward($data);
        $product_id = explode(",",$this->input->post('modelid'));
//        die('<pre>'.print_r($product_id,1).'</pre>');
//        $nestarray[] = array('nest'=>array());
        $upnestarray[] = array('nested'=>array());
        $imei_history[] = array('nest'=>array());
        for($i = 0; $i < count($product_id); $i++){
            $qty = $this->input->post('qty['.$i.']');
            $scanned_csv = '';
            if($this->input->post('scanned['.$i.']') || $this->input->post('scanned['.$i.']') != ''){
                $scanned_csv = $this->input->post('scanned['.$i.']');
            }else{
                $filename=$_FILES['csvfile']["tmp_name"][$i];
//                $allowed = array('csv');
//                $ext = pathinfo($filename, PATHINFO_EXTENSION);
//                if (!in_array($ext, $allowed)) {
//                    if($id_po == ''){
//                        $idredirect = $id_direct_inward;
//                    }else{
//                        $idredirect = $id_po;
//                    }
                    ?>
                    <script>
//                        if (confirm('You selected wrong file format. Go back and fill form again!!! आपण चुकीचे फाईल Format निवडले आहे. परत जाऊन पुन्हा फॉर्म भरा!!!')){
//                            window.location = "purchase_direct_inward/<?php // echo $idredirect ?>";
//                        }else{
//                            window.location = "purchase_direct_inward/<?php // echo $idredirect ?>";
//                        }
                    </script>
<?php //               die('You selected wrong file format. Go back and fill form again!!!. आपण चुकीचे फाईल Format निवडले आहे. परत जाऊन पुन्हा फॉर्म भरा!!!');
//                    return false;
//                 }
                if($_FILES['csvfile']["size"][$i] > 0){
                    $file = fopen($filename, "r");
                    $cnt=0;
                    while (($openingdata = fgetcsv($file, 10000, ",")) !== FALSE) {
                        if($cnt < $qty){
                            $scanned_csv .= $openingdata[0].',';
                            $cnt++;
                        }
                    }
                }
            }
            
            $inward_data[$i] = array(
                'idinward' => $idinward,
                'idgodown' => $this->input->post('idgodown['.$i.']'),
                'idproductcategory' => $this->input->post('idtype['.$i.']'),
                'idcategory' => $this->input->post('idcategory['.$i.']'),
                'idbrand' => $this->input->post('idbrand['.$i.']'),
                'idvariant' => $product_id[$i],
                'idmodel' => $this->input->post('idmainmodel['.$i.']'),
                'product_name' => $this->input->post('product_name['.$i.']'),
                'qty' => $this->input->post('qty['.$i.']'),
                'price' => $this->input->post('price['.$i.']'),
                'idskutype' => $this->input->post('skutype['.$i.']'),
                'charges_amt' => $this->input->post('chrgs_amt['.$i.']'),
                'discount_per' => $this->input->post('discount_per['.$i.']'),
                'discount_amt' => $this->input->post('discount_amt['.$i.']'),
                'basic' => $this->input->post('basic['.$i.']'),
                'taxable_amt' => $this->input->post('taxable['.$i.']'),
                'cgst_per' => $this->input->post('cgst['.$i.']'),
                'sgst_per' => $this->input->post('sgst['.$i.']'),
                'igst_per' => $this->input->post('igst['.$i.']'),
                'cgst_amt' => $this->input->post('cgst_amt['.$i.']'),
                'sgst_amt' => $this->input->post('sgst_amt['.$i.']'),
                'igst_amt' => $this->input->post('igst_amt['.$i.']'),
                'tax' => $this->input->post('tax['.$i.']'),
                'total_amount' => $this->input->post('total['.$i.']'),
                'imei_srno' => rtrim($scanned_csv,','),
            );
            $idinward_data = $this->Inward_model->save_inward_data($inward_data[$i]);
//            die('<pre>'.print_r($scanned,1).'</pre>');
            
            if($this->input->post('skutype['.$i.']') == 4){
                $inward_product[$i] = array(
                    'date' => $date,
                    'idgodown' => $this->input->post('idgodown['.$i.']'),
                    'idskutype' => $this->input->post('skutype['.$i.']'),
                    'idproductcategory' => $this->input->post('idtype['.$i.']'),
                    'idcategory' => $this->input->post('idcategory['.$i.']'),
                    'idvariant' => $product_id[$i],
                    'idmodel' => $this->input->post('idmainmodel['.$i.']'),
                    'idbrand' => $this->input->post('idbrand['.$i.']'),
                    'created_by' => $created_by,
                    'idvendor' => $idvendor,
                    'qty' => $this->input->post('qty['.$i.']'),
                    'idinward_data' => $idinward_data,
                    'idinward' => $idinward,
                    'product_name' => $this->input->post('product_name['.$i.']'),
                    'price' => $this->input->post('price['.$i.']'),
                    'mrp' => $this->input->post('mrp['.$i.']'),
                    'charges_amt' => $this->input->post('chrgs_amt['.$i.']'),
                    'discount_per' => $this->input->post('discount_per['.$i.']'),
                    'discount_amt' => $this->input->post('discount_amt['.$i.']'),
                    'basic' => $this->input->post('basic['.$i.']'),
                    'taxable_amt' => $this->input->post('taxable['.$i.']'),
                    'cgst_per' => $this->input->post('cgst['.$i.']'),
                    'cgst_amt' => $this->input->post('cgst_amt['.$i.']'),
                    'sgst_per' => $this->input->post('sgst['.$i.']'),
                    'sgst_amt' => $this->input->post('sgst_amt['.$i.']'),
                    'igst_per' => $this->input->post('igst['.$i.']'),
                    'igst_amt' => $this->input->post('igst_amt['.$i.']'),
                    'tax' => $this->input->post('tax['.$i.']'),
                    'total_amount' => $this->input->post('total['.$i.']'),
                );
                $this->Inward_model->save_inward_product($inward_product[$i]);
                $hostock = $this->Transfer_model->get_branchstock_byidmodel_skutype_godown($product_id[$i],4,$idbranch,$this->input->post('idgodown['.$i.']'));
//                $hostock = $this->Inward_model->get_hostock_byidmodel_skutype($product_id[$i], 4, 1);
                if(count($hostock) === 0){
                    if($sale_type[$i] == 0){
                        $inward_stock_sku[$i] = array(
                            'date' => $date,
                            'idgodown' => $this->input->post('idgodown['.$i.']'),
                            'product_name' => $this->input->post('product_name['.$i.']'),
                            'idskutype' => $this->input->post('skutype['.$i.']'),
                            'idproductcategory' => $this->input->post('idtype['.$i.']'),
                            'idcategory' => $this->input->post('idcategory['.$i.']'),
                            'is_gst'   => $this->input->post('gstradio'),
                            'idvariant' => $product_id[$i],
                            'idbranch' => $idbranch,
                            'idmodel' => $this->input->post('idmainmodel['.$i.']'),
                            'idbrand' => $this->input->post('idbrand['.$i.']'),
                            'created_by' => $created_by,
                            'idvendor' => $idvendor,
                            'qty' => $this->input->post('qty['.$i.']'),
                        );
                        $this->Inward_model->save_stock($inward_stock_sku[$i]);
                    }
                }else{
                    foreach ($hostock as $hstock){
                        $qty = $hstock->qty + $this->input->post('qty['.$i.']');
                        $this->Inward_model->update_stock_byid($hstock->id_stock,$qty);
                    }
                }
                $last_purchase_price = array(
                    'last_purchase_price' => $this->input->post('total['.$i.']') / $this->input->post('qty['.$i.']'),
                );
                $this->Inward_model->update_variants_last_purchase_price($product_id[$i], $last_purchase_price);
            }else{
                $scanned = explode(",",$scanned_csv);
                for($j = 0; $j < count($scanned); $j++){
                    if($scanned[$j] != ''){
                        $inward_product[$j] = array(
                            'date' => $date,
                            'imei_no' => rtrim($scanned[$j],','),
                            'idgodown' => $this->input->post('idgodown['.$i.']'),
                            'idskutype' => $this->input->post('skutype['.$i.']'),
                            'idproductcategory' => $this->input->post('idtype['.$i.']'),
                            'idcategory' => $this->input->post('idcategory['.$i.']'),
                            'idvariant' => $product_id[$i],
                            'idmodel' => $this->input->post('idmainmodel['.$i.']'),
                            'idbrand' => $this->input->post('idbrand['.$i.']'),
                            'created_by' => $created_by,
                            'idvendor' => $idvendor,
                            'idinward_data' => $idinward_data,
                            'idinward' => $idinward,
                            'product_name' => $this->input->post('product_name['.$i.']'),
                            'price' => $this->input->post('price['.$i.']'),
                            'charges_amt' => $this->input->post('chrgs_amt['.$i.']') / $qty,
                            'mrp' => $this->input->post('mrp['.$i.']'),
                            'price' => $this->input->post('price['.$i.']'),
                            'charges_amt' => $this->input->post('chrgs_amt['.$i.']') / $qty,
                            'discount_per' => $this->input->post('discount_per['.$i.']'),
                            'discount_amt' => $this->input->post('discount_amt['.$i.']') / $qty,
                            'basic' => $this->input->post('basic['.$i.']') / $qty,
                            'taxable_amt' => $this->input->post('taxable['.$i.']') / $qty,
                            'cgst_per' => $this->input->post('cgst['.$i.']'),
                            'cgst_amt' => $this->input->post('cgst_amt['.$i.']') / $qty,
                            'sgst_per' => $this->input->post('sgst['.$i.']'),
                            'sgst_amt' => $this->input->post('sgst_amt['.$i.']') / $qty,
                            'igst_per' => $this->input->post('igst['.$i.']'),
                            'igst_amt' => $this->input->post('igst_amt['.$i.']') / $qty,
                            'tax' => $this->input->post('tax['.$i.']') / $qty,
                            'total_amount' => $this->input->post('total['.$i.']') / $qty,
                        );
                       $idinward_product= $this->Inward_model->save_inward_product($inward_product[$j]);
                        $this->Inward_model->update_model_variant_mrp($product_id[$i], $this->input->post('mrp['.$i.']'));
                        $inward_stock[$j] = array(
                            'date' => $date,
                            'idgodown' => $this->input->post('idgodown['.$i.']'),
                            'product_name' => $this->input->post('product_name['.$i.']'),
                            'imei_no' => $scanned[$j],
                            'idbranch' => $idbranch,
                            'idskutype' => $this->input->post('skutype['.$i.']'),
                            'is_gst'   => $this->input->post('gstradio'),
                            'idproductcategory' => $this->input->post('idtype['.$i.']'),
                            'idcategory' => $this->input->post('idcategory['.$i.']'),
                            'idvariant' => $product_id[$i],
                            'idmodel' => $this->input->post('idmainmodel['.$i.']'),
                            'idbrand' => $this->input->post('idbrand['.$i.']'),
                            'created_by' => $created_by,
                            'idvendor' => $idvendor,
                            'idinward' => $idinward,
                            'idinward_product' => $idinward_product,
                        );
                        $this->Inward_model->save_stock($inward_stock[$j]);
                        
                        // update_variants_last_purchase_price
                        $last_purchase_price = array(
                            'last_purchase_price' => $this->input->post('total['.$i.']') / $qty,
                        );
                        $this->Inward_model->update_variants_last_purchase_price($product_id[$i], $last_purchase_price);
                        
                        $imei_history['nest'][]=array(
                            'imei_no' => $scanned[$j],
                            'entry_type' => 'Purchase Inward',
                            'entry_time' => $entry_time,
                            'date' => $date,
                            'idbranch' => $idbranch,
                            'idgodown' => $this->input->post('idgodown['.$i.']'),
                            'idvariant' => $product_id[$i],
//                            'model_variant_full_name' => $this->input->post('product_name['.$i.']'),
                            'idimei_details_link' => 1, // Purchase return from imei_details_link table
                            'idlink' => $idinward,
                            'iduser' => $created_by,
//                            'imei_latitude' => $this->input->post('branch_lat'),
//                            'imei_longitude' => $this->input->post('branch_long'),
                        );
                    }
                }
//                }
            }
            if(!$this->input->post('direct_inward')){
                if($this->input->post('id_purchase_order_product['.$i.']')){
                    $upnestarray['nested'][] = array(
                        'id_purchase_order_product' => $this->input->post('id_purchase_order_product['.$i.']'),
                        'inward_qty' => $this->input->post('qty['.$i.']'),
                    );
                }
            }
        }
        if(count($imei_history['nest']) > 0){
            $this->General_model->save_batch_imei_history($imei_history['nest']);
        }
        if($this->input->post('direct_inward')){
            $inwarddata = array(
              'status' => 3, //scanned
            );
            $this->Purchase_model->update_purchase_direct_inward($id_direct_inward,$inwarddata);
        }else{
            if(isset($upnestarray['nested'])){
                $this->Purchase_model->edit_batch_po_product_data($upnestarray['nested']);
            }
            $podata = array(
              'status' => 3, //scanned
            );
            $this->Purchase_model->update_purchase_order($id_po,$podata);
        }
//        die('<pre>'.print_r($nestarray['nest'],1).'<pre>');
//        $this->Inward_model->save_inwardbatch_product($nestarray['nest']);
//        die('hi');
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Product inward is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Product Inwarded');
        }
//        return redirect('purchase/inward_details/'.$idinward);
        return redirect('purchase/purchase_print/'.$idinward);
    }
    
    public function ajax_get_purchase_order_data() {
        $status = $this->input->post('status');
        $datefrom = $this->input->post('datefrom');
        $dateto = $this->input->post('dateto');
        $purchase_order = $this->Purchase_model->ajax_get_purchase_order_data($status, $datefrom, $dateto);
        $i=1; 
        foreach ($purchase_order as $po){ ?>
            <tr>
                <td><?php echo $i ?></td>
                <td><?php echo $po->financial_year.'-'.$po->id_purchase_order ?></td>
                <td><?php echo $po->date ?></td>
                <td><?php echo $po->branch_name ?></td>
                <td><?php echo $po->vendor_name ?></td>
                <td><?php echo $po->vendor_address ?></td>
                <td><?php if($po->status==0){ echo 'Pending'; }else{ echo 'Approved'; } ?></td>
                <td><center><a target="_blank" href="<?php echo base_url('purchase/purchase_order_details/'.$po->id_purchase_order) ?>" class="btn btn-sm btn-warning gradient_info waves-effect waves-light"><i class="fa fa-info fa-lg"></i></a></td>
            </tr>
        <?php $i++; }
    }
    public function csv_verification_get_string(){
        $this->db->trans_begin();
        $imei='';$count=0;$i=0;
        $filename=$_FILES["uploadfile"]["tmp_name"];
        if($_FILES["uploadfile"]["size"] > 0){
            $file = fopen($filename, "r");
            while (($openingdata = fgetcsv($file, 10000, ",")) !== FALSE) {
                if($i > 0){ 
                    $imei .= $openingdata[0].',';
                    $count++;
                }
                $i++;
            }
            fclose($file);
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'failed';
            $q['count'] = $count;
        }else{
            $this->db->trans_commit();
            $q['result'] = 'success';
            $q['count'] = $count;
            $q['imei'] = $imei;
        }
        echo json_encode($q);
    }
    
    public function purchase_report(){
        $q['tab_active'] = 'Report';
        $q['company_data'] = $this->General_model->get_company_data();
        $this->load->view('report/purchase_report',$q);
    }
    public function ajax_get_purchase_inward_data(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idcompany = $this->input->post('idcompany');
        
        $purchase_data = $this->Purchase_model->ajax_get_purchase_data($from, $to, $idcompany);
        
//        die('<pre>'.print_r($purchase_data,1).'</pre>');
        if(count($purchase_data) > 0){
        ?>
            <table class="table table-bordered table-condensed" id="purchasereport">
                <thead style="background-color: #9accfc" class="fixedelementtop">
                    <th>SR.</th>
                    <th>Vendor Invoice No</th>
                    <th>Invoice No</th>
                    <th>Invoice Type</th>
                    <th>Intake Date</th>
                    <th>Vendor Invoice Date</th>
                    <th>Vendor Name</th>
                    <th>Vendor Code</th>
                    <th>Vendor GST No</th>
                    <th>Vendor City</th>
                    <th>Vendor State</th>
                    <th>Intake Location</th>
                    <th>Intake Godown Type</th>
                    <th>Category</th>
                    <th>Brand</th>
                    <th>Product Name</th>
                    <th>Product Code</th>
                    <th>Imei 1</th>
                    <th>Imei 2</th>
                    <th>Serial No</th>
                    <th>GST Rate</th>
                    <th>Base Price</th>
                    <th>Freight Price</th>
                    <th>Trade Discount</th>
                    <th>General Discount</th>
                    <th>Landing Base Price</th>
                    <th>SGST</th>
                    <th>CGST</th>
                    <th>IGST</th>
                    <th>SGST(%)</th>
                    <th>CGST(%)</th>
                    <th>IGST(%)</th>
                    <th>CESS Amount</th>
                    <th>TCS Amount</th>
                    <th>Round off</th>
                    <th>Total Amount Per Qty</th>
                    <th>Additional Discount</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1;$tc=0;$overalldis=0; foreach ($purchase_data as $pur){
                        if($pur->tcs_amount != 0){
                            $t = $pur->total_amount;
                            $pr = (($t*100)/$pur->gross_amount);
                            $tc = ($pur->tcs_amount * $pr)/100;
                        }else{
                            $tc = 0;
                        }
                        
                        if($pur->overall_discount != 0){
                            $tt = $pur->total_amount;
                            $opr = (($tt*100)/$pur->gross_amount);
                            $overalldis = ($pur->overall_discount * $opr)/100;
                        }else{
                            $overalldis = 0;
                        }
                        ?>
                    <tr>
                        <td><?php echo $pur->idinward;
                        //echo $pur->overall_discount; ?></td>
                        <td><?php echo $pur->supplier_invoice_no ?></td>
                        <td><?php echo $pur->financial_year.$pur->idinward ?></td>
                        <td>Purchase</td>
                        <td><?php echo $pur->date ?></td>
                        <td><?php echo $pur->vendor_invoice_date ?></td>
                        <td><?php echo $pur->vendor_name ?></td>
                        <td><?php //echo $pur-> ?></td>
                        <td><?php echo $pur->vendor_gst ?></td>
                        <td><?php echo $pur->city ?></td>
                        <td><?php echo $pur->state ?></td>
                        <td><?php echo $pur->branch_name ?></td>
                        <td><?php echo $pur->godown_name ?></td>
                        <td><?php echo $pur->category_name ?></td>
                        <td><?php echo $pur->brand_name ?></td>
                        <td><?php echo $pur->product_name ?></td>
                        <td><?php echo $pur->hsn ?></td>
                        <td><?php if($pur->idskutype == 1){ echo "'".$pur->imei_no; } ?></td>
                        <td><?php //echo $pur-> ?></td>
                        <td><?php if($pur->idskutype == 2){ echo "'".$pur->imei_no; } ?></td>
                        <td><?php if($pur->igst_amt == 0){ echo $pur->cgst_per + $pur->cgst_per ;  }else{ $pur->igst_per; }   ?></td>
                        <td><?php $price = $pur->price; echo $price; ?></td>
                        <td><?php $freigt = $pur->charges_amt; echo $freigt; ?></td>
                        <td>0</td>
                        <td><?php  $discamt = $pur->discount_amt; echo $discamt; ?></td>
                        <td><?php  echo $pur->taxable_amt ?></td>
                        <td><?php echo number_format($pur->sgst_amt,2) ?></td>
                        <td><?php echo number_format($pur->cgst_amt,2) ?></td>
                        <td><?php echo number_format($pur->igst_amt,2) ?></td>
                        <td><?php echo 'Input SGST '. $pur->sgst_per.'%'?></td>
                        <td><?php echo 'Input CGST '. $pur->cgst_per.'%'?></td>
                        <td><?php  if($pur->cgst_per == 0){ echo 'Input IGST @'. $pur->igst_per.'%'; } else{ echo 'Input IGST @ 0%'; }?></td>
                        <td>0</td>
                        <td><?php echo number_format($tc,2) ?></td>
                        <td></td>
                        <td><?php echo number_format($pur->total_amount ,2)  ?></td>
                        <td><?php echo number_format($overalldis,2)  ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php 
        }else{ ?>
            <script>
                alert('Data Not Found');
            </script>
        <?php }
       
    }
    
    public function tally_purchase_return_report(){
        $q['tab_active'] = 'Tally Report';
        $q['company_data'] = $this->General_model->get_company_data();
        $this->load->view('report/tally_purchase_return_report',$q);
    }
    public function ajax_get_purchase_return_data(){
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $idcompany = $this->input->post('idcompany');
        
        $purchase_return = $this->Purchase_model->ajax_get_purchase_return_data($from, $to, $idcompany);
//        die('<pre>'.print_r($purchase_return,1).'</pre>');
        if(count($purchase_return) > 0){ ?>
            <table class="table table-bordered table-condensed" id="purchase_return_report"> 
                <thead style="background-color: #9accfc" class="fixedelementtop">
                    <th>Sr No.</th>
                    <th>Debit Note Type</th>
                    <th>Debit Note No</th>
                    <th>Date</th>
                    <th>Vendor Name</th>
                    <th>Vendor Code</th>
                    <th>Vendor GST NO</th>
                    <th>Vendor City</th>
                    <th>Vendor State</th>
                    <th>Godown Location</th>
                    <th>Godown Type</th>
                    <th>Brand Name</th>
                    <th>Product Name</th>
                    <th>Product Code</th>
                    <th>Imei 1 No</th>
                    <th>Imei 2 No</th>
                    <th>Serial No</th>
                    <th>Base Price Per Unit</th>
                    <th>Trade Discount</th>
                    <th>Landing Base Price</th>
                    <th>SGST</th>
                    <th>CGST</th>
                    <th>IGST</th>
                    <th>SGST(%)</th>
                    <th>CGST(%)</th>
                    <th>IGST(%)</th>
                    <th>Round Off</th>
                    <th>Total Amount Per Qty</th>
                    <th>Original Invoice Number</th>
                    <th>Original Invoice Date</th>
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; foreach ($purchase_return as $pur){ ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td>Purchase Return</td>
                        <td></td>
                        <td><?php echo $pur->date ?></td>
                        <td><?php echo $pur->vendor_name ?></td>
                        <td></td>
                        <td><?php echo $pur->vendor_gst ?></td>
                        <td><?php echo $pur->city ?></td>
                        <td><?php echo $pur->state ?></td>
                        <td></td>
                        <td><?php echo $pur->godown_name ?></td>
                        <td><?php echo $pur->brand_name ?></td>
                        <td><?php echo $pur->product_name ?></td>
                         <td><?php echo $pur->product_name ?></td>
                        <td><?php if($pur->idskutype == 1){ echo "'".$pur->imei_no; } ?></td>
                        <td></td>
                        <td><?php if($pur->idskutype == 2){ echo "'".$pur->imei_no; } ?></td>
                        <td><?php echo $pur->basic ?></td>
                        <td></td>
                        <td><?php echo number_format($pur->basic,2) ?></td>
                        <td><?php echo number_format($pur->sgst_amt,2) ?></td>
                        <td><?php echo number_format($pur->cgst_amt,2) ?></td>
                        <td><?php echo number_format($pur->igst_amt,2) ?></td>
                        <td><?php echo 'Input SGST '. $pur->sgst_per.'%'?></td>
                        <td><?php echo 'Input CGST '. $pur->cgst_per.'%'?></td>
                        <td><?php  if($pur->cgst_per == 0){ echo 'Input IGST @'. $pur->igst_per.'%'; } else{ echo 'Input IGST @ 0%'; }?></td>
                        <td></td>
                        <td><?php echo number_format($pur->total_amount,2) ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php }else{ ?>
            <script>
                alert("Data Not Found");
            </script>
        <?php }
    }
    public function purchase_price_test() {
//        echo $this->Purchase_model->test_price();
    }
    
    ////////////////////////////// PURCHASE EDIT - BY NIKHIL/////////////////////////
    
    public function purchase_edit(){
        $q['tab_active'] = '';
        $this->load->view('purchase/purchase_edit',$q);
    }
    public function get_inward_data_edit(){
       $q['tab_active'] = '';
       $inward_data = $this->Inward_model->get_inward_data_edit();
       $inward_product = $this->Inward_model->get_inward_product_byid_edit();    
       $vendor_data = $this->General_model->get_active_vendor_data();
       $model_variant = $this->General_model->get_model_variant_data();
       $branch_state = $this->Inward_model->get_branch_vendor_state_data();
       $branch_imei_check = $this->Inward_model->get_branch_imei_data($inward_product);
//       echo '<pre>';
//       print_r($inward_data);die;
       ?>
        <?php
        if(!empty($inward_data)){
        if($branch_imei_check == 1){ ?>   
        <div style="font-family: K2D; font-size: 15px;">
                <form id="inward_edit" method="POST" action="<?php echo base_url('Purchase/save_inward_edit') ?>">
                    <?php foreach($inward_data as $inward){ ?>
                    <div class="col-md-8">
                        <div class="p-1">
                            <input type="hidden" id="idinward"  name="idinward" value="<?php echo $inward->id_inward; ?>" />
                            <span class="col-md-3 text-muted" style="font-size: 14px;">Vendor : </span>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <?php $now = date('Y-m-d'); ?>
                                    <input type="hidden" name="date" value="<?php echo $now ?>" />
                                    <input type="hidden" name="idbranch" value="<?php echo $inward->idbranch; ?>" />
                                     <input type="hidden" class="input-sm" name="created_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
                                   <select name="idvendor" id="idvendor" style="width: 100%" class="form-control input-sm chosen-select">
                                        <option value="">Select Vendor</option>
                                        <option value="<?php echo $inward->id_vendor; ?>" selected="" ><?php echo $inward->vendor_name; ?></option>
                                        <?php foreach ($vendor_data as $vendor) { ?>
                                        <option value="<?php echo $vendor->id_vendor; ?>" ><?php echo $vendor->vendor_name; ?></option>
                                        <?php } ?>
                                    </select> 
                                     <input type="hidden" id="state_ven" class="state_ven" name="state_ven"  value="<?php echo $inward->state; ?>" />
                                </div>
                            </div>
                           
                            <span class="col-md-3 text-muted" style="font-size: 14px;">Vendor Invoice : </span>
                            <div class="col-md-9" style="width: 416px;">
                            <div class="input-group">
                                    <div class="input-group-btn">
                                        <input type="text" class="form-control input-sm" id="invoice_id" name="invoice_id" placeholder="Invoice ID" value="<?php echo $inward->supplier_invoice_no ?>">
                                        <input type="hidden" value="<?php echo $inward->idbranch; ?>" id="brnachid">
                                    </div>
                            </div>
                            </div>
                            
                            <span class="col-md-3 text-muted" style="font-size: 14px;">Vendor Invoice Date : </span>
                            <div class="col-md-9" style="width: 416px;">
                            <div class="input-group">
                                    <div class="input-group-btn">
                                        <input type="text" class="form-control input-sm" id="invoice_date" name="invoice_date" placeholder="vendor invoice date" data-provide="datepicker" value="<?php echo date('Y-m-d', strtotime($inward->vendor_invoice_date)); ?>">
                                    </div>
                            </div>
                            </div>
                            <?php if($branch_state->vendor_state == $branch_state->branch_state_name){ ?>
                                <input type="hidden" id="state" value="0" />
                            <?php }else{ ?>
                                <input type="hidden" id="state" value="1" />
                            <?php } ?>
                            <div class="gst-block">
                            <span class="col-md-3 text-muted" style="font-size: 14px;">Invoice Type : </span>
                            <div class="col-md-9">
                            <div class="col-md-6">
                                <input class="form-check-input gstradio"  type="radio" name="gstradio" id="gst" value="1" <?php if($inward->total_cgst_amt !=0 || $inward->total_sgst_amt !=0 || $inward->total_igst_amt != 0){echo "checked";}else echo "checked"; ?> >
                                <label class="form-check-label" for="gst">GST Invoice</label>
                            </div>
                            <div class="col-md-6">
                                <input class="form-check-input gstradio" type="radio" name="gstradio" id="nongst" value="0" <?php if($inward->total_cgst_amt ==0 && $inward->total_sgst_amt ==0 && $inward->total_igst_amt == 0){echo "checked";} ?> >
                                <label class="form-check-label" for="nongst">Non GST Invoice</label>
                            </div>
                            </div>
                            </div>
                            <div class="col-md-6  gst-text-block"></div>
                            </div><div class="clearfix"></div>
                            <hr>
                    </div>
                    <div class="col-md-4">
                            
                    </div>
                    <?php } ?>
                    <div class="clearfix"></div>
                    <table id="model_data" class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 14px;">
                    <thead class="bg-info">
                    <th>Product</th>
                    <th>IMEI</th>
                    <th>Qty</th>
                    <th>MRP</th>
                    <th>Rate</th>
                    <th>Basic</th>
                    <th>
                        <span class="col-md-8" style="padding: 0">Item Discount</span>
                        <span class="col-md-4" style="padding: 0">
                            <div class="material-switch" style="margin-top: -5px;">
                                <input id="discount_switch" name="discount_switch" type="checkbox" checked="" /> 
                                <label for="discount_switch" class="label-primary"></label> 
                            </div>
                        </span>
                    </th>
                    <th>Taxable</th>
                    <th>Total Amount</th>
                </thead>
            <tbody>
                <?php 
                $cnt = 1;
                $idmodels = '';
                foreach ($inward_product as $product){ 
                 $inward_product_data = $this->Inward_model->get_inward_product_byid_edit_data($product->idinward,$product->idvariant);   
                 ?>
                <tr class="product_data" id="m<?php echo $product->id_variant?>">
                    <td class="col-md-1">
                        <?php echo $product->product_name ?>
                    </td>
                    <td>
                       <?php if($product->idsku_type != 4){ ?>
                       <?php  
                       $imei_str = $product->imei_srno;
                       $imei_arry  = explode(",",$imei_str);
                       foreach($imei_arry as $imel){
                       ?>
                        <input type="text" value="<?php echo $imel?>" name="<?php echo 'imei_model_'.$cnt.'[]'; ?>" > <br>  
                       <?php } ?> 
                        <?php foreach($inward_product_data as $inward_pr){ ?>
                            <input type="hidden"  name="<?php echo 'idinward_product_old'.$cnt.'[]' ?>" value="<?php echo $inward_pr->id_inward_product; ?>" />
                            <input type="hidden"  name="<?php echo 'idinwardproduct_emi'.$cnt.'[]' ?>" value="<?php echo $inward_pr->imei_no; ?>" />
                        <?php } ?>
                        <?php }else{ ?>
                            <input type="hidden" value="" name="<?php echo 'imei_model_'.$cnt.'[]'; ?>" >
                        <?php } ?>
                    </td>
                    <td class="col-md-1">
                        
                        <input type="hidden" id="idinward" class="idinward" name="idinward" value="<?php echo $product->idinward; ?>" />
                        <input type="hidden" id="idgodown" class="idgodown" name="idgodown[]" value="<?php echo $product->idgodown ?>" />
                        <input type="hidden" id="idtype" class="idtype" name="idtype[]" value="<?php echo $product->idproductcategory ?>" />
                        <input type="hidden" id="idcategory" class="idcategory" name="idcategory[]" value="<?php echo $product->idcategory ?>" />
                        <input type="hidden" id="idbrand" class="idbrand" name="idbrand[]" value="<?php echo $product->idbrand ?>" />
                        <input type="hidden" id="idmainmodel" class="idmainmodel" name="idmainmodel[]" value="<?php echo $product->idmodel ?>" />
                        <input type="hidden" id="idmodel" class="idmodel" name="idmodel[]" value="<?php echo $product->id_variant ?>" />
                        <input type="hidden" id="skutype" class="skutype" name="skutype[]" value="<?php echo $product->idsku_type ?>" />                      
                        <input type="hidden" id="product_name" class="product_name" name="product_name[]" value="<?php echo $product->full_name; ?>" />
                        <input type="hidden" id="sale_type" class="sale_type" name="sale_type[]" value="<?php echo $product->sale_type; ?>" />
                        <?php if($product->idsku_type == 4){ ?>
                        <input type="number" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" min="1" value="<?php echo $product->qty; ?>" style="width: 80px" /> 
                       <?php }else{ ?>
                        <input type="number" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" readonly required="" min="1" value="<?php echo $product->qty; ?>" style="width: 80px" />     
                       <?php } ?>
                       <input type="hidden" id="old_qty" name="old_qty[]"  value="<?php echo $product->qty; ?>" />      
                    </td>
                    <td class="col-md-1">
                        <input type="text" id="mrp" name="mrp[]" class="form-control input-sm mrp" required="" placeholder="MRP" min="1" value="<?php echo $product->mrp ?>" style="width: 80px"/>
                    </td>
                    <td class="col-md-1">
                        <input type="text" id="price" name="price[]" class="form-control input-sm price" required="" placeholder="Price" value="<?php echo $product->price ?>" min="1" style="width: 80px"/>
                    </td>
                    <td class="col-md-1">
                        <input type="hidden" id="basic" name="basic[]" class="basic" placeholder="Basic" readonly="" value="<?php echo $product->basic; ?>" min="0"/>
                        <span class="input-sm spbasic" id="spbasic" name="spbasic[]"><?php echo $product->basic; ?></span>
                        <input type="hidden" id="basic_percent" name="basic_percent[]" class="basic_percent"/>
                        <input type="hidden" id="chrgs_amt" name="chrgs_amt[]" class="chrgs_amt input-sm" readonly="" placeholder="Amount" value="0"/>
                        <span class="input-sm spchrgs_amt hidden" id="spchrgs_amt" name="spchrgs_amt[]">0</span>
                    </td>
                    <td class="col-md-1">
                        <input type="hidden" id="discount_per" name="discount_per[]" class="discount_per input-sm" placeholder="Percentage" value="<?php echo $product->discount_amt; ?>" />
                        <input type="text" id="discount_amt" name="discount_amt[]" class="form-control discount_amt input-sm" placeholder="Amount" value="<?php echo $product->discount_amt; ?>" readonly="" style="width: 80px" />
                    </td>
                    <td>
                        <input type="hidden" id="taxable" name="taxable[]" class="taxable" placeholder="Taxable" readonly="" value="<?php echo $product->taxable_amt; ?>"/>
                        <span class="input-sm sptaxable" id="sptaxable" name="sptaxable[]"><?php echo $product->taxable_amt; ?></span>
                        <input type="hidden" id="hide_cgst" value="<?php echo $product->cgst; ?>">
                        <input type="hidden" id="hide_sgst" value="<?php echo $product->sgst; ?>">
                        <input type="hidden" id="hide_igst" value="<?php echo $product->igst; ?>">
                        <?php if($inward_data[0]->total_cgst_amt == 0 && $inward_data[0]->total_sgst_amt == 0 && $inward_data[0]->total_igst_amt == 0){ ?>
                        <div class="gst_cgst">
                            <input type="hidden" id="cgst" name="cgst[]" class="input-sm cgst" value="0" readonly=""/>
                            <input type="hidden" id="cgst_amt" name="cgst_amt[]" class="input-sm cgst_amt" value="0" placeholder="CGST 0%" readonly=""/>
                            <span class="input-sm spcgst_amt hidden" id="spcgst_amt" name="spcgst_amt[]">0%</span>
                        </div>
                        <div class="gst_sgst">
                            <input type="hidden" id="sgst" name="sgst[]" class="input-sm sgst" value="0" readonly=""/>
                            <input type="hidden" id="sgst_amt" name="sgst_amt[]" class="input-sm sgst_amt" value="0" placeholder="SGST 0%" readonly=""/>
                            <span class="input-sm spsgst_amt hidden" id="spsgst_amt" name="spsgst_amt[]">0%</span>
                        </div>
                        <div class="gst_igst">
                            <input type="hidden" id="igst" name="igst[]" class="input-sm igst" value="0" readonly=""/>
                            <input type="hidden" id="igst_amt" name="igst_amt[]" class="input-sm igst_amt" value="0" placeholder="IGST 0%" readonly=""/>
                            <span class="input-sm spigst_amt hidden" id="spigst_amt" name="spigst_amt[]">0%</span>
                            <input type="hidden" class="input-sm tax" id="tax" name="tax[]" value="0" placeholder="Tax" readonly=""/>
                        </div>
                        <?php }else{  ?>
                             <div class="gst_cgst">
                            <input type="hidden" id="cgst" name="cgst[]" class="input-sm cgst" value="<?php echo $product->cgst; ?>" readonly=""/>
                            <input type="hidden" id="cgst_amt" name="cgst_amt[]" class="input-sm cgst_amt" value="0" placeholder="CGST <?php echo $product->cgst; ?>%" readonly=""/>
                            <span class="input-sm spcgst_amt hidden" id="spcgst_amt" name="spcgst_amt[]"><?php echo $product->cgst; ?>%</span>
                        </div>
                        <div class="gst_sgst">
                            <input type="hidden" id="sgst" name="sgst[]" class="input-sm sgst" value="<?php echo $product->sgst; ?>" readonly=""/>
                            <input type="hidden" id="sgst_amt" name="sgst_amt[]" class="input-sm sgst_amt" value="0" placeholder="SGST <?php echo $product->sgst; ?>%" readonly=""/>
                            <span class="input-sm spsgst_amt hidden" id="spsgst_amt" name="spsgst_amt[]"><?php echo $product->sgst; ?>%</span>
                        </div>
                        <div class="gst_igst">
                            <input type="hidden" id="igst" name="igst[]" class="input-sm igst" value="<?php echo $product->igst; ?>" readonly=""/>
                            <input type="hidden" id="igst_amt" name="igst_amt[]" class="input-sm igst_amt" value="0" placeholder="IGST <?php echo $product->igst; ?>%" readonly=""/>
                            <span class="input-sm spigst_amt hidden" id="spigst_amt" name="spigst_amt[]"><?php echo $product->igst; ?>%</span>
                            <input type="hidden" class="input-sm tax" id="tax" name="tax[]" value="0" placeholder="Tax" readonly=""/>
                        </div>
                        <?php } ?>
                    </td>
                    <td>
                        <input type="hidden" class="total" id="total" name="total[]" placeholder="Total Amount" value="<?php echo $product->total_amount; ?>"/>
                        <span class="input-sm sptotal" id="sptotal" name="sptotal[]"><?php echo $product->total_amount; ?></span>
                    </td>
                </tr>
                <?php $cnt++; 
                $idmodels .= $product->id_variant.','; 
                } ?>
            </tbody>
        </table>
        <div class="col-md-5 col-md-offset-6">
            <div class="thumbnail">
                <table class="table table-success table-striped">
                    <tbody>
                        <tr>
                            <td>Total Basic</td>
                            <td>
                                <input type="hidden" id="total_basic_amt" name="total_basic_amt"  value="<?php echo $inward_data[0]->total_basic_amt; ?>"/>
                                &nbsp; <span id="total_basic_amt_label"><?php echo $inward_data[0]->total_basic_amt; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Freight/Other Charges</td>
                            <td>
                                <input type="text" class="form-control input-sm" id="total_charges" name="total_charges" value="<?php echo $inward_data[0]->total_charges_amt; ?>" style="width: 200px" required="" />
                            </td>
                        </tr>
                        <tr>
                            <td>Total Discount</td>
                            <td>
                                <input class="form-control input-sm" type="text" id="total_discount" name="total_discount" value="<?php echo $inward_data[0]->total_discount_amt; ?>" style="width: 200px" required="" />
                            </td>
                        </tr>
                        <tr>
                            <td>Total Taxable Amount</td>
                            <td>
                                <input type="hidden" class="total_taxable_amt" name="total_taxable_amt" id="total_taxable_amt" value="<?php echo $inward_data[0]->total_taxable_amt; ?>"/>
                                &nbsp; <span id="total_taxable_amt_label"><?php echo $inward_data[0]->total_taxable_amt; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Total CGST</td>
                            <td>
                                <input type="hidden" name="total_cgst_amt" id="total_cgst_amt" value="<?php echo $inward_data[0]->total_cgst_amt; ?>"/>
                                &nbsp; <span id="total_cgst_amt_label"><?php echo $inward_data[0]->total_cgst_amt; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Total SGST</td>
                            <td>
                                <input type="hidden" name="total_sgst_amt" id="total_sgst_amt" value="<?php echo $inward_data[0]->total_sgst_amt; ?>"/>
                                &nbsp; <span id="total_sgst_amt_label"><?php echo $inward_data[0]->total_sgst_amt; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Total IGST</td>
                            <td>
                                <input type="hidden" name="total_igst_amt" id="total_igst_amt" value="<?php echo $inward_data[0]->total_igst_amt; ?>"/>
                                &nbsp; <span id="total_igst_amt_label"><?php echo $inward_data[0]->total_igst_amt; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Total Tax</td>
                            <td>
                                <input type="hidden" id="total_tax" name="total_tax" value="<?php echo $inward_data[0]->total_tax; ?>"/>
                                &nbsp; <span id="total_tax_label"><?php echo $inward_data[0]->total_tax; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Total Amount</td>
                            <td>
                                <input type="hidden" name="gross_total" id="gross_total" class="grand_total" value="<?php echo $inward_data[0]->gross_amount; ?>" />
                                &nbsp; <span id="gross_total_label"><?php echo $inward_data[0]->gross_amount; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>Discount - After GST</td>
                            <td>
                                <input type="text" class="form-control input-sm" name="overall_discount" id="overall_discount" placeholder="Overall Discount in rupees" value="<?php echo $inward_data[0]->overall_discount; ?>" style="width: 200px" required=""/>
                            </td>
                        </tr>
                        <tr>
                            <td>Gross Amount</td>
                            <td>
                                <input type="hidden" name="final_total_test" id="final_total_test" value="<?php echo $inward_data[0]->final_amount; ?>" />
                                <input type="hidden" name="final_total" id="final_total" class="final_total" value="<?php echo $inward_data[0]->final_amount; ?>" />
                                &nbsp; <span id="final_total_label"><?php echo $inward_data[0]->final_amount; ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td>TCS Amount</td>
                            <td>
                                <input type="number" class="form-control input-sm" name="tcs_amount" id="tcs_amount" placeholder="Add TCS Amount" value="<?php echo $inward_data[0]->tcs_amount; ?>" style="width: 200px" required=""/>
                            </td>
                        </tr>
                        <tr>
                            <td>Overall Total</td>
                            <td>
                                <input type="hidden" name="overall_amount" id="overall_amount" class="overall_amount" value="<?php echo $inward_data[0]->overall_amount; ?>" />
                                &nbsp; <span id="overall_amount_label"><?php echo $inward_data[0]->overall_amount; ?></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div><div class="clearfix"></div>
        <input type="hidden" id="modelid" name="modelid" value="<?php echo rtrim($idmodels,','); ?>" />
        <div class="pull-right col-md-6">
            <div class="col-md-2">Remark</div>
            <div class="col-md-8">
                <input type="text" class="form-control input-sm" id="remark_inw" value="<?php echo $inward_data[0]->remark; ?>" name="remark" placeholder="Enter Remark" required="">
                <input type="hidden" name="direct_inward" value="0">
            </div>
            <div class="col-md-2">
                <input type="button"  class="btn btn-primary gradient2 waves-effect waves-light btn-sub" onclick="submit_inward_edit()" value="Submit">
            </div>
        </div>
        </form>
    </div>
    <?php }else{ ?>
            <h4 style="color: #cc0033"><i class="mdi mdi-alert"></i> You Can't Edit Inward...</h4>
    <?php } 
        }else{   
    ?>
            <h4 style="color: #cc0033"><i class="mdi mdi-alert"></i> Data Not Found...</h4>
    <?php } ?>        
    <?php   
    }    
    public function get_vendor_state_byid(){
        //print_r($_POST);die;
        $branch_vendor_id = $this->Inward_model->ajax_get_branch_vendor_id();
        echo $branch_vendor_id;
    }    
    public function save_inward_edit(){
//        echo '<pre>';
//        print_r($_POST);die;
       $branch_vendor_id = $this->Inward_model->save_inward_edit(); 
    }
}