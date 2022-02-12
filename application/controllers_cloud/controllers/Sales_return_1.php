<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales_return extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Sale_model');
        $this->load->model('Sales_return_model');
    }

    public function cash_return()
    {
        $q['tab_active'] = 'Sale';
        $this->load->view('sales_return/cash_return', $q);
    }
    public function replace_return()
    {
        $q['tab_active'] = 'Sale';
        $this->load->view('sales_return/sales_return_replace_product', $q);
    }
    public function report()
    {
        $q['tab_active'] = 'Sale';
        $q['sales_return'] = $this->Sales_return_model->get_sales_return();
        $this->load->view('sales_return/sales_return_report', $q);
    }
    public function sales_return_details($id){
        $q['tab_active'] = 'Reports';
        $q['sale_data'] = $this->Sales_return_model->get_sales_return_byid($id);
        $q['sale_product'] = $this->Sales_return_model->get_sales_return_product_byid($id);
        $q['sale_payment'] = $this->Sales_return_model->get_sales_return_payment_byid($id);
        $this->load->view('sales_return/sales_return_details', $q);
    }
    public function sales_return_product()
    {
        $q['tab_active'] = 'Sale';
        $q['type_data'] = $this->General_model->get_type_data();
        $q['category_data'] = $this->General_model->get_category_all_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();
        $q['branch_data'] = $this->General_model->get_branch_data();
        $this->load->view('sale/sales_return_product', $q);
    }
    
    public function replace_sales_return_form()
    {
        $idbranch = $_SESSION['idbranch'];
        $idmodel = $this->input->post('idmodel');
        $q['invoice_no'] = $this->Stock_model->get_invoice_no_by_branch($idbranch);
        $q['state_data'] = $this->General_model->get_state_data();
        $q['active_suppliers'] = $this->General_model->get_active_suppliers();
        $q['active_godown'] = $this->General_model->get_active_godown();
//        $q['model_data'] = $this->General_model->get_model_all_data();
        $q['model_data'] = $this->General_model->get_model_by_id($idmodel);
        $q['active_users_byrole'] = $this->General_model->get_active_users_byrole_branch(4, $idbranch);
        $q['payment_head'] = $this->Stock_model->get_payment_head();
        $q['payment_mode'] = $this->Stock_model->get_payment_mode();
        $this->load->view('sale/replace_sales_return_form', $q);
    }
    
    public function search_sales_cash_return_invoice_byinvno() {
        $invno = $this->input->post('invno');
        $branch = $this->input->post('branch');
        $level = $this->input->post('level');
        $sale_data = $this->Sale_model->get_sale_byinvno($invno, $branch, $level);
        $sale_product = $this->Sale_model->get_sale_product_byinvno($invno, $branch, $level);
        if(count($sale_data) > 0){
        foreach ($sale_data as $sale){ ?>
            <div class="col-md-3 pull-right">
                <span class="text-muted">Sale Id:</span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?php echo $sale->id_sale ?>
            </div><div class="clearfix"></div>
            <div class="col-md-3 pull-right">
                <span class="text-muted">Entry time:</span> &nbsp; &nbsp; <?php echo date('d/m/Y h:i a', strtotime($sale->entry_time)) ?>
            </div><div class="clearfix"></div>
            <div class="col-md-3 pull-right">
                <span class="text-muted">Invoice No:</span> &nbsp; &nbsp; &nbsp;<?php echo $sale->inv_no ?>
                <input type="hidden" name="inv_date" value="<?php echo $sale->date ?>" />
                <input type="hidden" name="inv_no" value="<?php echo $sale->inv_no ?>" />
                <input type="hidden" name="branch" value="<?php echo $branch ?>" />
                <input type="hidden" name="idcustomer" value="<?php echo $sale->idcustomer ?>" />
                <input type="hidden" name="fcustomer" value="<?php echo $sale->customer_fname ?>" />
                <input type="hidden" name="lcustomer" value="<?php echo $sale->customer_lname ?>" />
                <input type="hidden" name="gst_no" value="<?php echo $sale->customer_gst ?>" />
                <input type="hidden" name="mobile" value="<?php echo $sale->customer_contact ?>" />
                <input type="hidden" name="idsalesperson" value="<?php echo $sale->idsalesperson ?>" />
                <input type="hidden" name="created_by" value="<?php echo $sale->created_by ?>" />
                <input type="hidden" name="id_sale" value="<?php echo $sale->id_sale ?>" />
            </div><div class="clearfix"></div>To,
            <div class="col-md-3 pull-right">
                <span class="text-muted">Invoice Date:</span> &nbsp; <?php echo $sale->date ?>
            </div><div class="clearfix"></div>
            <div class="col-md-3">
                <span class="text-muted">Customer</span>: <?php echo $sale->customer_fname.' '.$sale->customer_lname ?>
            </div><div class="clearfix"></div>
            <div class="col-md-3">
                <span class="text-muted">Contact</span>: <?php echo $sale->customer_contact ?>
            </div><div class="clearfix"></div>
            <?php if($sale->customer_gst !=''){ ?>
            <div class="col-md-2">
                <span class="text-muted">GST</span>: <?php echo $sale->customer_gst ?>
            </div><div class="clearfix"></div>
            <?php } ?>
            <div class="col-md-7">
                <span class="text-muted">Promoter</span>: <?php echo $sale->user_name ?>
            </div><div class="clearfix"></div>
            <div class="thumbnail" style="overflow: auto;padding: 0">
                <table id="model_data" class="table table-bordered table-condensed table-responsive table-hover" style="font-size: 13px; margin-bottom: 0;">
                    <thead class="bg-info">
                        <th class="col-md-4">Product</th>
                        <th>SKU</th>
                        <th>Total Qty</th>
                        <th>Return Qty</th>
                        <th>Avail Qty</th>
                        <th>Rate</th>
                        <th>Basic</th>
                        <th>Dis </th>
                        <th>Amount</th>
                        <th class="col-md-1">IMEI/SRNO</th>
                        <th>Select</th>
                    </thead>
                    <tbody>
                        <?php $taxable_total=0; $cgstamt_total=0; $igstamt_total=0; $cgstamt=0; $igstamt=0; $tax=0; $totalqty=0; $total_sale_return_qty=0;$total_avail_qty=0;$avail_qty=0;
                        foreach ($sale_product as $product) {
                            $id_saleproduct = $product->id_saleproduct;
                            if($sale->gst_type){
                                // igst
                                $cal = ($product->igst_per + 100) / 100;
                                $taxable = $product->total_amount / $cal;
                                $taxable_total += $taxable;
                                $igstamt = $product->total_amount - $taxable;
                                $igstamt_total += $igstamt;
                            }else{
                                $cal = ($product->cgst_per + $product->sgst_per + 100) / 100;
                                $taxable = $product->total_amount / $cal;
                                $taxable_total += $taxable;
                                $cgst = $product->total_amount - $taxable;
                                $cgstamt = $cgst / 2;
                                $cgstamt_total += $cgstamt;
                            }
                            $tax = $cgstamt + $cgstamt + $igstamt; 
                            $avail_qty = $product->qty - $product->sale_return_qty;
                            ?>
                        <?php if($avail_qty > 0){ ?>
                        <tr>
                            <td>
                                <?php echo $product->product_name; ?>
                                <input type="hidden" class="saleproduct_name" value="<?php echo $product->product_name; ?>" />
                                <input type="hidden" id="idmodel" class="idmodel" name="idmodel<?php echo $id_saleproduct ?>"  value="<?php echo $product->idmodel ?>" />
                                <input type="hidden" class="saleproduct_id" name="id_saleproduct<?php echo $id_saleproduct ?>" value="<?php echo $id_saleproduct ?>" />
                                <input type="hidden" name="product_name<?php echo $id_saleproduct ?>" value="<?php echo $product->product_name ?>" />
                                <input type="hidden" name="idtype<?php echo $id_saleproduct ?>" value="<?php echo $product->idproductcategory ?>" />
                                <input type="hidden" name="idcategory<?php echo $id_saleproduct ?>" value="<?php echo $product->idcategory ?>" />
                                <input type="hidden" name="idbrand<?php echo $id_saleproduct ?>" value="<?php echo $product->idbrand ?>" />
                                <input type="hidden" name="is_gst<?php echo $id_saleproduct ?>" value="<?php echo $product->is_gst ?>" />
                                <input type="hidden" name="idvendor<?php echo $id_saleproduct ?>" value="<?php echo $product->idvendor ?>" />
                                <input type="hidden" name="idgodown<?php echo $id_saleproduct ?>" value="<?php echo $product->idgodown ?>" />
                                <input type="hidden" name="idvariant<?php echo $id_saleproduct ?>" value="<?php echo $product->idvariant ?>" />
                            </td>
                            <td>
                                <?php echo $product->sku_type ?>
                                <input type="hidden" class="skutype" name="skutype<?php echo $id_saleproduct ?>" value="<?php echo $product->idskutype ?>" />
                            </td>
                            <td>
                                <?php echo $product->qty; 
                                $totalqty = $product->qty + $totalqty; ?>
                                <input type="hidden" name="qty<?php echo $id_saleproduct ?>" class="qty" id="qty" value="<?php echo $product->qty ?>" />
                            </td>
                            <td style="color: #ff3333">
                                <?php echo $product->sale_return_qty; 
                                $total_sale_return_qty += $product->sale_return_qty; ?>
                                <input type="hidden" name="sale_return_qty<?php echo $id_saleproduct ?>" class="sale_return_qty" id="sale_return_qty" value="<?php echo $product->sale_return_qty ?>" />
                            </td>
                            <td>
                                <?php echo $avail_qty;
                                $total_avail_qty += $avail_qty; ?>
                                <input type="hidden" name="avail_qty<?php echo $id_saleproduct ?>" class="avail_qty" id="avail_qty" value="<?php echo $avail_qty ?>" />
                            </td>
                            <td>
                                <?php echo $product->price ?>
                                <input type="hidden" class="price" name="price<?php echo $id_saleproduct ?>" value="<?php echo $product->price ?>" />
                            </td>
                            <td>
                                <?php echo $product->basic ?>
                                <input type="hidden" name="basic<?php echo $id_saleproduct ?>" value="<?php echo $product->basic ?>" />
                            </td>
                            <td>
                                <?php echo $product->discount_amt ?>
                                <input type="hidden" name="discount_amt<?php echo $id_saleproduct ?>" value="<?php echo $product->discount_amt ?>" />
                                <input type="hidden" name="taxable<?php echo $id_saleproduct ?>" value="<?php echo $taxable ?>" />
                                <input type="hidden" name="cgst_amt<?php echo $id_saleproduct ?>" value="<?php echo $cgstamt ?>" />
                                <input type="hidden" name="cgst<?php echo $id_saleproduct ?>" value="<?php echo $product->cgst_per ?>" />
                                <input type="hidden" name="sgst_amt<?php echo $id_saleproduct ?>" value="<?php echo $cgstamt ?>" />
                                <input type="hidden" name="sgst<?php echo $id_saleproduct ?>" value="<?php echo $product->sgst_per ?>" />
                                <input type="hidden" name="igst_amt<?php echo $id_saleproduct ?>" value="<?php echo $igstamt ?>" />
                                <input type="hidden" name="igst<?php echo $id_saleproduct ?>" value="<?php echo $product->igst_per ?>" />
                                <input type="hidden" name="tax<?php echo $id_saleproduct ?>" value="<?php echo $tax ?>" />
                                <!--<input type="hidden" name="row_cash_amount<?php // echo $id_saleproduct ?>" class="row_cash_amount" value="<?php // echo $product->total_amount ?>" />-->
                            </td>
                            <td>
                                <?php echo $product->total_amount ?>
                                <input type="hidden" name="total_amt<?php echo $id_saleproduct ?>" value="<?php echo $product->total_amount ?>" />
                            </td>
                            <td>
                                <?php if($product->idskutype != 4){ ?>
                                <?php echo $product->imei_no ?>
                                <input type="hidden" name="imei_no<?php echo $id_saleproduct ?>" value="<?php echo $product->imei_no ?>" />
                                <?php if($avail_qty > 0){ ?>
                                    <input type="hidden" name="selected_qty<?php echo $id_saleproduct ?>" value="1" class="selected_qty" id="selected_qty" />
                                <?php }}else{ ?>
                                    <?php if($avail_qty > 0){ ?>
                                    <input type="number" name="selected_qty<?php echo $id_saleproduct ?>" class="form-control input-sm selected_qty" id="selected_qty" readonly="" placeholder="Qty" max="<?php echo $avail_qty ?>" value="0" />
                                <?php }} ?>
                            </td>
                            <td>
                                <?php if($avail_qty > 0){ ?>
                                <input id="chk_return" type="checkbox" class="chk_return" name="chk_return[]" value="<?php echo $id_saleproduct ?>" />
                                <?php } ?>
                            </td>
                        </tr>
                        <?php }else{ ?>
                        <tr class="text-muted">
                            <td><?php echo $product->product_name; ?></td>
                            <td><?php echo $product->sku_type ?></td>
                            <td><?php echo $product->qty; 
                                $totalqty = $product->qty + $totalqty; ?>
                            </td>
                            <td style="color: #ff3333">
                                <?php echo $product->sale_return_qty; 
                                $total_sale_return_qty += $product->sale_return_qty; ?>
                            </td>
                            <td><?php echo $avail_qty;
                                $total_avail_qty += $avail_qty; ?>
                            </td>
                            <td><?php echo $product->price ?></td>
                            <td><?php echo $product->basic ?></td>
                            <td><?php echo $product->discount_amt ?></td>
                            <td><?php echo $product->total_amount ?></td>
                            <td><?php echo $product->imei_no ?></td>
                            <td>Returned</td>
                        </tr>
                        <?php }} ?>
                        <tr class="bg-info">
                            <td colspan="5"></td>
                            <td>Total</td>
                            <td>
                                <?php echo $sale->basic_total ?>
                                <input type="hidden" name="gross_total" value="<?php echo $sale->basic_total ?>" />
                            </td>
                            <td>
                                <?php echo $sale->discount_total ?>
                                <input type="hidden" name="final_discount" value="<?php echo $sale->discount_total ?>" />
                                <input type="hidden" name="taxable_total" value="<?php echo $taxable_total ?>" />
                                <input type="hidden" name="cgstamt_total" value="<?php echo $cgstamt_total ?>" />
                                <input type="hidden" name="sgstamt_total" value="<?php echo $cgstamt_total ?>" />
                                <input type="hidden" name="igstamt_total" value="<?php echo $igstamt_total ?>" />
                            </td>
                            <td>
                                <?php echo $sale->final_total ?>
                                <input type="hidden" name="final_total" value="<?php echo $sale->final_total ?>" />
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div><div class="clearfix"></div>
            <?php if($sale->idbranch == $_SESSION['idbranch']){ if($total_avail_qty != 0){ ?>
            <!--<input type="text" id="total_qty" value="<?php // echo $totalqty ?>" />-->
            <textarea class="txt_selected_sale_products" id="txt_selected_sale_products" name="txt_selected_sale_products" style="display: none"></textarea>
            <input type="hidden" id="sales_return_type" value="<?php echo $sale->sales_return_type ?>" />
            <div class="thumbnail">
                <center><h4><i class="mdi mdi-clipboard-text"></i> Product Return Form</h4></center><hr>
                <div class="col-md-3">Return Approved by
                    <input type="text" class="form-control input-sm" name="sales_return_approved_by" placeholder="Sale Return Approved By" required="" />
                </div>
                <div class="col-md-7">Return Reason
                    <input type="text" class="form-control input-sm" name="sales_return_reason" placeholder="Enter Reason" required="" />
                </div>
                <div class="hidden">
                    <div name="sales_return_cash_lb" id="sales_return_cash_lb">0 <i class="fa fa-rupee"></i></div>
                    <input type="hidden" class="form-control input-sm" name="sales_return_cash" id="sales_return_cash" required="" value="0" />
                    <input type="text" class="form-control input-sm" name="temp_cash" id="temp_cash" value="0"/>
                </div>
                <div class="col-md-2"><br>
                    <button type="submit" class="btn btn-sm btn-danger waves-effect waves-light" id="btn_product_return" formmethod="POST" formaction="<?php echo base_url('Sales_return/save_cash_return') ?>"><span class="mdi mdi-repeat fa-lg"></span> Product Return</button>
                </div><div class="clearfix"></div>
                </div><div class="clearfix"></div>
            <input type="hidden" id="sales_return_product_id" />
            <input type="hidden" id="sales_return_model_id" />
        <?php }else{
            echo '<center><h3><i class="mdi mdi-alert"></i> Products are not available to return.</h3>'.
                '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            . '</center>'; 
        }}else{
            echo '<center><h3><i class="mdi mdi-alert"></i> This Invoice is not generated by your branch.</h3>'.
                    '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
                . '</center>'; 
        }}} else{
            echo '<center><h3><i class="mdi mdi-alert"></i> You Enter Wrong Invoice Number</h3>'.
                '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            . '</center>'; 
        }
    }
        
    public function save_cash_return() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $this->load->model('Transfer_model');
        $this->load->model('Inward_model');
        $total_return_cash = 0;
        $this->db->trans_begin();
        $inv_no = $this->input->post('inv_no');
        $idbranch = $this->input->post('branch');
        $sales_return_by = $this->input->post('sales_return_by');
        
        $id_sale = $this->input->post('id_sale');
        $date = date('Y-m-d');
        $return_date = date('Y-m-d H:i:s');
//        $sales_return_invid = $this->input->post('sales_return_invid');
        $invid = $this->Sale_model->get_invoice_no_by_branch($idbranch); 
        $y = date('y', mktime(0, 0, 0, 9 + date('m')));
        $y1 = $y - 1;
        $sales_return_invid = 'SR'.$y1.$y . '/'. $invid->branch_code . '/' . sprintf('%04d', $invid->sales_return_invoice_no);
        
        // save sales return
        $selected_sale_products = $this->input->post('chk_return');
        $overall_total_amt=0;$overall_discount_amt=0; $overall_basic=0;
        for($i = 0; $i < count($selected_sale_products); $i++){
            $total_amt=0;$total_discount=0;$total_basic=0;
            $product_id = $selected_sale_products[$i];
            if($this->input->post('skutype'.$product_id) == 4){
                $qty=0;$total_qty=0;$row_total=0;$row_discount_amt=0;$row_basic=0;$single_total=0;$single_discount_amt_total=0;$single_basic_total=0;
                $qty = $this->input->post('selected_qty'.$product_id);
                $total_qty = $this->input->post('qty'.$product_id);
                $row_total = $this->input->post('total_amt'.$product_id);
                $row_basic = $this->input->post('basic'.$product_id);
                $single_total = $row_total / $total_qty;
                $single_basic_total = $row_basic / $total_qty;
                $total_amt = $single_total * $qty;
                $total_discount = $single_discount_amt_total * $qty;
                $total_basic = $single_basic_total * $qty;
            }else{
                $total_amt = $this->input->post('total_amt'.$product_id);
                $total_discount = $this->input->post('discount_amt'.$product_id);
                $total_basic = $this->input->post('basic'.$product_id);
            }
            $overall_total_amt += $total_amt;
            $overall_discount_amt += $total_discount;
            $overall_basic += $total_basic;
        }
        $data = array(
            'date' => $date,
            'idsale' => $id_sale,
            'sales_return_invid' => $sales_return_invid,
            'sales_return_type' => 1, // cash return
            'inv_no' => $inv_no,
            'inv_date' => $this->input->post('inv_date'),
            'idbranch' => $idbranch,
            'idcustomer' => $this->input->post('idcustomer'),
            'sales_return_approved_by'=> $this->input->post('sales_return_approved_by'),
            'sales_return_reason'=> $this->input->post('sales_return_reason'),
            'idsalesperson' => $this->input->post('idsalesperson'),
            'final_total' => $overall_total_amt,
            'discount_total' => $overall_discount_amt,
            'basic_total' => $overall_basic,
            'sales_return_by' => $sales_return_by,
        );
        // save sales return product
        $idsalereturn = $this->Sales_return_model->save_sales_return($data);
        // update sale product
        $saleupdate = array(
            'sales_return_type' => 1, // cash return
            'sales_return_invid' => $sales_return_invid,
            'sales_return_by' => $sales_return_by,
            'sales_return_date' => $return_date,
        );
        // Update Sale
        $this->Sale_model->update_sale($id_sale, $saleupdate);
        for($i = 0; $i < count($selected_sale_products); $i++){
            $product_id = $selected_sale_products[$i];
            $qty = $this->input->post('selected_qty'.$product_id);
            $previous_return_qty = $this->input->post('sale_return_qty'.$product_id);
            $idmodel = $this->input->post('idmodel'.$product_id);
            $total_qty = $this->input->post('qty'.$product_id);
//            die($total_qty);
            // update sale product
            $saleproductupdate = array(
                'sales_return_type' => 1, // cash return
                'sales_return_invid' => $sales_return_invid,
                'sales_return_by' => $sales_return_by,
                'sales_return_date' => $return_date,
                'sale_return_qty' => $qty + $previous_return_qty,
            );
            $this->Sale_model->update_sale_product_byidsaleproduct($product_id, $saleproductupdate);
            $imei_history[] = array('nest'=>array());
            if($this->input->post('skutype'.$product_id) == 4){
                $row_total=0;$total_amt=0;$row_discount_amt=0;$row_basic=0;$row_taxable_amt=0;$row_cgst_amt=0;$row_sgst_amt=0;$row_igst_amt=0;$row_tax=0;
                $single_total=0;$single_discount_amt_total=0;$single_basic_total=0;$single_taxable_total=0;$single_cgst_total=0;$single_sgst_total=0;$single_igst_total=0;$single_tax_total=0;
                $total_amt=0;$total_discount=0;$total_basic=0;$total_taxable=0;$total_cgst=0;$total_sgst=0;$total_igst=0;$total_tax=0;
                
                $row_total = $this->input->post('total_amt'.$product_id);
                $row_discount_amt = $this->input->post('discount_amt'.$product_id);
                $row_basic = $this->input->post('basic'.$product_id);
                $row_taxable_amt = $this->input->post('taxable'.$product_id);
                $row_cgst_amt = $this->input->post('cgst_amt'.$product_id);
                $row_sgst_amt = $this->input->post('sgst_amt'.$product_id);
                $row_igst_amt = $this->input->post('igst_amt'.$product_id);
                $row_tax = $this->input->post('tax'.$product_id);
                // Single quantity amount
                $single_total = $row_total / $total_qty;
                $single_discount_amt_total = $row_discount_amt / $total_qty;
                $single_basic_total = ($row_basic / $total_qty);
                $single_taxable_total = $row_taxable_amt / $total_qty;
                $single_cgst_total = $row_cgst_amt / $total_qty;
                $single_sgst_total = $row_sgst_amt / $total_qty;
                $single_igst_total = $row_igst_amt / $total_qty;
                $single_tax_total = $row_tax / $total_qty;
                // Selected quantity amount
                $total_amt = $single_total * $qty;
                $total_discount = $single_discount_amt_total * $qty;
                $total_basic = $single_basic_total * $qty;
                $total_taxable = $single_taxable_total * $qty;
                $total_cgst = $single_cgst_total * $qty;
                $total_sgst = $single_sgst_total * $qty;
                $total_igst = $single_igst_total * $qty;
                $total_tax = $single_tax_total * $qty;
//                die(''.$total_basic);
                $sale_product = array(
                    'date' => $date,
                    'sales_return_invid' => $sales_return_invid,
                    'sales_return_type' => 1, // cash return
                    'idskutype' => $this->input->post('skutype'.$product_id),
                    'idproductcategory' => $this->input->post('idtype'.$product_id),
                    'idcategory' => $this->input->post('idcategory'.$product_id),
                    'idgodown' => $this->input->post('idgodown'.$product_id),
                    'idmodel' => $idmodel,
                    'idvariant' => $this->input->post('idvariant'.$product_id),
                    'idbranch' => $idbranch,
                    'idbrand' => $this->input->post('idbrand'.$product_id),
                    'sales_return_by' => $sales_return_by,
                    'idsales_return' => $idsalereturn,
                    'product_name' => $this->input->post('product_name'.$product_id),
                    'price' => $this->input->post('price'.$product_id),
                    'inv_no' => $inv_no,
                    'qty' => $qty,
                    'discount_amt' => $total_discount,
                    'basic' => $total_basic,
                    'taxable_amt' => $total_taxable,
                    'cgst_per' => $this->input->post('cgst'.$product_id),
                    'sgst_per' => $this->input->post('sgst'.$product_id),
                    'igst_per' => $this->input->post('igst'.$product_id),
                    'cgst_amt' => $total_cgst,
                    'sgst_amt' => $total_sgst,
                    'igst_amt' => $total_igst,
                    'tax' => $total_tax,
                    'total_amount' => $total_amt,
                );
//                die('<pre>'.print_r($sale_product,1).'</pre>');
                $idsalereturnproduct = $this->Sales_return_model->save_sales_return_product($sale_product);
                $branchstock = $this->Transfer_model->get_branchstock_byidmodel_skutype_godown($this->input->post('idvariant'.$product_id),4,$idbranch,$this->input->post('idgodown'.$product_id));
                if(count($branchstock) == 1){
                    foreach ($branchstock as $brstock){
                        $qty = $brstock->qty + $qty;
                        $this->Inward_model->update_stock_byid($brstock->id_stock, $qty);
                    }
                }else{
                    $inward_stock = array(
                        'date' => $date,
                        'idmodel' => $idmodel,
                        'created_by' => $sales_return_by,
                        'sales_return_by' => $sales_return_by,
                        'return_date' => $return_date,
                        'sales_return_type' => 1, // cash return
                        'sale_date' => $this->input->post('inv_date'),
                        'product_name' => $this->input->post('product_name'.$product_id),
                        'idvariant' => $this->input->post('idvariant'.$product_id),
                        'idskutype' => 4,
                        'idproductcategory' => $this->input->post('idtype'.$product_id),
                        'idcategory' => $this->input->post('idcategory'.$product_id),
                        'idbrand' => $this->input->post('idbrand'.$product_id),
                        'is_gst'   => $this->input->post('is_gst'.$product_id),
                        'idvendor' => $this->input->post('idvendor'.$product_id),
                        'idgodown' => $this->input->post('idgodown'.$product_id),
                        'idbranch' => $idbranch,
                    );
                    $this->Inward_model->save_stock($inward_stock);
                }
                $total_return_cash += $total_amt;
            }else{
                $imeino = $this->input->post('imei_no'.$product_id);
                $sale_product = array(
                    'sales_return_type' => 1, // cash return
                    'date' => $date,
                    'imei_no' => $imeino,
                    'sales_return_invid' => $sales_return_invid,
                    'idskutype' => $this->input->post('skutype'.$product_id),
                    'idproductcategory' => $this->input->post('idtype'.$product_id),
                    'idcategory' => $this->input->post('idcategory'.$product_id),
                    'idgodown' => $this->input->post('idgodown'.$product_id),
                    'idvariant' => $this->input->post('idvariant'.$product_id),
                    'idmodel' => $idmodel,
                    'idbranch' => $idbranch,
                    'idbrand' => $this->input->post('idbrand'.$product_id),
                    'sales_return_by' => $sales_return_by,
                    'idsales_return' => $idsalereturn,
                    'product_name' => $this->input->post('product_name'.$product_id),
                    'price' => $this->input->post('price'.$product_id),
                    'inv_no' => $inv_no,
                    'qty' => 1,
                    'discount_amt' => $this->input->post('discount_amt'.$product_id),
                    'basic' => $this->input->post('basic'.$product_id),
                    'taxable_amt' => $this->input->post('taxable'.$product_id),
                    'cgst_per' => $this->input->post('cgst'.$product_id),
                    'sgst_per' => $this->input->post('sgst'.$product_id),
                    'igst_per' => $this->input->post('igst'.$product_id),
                    'cgst_amt' => $this->input->post('cgst_amt'.$product_id),
                    'sgst_amt' => $this->input->post('sgst_amt'.$product_id),
                    'igst_amt' => $this->input->post('igst_amt'.$product_id),
                    'tax' => $this->input->post('tax'.$product_id),
                    'taxable_amt' => $this->input->post('taxable'.$product_id),
                    'total_amount' => $this->input->post('total_amt'.$product_id),
                );
                $idsalereturnproduct = $this->Sales_return_model->save_sales_return_product($sale_product);
                $total_return_cash += $this->input->post('total_amt'.$product_id);
//              add into stock remaining    
                $inward_stock[$i] = array(
                    'date' => $date,
                    'imei_no' => $imeino,
                    'idmodel' => $idmodel,
                    'created_by' => $sales_return_by,
                    'sales_return_by' => $sales_return_by,
                    'return_date' => $return_date,
                    'sales_return_type' => 1, // cash return
                    'sale_date' => $this->input->post('inv_date'),
                    'idvariant' => $this->input->post('idvariant'.$product_id),
                    'product_name' => $this->input->post('product_name'.$product_id),
                    'idskutype' => $this->input->post('skutype'.$product_id),
                    'idproductcategory' => $this->input->post('idtype'.$product_id),
                    'idcategory' => $this->input->post('idcategory'.$product_id),
                    'idbrand' => $this->input->post('idbrand'.$product_id),
                    'is_gst'   => $this->input->post('is_gst'.$product_id),
                    'idvendor' => $this->input->post('idvendor'.$product_id),
                    'idgodown' => $this->input->post('idgodown'.$product_id),
                    'idbranch' => $idbranch,
                );
                $this->Inward_model->save_stock($inward_stock[$i]);
                $imei_history['nest'][]=array(
                    'imei_no' => $imeino,
                    'entry_type' => 'Sales Return - Cash',
                    'entry_time' => $return_date,
                    'date' => $date,
                    'idbranch' => $idbranch,
                    'idgodown' => $this->input->post('idgodown'.$product_id),
                    'idvariant' => $this->input->post('idvariant'.$product_id),
                    'model_variant_full_name' => $this->input->post('product_name'.$product_id),
                    'idimei_details_link' => 7, // Sales Return from imei_details_link table
                    'idlink' => $idsalereturn,
                );
            }
        }
        if(count($imei_history['nest']) > 0){
            $this->General_model->save_batch_imei_history($imei_history['nest']);
        }
        $srpayment = array(
            'date' => $date,
            'inv_no' => $sales_return_invid,
            'entry_type' => 2,
            'idbranch' => $idbranch,
            'idtable' => $idsalereturn,
            'table_name' => 'sales_return',
            'idcustomer' => $this->input->post('idcustomer'),
            'customer_fname' => $this->input->post('fcustomer'),
            'customer_lname' => $this->input->post('lcustomer'),
            'customer_gst' => $this->input->post('gst_no'),
            'amount' => -$total_return_cash,
        );
        $this->Sale_model->save_daybook_cash_payment($srpayment);
        
        $next_srinv_no =  $invid->sales_return_invoice_no + 1;
        $invoice_data = array( 'sales_return_invoice_no' => $next_srinv_no );
        $this->General_model->edit_db_branch($idbranch, $invoice_data);
        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Sales Cash Return is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Sales Cash Return Successfull');
        }
        return redirect('Sales_return/sales_return_details/'.$idsalereturn);
    }
    
    public function search_sales_product_return_invoice_byinvno() {
        $invno = $this->input->post('invno');
        $branch = $this->input->post('branch');
        $level = $this->input->post('level');
        $sale_data = $this->Sale_model->get_sale_byinvno($invno, $branch, $level);
        $sale_product = $this->Sale_model->get_sale_product_byinvno($invno, $branch, $level);
        
        if(count($sale_data) > 0){
        foreach ($sale_data as $sale){ ?>
            <div class="col-md-3 pull-right">
                <span class="text-muted">Sale Id:</span> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; <?php echo $sale->id_sale ?>
            </div><div class="clearfix"></div>
            <div class="col-md-3 pull-right">
                <span class="text-muted">Entry time:</span> &nbsp; &nbsp; <?php echo date('d/m/Y h:i a', strtotime($sale->entry_time)) ?>
            </div><div class="clearfix"></div>
            <div class="col-md-3 pull-right">
                <span class="text-muted">Invoice No:</span> &nbsp; &nbsp; &nbsp;<?php echo $sale->inv_no ?>
                <input type="hidden" name="inv_date" value="<?php echo $sale->date ?>" />
                <input type="hidden" name="inv_no" value="<?php echo $sale->inv_no ?>" />
                <input type="hidden" name="branch" value="<?php echo $branch ?>" />
                <input type="hidden" name="idcustomer" value="<?php echo $sale->idcustomer ?>" />
                <input type="hidden" name="cust_fname" value="<?php echo $sale->customer_fname ?>" />
                <input type="hidden" name="cust_lname" value="<?php echo $sale->customer_lname ?>" />
                <input type="hidden" class="form-control input-sm" name="address" id="address" placeholder="Address" value="<?php echo $sale->customer_address ?>" />
                <input type="hidden" name="gst_no" value="<?php echo $sale->customer_gst ?>" />
                <input type="hidden" name="mobile" value="<?php echo $sale->customer_contact ?>" />
                <input type="hidden" name="idsalesperson" value="<?php echo $sale->idsalesperson ?>" />
                <input type="hidden" name="created_by" value="<?php echo $sale->created_by ?>" />
                <input type="hidden" name="id_sale" value="<?php echo $sale->id_sale ?>" />
                <input type="hidden" name="cust_idstate" id="cust_idstate" value="<?php echo $sale->customer_idstate ?>" />
                <input type="hidden" name="cust_pincode" id="cust_pincode" value="<?php echo $sale->customer_pincode ?>" />
            </div><div class="clearfix"></div>To,
            <div class="col-md-3 pull-right">
                <span class="text-muted">Invoice Date:</span> &nbsp; <?php echo $sale->date ?>
            </div><div class="clearfix"></div>
            <div class="col-md-3">
                <span class="text-muted">Customer</span>: <?php echo $sale->customer_fname.' '.$sale->customer_lname ?>
            </div><div class="clearfix"></div>
            <div class="col-md-3">
                <span class="text-muted">Contact</span>: <?php echo $sale->customer_contact ?>
            </div><div class="clearfix"></div>
            <?php if($sale->customer_gst !=''){ ?>
            <div class="col-md-2">
                <span class="text-muted">GST</span>: <?php echo $sale->customer_gst ?>
            </div><div class="clearfix"></div>
            <?php } ?>
            <div class="col-md-7">
                <span class="text-muted">Promoter</span>: <?php echo $sale->user_name ?>
            </div><div class="clearfix"></div>
            <div class="thumbnail" style="overflow: auto;padding: 0">
                <table id="model_data" class="table table-bordered table-condensed" style="font-size: 13px; margin-bottom: 0;">
                    <thead class="bg-danger">
                        <th class="col-md-4">Product</th>
                        <th>SKU</th>
                        <th>Total Qty</th>
                        <th>Return Qty</th>
                        <th>Avail Qty</th>
                        <th>Rate</th>
                        <th>Basic</th>
                        <th>Dis </th>
                        <th>Amount</th>
                        <th class="col-md-1">IMEI/SRNO</th>
                        <th>Select</th>
                    </thead>
                    <tbody>
                        <?php $taxable_total=0; $cgstamt_total=0; $igstamt_total=0; $cgstamt=0; $igstamt=0; $tax=0; $totalqty=0; $total_sale_return_qty=0;$total_avail_qty=0;$avail_qty=0;
                        foreach ($sale_product as $product) {
                            $id_saleproduct = $product->id_saleproduct;
                            if($sale->gst_type){
                                // igst
                                $cal = ($product->igst_per + 100) / 100;
                                $taxable = $product->total_amount / $cal;
                                $taxable_total += $taxable;
                                $igstamt = $product->total_amount - $taxable;
                                $igstamt_total += $igstamt;
                            }else{
                                $cal = ($product->cgst_per + $product->sgst_per + 100) / 100;
                                $taxable = $product->total_amount / $cal;
                                $taxable_total += $taxable;
                                $cgst = $product->total_amount - $taxable;
                                $cgstamt = $cgst / 2;
                                $cgstamt_total += $cgstamt;
                            }
                            $tax = $cgstamt + $cgstamt + $igstamt; 
                            $avail_qty = $product->qty - $product->sale_return_qty;
                            ?>
                        <?php if($avail_qty > 0){ ?>
                        <tr class="tr_row">
                            <td>
                                <?php echo $product->product_name; ?>
                                <input type="hidden" class="saleproduct_name" value="<?php echo $product->product_name; ?>" />
                                <input type="hidden" id="idmodel" class="idmodel" name="idmodel<?php echo $id_saleproduct ?>"  value="<?php echo $product->idmodel ?>" />
                                <input type="hidden" class="saleproduct_id" name="id_saleproduct<?php echo $id_saleproduct ?>" value="<?php echo $id_saleproduct ?>" />
                                <input type="hidden" name="product_name<?php echo $id_saleproduct ?>" value="<?php echo $product->product_name ?>" />
                                <input type="hidden" name="idtype<?php echo $id_saleproduct ?>" value="<?php echo $product->idproductcategory ?>" />
                                <input type="hidden" name="idcategory<?php echo $id_saleproduct ?>" value="<?php echo $product->idcategory ?>" />
                                <input type="hidden" name="idbrand<?php echo $id_saleproduct ?>" value="<?php echo $product->idbrand ?>" />
                                <input type="hidden" name="is_gst<?php echo $id_saleproduct ?>" value="<?php echo $product->is_gst ?>" />
                                <input type="hidden" name="retidvendor<?php echo $id_saleproduct ?>" value="<?php echo $product->idvendor ?>" />
                                <input type="hidden" name="idgodown<?php echo $id_saleproduct ?>" value="<?php echo $product->idgodown ?>" />
                                <input type="hidden" name="idvariant<?php echo $id_saleproduct ?>" value="<?php echo $product->idvariant ?>" />
                            </td>
                            <td>
                                <?php echo $product->sku_type ?>
                                <input type="hidden" class="skutype" name="skutype<?php echo $id_saleproduct ?>" value="<?php echo $product->idskutype ?>" />
                            </td>
                            <td>
                                <?php echo $product->qty; 
                                $totalqty = $product->qty + $totalqty; ?>
                                <input type="hidden" name="qty<?php echo $id_saleproduct ?>" class="qty" id="qty" value="<?php echo $product->qty ?>" />
                            </td>
                            <td style="color: #ff3333">
                                <?php echo $product->sale_return_qty; 
                                $total_sale_return_qty += $product->sale_return_qty; ?>
                                <input type="hidden" name="sale_return_qty<?php echo $id_saleproduct ?>" class="sale_return_qty" id="sale_return_qty" value="<?php echo $product->sale_return_qty ?>" />
                            </td>
                            <td>
                                <?php echo $avail_qty;
                                $total_avail_qty += $avail_qty; ?>
                                <input type="hidden" name="avail_qty<?php echo $id_saleproduct ?>" class="avail_qty" id="avail_qty" value="<?php echo $avail_qty ?>" />
                            </td>
                            <td>
                                <?php echo $product->price ?>
                                <input type="hidden" class="price" name="price<?php echo $id_saleproduct ?>" value="<?php echo $product->price ?>" />
                            </td>
                            <td>
                                <?php echo $product->basic ?>
                                <input type="hidden" name="basic<?php echo $id_saleproduct ?>" value="<?php echo $product->basic ?>" />
                            </td>
                            <td>
                                <?php echo $product->discount_amt ?>
                                <input type="hidden" class="retdiscount_amt" name="retdiscount_amt<?php echo $id_saleproduct ?>" value="<?php echo $product->discount_amt ?>" />
                                <input type="hidden" name="taxable<?php echo $id_saleproduct ?>" value="<?php echo $taxable ?>" />
                                <input type="hidden" name="cgst_amt<?php echo $id_saleproduct ?>" value="<?php echo $cgstamt ?>" />
                                <input type="hidden" name="cgst<?php echo $id_saleproduct ?>" value="<?php echo $product->cgst_per ?>" />
                                <input type="hidden" name="sgst_amt<?php echo $id_saleproduct ?>" value="<?php echo $cgstamt ?>" />
                                <input type="hidden" name="sgst<?php echo $id_saleproduct ?>" value="<?php echo $product->sgst_per ?>" />
                                <input type="hidden" name="igst_amt<?php echo $id_saleproduct ?>" value="<?php echo $igstamt ?>" />
                                <input type="hidden" name="igst<?php echo $id_saleproduct ?>" value="<?php echo $product->igst_per ?>" />
                                <input type="hidden" name="tax<?php echo $id_saleproduct ?>" value="<?php echo $tax ?>" />
                                <!--<input type="hidden" name="row_cash_amount<?php // echo $id_saleproduct ?>" class="row_cash_amount" value="<?php // echo $product->total_amount ?>" />-->
                            </td>
                            <td>
                                <?php echo $product->total_amount ?>
                                <input type="hidden" class="rettotal_amt" name="rettotal_amt<?php echo $id_saleproduct ?>" value="<?php echo $product->total_amount ?>" />
                            </td>
                            <td>
                                <?php if($product->idskutype != 4){ ?>
                                <?php echo $product->imei_no ?>
                                <input type="hidden" name="imei_no<?php echo $id_saleproduct ?>" value="<?php echo $product->imei_no ?>" />
                                <?php if($avail_qty > 0){ ?>
                                    <input type="hidden" name="selected_qty<?php echo $id_saleproduct ?>" value="1" class="selected_qty" id="selected_qty" />
                                <?php }}else{ ?>
                                    <?php if($avail_qty > 0){ ?>
                                    <input type="number" name="selected_qty<?php echo $id_saleproduct ?>" class="form-control input-sm selected_qty" id="selected_qty" readonly="" placeholder="Qty" max="<?php echo $avail_qty ?>" value="0" />
                                <?php }} ?>
                            </td>
                            <td>
                                <?php if($avail_qty > 0){ ?>
                                <input id="chk_return" type="checkbox" class="chk_return" name="chk_return[]" value="<?php echo $id_saleproduct ?>"/>
                                <input type="hidden" class="selected_row_total" value="0" />
                                <small class="seleted_lbl" style="color: #228dc7; display: none">Selected</small>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php }else{ ?>
                        <tr class="text-muted">
                            <td><?php echo $product->product_name; ?></td>
                            <td><?php echo $product->sku_type ?></td>
                            <td><?php echo $product->qty; 
                                $totalqty = $product->qty + $totalqty; ?>
                            </td>
                            <td style="color: #ff3333">
                                <?php echo $product->sale_return_qty; 
                                $total_sale_return_qty += $product->sale_return_qty; ?>
                            </td>
                            <td><?php echo $avail_qty;
                                $total_avail_qty += $avail_qty; ?>
                            </td>
                            <td><?php echo $product->price ?></td>
                            <td><?php echo $product->basic ?></td>
                            <td><?php echo $product->discount_amt ?></td>
                            <td><?php echo $product->total_amount ?></td>
                            <td><?php echo $product->imei_no ?></td>
                            <td>Returned</td>
                        </tr>
                        <?php }} ?>
                        <tr class="bg-danger">
                            <td colspan="5"></td>
                            <td>Total</td>
                            <td>
                                <?php echo $sale->basic_total ?>
                                <input type="hidden" name="gross_total" value="<?php echo $sale->basic_total ?>" />
                            </td>
                            <td>
                                <?php echo $sale->discount_total ?>
                                <input type="hidden" name="retfinal_discount" value="<?php echo $sale->discount_total ?>" />
                                <input type="hidden" name="taxable_total" value="<?php echo $taxable_total ?>" />
                                <input type="hidden" name="cgstamt_total" value="<?php echo $cgstamt_total ?>" />
                                <input type="hidden" name="sgstamt_total" value="<?php echo $cgstamt_total ?>" />
                                <input type="hidden" name="igstamt_total" value="<?php echo $igstamt_total ?>" />
                            </td>
                            <td>
                                <?php echo $sale->final_total ?>
                                <input type="hidden" name="retfinal_total" value="<?php echo $sale->final_total ?>" />
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div><div class="clearfix"></div>
            <div id="">
                <div class="col-md-2 pull-right" id="confirm_return_block">
                    <button type="button" class="btn btn-danger" id="confirm_return">Confirm Return</button>
                    <!--<a href="#" class="btn btn-danger" id="confirm_return">Confirm Return</a>-->
                </div>
                <div class="col-md-5 pull-right">
                    <div class="thumbnail text-center" style="padding: 0px">
                        <h4 style="padding: 0px 10px">Return Products Amount: <spna id="selected_total_amountlb">0</spna> <i class="fa fa-rupee"></i></h4>
                        <input type="hidden" id="selected_total_amount" value="0" />
                    </div>
                </div><div class="clearfix"></div>
            </div>
            <script>
                $(document).ready(function(){
                    $("button[type='reset']").closest('form').on('reset', function(event) {
                    //        javascript:void(0)
                        setTimeout(function() {
                            $('.tr_row').each(function () {
                                $(this).css("background-color", "#FFFFFF");
                                $(this).find('.chk_return').each(function () {
                                    $(this).show();
                                });
                                $(this).find('.seleted_lbl').each(function () {
                                    $(this).hide();
                                });
                            });
                            $("#replacement_form").html('');
                            swal("Cancelled return process!", "Reset selection successfully", "success");
                        }, 1);
                    });
                });
            </script>
            <?php if($sale->idbranch == $_SESSION['idbranch']){ ?>
            <form class="final_submit_form">
                <div id="replacement_form" style="font-size: 14px; min-height: 550px; overflow: auto"></div>
            </form>
        <?php }else{
            echo '<center><h3><i class="mdi mdi-alert"></i> This Invoice is not generated by your branch.</h3>'.
                    '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
                . '</center>'; 
        }}} else{
            echo '<center><h3><i class="mdi mdi-alert"></i> You Enter Wrong Invoice Number</h3>'.
                '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            . '</center>'; 
        }
    }
    
    public function product_replacement_form() {
        $idbranch = $_SESSION['idbranch'];
        $total_selected_cash = $this->input->post('total_selected_sum');
        $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);
        $payment_head = $this->General_model->get_active_payment_head();
        $payment_mode = $this->General_model->get_active_payment_mode();
        $payment_attribute = $this->General_model->get_payment_head_has_attributes();
        $state_data = $this->General_model->get_state_data();
        $active_users_byrole = $this->General_model->get_active_users_byrole(17); // sales promoter
        $model_variant = $this->General_model->ajax_get_model_variant_byidskutype(4);
//        $this->load->view('sales_return/product_replacement_form', $q);
        ?>
        <div class="col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1" style="padding: 0">
            <marquee style="color: #9b0c13"><i class="fa fa-barcode"></i> Scan new product to Replace/Upgrade</marquee><br><br>
            <!--<form>-->
            <!--<div class="" style="font-size: 13px; padding: 10px;border-radius: 1rem;background: #fbfbff;border: 1px solid #e3e3e3;box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.15);">-->
            <div class="neucard shadow-inset border-light p-4">
                <div class="shadow-soft border-light rounded p-4" style="background-color: #fff">
                    <div style="background-image: linear-gradient(to right top, #510a0a, #750c11, #9b0c13, #c30e14, #eb1212);box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5);border-radius: 5px; margin-top: -45px">
                        <div style="padding: 8px; font-size: 17px; margin: 0px; color: #fff">
                            <center><i class="fa fa-clipboard"></i> Product Replace/Upgrade Form </center>
                        </div>
                    </div><div class="clearfix"></div>
                    <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch'] ?>"/><br>
                    <input type="hidden" name="bfl_store_id" id="bfl_store_id" value="<?php echo $invoice_no->bfl_store_id ?>"/>
                    <input type="hidden" name="idstate" id="idstate" value="<?php echo $invoice_no->idstate ?>"/>
                    <div class="col-md-2 col-sm-4" style="padding: 0 5px">Approved by</div>
                    <div class="col-md-4 col-sm-4" style="padding: 0 5px">
                        <input type="text" class="form-control input-sm" name="sales_return_approved_by" placeholder="Sale Return Approved By" required="" />
                    </div>
                    <div class="col-md-2 col-sm-4" style="padding: 0 5px">Return Reason</div>
                    <div class="col-md-4 col-sm-4" style="padding: 0 5px">
                        <input type="text" class="form-control input-sm" name="sales_return_reason" placeholder="Enter Reason for product replacement" required="" />
                    </div><div class="clearfix"></div><br>
                    <div class="col-md-2 col-sm-4" style="padding: 0 5px">
                        <span>Sales Promoter</span>
                    </div>
                    <div class="col-md-4 col-sm-4" style="padding: 0 5px">
                        <select class="form-control input-sm" name="idsalesperson" required="">
                            <option value="">Select Sales Promoter</option>
                            <?php foreach ($active_users_byrole as $user) { ?>
                                <option value="<?php echo $user->id_users ?>"><?php echo $user->user_name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4 col-sm-4" style="padding: 5px">
                        Default Godown -> New Godown
                        <input type="hidden" id="idgodown" value="1"/>
                    </div><div class="clearfix"></div><br>
                    <div class="col-md-2 col-sm-4" style="padding: 0 5px">
                        <span>Scan IMEI</span>
                    </div>
                    <div class="col-md-4 col-sm-4" style="padding: 0 5px">
                        <input type="text" class="form-control input-sm" placeholder="Scan IMEI/SRNO/Barcode" id="enter_imei"/>
                    </div>
                    <div class="col-md-2 col-sm-4" style="padding: 0 5px">
                        <span>Select Product</span>
                    </div>
                    <div class="col-md-4 col-sm-4" style="padding: 0 5px">
                        <select class="chosen-select form-control input-sm" name="skuvariant" id="skuvariant">
                            <option value="">Select Quantity Based Product</option>
                            <?php foreach ($model_variant as $variant) { ?>
                                <option value="<?php echo $variant->id_variant; ?>"><?php echo $variant->product_category_name.' '.$variant->full_name; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="clearfix"></div>
                    <center id="img_scanner" style="margin-top: 10px">
                        <img src="<?php echo base_url() ?>assets/images/scanner.gif" style="max-width: 100%" />
                        <!--<h4 style="color:#1b6caa;">Scan IMEI/ SRNO or Select Product</h4>-->
                        <!--<h4 style="color:#1b6caa;font-family: Kurale;">Scan IMEI/ SRNO or Select Product</h4>-->
                    </center>
                    <div id="product" style="display: none;">
                        <div class="thumbnail" id="product" style="overflow: auto;margin-top: 10px; padding: 0">
                            <table id="inward_table" class="table table-bordered table-condensed table-hover" style="font-size: 13px; margin-bottom: 0">
                                <!--<thead class="" style="background-image: linear-gradient(to right, #81fdff, #78f3ff, #76e8ff, #7adcff, #83d0ff, #83d0ff, #83d0ff, #83d0ff, #7adcff, #76e8ff, #78f3ff, #81fdff); font-size: 14px">-->
                                <thead style="color: #fff; background-image: linear-gradient(to right top, #510a0a, #750c11, #9b0c13, #c30e14, #eb1212);">
                                    <td>Product</td>
                                    <td>IMEI/SRNO</td>
                                    <td>Avail</td>
                                    <td>MRP</td>
                                    <td>MOP</td>
                                    <td>Price</td>
                                    <td style="width: 100px">Qty</td>
                                    <td>Basic</td>
                                    <td style="width: 100px">Discount</td>
                                    <td>Tax</td>
                                    <td>Total</td>
                                    <td>Remove</td>
                                </thead>
                                <tbody id="product_data">
                                </tbody>
                                <thead id="product_data1">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>Total</td>
                                        <td>
                                            <input type="hidden" name="gross_total" id="gross_total"/>
                                            <span id="spgross_total">0</span>
                                        </td>
                                        <td>
                                            <input type="hidden" name="final_discount" id="final_discount" class="form-control input-sm final_discount" placeholder="Total Discount" value="0" readonly=""/>
                                            <span id="spfinal_discount">0</span>
                                        </td>
                                        <td></td>
                                        <td colspan="2">
                                            <input type="hidden" name="final_total" id="final_total"/>
                                            <span id="spfinal_total">0</span>
                                        </td>
                                    </tr>
                                </thead>
                            </table>
                        </div><div class="clearfix"></div><hr>
                        <h5 style="color:#1b6caa;font-family: Kurale;">Mode of payment</h5>
                        <?php foreach ($payment_head as $head) { if($head->id_paymenthead == 1){ ?>
                        <div class="col-md-2 col-sm-2 col-xs-6" style="font-family: Kurale; font-size: 15px; padding: 2px 5px;">
                            <label class="material-switch waves-block waves-effect waves-ripple" for="paymentmode1" style="font-weight: 100;box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.15);padding: 7px 12px;">
                                <input class="paymenthead" id="paymentmode<?php echo $head->payment_head ?>" type="checkbox" value="1" selected_head="Cash" checked="" disabled />
                                <label for="paymentmodeCash" class="label-primary" style="margin-bottom: 10px"></label> 
                                <span><?php echo $head->payment_head; ?></span>
                            </label>
                        </div>
                        <?php }else{ ?>
                        <div class="col-md-2 col-sm-2 col-xs-6" style="font-family: Kurale; font-size: 15px; padding: 2px 5px;">
                            <label class="material-switch waves-block waves-effect waves-ripple" for="paymentmode<?php echo $head->payment_head ?>" style="font-weight: 100;box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.15);padding: 7px 12px;">
                                <input class="paymenthead" id="paymentmode<?php echo $head->payment_head ?>" type="checkbox" value="<?php echo $head->id_paymenthead ?>" selected_head="<?php echo $head->payment_head ?>" />
                                <label for="paymentmode<?php echo $head->payment_head ?>" class="label-primary" style="margin-bottom: 10px"></label> 
                                <span><?php echo $head->payment_head ?></span>
                            </label>
                        </div>
                        <?php }} ?><div class="clearfix"></div>
                        <div id="modes_block1" class="modes_block modes_blockc1 thumbnail" style="margin-bottom: 5px; padding: 5px;">
                            <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                                <span style="font-size: 15px; font-family: Kurale">Cash</span>
                                <select class="form-control input-sm payment_type" name="payment_type[]">
                                    <option value="1">Cash</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                                Amount
                                <input type="number" class="form-control input-sm amount" id="amount1" name="amount[]" placeholder="Amount" value="<?php echo $total_selected_cash ?>" min="<?php echo $total_selected_cash ?>" required="" />
                                <input type="hidden" class="idpaymenthead" name="idpaymenthead[]" value="1" />
                                <input type="hidden" class="headname" name="headname[]" value="Cash" />
                                <input type="hidden" class="credit_type" name="credit_type[]" value="0" />
                            </div>
                            <div class="col-md-2 col-sm-3 hidden">                            
                                <input type="text" class="form-control input-sm tranxid" id="tranxid1" name="tranxid[]" value="<?php echo NULL; ?>" />
                            </div><div class="clearfix"></div>
                        </div><div class="clearfix"></div>
                        <div class="payment_modes" style="font-size: 12px"></div>
                        <div id="bfl_form"></div><hr>
                        <div class="col-md-2 col-sm-3 col-xs-4">
                            <a class="btn btn-warning gradient1" href="<?php echo base_url('Sales_return/replace_return'); ?>">Cancel</a>
                        </div>
                        <div class="col-md-5 col-md-offset-3 col-sm-9 col-xs-8">
                            <input type="text" class="form-control input-sm" name="remark" placeholder="Enter Remark"/>
                        </div>
                        <div class="col-md-2 col-sm-3 col-xs-4">
                            <button type="submit" id="invoice_submit" class="btn btn-success btn-sub" formmethod="POST" formaction="<?php echo site_url('Sales_return/save_product_replace_return') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Submit</button>
                        </div><div class="clearfix"></div>
                    </div>
                </div><div class="clearfix"></div>
                <!--</form>-->
            </div><div class="clearfix"></div>
        </div><div class="clearfix"></div>
        <a href="<?php echo base_url('Sale/imei_tracker') ?>" target="_blank" class="simple-tooltip waves-effect waves-light" title="Track imei" id="floatingButton">
            <i class="mdi mdi-barcode-scan" style="font-size: 24px"></i>
        </a>
<?php }
    
    public function ajax_get_imei_details() {
        $imei = $this->input->post('imei');
        $idbranch = $this->input->post('idbranch'); 
        $skuvariant = $this->input->post('skuvariant'); 
        $idgodown = $this->input->post('idgodown'); 
        $is_dcprint = $this->input->post('is_dcprint'); 
        // Quantity
        if($skuvariant){
            $models = $this->Sale_model->ajax_get_variant_byid_branch_godown($skuvariant, $idbranch, $idgodown);
            if(count($models)){
                foreach($models as $model){
                    if($model->dcprint != $is_dcprint && $is_dcprint != ''){
                        if($is_dcprint == 0){
                            echo '1'; // previous is dc product
                        }else{
                            echo '2'; // previous is dc invoice
                        }
                    }else{ ?>
                <tr class="product_tr" id="m<?php echo $model->id_stock ?>">
                    <td>
                        <?php echo $model->product_name; ?>
                        <input type="hidden" id="idtype" class="form-control idtype" name="idtype[]" value="<?php echo $model->idproductcategory ?>" />
                        <input type="hidden" id="idcategory" class="form-control idcategory" name="idcategory[]" value="<?php echo $model->idcategory ?>" />
                        <input type="hidden" id="idbrand" class="form-control" name="idbrand[]" value="<?php echo $model->idbrand ?>" />
                        <input type="hidden" id="idmodel" class="form-control idmodel" name="idmodel[]" value="<?php echo $model->idmodel ?>" />
                        <input type="hidden" id="idvariant" class="form-control idvariant" name="idvariant[]" value="<?php echo $model->idvariant ?>" />
                        <input type="hidden" id="idgodown" class="form-control idgodown" name="idgodown[]" value="<?php echo $idgodown ?>" />
                        <input type="hidden" id="skutype" class="form-control skutype" name="skutype[]" value="<?php echo $model->idskutype ?>" />
                        <input type="hidden" id="product_name" class="form-control product_name" name="product_name[]" value="<?php echo $model->product_name; ?>" />
                        <input type="hidden" id="is_mop" class="form-control is_mop" name="is_mop[]" value="<?php echo $model->is_mop; ?>" />
                        <input type="hidden" id="hsn" class="form-control hsn" name="hsn[]" value="<?php echo $model->hsn; ?>" />
                        <input type="hidden" id="is_gst" class="form-control is_gst" name="is_gst[]" value="<?php echo $model->is_gst; ?>" />
                        <small class="pull-right" style="color:#1b6caa;font-family: Kurale;">New Godown</small>
                        <input type="hidden" id="dcprint" class="dcprint" name="dcprint[]" value="<?php echo $model->dcprint; ?>" />
                    </td>
                    <td><input type="hidden" id="imei" name="imei[]" class="imei" value="<?php echo NULL ?>" /></td>
                    <td><?php echo $model->qty; ?></td>
                    <td><?php echo $model->mrp; ?></td>
                    <td><?php echo $model->mop; ?></td>
                    <td>
                        <input type="hidden" id="read_skuprice<?php echo $skuvariant ?>" value="<?php echo $model->mop ?>" />
                        <input type="hidden" id="readprice<?php echo $model->id_stock ?>" value="<?php echo $model->mop ?>" />
                        <input type="hidden" id="landing" name="landing[]" class="landing" value="<?php echo $model->landing ?>" />
                        <input type="hidden" id="mop" name="mop[]" class="mop" value="<?php echo $model->mop ?>" />
                        <input type="hidden" id="mrp" name="mrp[]" class="mrp" value="<?php echo $model->mrp ?>" />
                        <input type="hidden" id="salesman_price" name="salesman_price[]" class="salesman_price" value="<?php echo $model->salesman_price ?>" />
                        <input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $model->mop ?>" min="<?php echo $model->landing ?>" step="0.001" style="width: 90px" max="<?php echo $model->mrp ?>" />
                    </td>
                    <td>
                        <input type="number" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" value="1" min="1" step="0.001" style="width: 90px" max="<?php echo $model->qty; ?>"/>
                    </td>
                    <td>
                        <input type="hidden" id="basic" name="basic[]" class="basic" placeholder="Basic" readonly="" value="<?php echo $model->mop ?>"/>
                        <span class="spbasic" id="spbasic" name="spbasic[]"><?php echo $model->mop ?></span>
                    </td>
                    <td><input type="number" id="discount_amt" name="discount_amt[]" class="form-control discount_amt input-sm" placeholder="Amount" value="0" required="" min="0" step="0.001" style="width: 90px"/></td>
                    <td>
                        <input type="hidden" id="isgst" name="isgst[]" class="isgst"  />
                        <input type="hidden" id="idvendor" name="idvendor[]" class="idvendor" value="<?php echo $model->idvendor ?>"/>
                        <input type="hidden" id="cgst" name="cgst[]" class="form-control input-sm cgst" value="<?php echo $model->cgst; ?>" readonly=""/>
                        <input type="hidden" id="sgst" name="sgst[]" class="form-control input-sm sgst" value="<?php echo $model->sgst; ?>" readonly=""/>
                        <input type="hidden" id="igst" name="igst[]" class="form-control input-sm igst" value="<?php echo $model->igst; ?>" readonly=""/>
                        <?php echo $model->igst ?>%
                    </td>
                    <td>
                        <input type="hidden" id="total_amt" name="total_amt[]" class="form-control total_amt input-sm" placeholder="Amount" value="<?php echo $model->mop ?>" required="" />
                        <span id="sptotal_amt" class="sptotal_amt" name="sptotal_amt[]"><?php echo $model->mop ?></span>
                    </td>
                    <td>
                        <a class="btn btn-sm gradient1 btn-warning remove" name="remove" id="remove"><i class="fa fa-trash-o fa-lg"></i></a>
                        <input type="hidden" name="rowid[]" id="rowid" class="rowid" value="<?php echo $model->id_stock ?>" />
                    </td>
                </tr>
                <?php }}}else{
                echo '0';
            }
        }else{ // IMEI/ SRNO
            $models = $this->Sale_model->ajax_stock_data_byimei_branch($imei, $idbranch);
            if(count($models)){
                foreach($models as $model){
                    if($model->idgodown != 1){
                        echo '3'; // Other that New Godown not accepted
                    }else{
                        if($model->dcprint != $is_dcprint && $is_dcprint != ''){
                            if($is_dcprint == 0){ 
                                echo '1'; // previous is dc product
                            }else{ 
                                echo '2'; // previous is dc invoice
                            }
                        }else{ ?>
                            <tr class="product_tr" id="m<?php echo $model->id_stock ?>">
                                <td>
                                    <?php echo $model->product_name; ?>
                                    <input type="hidden" id="idtype" class="form-control idtype" name="idtype[]" value="<?php echo $model->idproductcategory ?>" />
                                    <input type="hidden" id="idcategory" class="form-control idcategory" name="idcategory[]" value="<?php echo $model->idcategory ?>" />
                                    <input type="hidden" id="idbrand" class="form-control" name="idbrand[]" value="<?php echo $model->idbrand ?>" />
                                    <input type="hidden" id="idvariant" class="form-control idvariant" name="idvariant[]" value="<?php echo $model->idvariant ?>" />
                                    <input type="hidden" id="idmodel" class="form-control idmodel" name="idmodel[]" value="<?php echo $model->idmodel ?>" />
                                    <input type="hidden" id="idgodown" class="form-control idgodown" name="idgodown[]" value="<?php echo $model->idgodown ?>" />
                                    <input type="hidden" id="skutype" class="form-control skutype" name="skutype[]" value="<?php echo $model->idskutype ?>" />
                                    <input type="hidden" id="product_name" class="form-control product_name" name="product_name[]" value="<?php echo $model->product_name; ?>" />
                                    <input type="hidden" id="is_mop" class="form-control is_mop" name="is_mop[]" value="<?php echo $model->is_mop; ?>" />
                                    <input type="hidden" id="hsn" class="form-control hsn" name="hsn[]" value="<?php echo $model->hsn; ?>" />
                                    <input type="hidden" id="is_gst" class="form-control is_gst" name="is_gst[]" value="<?php echo $model->is_gst; ?>" />
                                    <small class="pull-right" style="color:#1b6caa;font-family: Kurale;">New Godown</small>
                                    <input type="hidden" id="dcprint" class="dcprint" name="dcprint[]" value="<?php echo $model->dcprint; ?>" />
                                </td>
                                <td>
                                    <input type="hidden" id="imei" name="imei[]" class="imei" value="<?php echo $imei ?>" />
                                    <?php echo $imei; ?>
                                </td>
                                <td>1</td>
                                <td><?php echo $model->mrp; ?></td>
                                <td><?php echo $model->mop; ?></td>
                                <td>
                                    <input type="hidden" id="read_skuprice<?php echo $skuvariant ?>" value="<?php echo $model->mop ?>" />
                                    <input type="hidden" id="readprice<?php echo $model->id_stock ?>" value="<?php echo $model->mop ?>" />
                                    <input type="hidden" id="landing" name="landing[]" class="landing" value="<?php echo $model->landing ?>" />
                                    <input type="hidden" id="mop" name="mop[]" class="mop" value="<?php echo $model->mop ?>" />
                                    <input type="hidden" id="mrp" name="mrp[]" class="mrp" value="<?php echo $model->mrp ?>" />
                                    <input type="hidden" id="salesman_price" name="salesman_price[]" class="salesman_price" value="<?php echo $model->salesman_price ?>" />
                                    <input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $model->mop ?>" min="<?php echo $model->landing ?>" step="0.001" style="width: 90px" max="<?php echo $model->mrp ?>" />
                                </td>
                                <td>
                                    <input type="hidden" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" value="1" style="width: 70px"/>
                                    <span id="spqty" class="spqty">1</span>
                                </td>
                                <td>
                                    <input type="hidden" id="basic" name="basic[]" class="basic" placeholder="Basic" readonly="" value="<?php echo $model->mop ?>"/>
                                    <span class="spbasic" id="spbasic" name="spbasic[]"><?php echo $model->mop ?></span>
                                </td>
                                    <td><input type="number" id="discount_amt" name="discount_amt[]" class="form-control discount_amt input-sm" placeholder="Amount" value="0" required="" min="0" step="0.001" style="width: 90px"/></td>
                                <td>
                                    <input type="hidden" id="isgst" name="isgst[]" class="isgst"/>
                                    <input type="hidden" id="idvendor" name="idvendor[]" class="idvendor" value="<?php echo $model->idvendor ?>"/>
                                    <input type="hidden" id="cgst" name="cgst[]" class="form-control input-sm cgst" value="<?php echo $model->cgst; ?>" readonly=""/>
                                    <input type="hidden" id="sgst" name="sgst[]" class="form-control input-sm sgst" value="<?php echo $model->sgst; ?>" readonly=""/>
                                    <input type="hidden" id="igst" name="igst[]" class="form-control input-sm igst" value="<?php echo $model->igst; ?>" readonly=""/>
                                    <?php echo $model->igst ?>%
                                </td>
                                <td>
                                    <input type="hidden" id="total_amt" name="total_amt[]" class="form-control total_amt input-sm" placeholder="Amount" value="<?php echo $model->mop ?>" required="" />
                                    <span id="sptotal_amt" class="sptotal_amt" name="sptotal_amt[]"><?php echo $model->mop ?></span>
                                </td>
                                <td>
                                    <a class="btn btn-sm gradient1 btn-warning remove" name="remove" id="remove"><i class="fa fa-trash-o fa-lg"></i></a>
                                    <input type="hidden" name="rowid[]" id="rowid" class="rowid" value="<?php echo $model->id_stock ?>" />
                                </td>
                            </tr>
                <?php }}}}else{
                die('0');
            }
        }
    }
    
    public function ajax_get_payment_mode_data_byidhead() {
        $head = $this->input->post('paymenthead');
        $headname = $this->input->post('headname');
        $payment_head = $this->General_model->get_payment_head_byid($head); 
        $payment_mode = $this->General_model->ajax_get_payment_mode_byhead($head); 
        $payment_attribute = $this->General_model->get_payment_head_has_attributes_byhead($head); ?>
        <div id="modes_block<?php echo $head ?>" class="modes_block modes_blockc<?php echo $head ?> thumbnail" style="margin-bottom: 5px; padding: 5px;">
            <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                <span style="font-size: 15px; font-family: Kurale"><?php echo $headname ?></span>
                <select class="form-control input-sm payment_type" name="payment_type[]">
                    <?php foreach ($payment_mode as $mode) { ?>
                    <option value="<?php echo $mode->id_paymentmode ?>"><?php echo $mode->payment_mode ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                Amount
                <input type="number" class="form-control input-sm amount" id="amount<?php echo $head ?>" name="amount[]" placeholder="Amount" value="0" min="1" required="" />
                <input type="hidden" class="idpaymenthead" name="idpaymenthead[]" value="<?php echo $head ?>" />
                <input type="hidden" class="headname" name="headname[]" value="<?php echo $headname ?>" />
                <input type="hidden" class="credit_type" name="credit_type[]" value="<?php echo $payment_head->credit_type ?>" />
            </div>
            <?php if($payment_head->tranxid_type == NULL){ ?>
            <div class="col-md-2 col-sm-3 hidden">
                <?php echo $payment_head->tranxid_type ?>
                <input type="text" class="form-control input-sm tranxid" id="tranxid<?php echo $head ?>" name="tranxid[]" placeholder="<?php echo $payment_head->tranxid_type ?>" value="<?php echo NULL; ?>" />
            </div>
            <?php }else{ ?>
            <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                <?php echo $payment_head->tranxid_type ?>
                <input type="text" class="form-control input-sm tranxid" id="tranxid<?php echo $head ?>" name="tranxid[]" placeholder="<?php echo $payment_head->tranxid_type ?>" required="" />
            </div>
            <?php } ?>
            <?php foreach ($payment_attribute as $attribute){ ?>
            <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                <?php echo $attribute->attribute_name ?>
                <input type="text" class="form-control input-sm headattr" name="headattr[<?php echo $head ?>][<?php echo $attribute->id_payment_attribute ?>][]" placeholder="<?php echo $attribute->attribute_name ?>" required="" />
            </div>
            <?php } if($payment_head->multiple_rows){ ?>
            <div class="col-md-2 col-sm-3 pull-right" style="padding: 0;">
                <center>Add More<br>
                    <a class="btn btn-primary btn-floating waves-effect add_more_payment" id="add_more_payment"><i class="fa fa-plus"></i></a>
                </center>
            </div>
            <?php } ?>
            <div class="clearfix"></div>
        </div><div class="clearfix"></div> <?php
    }
    
    public function save_product_replace_return() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $this->load->model('Transfer_model');
        $this->load->model('Inward_model');
        $this->load->model('Purchase_model');
        $total_return_cash = 0;
        $this->db->trans_begin();
        $inv_no = $this->input->post('inv_no');
        $idbranch = $this->input->post('idbranch');
        $date = date('Y-m-d');
        $sales_return_by = $this->input->post('sales_return_by');
        $id_sale = $this->input->post('id_sale');
        $return_date = date('Y-m-d H:i:s');
        $sales_return_invid = $this->input->post('sales_return_invid');
        $invid = $this->Sale_model->get_invoice_no_by_branch($idbranch); 
        $y = date('y', mktime(0, 0, 0, 9 + date('m')));
        $y1 = $y - 1;
        $next_srinv_no =  $invid->sales_return_invoice_no + 1;
        $sales_return_invid = 'SR'.$y1.$y . '/'. $invid->branch_code . '/' . sprintf('%04d', $next_srinv_no);
        $cust_fname = $this->input->post('cust_fname');
        $cust_lname = $this->input->post('cust_lname');
        // save sales return
        $selected_sale_products = $this->input->post('chk_return');
        $overall_total_amt=0;$overall_discount_amt=0; $overall_basic=0;
        for($i = 0; $i < count($selected_sale_products); $i++){
            $total_amt=0;$total_discount=0;$total_basic=0;
            $product_id = $selected_sale_products[$i];
            if($this->input->post('skutype'.$product_id) == 4){
                $qty=0;$total_qty=0;$row_total=0;$row_discount_amt=0;$row_basic=0;$single_total=0;$single_discount_amt_total=0;$single_basic_total=0;
                $qty = $this->input->post('selected_qty'.$product_id);
                $total_qty = $this->input->post('qty'.$product_id);
                $row_total = $this->input->post('total_amt'.$product_id);
                $row_basic = $this->input->post('basic'.$product_id);
                $single_total = $row_total / $total_qty;
                $single_basic_total = $row_basic / $total_qty;
                $total_amt = $single_total * $qty;
                $total_discount = $single_discount_amt_total * $qty;
                $total_basic = $single_basic_total * $qty;
            }else{
                $total_amt = $this->input->post('total_amt'.$product_id);
                $total_discount = $this->input->post('discount_amt'.$product_id);
                $total_basic = $this->input->post('basic'.$product_id);
            }
            $overall_total_amt += $total_amt;
            $overall_discount_amt += $total_discount;
            $overall_basic += $total_basic;
        }
        $data = array(
            'date' => $date,
            'idsale' => $id_sale,
            'sales_return_invid' => $sales_return_invid,
            'sales_return_type' => 2, // product return
            'inv_no' => $inv_no,
            'inv_date' => $this->input->post('inv_date'),
            'idbranch' => $idbranch,
            'idcustomer' => $this->input->post('idcustomer'),
            'sales_return_approved_by'=> $this->input->post('sales_return_approved_by'),
            'sales_return_reason'=> $this->input->post('sales_return_reason'),
            'idsalesperson' => $this->input->post('idsalesperson'),
            'final_total' => $overall_total_amt,
            'discount_total' => $overall_discount_amt,
            'basic_total' => $overall_basic,
            'sales_return_by' => $sales_return_by,
        );
        // save sales return product
        $idsalereturn = $this->Sales_return_model->save_sales_return($data);
        // update sale product
        $saleupdate = array(
            'sales_return_type' => 2, // product return
            'sales_return_invid' => $sales_return_invid,
            'sales_return_by' => $sales_return_by,
            'sales_return_date' => $return_date,
        );
        //// update query at bottom line no 1782
//        $imei_history[] = array('nest'=>array());
        for($i = 0; $i < count($selected_sale_products); $i++){
            $product_id = $selected_sale_products[$i];
            $qty = $this->input->post('selected_qty'.$product_id);
            $previous_return_qty = $this->input->post('sale_return_qty'.$product_id);
            $idmodel = $this->input->post('idmodel'.$product_id);
            $total_qty = $this->input->post('qty'.$product_id);
//            die($total_qty);
            // update sale product
            $saleproductupdate = array(
                'sales_return_type' => 1, // cash return
                'sales_return_invid' => $sales_return_invid,
                'sales_return_by' => $sales_return_by,
                'sales_return_date' => $return_date,
                'sale_return_qty' => $qty + $previous_return_qty,
            );
            $this->Sale_model->update_sale_product_byidsaleproduct($product_id, $saleproductupdate);
            if($this->input->post('skutype'.$product_id) == 4){
                $row_total=0;$total_amt=0;$row_discount_amt=0;$row_basic=0;$row_taxable_amt=0;$row_cgst_amt=0;$row_sgst_amt=0;$row_igst_amt=0;$row_tax=0;
                $single_total=0;$single_discount_amt_total=0;$single_basic_total=0;$single_taxable_total=0;$single_cgst_total=0;$single_sgst_total=0;$single_igst_total=0;$single_tax_total=0;
                $total_amt=0;$total_discount=0;$total_basic=0;$total_taxable=0;$total_cgst=0;$total_sgst=0;$total_igst=0;$total_tax=0;
                
                $row_total = $this->input->post('total_amt'.$product_id);
                $row_discount_amt = $this->input->post('discount_amt'.$product_id);
                $row_basic = $this->input->post('basic'.$product_id);
                $row_taxable_amt = $this->input->post('taxable'.$product_id);
                $row_cgst_amt = $this->input->post('cgst_amt'.$product_id);
                $row_sgst_amt = $this->input->post('sgst_amt'.$product_id);
                $row_igst_amt = $this->input->post('igst_amt'.$product_id);
                $row_tax = $this->input->post('tax'.$product_id);
                // Single quantity amount
                $single_total = $row_total / $total_qty;
                $single_discount_amt_total = $row_discount_amt / $total_qty;
                $single_basic_total = ($row_basic / $total_qty);
                $single_taxable_total = $row_taxable_amt / $total_qty;
                $single_cgst_total = $row_cgst_amt / $total_qty;
                $single_sgst_total = $row_sgst_amt / $total_qty;
                $single_igst_total = $row_igst_amt / $total_qty;
                $single_tax_total = $row_tax / $total_qty;
                // Selected quantity amount
                $total_amt = $single_total * $qty;
                $total_discount = $single_discount_amt_total * $qty;
                $total_basic = $single_basic_total * $qty;
                $total_taxable = $single_taxable_total * $qty;
                $total_cgst = $single_cgst_total * $qty;
                $total_sgst = $single_sgst_total * $qty;
                $total_igst = $single_igst_total * $qty;
                $total_tax = $single_tax_total * $qty;
//                die(''.$total_basic);
                $sale_product = array(
                    'date' => $date,
                    'sales_return_invid' => $sales_return_invid,
                    'sales_return_type' => 1, // cash return
                    'idskutype' => $this->input->post('skutype'.$product_id),
                    'idproductcategory' => $this->input->post('idtype'.$product_id),
                    'idcategory' => $this->input->post('idcategory'.$product_id),
                    'idgodown' => $this->input->post('idgodown'.$product_id),
                    'idmodel' => $idmodel,
                    'idvariant' => $this->input->post('idvariant'.$product_id),
                    'idbranch' => $idbranch,
                    'idbrand' => $this->input->post('idbrand'.$product_id),
                    'sales_return_by' => $sales_return_by,
                    'idsales_return' => $idsalereturn,
                    'product_name' => $this->input->post('product_name'.$product_id),
                    'price' => $this->input->post('price'.$product_id),
                    'inv_no' => $inv_no,
                    'qty' => $qty,
                    'discount_amt' => $total_discount,
                    'basic' => $total_basic,
                    'taxable_amt' => $total_taxable,
                    'cgst_per' => $this->input->post('cgst'.$product_id),
                    'sgst_per' => $this->input->post('sgst'.$product_id),
                    'igst_per' => $this->input->post('igst'.$product_id),
                    'cgst_amt' => $total_cgst,
                    'sgst_amt' => $total_sgst,
                    'igst_amt' => $total_igst,
                    'tax' => $total_tax,
                    'total_amount' => $total_amt,
                );
//                die('<pre>'.print_r($sale_product,1).'</pre>');
                $idsalereturnproduct = $this->Sales_return_model->save_sales_return_product($sale_product);
                $branchstock = $this->Transfer_model->get_branchstock_byidmodel_skutype_godown($this->input->post('idvariant'.$product_id),4,$idbranch,$this->input->post('idgodown'.$product_id));
                if(count($branchstock) == 1){
                    foreach ($branchstock as $brstock){
                        $qty = $brstock->qty + $qty;
                        $this->Inward_model->update_stock_byid($brstock->id_stock, $qty);
                    }
                }else{
                    $inward_stock = array(
                        'date' => $date,
                        'idmodel' => $idmodel,
                        'created_by' => $sales_return_by,
                        'sales_return_by' => $sales_return_by,
                        'return_date' => $return_date,
                        'sales_return_type' => 1, // cash return
                        'sale_date' => $this->input->post('inv_date'),
                        'product_name' => $this->input->post('product_name'.$product_id),
                        'idvariant' => $this->input->post('idvariant'.$product_id),
                        'idskutype' => 4,
                        'idproductcategory' => $this->input->post('idtype'.$product_id),
                        'idcategory' => $this->input->post('idcategory'.$product_id),
                        'idbrand' => $this->input->post('idbrand'.$product_id),
                        'is_gst'   => $this->input->post('is_gst'.$product_id),
                        'idvendor' => $this->input->post('retidvendor'.$product_id),
                        'idgodown' => $this->input->post('idgodown'.$product_id),
                        'idbranch' => $idbranch,
                    );
                    $this->Inward_model->save_stock($inward_stock);
                }
                $total_return_cash += $total_amt;
            }else{
                $imeino = $this->input->post('imei_no'.$product_id);
                $sale_product = array(
                    'sales_return_type' => 1, // cash return
                    'date' => $date,
                    'imei_no' => $imeino,
                    'sales_return_invid' => $sales_return_invid,
                    'idskutype' => $this->input->post('skutype'.$product_id),
                    'idproductcategory' => $this->input->post('idtype'.$product_id),
                    'idcategory' => $this->input->post('idcategory'.$product_id),
                    'idgodown' => $this->input->post('idgodown'.$product_id),
                    'idvariant' => $this->input->post('idvariant'.$product_id),
                    'idmodel' => $idmodel,
                    'idbranch' => $idbranch,
                    'idbrand' => $this->input->post('idbrand'.$product_id),
                    'sales_return_by' => $sales_return_by,
                    'idsales_return' => $idsalereturn,
                    'product_name' => $this->input->post('product_name'.$product_id),
                    'price' => $this->input->post('price'.$product_id),
                    'inv_no' => $inv_no,
                    'qty' => 1,
                    'discount_amt' => $this->input->post('discount_amt'.$product_id),
                    'basic' => $this->input->post('basic'.$product_id),
                    'taxable_amt' => $this->input->post('taxable'.$product_id),
                    'cgst_per' => $this->input->post('cgst'.$product_id),
                    'sgst_per' => $this->input->post('sgst'.$product_id),
                    'igst_per' => $this->input->post('igst'.$product_id),
                    'cgst_amt' => $this->input->post('cgst_amt'.$product_id),
                    'sgst_amt' => $this->input->post('sgst_amt'.$product_id),
                    'igst_amt' => $this->input->post('igst_amt'.$product_id),
                    'tax' => $this->input->post('tax'.$product_id),
                    'taxable_amt' => $this->input->post('taxable'.$product_id),
                    'total_amount' => $this->input->post('total_amt'.$product_id),
                );
                $idsalereturnproduct = $this->Sales_return_model->save_sales_return_product($sale_product);
                $total_return_cash += $this->input->post('total_amt'.$product_id);
//              add into stock remaining    
                $inward_stock[$i] = array(
                    'date' => $date,
                    'imei_no' => $imeino,
                    'idmodel' => $idmodel,
                    'created_by' => $sales_return_by,
                    'sales_return_by' => $sales_return_by,
                    'return_date' => $return_date,
                    'sales_return_type' => 1, // cash return
                    'sale_date' => $this->input->post('inv_date'),
                    'idvariant' => $this->input->post('idvariant'.$product_id),
                    'product_name' => $this->input->post('product_name'.$product_id),
                    'idskutype' => $this->input->post('skutype'.$product_id),
                    'idproductcategory' => $this->input->post('idtype'.$product_id),
                    'idcategory' => $this->input->post('idcategory'.$product_id),
                    'idbrand' => $this->input->post('idbrand'.$product_id),
                    'is_gst'   => $this->input->post('is_gst'.$product_id),
                    'idvendor' => $this->input->post('retidvendor'.$product_id),
                    'idgodown' => $this->input->post('idgodown'.$product_id),
                    'idbranch' => $idbranch,
                );
                $this->Inward_model->save_stock($inward_stock[$i]);
                $imei_history[]=array(
                    'imei_no' => $imeino,
                    'entry_type' => 'Sales Return - Cash',
                    'entry_time' => $return_date,
                    'date' => $date,
                    'idbranch' => $idbranch,
                    'idgodown' => $this->input->post('idgodown'.$product_id),
                    'idvariant' => $this->input->post('idvariant'.$product_id),
                    'model_variant_full_name' => $this->input->post('product_name'.$product_id),
                    'idimei_details_link' => 7, // Sales Return from imei_details_link table
                    'idlink' => $idsalereturn,
                );
            }
        }
        $srpayment = array(
            'date' => $date,
            'inv_no' => $sales_return_invid,
            'entry_type' => 2,
            'idbranch' => $idbranch,
            'idtable' => $idsalereturn,
            'table_name' => 'sales_return',
            'idcustomer' => $this->input->post('idcustomer'),
            'customer_fname' => $cust_fname,
            'customer_lname' => $cust_fname,
            'customer_gst' => $this->input->post('gst_no'),
            'amount' => -$total_return_cash,
        );
        $this->Sale_model->save_daybook_cash_payment($srpayment);
        
        
        
        
        
        
        
        // save sale       
//        $idbranch = $this->input->post('idbranch');
        $dcprint = $this->input->post('dcprint');
        $sinvid = $invid->invoice_no + 1; 
        if($dcprint[0] == 0){
            $sinv_no = $y1 .'-'. $y . '/'. $invid->branch_code . '/' . sprintf('%05d', $sinvid);
        }else{
            $sinv_no = 'DC'.$y1 .'-'. $y . '/'. $invid->branch_code . '/' . sprintf('%05d', $sinvid);
        }
        $datetime = date('Y-m-d H:i:s');
        $idstate = $this->input->post('idstate');
        $idcustomer = $this->input->post('idcustomer');
        $cust_idstate = $this->input->post('cust_idstate');
        $cust_pincode = $this->input->post('cust_pincode');
        $gst_type = 0; //cgst
        if($idstate != $cust_idstate){
            $gst_type = 1; //igst
        }
        $data = array(
            'date' => $date,
            'inv_no' => $sinv_no,
            'idbranch' => $idbranch,
            'idcustomer' => $idcustomer,
            'customer_fname' => $cust_fname,
            'customer_lname' => $cust_lname,
            'customer_idstate' => $cust_idstate,
            'customer_pincode' => $cust_pincode,
            'customer_contact' => $this->input->post('mobile'),
            'customer_address' => $this->input->post('address'),
            'customer_gst' => $this->input->post('gst_no'),
            'idsalesperson' => $this->input->post('idsalesperson'),
            'basic_total' => $this->input->post('gross_total'),
            'discount_total' => $this->input->post('final_discount'),
            'final_total' => $this->input->post('final_total'),
            'gst_type' => $gst_type,
            'created_by' => $sales_return_by,
            'remark' => $this->input->post('remark'),
            'entry_time' => $datetime,
            'dcprint' => $dcprint[0],
        );
        $idsale = $this->Sale_model->save_sale($data);
        // Payment
        $idpaymenthead = $this->input->post('idpaymenthead'); // buyback1,2,
        $credittype = $this->input->post('credit_type');
        $amount = $this->input->post('amount');
        $payment_type = $this->input->post('payment_type');
        $tranxid = $this->input->post('tranxid');
        $headattr = $this->input->post('headattr');
        $vin=array();
        foreach ($headattr as $idpayment_head => $attributedata){
            $payment_attribute = $this->General_model->get_payment_head_has_attributes_byhead($idpayment_head);
            $mode_count=count($attributedata[$payment_attribute[0]->id_payment_attribute]);
            $attr_value=array();
            for($m=0;$m<$mode_count;$m++){
                foreach ($payment_attribute as $attr){
                    $attr_value[$attr->column_name] = $attributedata[$attr->id_payment_attribute][$m];
                }
            }
            for($j=0; $j < count($idpaymenthead); $j++){
                if($idpaymenthead[$j] == $idpayment_head){
                    $vin[$j]=$attr_value;
                }
            }
        }
        $parr=array();
        for($j=0; $j < count($idpaymenthead); $j++){
            if($idpaymenthead[$j] == 1){
                $srpayment = array(
                    'date' => $date,
                    'inv_no' => $inv_no,
                    'entry_type' => 1,
                    'idbranch' => $idbranch,
                    'idtable' => $idsale,
                    'table_name' => 'sale',
                    'idcustomer' => $idcustomer,
                    'customer_fname' => $cust_fname,
                    'customer_lname' => $cust_lname,
                    'customer_gst' => $this->input->post('gst_no'),
                    'amount' => $amount[$j],
                );
                $this->Sale_model->save_daybook_cash_payment($srpayment);
            }
            $payment = array(
                'date' => $date,
                'idsale' => $idsale,
                'amount' => $amount[$j],
                'idpayment_head' => $idpaymenthead[$j],
                'idpayment_mode' => $payment_type[$j],
                'transaction_id' => $tranxid[$j],
                'inv_no' => $sinv_no,
                'idcustomer' => $idcustomer,
                'idbranch' => $idbranch,
                'created_by' => $sales_return_by,
                'entry_time' => $datetime,
            );
            if(isset($vin[$j])>0){
                $payment = array_merge($payment, $vin[$j]); 
            }
            $id_sale_payment = $this->Sale_model->save_sale_payment($payment);
            if($credittype[$j] == 0){
                $npayment[$j] = array(
                    'idsale_payment' => $id_sale_payment,
                    'inv_no' => $sinv_no,
                    'idsale' => $idsale,
                    'date' => $date,
                    'idcustomer' => $idcustomer,
                    'idbranch' => $idbranch,
                    'amount' => $amount[$j],
                    'idpayment_head' => $idpaymenthead[$j],
                    'idpayment_mode' => $payment_type[$j],
                    'transaction_id' => $tranxid[$j],
                    'created_by' => $sales_return_by,
                    'entry_time' => $datetime,
                );
                $this->Sale_model->save_payment_reconciliation($npayment[$j]);
            }
            $parr[] = $payment;
        }
//        die('<pre>'.print_r($parr,1).'</pre>');
        //Sale_product
        $idtype = $this->input->post('idtype');
        $idcategory = $this->input->post('idcategory');
        $idbrand = $this->input->post('idbrand');
        $idmodel = $this->input->post('idmodel');
        $idvariant = $this->input->post('idvariant');
        $idgodown = $this->input->post('idgodown');
        $skutype = $this->input->post('skutype');
        $product_name = $this->input->post('product_name');
        $imei = $this->input->post('imei');
        $price = $this->input->post('price');
        $basic = $this->input->post('basic');
        $discount_amt = $this->input->post('discount_amt');
        $total_amt = $this->input->post('total_amt');
        $landing = $this->input->post('landing');
        $mrp = $this->input->post('mrp');
        $mop = $this->input->post('mop');
        $salesman_price = $this->input->post('salesman_price');
        $qty = $this->input->post('qty');
        $rowid = $this->input->post('rowid');
        $is_gst = $this->input->post('is_gst');
        $idvendor = $this->input->post('idvendor');
        $hsn = $this->input->post('hsn'); 
        $is_mop = $this->input->post('is_mop'); // price on invoice
//        $imei_history[] = array('nest'=>array());
        for($i = 0; $i < count($idvariant); $i++){
            $cgst = 0; $sgst = 0; $igst = 0;
            if($gst_type == 1){
                $igst = $this->input->post('igst['.$i.']');
            }else{
                $cgst = $this->input->post('cgst['.$i.']');
                $sgst = $this->input->post('sgst['.$i.']');
            }
            $sale_product[$i] = array(
                'date' => $date,
                'idsale' => $idsale,
                'idmodel' => $idmodel[$i],
                'idvariant' => $idvariant[$i],
                'imei_no' => $imei[$i],
                'hsn' => $hsn[$i],
                'idskutype' => $skutype[$i],
                'idgodown' => $idgodown[$i],
                'idproductcategory' => $idtype[$i],
                'idcategory' => $idcategory[$i],
                'idbrand' => $idbrand[$i],
                'product_name' => $product_name[$i],
                'price' => $price[$i],
                'landing' => $landing[$i],
                'mrp' => $mrp[$i],
                'mop' => $mop[$i],
                'salesman_price' => $salesman_price[$i],
                'inv_no' => $sinv_no,
                'qty' => $qty[$i],
                'idbranch' => $idbranch,
                'discount_amt' => $discount_amt[$i],
                'is_gst' => $is_gst[$i],
                'is_mop' => $is_mop[$i],
                'basic' => $basic[$i],
                'idvendor' => $idvendor[$i],
                'cgst_per' => $cgst,
                'sgst_per' => $sgst,
                'igst_per' => $igst,
                'total_amount' => $total_amt[$i],
                'entry_time' => $datetime,
            );
            $idsaleproduct = $this->Sale_model->save_sale_product($sale_product[$i]);
            if($skutype[$i] == 4){ //qunatity
                $this->Sale_model->minus_stock_byidstock($rowid[$i], $qty[$i]);
            }else{
                $this->Purchase_model->delete_stock_byidstock($rowid[$i]);
                // IMEI History
                $imei_history[]=array(
                    'imei_no' => $imei[$i],
                    'entry_type' => 'Sale',
                    'entry_time' => $datetime,
                    'date' => $date,
                    'idbranch' => $idbranch,
                    'idgodown' => $idgodown[$i],
                    'idvariant' => $idvariant[$i],
                    'model_variant_full_name' => $product_name[$i],
                    'idimei_details_link' => 4, // Sale from imei_details_link table
                    'idlink' => $idsale,
//                    'imei_latitude' => $this->input->post('cust_latitude'),
//                    'imei_longitude' => $this->input->post('cust_longitude'),
                );
            }
        }
//        die('<pre>'.print_r($imei_history,1).'</pre>');
        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
        
        // BFL integration
        if($this->input->post('bfl_do_id')){
            $bfl_data = array(
                'do_id' => $this->input->post('bfl_do_id'),
                'idsale' => $idsale,
                'idsale_product' => $idsaleproduct,
                'bfl_brand' => $this->input->post('bfl_brand'),
                'bfl_model' => $this->input->post('bfl_model'),
                'bfl_srno' => $this->input->post('bfl_srno'),
                'idcustomer' => $idcustomer,
                'mobile' => $this->input->post('mobile'),
                'customer_name' => $this->input->post('bfl_customer'),
                'customer_gst' => $this->input->post('gst_no'),
                'scheme_code' => $this->input->post('scheme_code'),
                'scheme' => $this->input->post('scheme'),
                'mop' => $this->input->post('bfl_mop'),
                'downpayment' => $this->input->post('bfl_downpayment'),
                'loan' => $this->input->post('bfl_loan'),
                'emi_amount' => $this->input->post('bfl_emi_amount'),
                'tenure' => $this->input->post('bfl_tenure'),
                'bfl_remark' => $this->input->post('bfl_remark'),
                'entry_time' => $datetime,
            );
            $this->Sale_model->save_bfl($bfl_data);			
        }
        


        // Update Sale from sales return
        $this->Sale_model->update_sale($id_sale, $saleupdate);
        $invoice_data = array(
            'invoice_no' => $sinvid,
            'sales_return_invoice_no' => $next_srinv_no );
        $this->General_model->edit_db_branch($idbranch, $invoice_data);
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Invoice billing is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Invoice bill generated');
        }
        die('<pre>'.print_r($_POST,1).'</pre>');
        if($dcprint[0] == 0){
            return redirect('Sale/invoice_print/'.$idsale);
        }else{
            return redirect('Sale/dc_print/'.$idsale);
        }
    }
    
    
    
    
//    public function save_product_replace_return1() {
////        die('<pre>'.print_r($_POST,1).'</pre>');
//        $this->load->model('Transfer_model');
//        $this->load->model('Inward_model');
//        $this->load->model('Purchase_model');
//        $total_return_cash = 0;
//        $this->db->trans_begin();
//        $inv_no = $this->input->post('inv_no');
//        $idbranch = $this->input->post('idbranch');
//        $date = date('Y-m-d');
//        $sales_return_by = $this->input->post('sales_return_by');
//        $id_sale = $this->input->post('id_sale');
//        $return_date = date('Y-m-d H:i:s');
//        $sales_return_invid = $this->input->post('sales_return_invid');
//        $invid = $this->Sale_model->get_invoice_no_by_branch($idbranch); 
//        $y = date('y', mktime(0, 0, 0, 9 + date('m')));
//        $y1 = $y - 1;
//        $next_srinv_no =  $invid->sales_return_invoice_no + 1;
//        $sales_return_invid = 'SR'.$y1.$y . '/'. $invid->branch_code . '/' . sprintf('%04d', $next_srinv_no);
//        $cust_fname = $this->input->post('cust_fname');
//        $cust_lname = $this->input->post('cust_lname');
//        // save sales return
//        $selected_sale_products = $this->input->post('chk_return');
//        $overall_total_amt=0;$overall_discount_amt=0; $overall_basic=0;
//        for($i = 0; $i < count($selected_sale_products); $i++){
//            $total_amt=0;$total_discount=0;$total_basic=0;
//            $product_id = $selected_sale_products[$i];
//            if($this->input->post('skutype'.$product_id) == 4){
//                $qty=0;$total_qty=0;$row_total=0;$row_discount_amt=0;$row_basic=0;$single_total=0;$single_discount_amt_total=0;$single_basic_total=0;
//                $qty = $this->input->post('selected_qty'.$product_id);
//                $total_qty = $this->input->post('qty'.$product_id);
//                $row_total = $this->input->post('total_amt'.$product_id);
//                $row_basic = $this->input->post('basic'.$product_id);
//                $single_total = $row_total / $total_qty;
//                $single_basic_total = $row_basic / $total_qty;
//                $total_amt = $single_total * $qty;
//                $total_discount = $single_discount_amt_total * $qty;
//                $total_basic = $single_basic_total * $qty;
//            }else{
//                $total_amt = $this->input->post('total_amt'.$product_id);
//                $total_discount = $this->input->post('discount_amt'.$product_id);
//                $total_basic = $this->input->post('basic'.$product_id);
//            }
//            $overall_total_amt += $total_amt;
//            $overall_discount_amt += $total_discount;
//            $overall_basic += $total_basic;
//        }
//        $data = array(
//            'date' => $date,
//            'idsale' => $id_sale,
//            'sales_return_invid' => $sales_return_invid,
//            'sales_return_type' => 2, // product return
//            'inv_no' => $inv_no,
//            'inv_date' => $this->input->post('inv_date'),
//            'idbranch' => $idbranch,
//            'idcustomer' => $this->input->post('idcustomer'),
//            'sales_return_approved_by'=> $this->input->post('sales_return_approved_by'),
//            'sales_return_reason'=> $this->input->post('sales_return_reason'),
//            'idsalesperson' => $this->input->post('idsalesperson'),
//            'final_total' => $overall_total_amt,
//            'discount_total' => $overall_discount_amt,
//            'basic_total' => $overall_basic,
//            'sales_return_by' => $sales_return_by,
//        );
//        // save sales return product
//        $idsalereturn = $this->Sales_return_model->save_sales_return($data);
//        // update sale product
//        $saleupdate = array(
//            'sales_return_type' => 2, // product return
//            'sales_return_invid' => $sales_return_invid,
//            'sales_return_by' => $sales_return_by,
//            'sales_return_date' => $return_date,
//        );
//        //// update query at bottom line no 1782
////        $imei_history[] = array('nest'=>array());
//        for($i = 0; $i < count($selected_sale_products); $i++){
//            $product_id = $selected_sale_products[$i];
//            $qty = $this->input->post('selected_qty'.$product_id);
//            $previous_return_qty = $this->input->post('sale_return_qty'.$product_id);
//            $idmodel = $this->input->post('idmodel'.$product_id);
//            $total_qty = $this->input->post('qty'.$product_id);
////            die($total_qty);
//            // update sale product
//            $saleproductupdate = array(
//                'sales_return_type' => 1, // cash return
//                'sales_return_invid' => $sales_return_invid,
//                'sales_return_by' => $sales_return_by,
//                'sales_return_date' => $return_date,
//                'sale_return_qty' => $qty + $previous_return_qty,
//            );
//            $this->Sale_model->update_sale_product_byidsaleproduct($product_id, $saleproductupdate);
//            if($this->input->post('skutype'.$product_id) == 4){
//                $row_total=0;$total_amt=0;$row_discount_amt=0;$row_basic=0;$row_taxable_amt=0;$row_cgst_amt=0;$row_sgst_amt=0;$row_igst_amt=0;$row_tax=0;
//                $single_total=0;$single_discount_amt_total=0;$single_basic_total=0;$single_taxable_total=0;$single_cgst_total=0;$single_sgst_total=0;$single_igst_total=0;$single_tax_total=0;
//                $total_amt=0;$total_discount=0;$total_basic=0;$total_taxable=0;$total_cgst=0;$total_sgst=0;$total_igst=0;$total_tax=0;
//                
//                $row_total = $this->input->post('total_amt'.$product_id);
//                $row_discount_amt = $this->input->post('discount_amt'.$product_id);
//                $row_basic = $this->input->post('basic'.$product_id);
//                $row_taxable_amt = $this->input->post('taxable'.$product_id);
//                $row_cgst_amt = $this->input->post('cgst_amt'.$product_id);
//                $row_sgst_amt = $this->input->post('sgst_amt'.$product_id);
//                $row_igst_amt = $this->input->post('igst_amt'.$product_id);
//                $row_tax = $this->input->post('tax'.$product_id);
//                // Single quantity amount
//                $single_total = $row_total / $total_qty;
//                $single_discount_amt_total = $row_discount_amt / $total_qty;
//                $single_basic_total = ($row_basic / $total_qty);
//                $single_taxable_total = $row_taxable_amt / $total_qty;
//                $single_cgst_total = $row_cgst_amt / $total_qty;
//                $single_sgst_total = $row_sgst_amt / $total_qty;
//                $single_igst_total = $row_igst_amt / $total_qty;
//                $single_tax_total = $row_tax / $total_qty;
//                // Selected quantity amount
//                $total_amt = $single_total * $qty;
//                $total_discount = $single_discount_amt_total * $qty;
//                $total_basic = $single_basic_total * $qty;
//                $total_taxable = $single_taxable_total * $qty;
//                $total_cgst = $single_cgst_total * $qty;
//                $total_sgst = $single_sgst_total * $qty;
//                $total_igst = $single_igst_total * $qty;
//                $total_tax = $single_tax_total * $qty;
////                die(''.$total_basic);
//                $sale_product = array(
//                    'date' => $date,
//                    'sales_return_invid' => $sales_return_invid,
//                    'sales_return_type' => 1, // cash return
//                    'idskutype' => $this->input->post('skutype'.$product_id),
//                    'idproductcategory' => $this->input->post('idtype'.$product_id),
//                    'idcategory' => $this->input->post('idcategory'.$product_id),
//                    'idgodown' => $this->input->post('idgodown'.$product_id),
//                    'idmodel' => $idmodel,
//                    'idvariant' => $this->input->post('idvariant'.$product_id),
//                    'idbranch' => $idbranch,
//                    'idbrand' => $this->input->post('idbrand'.$product_id),
//                    'sales_return_by' => $sales_return_by,
//                    'idsales_return' => $idsalereturn,
//                    'product_name' => $this->input->post('product_name'.$product_id),
//                    'price' => $this->input->post('price'.$product_id),
//                    'inv_no' => $inv_no,
//                    'qty' => $qty,
//                    'discount_amt' => $total_discount,
//                    'basic' => $total_basic,
//                    'taxable_amt' => $total_taxable,
//                    'cgst_per' => $this->input->post('cgst'.$product_id),
//                    'sgst_per' => $this->input->post('sgst'.$product_id),
//                    'igst_per' => $this->input->post('igst'.$product_id),
//                    'cgst_amt' => $total_cgst,
//                    'sgst_amt' => $total_sgst,
//                    'igst_amt' => $total_igst,
//                    'tax' => $total_tax,
//                    'total_amount' => $total_amt,
//                );
////                die('<pre>'.print_r($sale_product,1).'</pre>');
//                $idsalereturnproduct = $this->Sales_return_model->save_sales_return_product($sale_product);
//                $branchstock = $this->Transfer_model->get_branchstock_byidmodel_skutype_godown($this->input->post('idvariant'.$product_id),4,$idbranch,$this->input->post('idgodown'.$product_id));
//                if(count($branchstock) == 1){
//                    foreach ($branchstock as $brstock){
//                        $qty = $brstock->qty + $qty;
//                        $this->Inward_model->update_stock_byid($brstock->id_stock, $qty);
//                    }
//                }else{
//                    $inward_stock = array(
//                        'date' => $date,
//                        'idmodel' => $idmodel,
//                        'created_by' => $sales_return_by,
//                        'sales_return_by' => $sales_return_by,
//                        'return_date' => $return_date,
//                        'sales_return_type' => 1, // cash return
//                        'sale_date' => $this->input->post('inv_date'),
//                        'product_name' => $this->input->post('product_name'.$product_id),
//                        'idvariant' => $this->input->post('idvariant'.$product_id),
//                        'idskutype' => 4,
//                        'idproductcategory' => $this->input->post('idtype'.$product_id),
//                        'idcategory' => $this->input->post('idcategory'.$product_id),
//                        'idbrand' => $this->input->post('idbrand'.$product_id),
//                        'is_gst'   => $this->input->post('is_gst'.$product_id),
//                        'idvendor' => $this->input->post('retidvendor'.$product_id),
//                        'idgodown' => $this->input->post('idgodown'.$product_id),
//                        'idbranch' => $idbranch,
//                    );
//                    $this->Inward_model->save_stock($inward_stock);
//                }
//                $total_return_cash += $total_amt;
//            }else{
//                $imeino = $this->input->post('imei_no'.$product_id);
//                $sale_product = array(
//                    'sales_return_type' => 1, // cash return
//                    'date' => $date,
//                    'imei_no' => $imeino,
//                    'sales_return_invid' => $sales_return_invid,
//                    'idskutype' => $this->input->post('skutype'.$product_id),
//                    'idproductcategory' => $this->input->post('idtype'.$product_id),
//                    'idcategory' => $this->input->post('idcategory'.$product_id),
//                    'idgodown' => $this->input->post('idgodown'.$product_id),
//                    'idvariant' => $this->input->post('idvariant'.$product_id),
//                    'idmodel' => $idmodel,
//                    'idbranch' => $idbranch,
//                    'idbrand' => $this->input->post('idbrand'.$product_id),
//                    'sales_return_by' => $sales_return_by,
//                    'idsales_return' => $idsalereturn,
//                    'product_name' => $this->input->post('product_name'.$product_id),
//                    'price' => $this->input->post('price'.$product_id),
//                    'inv_no' => $inv_no,
//                    'qty' => 1,
//                    'discount_amt' => $this->input->post('discount_amt'.$product_id),
//                    'basic' => $this->input->post('basic'.$product_id),
//                    'taxable_amt' => $this->input->post('taxable'.$product_id),
//                    'cgst_per' => $this->input->post('cgst'.$product_id),
//                    'sgst_per' => $this->input->post('sgst'.$product_id),
//                    'igst_per' => $this->input->post('igst'.$product_id),
//                    'cgst_amt' => $this->input->post('cgst_amt'.$product_id),
//                    'sgst_amt' => $this->input->post('sgst_amt'.$product_id),
//                    'igst_amt' => $this->input->post('igst_amt'.$product_id),
//                    'tax' => $this->input->post('tax'.$product_id),
//                    'taxable_amt' => $this->input->post('taxable'.$product_id),
//                    'total_amount' => $this->input->post('total_amt'.$product_id),
//                );
//                $idsalereturnproduct = $this->Sales_return_model->save_sales_return_product($sale_product);
//                $total_return_cash += $this->input->post('total_amt'.$product_id);
////              add into stock remaining    
//                $inward_stock[$i] = array(
//                    'date' => $date,
//                    'imei_no' => $imeino,
//                    'idmodel' => $idmodel,
//                    'created_by' => $sales_return_by,
//                    'sales_return_by' => $sales_return_by,
//                    'return_date' => $return_date,
//                    'sales_return_type' => 1, // cash return
//                    'sale_date' => $this->input->post('inv_date'),
//                    'idvariant' => $this->input->post('idvariant'.$product_id),
//                    'product_name' => $this->input->post('product_name'.$product_id),
//                    'idskutype' => $this->input->post('skutype'.$product_id),
//                    'idproductcategory' => $this->input->post('idtype'.$product_id),
//                    'idcategory' => $this->input->post('idcategory'.$product_id),
//                    'idbrand' => $this->input->post('idbrand'.$product_id),
//                    'is_gst'   => $this->input->post('is_gst'.$product_id),
//                    'idvendor' => $this->input->post('retidvendor'.$product_id),
//                    'idgodown' => $this->input->post('idgodown'.$product_id),
//                    'idbranch' => $idbranch,
//                );
//                $this->Inward_model->save_stock($inward_stock[$i]);
//                $imei_history[]=array(
//                    'imei_no' => $imeino,
//                    'entry_type' => 'Sales Return - Cash',
//                    'entry_time' => $return_date,
//                    'date' => $date,
//                    'idbranch' => $idbranch,
//                    'idgodown' => $this->input->post('idgodown'.$product_id),
//                    'idvariant' => $this->input->post('idvariant'.$product_id),
//                    'model_variant_full_name' => $this->input->post('product_name'.$product_id),
//                    'idimei_details_link' => 7, // Sales Return from imei_details_link table
//                    'idlink' => $idsalereturn,
//                );
//            }
//        }
//        $srpayment = array(
//            'date' => $date,
//            'inv_no' => $sales_return_invid,
//            'entry_type' => 2,
//            'idbranch' => $idbranch,
//            'idtable' => $idsalereturn,
//            'table_name' => 'sales_return',
//            'idcustomer' => $this->input->post('idcustomer'),
//            'customer_fname' => $cust_fname,
//            'customer_lname' => $cust_fname,
//            'customer_gst' => $this->input->post('gst_no'),
//            'amount' => -$total_return_cash,
//        );
//        $this->Sale_model->save_daybook_cash_payment($srpayment);
//        
//        
//        
//        
//        
//        
//        
//        // save sale       
////        $idbranch = $this->input->post('idbranch');
//        $dcprint = $this->input->post('dcprint');
//        $sinvid = $invid->invoice_no + 1; 
//        if($dcprint[0] == 0){
//            $sinv_no = $y1 .'-'. $y . '/'. $invid->branch_code . '/' . sprintf('%05d', $sinvid);
//        }else{
//            $sinv_no = 'DC'.$y1 .'-'. $y . '/'. $invid->branch_code . '/' . sprintf('%05d', $sinvid);
//        }
//        $datetime = date('Y-m-d H:i:s');
//        $idstate = $this->input->post('idstate');
//        $idcustomer = $this->input->post('idcustomer');
//        $cust_idstate = $this->input->post('cust_idstate');
//        $cust_pincode = $this->input->post('cust_pincode');
//        $gst_type = 0; //cgst
//        if($idstate != $cust_idstate){
//            $gst_type = 1; //igst
//        }
//        $data = array(
//            'date' => $date,
//            'inv_no' => $sinv_no,
//            'idbranch' => $idbranch,
//            'idcustomer' => $idcustomer,
//            'customer_fname' => $cust_fname,
//            'customer_lname' => $cust_lname,
//            'customer_idstate' => $cust_idstate,
//            'customer_pincode' => $cust_pincode,
//            'customer_contact' => $this->input->post('cust_mobile'),
//            'customer_address' => $this->input->post('address'),
//            'customer_gst' => $this->input->post('gst_no'),
//            'idsalesperson' => $this->input->post('idsalesperson'),
//            'basic_total' => $this->input->post('gross_total'),
//            'discount_total' => $this->input->post('final_discount'),
//            'final_total' => $this->input->post('final_total'),
//            'gst_type' => $gst_type,
//            'created_by' => $sales_return_by,
//            'remark' => $this->input->post('remark'),
//            'entry_time' => $datetime,
//            'dcprint' => $dcprint[0],
//        );
//        $idsale = $this->Sale_model->save_sale($data);
//        // Payment
//        $idpaymenthead = $this->input->post('idpaymenthead'); // buyback1,2,
//        $credittype = $this->input->post('credit_type');
//        $amount = $this->input->post('amount');
//        $payment_type = $this->input->post('payment_type');
//        $tranxid = $this->input->post('tranxid');
//        $headattr = $this->input->post('headattr');
//        $vin=array();
//        foreach ($headattr as $idpayment_head => $attributedata){
//            $payment_attribute = $this->General_model->get_payment_head_has_attributes_byhead($idpayment_head);
//            $mode_count=count($attributedata[$payment_attribute[0]->id_payment_attribute]);
//            $attr_value=array();
//            for($m=0;$m<$mode_count;$m++){
//                foreach ($payment_attribute as $attr){
//                    $attr_value[$attr->column_name] = $attributedata[$attr->id_payment_attribute][$m];
//                }
//            }
//            for($j=0; $j < count($idpaymenthead); $j++){
//                if($idpaymenthead[$j] == $idpayment_head){
//                    $vin[$j]=$attr_value;
//                }
//            }
//        }
//        $parr=array();
//        for($j=0; $j < count($idpaymenthead); $j++){
//            if($idpaymenthead[$j] == 1){
//                $srpayment = array(
//                    'date' => $date,
//                    'inv_no' => $inv_no,
//                    'entry_type' => 1,
//                    'idbranch' => $idbranch,
//                    'idtable' => $idsale,
//                    'table_name' => 'sale',
//                    'idcustomer' => $idcustomer,
//                    'customer_fname' => $cust_fname,
//                    'customer_lname' => $cust_lname,
//                    'customer_gst' => $this->input->post('gst_no'),
//                    'amount' => $amount[$j],
//                );
//                $this->Sale_model->save_daybook_cash_payment($srpayment);
//            }
//            $payment = array(
//                'date' => $date,
//                'idsale' => $idsale,
//                'amount' => $amount[$j],
//                'idpayment_head' => $idpaymenthead[$j],
//                'idpayment_mode' => $payment_type[$j],
//                'transaction_id' => $tranxid[$j],
//                'inv_no' => $sinv_no,
//                'idcustomer' => $idcustomer,
//                'idbranch' => $idbranch,
//                'created_by' => $sales_return_by,
//                'entry_time' => $datetime,
//            );
//            if(isset($vin[$j])>0){
//                $payment = array_merge($payment, $vin[$j]); 
//            }
//            $id_sale_payment = $this->Sale_model->save_sale_payment($payment);
//            if($credittype[$j] == 0){
//                $npayment[$j] = array(
//                    'idsale_payment' => $id_sale_payment,
//                    'inv_no' => $sinv_no,
//                    'idsale' => $idsale,
//                    'date' => $date,
//                    'idcustomer' => $idcustomer,
//                    'idbranch' => $idbranch,
//                    'amount' => $amount[$j],
//                    'idpayment_head' => $idpaymenthead[$j],
//                    'idpayment_mode' => $payment_type[$j],
//                    'transaction_id' => $tranxid[$j],
//                    'created_by' => $sales_return_by,
//                    'entry_time' => $datetime,
//                );
//                $this->Sale_model->save_payment_reconciliation($npayment[$j]);
//            }
//            $parr[] = $payment;
//        }
////        die('<pre>'.print_r($parr,1).'</pre>');
//        //Sale_product
//        $idtype = $this->input->post('idtype');
//        $idcategory = $this->input->post('idcategory');
//        $idbrand = $this->input->post('idbrand');
//        $idmodel = $this->input->post('idmodel');
//        $idvariant = $this->input->post('idvariant');
//        $idgodown = $this->input->post('idgodown');
//        $skutype = $this->input->post('skutype');
//        $product_name = $this->input->post('product_name');
//        $imei = $this->input->post('imei');
//        $price = $this->input->post('price');
//        $basic = $this->input->post('basic');
//        $discount_amt = $this->input->post('discount_amt');
//        $total_amt = $this->input->post('total_amt');
//        $landing = $this->input->post('landing');
//        $mrp = $this->input->post('mrp');
//        $mop = $this->input->post('mop');
//        $salesman_price = $this->input->post('salesman_price');
//        $qty = $this->input->post('qty');
//        $rowid = $this->input->post('rowid');
//        $is_gst = $this->input->post('is_gst');
//        $idvendor = $this->input->post('idvendor');
//        $hsn = $this->input->post('hsn'); 
//        $is_mop = $this->input->post('is_mop'); // price on invoice
////        $imei_history[] = array('nest'=>array());
//        for($i = 0; $i < count($idvariant); $i++){
//            $cgst = 0; $sgst = 0; $igst = 0;
//            if($gst_type == 1){
//                $igst = $this->input->post('igst['.$i.']');
//            }else{
//                $cgst = $this->input->post('cgst['.$i.']');
//                $sgst = $this->input->post('sgst['.$i.']');
//            }
//            $sale_product[$i] = array(
//                'date' => $date,
//                'idsale' => $idsale,
//                'idmodel' => $idmodel[$i],
//                'idvariant' => $idvariant[$i],
//                'imei_no' => $imei[$i],
//                'hsn' => $hsn[$i],
//                'idskutype' => $skutype[$i],
//                'idgodown' => $idgodown[$i],
//                'idproductcategory' => $idtype[$i],
//                'idcategory' => $idcategory[$i],
//                'idbrand' => $idbrand[$i],
//                'product_name' => $product_name[$i],
//                'price' => $price[$i],
//                'landing' => $landing[$i],
//                'mrp' => $mrp[$i],
//                'mop' => $mop[$i],
//                'salesman_price' => $salesman_price[$i],
//                'inv_no' => $sinv_no,
//                'qty' => $qty[$i],
//                'idbranch' => $idbranch,
//                'discount_amt' => $discount_amt[$i],
//                'is_gst' => $is_gst[$i],
//                'is_mop' => $is_mop[$i],
//                'basic' => $basic[$i],
//                'idvendor' => $idvendor[$i],
//                'cgst_per' => $cgst,
//                'sgst_per' => $sgst,
//                'igst_per' => $igst,
//                'total_amount' => $total_amt[$i],
//                'entry_time' => $datetime,
//            );
//            $idsaleproduct = $this->Sale_model->save_sale_product($sale_product[$i]);
//            if($skutype[$i] == 4){ //qunatity
//                $this->Sale_model->minus_stock_byidstock($rowid[$i], $qty[$i]);
//            }else{
//                $this->Purchase_model->delete_stock_byidstock($rowid[$i]);
//                // IMEI History
//                $imei_history[]=array(
//                    'imei_no' => $imei[$i],
//                    'entry_type' => 'Sale',
//                    'entry_time' => $datetime,
//                    'date' => $date,
//                    'idbranch' => $idbranch,
//                    'idgodown' => $idgodown[$i],
//                    'idvariant' => $idvariant[$i],
//                    'model_variant_full_name' => $product_name[$i],
//                    'idimei_details_link' => 4, // Sale from imei_details_link table
//                    'idlink' => $idsale,
//                    'imei_latitude' => $this->input->post('cust_latitude'),
//                    'imei_longitude' => $this->input->post('cust_longitude'),
//                );
//            }
//        }
////        die('<pre>'.print_r($imei_history,1).'</pre>');
//        if(count($imei_history) > 0){
//            $this->General_model->save_batch_imei_history($imei_history);
//        }
//        
//        // BFL integration
//        if($this->input->post('bfl_do_id')){
//            $bfl_data = array(
//                'do_id' => $this->input->post('bfl_do_id'),
//                'idsale' => $idsale,
//                'idsale_product' => $idsaleproduct,
//                'bfl_brand' => $this->input->post('bfl_brand'),
//                'bfl_model' => $this->input->post('bfl_model'),
//                'bfl_srno' => $this->input->post('bfl_srno'),
//                'idcustomer' => $idcustomer,
//                'mobile' => $this->input->post('mobile'),
//                'customer_name' => $this->input->post('bfl_customer'),
//                'customer_gst' => $this->input->post('gst_no'),
//                'scheme_code' => $this->input->post('scheme_code'),
//                'scheme' => $this->input->post('scheme'),
//                'mop' => $this->input->post('bfl_mop'),
//                'downpayment' => $this->input->post('bfl_downpayment'),
//                'loan' => $this->input->post('bfl_loan'),
//                'emi_amount' => $this->input->post('bfl_emi_amount'),
//                'tenure' => $this->input->post('bfl_tenure'),
//                'bfl_remark' => $this->input->post('bfl_remark'),
//                'entry_time' => $datetime,
//            );
//            $this->Sale_model->save_bfl($bfl_data);			
//        }
//        
//
//
//        // Update Sale from sales return
//        $this->Sale_model->update_sale($id_sale, $saleupdate);
//        $invoice_data = array(
//            'invoice_no' => $sinvid,
//            'sales_return_invoice_no' => $next_srinv_no );
//        $this->General_model->edit_db_branch($idbranch, $invoice_data);
//        if ($this->db->trans_status() === FALSE){
//            $this->db->trans_rollback();
//            $this->session->set_flashdata('save_data', 'Invoice billing is aborted. Try again with same details');
//        }else{
//            $this->db->trans_commit();
//            $this->session->set_flashdata('save_data', 'Invoice bill generated');
//        }
//        die('<pre>'.print_r($_POST,1).'</pre>');
//        if($dcprint[0] == 0){
//            return redirect('Sale/invoice_print/'.$idsale);
//        }else{
//            return redirect('Sale/dc_print/'.$idsale);
//        }
//    }
    
    
}