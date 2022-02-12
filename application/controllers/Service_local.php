<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Service extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Service_model');
        $this->load->model('Sale_model');
        $this->load->model('Inward_model');
    }
    public function service_inward()
    {        
        $q['tab_active'] = 'Sales_return';      
        $this->load->view('service/service_inward', $q);
    } 
    public function search_invoice_byimei() {
        $invno = $this->input->post('invno');
        $branch = $this->input->post('branch');
        $level = $this->input->post('level');
        if ($invno == 0) {
            $imei = $this->input->post('imei');
            $in = $this->Service_model->get_inv_byimei($imei);
            if ($in) {
                $invno = $in->inv_no;
            }
        }
        $service_problems = $this->Service_model->get_service_problems();
        $active_users_byrole = $this->General_model->get_active_users_byrole_branch(17, $branch);
        
        $sale_data = $this->Service_model->get_sale_by_invno($invno);
        if (count($sale_data) > 0) {
            $datee = explode('-', $sale_data[0]->date);
            $c_date = strtotime(date('Y-m-d'));
            $w_date = strtotime(($datee[0] + 1) . '-' . $datee[1] . '-' . $datee[2]);
            if ($c_date > $w_date) {
                echo '<center><h3><i class="mdi mdi-alert"></i> Out Of Warranty </h3>' .
                '<img src="' . base_url() . 'assets/images/highAlertIcon.gif" />'
                . '</center>';
            } else {
                
                $sale_product = $this->Service_model->get_sale_product_by_invno($invno);
                
                if (count($sale_data) > 0) {
                    foreach ($sale_data as $sale) {
                        ?>
                            <br>
                            <div class="thumbnail" style="overflow: auto;padding: 0">
                            <br>
                            <div class="col-md-8 col-xs-8" style="padding-left: 30px;">
                                Invoice Date :- &nbsp;<b><?php echo $sale->date ?></b><br>
                                Customer :- &nbsp; <b> <?php echo $sale->customer_fname . ' ' . $sale->customer_lname ?></b><br>                        
                                Contact :- &nbsp; <b><?php echo $sale->customer_contact ?></b> <br>
                            </div>
                            <div class="col-md-4 col-xs-4" style="padding-left: 30px;">
                                Sale Id :-  &nbsp; <b><?php echo $sale->id_sale ?></b><br>
                                Entry time :- &nbsp; <b><?php echo date('d/m/Y h:i a', strtotime($sale->entry_time)) ?></b><br>                        
                                Invoice No :- &nbsp;<b><?php echo $sale->inv_no ?></b> <br>
                                Promoter :- &nbsp;<b><?php echo $sale->user_name ?></b> <br>
                                <input type="hidden" name="inv_date" value="<?php echo $sale->date ?>" />
                                <input type="hidden" name="inv_no" value="<?php echo $sale->inv_no ?>" />
                                <input type="hidden" name="branch" value="<?php echo $branch ?>" />
                                <input type="hidden" name="sold_by" value="<?php echo $sale->idbranch ?>" />
                                <input type="hidden" name="idcustomer" value="<?php echo $sale->idcustomer ?>" />
                                <input type="hidden" name="fcustomer" value="<?php echo $sale->customer_fname ?>" />
                                <input type="hidden" name="lcustomer" value="<?php echo $sale->customer_lname ?>" />
                                <input type="hidden" name="gst_no" value="<?php echo $sale->customer_gst ?>" />
                                <input type="hidden" name="mobile" value="<?php echo $sale->customer_contact ?>" />
                                <input type="hidden" name="idsalesperson" value="<?php echo $sale->idsalesperson ?>" />
                                <input type="hidden" name="created_by" value="<?php echo $sale->created_by ?>" />
                                <input type="hidden" name="id_sale" value="<?php echo $sale->id_sale ?>" />
                                <input type="hidden" name="customer_address" value="<?php echo $sale->customer_address ?>" />
                                <input type="hidden" name="customer_state" value="<?php echo $sale->customer_idstate ?>" />
                            </div>  
                            <div class="clearfix"></div>            
                            <br>
                            </div>
                            <div class="thumbnail" style="overflow: auto;padding: 0">
                                <table id="model_data" class="table table-bordered table-condensed table-responsive table-hover" style="font-size: 13px; margin-bottom: 0;">
                                    <thead class="bg-info">
                                        <th class="col-md-4">Product</th>
                                        <th>SKU</th>                       
                                        <th>Amount</th>
                                        <th class="col-md-1">IMEI/SRNO</th>
                                        <th>Select</th>
                                    </thead>
                                    <tbody>
                        <?php
                        $avail_qty = 0;
                        foreach ($sale_product as $product) {
                            $id_saleproduct = $product->id_saleproduct;
                            ?>                       
                                    <tr>
                                        <td>
                            <?php echo $product->product_name; ?>
                                            <input type="hidden" class="saleproduct_name" value="<?php echo $product->product_name; ?>" />                                            
                                            <input type="hidden" class="saleproduct_id" id="id_saleproduct" value="<?php echo $id_saleproduct ?>" />
                                            <input type="hidden" id="is_gst" value="<?php echo $product->is_gst ?>" />
                                            <input type="hidden" id="idvendor" value="<?php echo $product->idvendor ?>" />
                                            <input type="hidden" class="model" value="<?php echo $product->idvariant ?>" />
                                            <input type="hidden" id="idgodown" value="<?php echo $product->idgodown ?>" />
                                        </td>
                                        <td>
                            <?php echo $product->sku_type ?>
                                            <input type="hidden" class="skutype" name="skutype" value="<?php echo $product->idskutype ?>" />
                                        </td>
                                        <td>
                            <?php echo $product->total_amount ?>
                                            <input type="hidden" name="price"  class="price" name="total_amt" value="<?php echo $product->total_amount ?>" />
                                        </td>
                                        <td>
                            <?php if ($product->idskutype != 4) { ?>
                                <?php echo $product->imei_no ?>
                                            <input type="hidden" name="imei_no" class="imei" value="<?php echo $product->imei_no ?>" />                                
                                            <input type="hidden" name="selected_qty" value="1" class="selected_qty" id="selected_qty" />
                            <?php } else { ?>                                    
                                            <input type="number" name="selected_qty" class="form-control input-sm selected_qty" id="selected_qty" readonly="" placeholder="Qty" max="<?php echo $avail_qty ?>" value="0" />
                            <?php } ?>
                                        </td>
                                        <td>  
                                <?php if($product->sales_return_type == 0){ ?>
                                            <input id="chk_return" type="radio" class="chk_return" name="chk_return" value="<?php echo $id_saleproduct ?>" />                                                    
                                <?php }else{ ?>
                                            Already return
                                <?php } ?>
                                        </td>
                                    </tr>
                        <?php } ?>
                                    <tr class="bg-info">
                                        <td colspan="1"></td>
                                        <td>Total</td>
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

                        <!--<input type="text" id="total_qty" value="<?php // echo $totalqty  ?>" />-->
                        <textarea class="txt_selected_sale_products" id="txt_selected_sale_products" name="txt_selected_sale_products" style="display: none"></textarea>
                        <input type="hidden" id="sales_return_type" value="<?php echo $sale->sales_return_type ?>" />
                        <div class="thumbnail col-md-6 col-md-offset-3">
                            <center><h4><i class="mdi mdi-clipboard-text"></i> Service Inward Form</h4></center><hr>   
                            <input type="hidden" name="erp" class="erp" value="1" />
                            <div class="col-md-2">Service Issues </div>
                            <div class="col-md-7">
                                <select class="chosen-select form-control input-sm" required="" name="idproblem" id="idproblem">
                                    <option value="">Select Issues</option>
                                    <?php foreach ($service_problems as $problems){ ?>
                                    <option value="<?php echo $problems->id; ?>"><?php echo $problems->problem; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <input type="hidden" id="problem" name="problem" class="problem" value="" />
                            <div class="clearfix"></div><br>
                            <div class="col-md-2">Promoter </div>
                            <div class="col-md-7">
                                <select class="chosen-select  form-control input-sm" name="idsalesperson" required="" id="idsalesperson">
                                    <option value="">Select Sales Promoter</option>
                                    <?php foreach ($active_users_byrole as $user) { if($user->id_users != 0){ ?>
                                        <option value="<?php echo $user->id_users ?>"><?php echo $user->user_name ?></option>
                                    <?php }} ?>
                                </select>
                            </div> <div class="clearfix"></div><br>
                            <div class="col-md-2"> Remark </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="remark" id="remark" required="" />                                
                            </div>
                            <div class="clearfix"></div><br>
                            <input type="hidden" class="form-control input-sm" name="is_selected" id="is_selected" required="" value="0" />
                            
                            <input type="hidden" id="dsaleproduct_name" name="dsaleproduct_name" />
                            <input type="hidden" id="dididvariant" name="dididvariant" />
                            <input type="hidden" id="dis_gst" name="dis_gst" />
                            <input type="hidden" id="didvendor" name="didvendor" />
                            <input type="hidden" id="didgodown" name="didgodown" />
                            <input type="hidden" id="dskutype" name="dskutype" />
                            <input type="hidden" id="dprice" name="dprice" />
                            <input type="hidden" id="dimei_no" name="dimei_no" />
                            <input type="hidden" id="dimodel" name="model" />
                            
                            <div class="col-md-2 col-md-offset-4">
                                <button type="submit" class="btn btn-sm btn-primary waves-effect waves-light" id="btn_inward" formmethod="POST" formaction="<?php echo base_url('Service/save_service_inward') ?>"><span class="mdi mdi-cellphone-android fa-lg"></span> Inward </button>
                            </div><div class="clearfix"></div>
                            </div><div class="clearfix"></div>
                        <input type="hidden" id="sales_return_product_id" />
                        <input type="hidden" id="sales_return_model_id" />
                        
                        <?php
                    }
                } else {
                    echo '<center><h3><i class="mdi mdi-alert"></i> You Enter Wrong Invoice/IMEI Number</h3>' .
                    '<img src="' . base_url() . 'assets/images/highAlertIcon.gif" />'
                    . '</center>';
                }
            }
        } else {
           $state_data = $this->General_model->get_state_data();
            $brands = $this->General_model->get_active_brand_data();
            if ($invno == 0) {
                $imei = $this->input->post('imei');
                $in = $this->Service_model->get_olderp_inv_byimei($imei);
                if ($in) {
                    $invno = $in->invoice_no;
                }
            }
            $sale_data = $this->Service_model->get_old_sale_byinvno($invno);
            if (count($sale_data) > 0) {
                $datee = explode('-', $sale_data[0]->invoice_date);
                $c_date = strtotime(date('Y-m-d'));
                $w_date = strtotime(($datee[0] + 1) . '-' . $datee[1] . '-' . $datee[2]);
                if ($c_date > $w_date) {
                    echo '<center><h3><i class="mdi mdi-alert"></i> Out Of Warranty </h3>' .
                    '<img src="' . base_url() . 'assets/images/highAlertIcon.gif" />'
                    . '</center>';
                } else {

                    $sale = $sale_data[0];
                    ?>
                        <br>
                        <center>
                            <h3 style="margin-top: 0"><span class=""></span> Billed In OLD ERP</h3>
                        </center><hr>

                        <div class="thumbnail" style="overflow: auto;padding: 0">
                        <br>
                        <div class="col-md-8 col-xs-8" style="padding-left: 30px;">
                            Invoice Date :- &nbsp;<b><?php echo $sale->invoice_date ?></b><br>
                            Customer :- &nbsp; <b> <?php echo $sale->customer_name ?></b><br>                        
                            Contact :- &nbsp; <b><?php echo $sale->customer_mobile ?></b> <br>
                        </div>
                        <div class="col-md-4 col-xs-4" style="padding-left: 30px;">
                            Sale Id :-  &nbsp; <b><?php echo $sale->id_old_sale ?></b><br>                            
                            Invoice No :- &nbsp;<b><?php echo $sale->invoice_no ?></b> <br>
                            Promoter :- &nbsp;<b><?php echo $sale->promoter_name ?></b> <br>
                            <input type="hidden" name="inv_date" value="<?php echo $sale->invoice_date ?>" />
                            <input type="hidden" name="inv_no" value="<?php echo $sale->invoice_no ?>" />
                            <input type="hidden" name="branch" value="<?php echo $branch ?>" />
                            <input type="hidden" name="sold_by" value="<?php echo $sale->idbranch ?>" />
                            <input type="hidden" name="idcustomer" value="0" />
                            <input type="hidden" name="fcustomer" value="<?php echo $sale->customer_name ?>" />
                            <input type="hidden" name="lcustomer" value="" />
                            <input type="hidden" name="gst_no" value="<?php echo $sale->customer_gst_no ?>" />
                            <input type="hidden" name="mobile" value="<?php echo $sale->customer_mobile ?>" />
                            <input type="hidden" name="id_sale" value="<?php echo $sale->id_old_sale ?>" />
                        </div>  
                        <div class="clearfix"></div>            
                        <br>
                        </div>
                        <div class="thumbnail" style="overflow: auto;padding: 0">
                            <table id="model_data" class="table table-bordered table-condensed table-responsive table-hover" style="font-size: 13px; margin-bottom: 0;">
                                <thead class="bg-info">
                                    <th class="col-md-4">Product</th>

                                    <th>Amount</th>
                                    <th class="col-md-1">IMEI/SRNO</th>
                                    <th>Select</th>
                                </thead>
                                <tbody>
                    <?php
                    $avail_qty = 0;
                    $final_total = 0;
                    foreach ($sale_data as $product) {
                        $id_saleproduct = $product->id_old_sale;
                        ?>                       
                            <tr>
                                <td>
                                    <?php echo $product->product_name; ?>
                                    <input type="hidden" class="saleproduct_name" value="<?php echo $product->product_name; ?>" />
                                    <input type="hidden" class="saleproduct_id" name="id_saleproduct" value="" />
                                    <input type="hidden" name="product_name" class="product_name" value="<?php echo $product->product_name ?>" />
                                    <input type="hidden" name="is_gst" class="is_gst" value="0" />
                                    <input type="hidden" name="idvendor" class="idvendor" value="0>" />
                                    <input type="hidden" name="idgodown" class="idgodown" value="1>" />
                                </td>
                                <td>
                        <?php echo $product->settlement_amount ?>
                                    <input type="hidden"  name="price" class="price" name="total_amt" value="<?php echo $product->settlement_amount ?>" />
                                </td>
                                <td>         
                        <?php if ($product->imei_1_no == "") { ?>
                            <?php echo $product->serial_no; ?>
                                    <input type="hidden" name="imei_no" class="imei" value="<?php echo $product->serial_no; ?>" />                                
                                    <input type="hidden" class="skutype" name="skutype" value="2" />
                                       
                        <?php } else { ?>
                            <?php echo $product->imei_1_no; ?>
                                    <input type="hidden" class="skutype" name="skutype" value="1" />
                                    <input type="hidden" name="imei_no" class="imei" value="<?php echo $product->imei_1_no; ?>" />                                                                                    
                        <?php } ?>                                      
                                    <input type="hidden" name="selected_qty" value="1" class="selected_qty" id="selected_qty" />
                                </td>
                                <td>    
                                    <?php if($product->sales_return_type == 0){ ?>
                                            <input id="chk_return" type="radio" class="chk_return" name="chk_return" value="<?php echo $id_saleproduct ?>" />                                                    
                                <?php }else{ ?>
                                            Already return
                                <?php } ?>                                   
                                </td>
                            </tr>
                        <?php $final_total = $final_total + $product->settlement_amount;
                    } ?>
                            <tr class="bg-info">
                                <td>Total</td>
                                <td>
                    <?php echo $final_total ?>
                                    <input type="hidden" name="final_total" value="<?php echo $final_total ?>" />
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div><div class="clearfix"></div>

                    
                    <!--<input type="hidden" id="sales_return_type" value="<?php echo $sale->sales_return_type ?>" />-->
                    <div class="thumbnail col-md-6 col-md-offset-3">
                            <center><h4><i class="mdi mdi-clipboard-text"></i> Service Inward Form</h4></center><hr>   
                            <input type="hidden" name="erp" class="erp"  value="0" />
                            <div class="col-md-2">Service Issues </div>
                            <div class="col-md-7">
                                <select class="chosen-select form-control input-sm" name="idproblem" id="idproblem">
                                    <option value="">Select Issues</option>
                                    <?php foreach ($service_problems as $problems){ ?>
                                    <option value="<?php echo $problems->id; ?>"><?php echo $problems->problem; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <input type="hidden" id="problem" name="problem" class="problem" value="" />
                            <div class="clearfix"></div><br>
                            <div class="col-md-2">Promoter </div>
                            <div class="col-md-7">
                                <select class="chosen-select  form-control input-sm" name="idsalesperson" required="" id="idsalesperson">
                                    <option value="">Select Sales Promoter</option>
                                    <?php foreach ($active_users_byrole as $user) { if($user->id_users != 0){ ?>
                                        <option value="<?php echo $user->id_users ?>"><?php echo $user->user_name ?></option>
                                    <?php }} ?>
                                </select>
                            </div> <div class="clearfix"></div><br>  
                            <center><h5> Customer details </h5></center><hr>  
                            <div class="col-md-2">State </div>
                            <div class="col-md-7">                                
                                <select class="chosen-select form-control input-sm required" placeholder="Customer State" name="customer_state" id="customer_state" required="">
                                    <?php foreach ($state_data as $state){ ?>
                                    <option><?php echo $state->state_name ?></option>
                                    <?php } ?>
                                </select>                                
                            </div>
                            <div class="clearfix"></div><br>
                             <div class="col-md-2">Address </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm required" placeholder="Customer Address" name="customer_address" required=""/>
                            </div><div class="clearfix"></div><br>
                            <div class="old_model">
                             
                             </div>
                             <div class="col-md-2"> Remark </div>
                            <div class="col-md-7">
                                <input type="text" class="form-control input-sm" name="remark" id="remark" required="" />                                
                            </div>
                             <div class="clearfix"></div><br>
                            <input type="hidden" class="form-control input-sm" name="is_selected" id="is_selected" required="" value="0" />
                            
                            <input type="hidden" id="dididvariant" name="dididvariant" />
                            <input type="hidden" id="dis_gst" name="dis_gst" />
                            <input type="hidden" id="didvendor" name="didvendor" />
                            <input type="hidden" id="didgodown" name="didgodown" />
                            <input type="hidden" id="dskutype" name="dskutype" />
                            <input type="hidden" id="dprice" name="dprice" />
                            <input type="hidden" id="dimei_no" name="dimei_no" />
                            <input type="hidden" id="dimodel" name="dimodel" />
                            
                            <div class="col-md-2 col-md-offset-4">
                                <button type="submit" class="btn btn-sm btn-primary waves-effect waves-light" id="btn_inward" formmethod="POST" formaction="<?php echo base_url('Service/save_service_inward') ?>"><span class="mdi mdi-cellphone-android fa-lg"></span> Inward </button>
                            </div><div class="clearfix"></div>
                            </div><div class="clearfix"></div>
                        <input type="hidden" id="sales_return_product_id" />
                        <input type="hidden" id="sales_return_model_id" />
                        
                        
                    <?php
                }
            } else {
                echo '<center><h3><i class="mdi mdi-alert"></i> You Enter Wrong Invoice/IMEI Number</h3>' .
                '<img src="' . base_url() . 'assets/images/highAlertIcon.gif" />'
                . '</center>';
            }
        }
    }

    public function save_service_inward() {
        
//           die('<pre>'.print_r($_POST,1).'</pre>');
        $date = date('Y-m-d');
        $inward_date = date('Y-m-d H:i:s');        
        $idvariant = $this->input->post('model');
        $var_data = $this->General_model->get_model_variant_data_byidvariant($idvariant); 
         
        $idmodel = $var_data->idmodel;
        $idskutype = $var_data->idsku_type;
        $idgodown = 4; 
        $idbrand = $var_data->idbrand;
        $idproductcategory = $var_data->idproductcategory;        
        $idcategory = $var_data->idcategory; 
        $full_name = $var_data->full_name; 
        $user_id=$this->session->userdata('id_users');
        $idcustomer = $this->input->post('idcustomer');
        $mobile = $this->input->post('mobile');
        $sold_by  = $this->input->post('sold_by');
        $erp_type = $this->input->post('erp');
        $inv_no = $this->input->post('inv_no');
        $inv_date = $this->input->post('inv_date');
        $idbranch = $this->input->post('branch');       
        $price = $this->input->post('dprice');       
        $idsalesperson = $this->input->post('idsalesperson');        
        $id_sale = $this->input->post('id_sale');       
        $idsale_product = $this->input->post('chk_return');
        $idproblem = $this->input->post('idproblem');
        $problem  = $this->input->post('problem');
        $imei_no = $this->input->post('dimei_no');
        $remark = $this->input->post('remark');
        $customer_address = $this->input->post('customer_address');
        $customer_state = $this->input->post('customer_state');
       
        if($idcustomer==0){
            $cus_data = $this->Sale_model->ajax_get_customer_bycontact($mobile);             
            if(count($cus_data)>0){
                $fcustomer = $cus_data[0]->customer_fname;
                $lcustomer = $cus_data[0]->customer_lname;
                $idcustomer = $cus_data[0]->id_customer;            
            }else{
                 $data = array(
                    'customer_fname' => $this->input->post('fcustomer'),
                    'customer_lname' => $this->input->post('lcustomer'),
                    'customer_contact' => $mobile,
                    'customer_address' => $customer_address,
                    'idstate' => $customer_state,
                    'customer_gst' => $this->input->post('gst_no'),
                    'idbranch' => $this->input->post('branch'),
                    'created_by' => $user_id,
                    'entry_time' => $inward_date,
                );        
                $idcustomer = $this->Sale_model->save_customer($data);
                $fcustomer = $this->input->post('fcustomer');
                $lcustomer = $this->input->post('lcustomer');
            }
        }else{
            $fcustomer = $this->input->post('fcustomer');
            $lcustomer = $this->input->post('lcustomer');
        }        
        $this->db->trans_begin();             
        $data = array(
                    'idbranch' => $idbranch,  
                    'soldby_idbranch' => $sold_by,  
                    'imei' => $imei_no,
                    'erp_type' => $erp_type,                    
                    'idskutype' => $idskutype,
                    'idgodown' => $idgodown,
                    'idproductcategory' => $idproductcategory,
                    'idcategory' => $idcategory,
                    'idmodel' => $idmodel,
                    'idvariant' => $idvariant,
                    'idbrand' => $idbrand,
                    'idusers' => $user_id,
                    'idsalesperson' => $idsalesperson,
                    'idsale' => $id_sale,
                    'idsale_product' => $idsale_product,
                    'inv_no' => $inv_no,
                    'inv_date' => $inv_date,            
                    'idcustomer' => $idcustomer,
                    'customer_name' => $fcustomer.' '.$lcustomer,
                    'cust_address' => $customer_address,
                    'cust_idstate' => $customer_state,
                    'mob_number'=> $mobile,
                    'idsalesperson' => $idsalesperson,
                    'problem_id' => $idproblem,
                    'problem' => $problem,
                    'process_status' => 1,
                    'entry_time' => $inward_date,
                    'remark' => $remark,
                    'sold_amount' => $price
        );
        // save service inward
        $idservice=$this->Service_model->save_service_inward($data);
       
                     $inward_stock = array(
                        'date' => $date,
                        'imei_no' => $imei_no,
                        'idmodel' => $idmodel,
                        'created_by' => $user_id,                        
                        'sale_date' => $inv_date,
                        'product_name' => $full_name,
                        'idvariant' => $idvariant,
                        'idskutype' => $idskutype,
                        'idproductcategory' => $idproductcategory,
                        'idcategory' => $idcategory,
                        'idbrand' => $idbrand,
                        'is_gst'   => $this->input->post('is_gst'),
                        'idvendor' => $this->input->post('idvendor'),
                        'idgodown' => $idgodown,
                        'idbranch' => $idbranch,
                        'idservice' => $idservice
                    );
                    if(count($inward_stock)>0){
                        $this->Inward_model->save_stock($inward_stock);                    
                    }        
                    $imei_history=array();
                    $imei_history[]=array(
                    'imei_no' => $imei_no,
                    'entry_type' => 'Service Inward',
                    'entry_time' => $inward_date,
                    'date' => $date,
                    'idbranch' => $idbranch,
                    'idgodown' => $idgodown,
                    'idvariant' => $idvariant,
                    'idimei_details_link' => 16, // Sales Return from imei_details_link table
                    'idlink' => $idservice,
                );
                if(count($imei_history) > 0){
                    $this->General_model->save_batch_imei_history($imei_history);
                }        
        $this->db->trans_complete();
        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Fail to inward service. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Serive case inwarded Successfully');
        }
        return redirect('Service/service_details/'.$idservice);
    }
    
    public function service_details($id){
        $q['tab_active'] = 'Reports';
        $q['service_data'] = $this->Service_model->get_service_details_byid($id);
        $this->load->view('service/service_details', $q);
    }
    
    public function service_report(){
        $user_id=$this->session->userdata('id_users'); 
        $role_type=$this->session->userdata('role_type');   
        $level=$this->session->userdata('level');   
        $q['tab_active'] = '';
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();          
        if($role_type==0){
            if($level==3){
                $q['branch_data'] = $this->General_model->get_branches_by_user($user_id);                  
            }else{
                $q['branch_data'] = $this->General_model->get_active_branch_data();                        
            }
        }elseif($role_type==2){   
             $idbranch = $this->session->userdata('idbranch');
            $q['branch_data'] = $this->General_model->get_branch_array_byid($idbranch);  
        }else{
               $q['branch_data'] = $this->General_model->get_active_branch_data();   
        }             
        $this->load->view('service/service_stock_report', $q);
    }
    public function ajax_get_service_stock_report(){
        
        $brand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
        $status = $this->input->post('status');
        $product_category = $this->input->post('product_category');
        $service_stock = $this->Service_model->get_service_stock_report($brand, $product_category,$idbranch,$status);
        if($service_stock){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>Case ID</th>
                    <th>Branch</th>
                    <th>Inward Date</th>                    
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Product name</th>
                    <th>IMEI/SRNO</th>
                    <th>Problem</th>
                    <th>Remark</th>                    
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Info</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $stock->id_service; ?></td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($stock->entry_time)); ?></td>
                        <td><?php echo $stock->inv_no ?></td>
                        <td><?php echo $stock->inv_date ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->imei ?></td>
                        <td><?php echo $stock->problem ?></td>
                        <td><?php echo $stock->remark ?></td>
                        <td><?php echo $stock->customer_name.'-'.$stock->mob_number ?></td>
                        <td><?php echo $stock->delivery_status ?></td>                        
                        <td><a href="<?php echo base_url('Service/service_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-info"></i></center></a></td>
                </tr>
                <?php $i++; } ?>
            </tbody>
        </table>
            
        <?php }else{ ?>
            <script>
                $(document).ready(function (){
                   alert("Data Not Found"); 
                });
            </script>
        <?php }
        
    }
    
    public function my_branch_inward_service(){   
        $idbranch=$this->session->userdata('idbranch');   
        $q['tab_active'] = '';
         $q['service_stock'] = $this->Service_model->get_service_stock_report(0, 0,$idbranch,1);             
        $this->load->view('service/my_service_stock', $q);
    }
    public function ajax_sent_to_local(){
       
        $idservice=$this->input->post('idservice');
        $care=$this->input->post('care');
        $datetime = date('Y-m-d');
        $data = array(
            'process_status' => 2,
            'branch_to_local' => $datetime,
            'carename' => $care
        );
        $data=$this->Service_model->update_service_stock($idservice, $data);
        if($data){
            echo 1;
        } else {
            echo 0;    
        }
   }
    public function pending_service_data(){   
        $idbranch=$this->session->userdata('idbranch');  
        $type=2;
        $q['tab_active'] = '';
        $q['service_stock'] = $this->Service_model->get_inprocess_service_stock_report(0, 0,$idbranch,$type);             
        $this->load->view('service/processing_service_stock', $q);
    }
    public function ajax_get_pending_service_stock_report(){
        $idbranch = $this->input->post('idbranch');
         $type=$this->input->post('type');
        $service_stock = $this->Service_model->get_inprocess_service_stock_report(0, 0,$idbranch,$type);
        if($service_stock){ ?>
            <table  id="service_data" class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>Case ID</th>
                    <th>Branch</th>
                    <th>Inward Date</th>                    
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Product name</th>
                    <th>IMEI/SRNO</th>
                    <th>Problem</th>
                    <th>Remark</th>                    
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Info</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $stock->id_service; ?></td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($stock->entry_time)); ?></td>
                        <td><?php echo $stock->inv_no ?></td>
                        <td><?php echo $stock->inv_date ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->imei ?></td>
                        <td><?php echo $stock->problem ?></td>
                        <td><?php echo $stock->remark ?></td>
                        <td><?php echo $stock->customer_name.'-'.$stock->mob_number ?></td>
                        <td><?php echo $stock->delivery_status ?></td>                        
                        <td><a href="<?php echo base_url('Service/service_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-info"></i></center></a></td>
                </tr>
                <?php $i++; } ?>
            </tbody>
        </table>
            
        <?php }else{ ?>
            <script>
                $(document).ready(function (){
                   alert("Data Not Found"); 
                });
            </script>
        <?php }
        
    }
    
    public function make_doa($id){
        $q['tab_active'] = 'Reports';
        $q['service_data'] = $this->Service_model->get_service_details_byid($id);
        if($q['service_data'][0]->process_status==2){
            if($q['service_data'][0]->erp_type==0){
                $q['sale'] = $this->Service_model->get_sale_product_by_idproduct($q['service_data'][0]->idsale_product,$q['service_data'][0]->inv_no);                
            }else{            
                $q['sale'] = $this->Service_model->get_sale_product_by_idsaleproduct($q['service_data'][0]->idsale_product,$q['service_data'][0]->inv_no);              
            }            
        $this->load->view('service/service_doa', $q);
        }else{
            return redirect('Service/pending_service_data');
        }
       
    }
    
    public function product_replacement_form() {
        $idbranch = $_SESSION['idbranch'];
        $total_selected_cash = $this->input->post('total_selected_sum');
        $doa_return_type = $this->input->post('doa_return_type');
        $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);
        $payment_head = $this->General_model->get_active_payment_head();
        $payment_mode = $this->General_model->get_active_payment_mode();
        $payment_attribute = $this->General_model->get_payment_head_has_attributes();
        $state_data = $this->General_model->get_state_data();
        $active_users_byrole = $this->General_model->get_active_users_byrole_branch(17, $idbranch);
        $model_variant = $this->General_model->ajax_get_model_variant_byidskutype(4);
        ?>
        <br>
        <div class="col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1" style="padding: 0">
            <?php $title="Product Replace/Upgrade Form"; if($doa_return_type==1){ ?>
                <center><span style="color: #9b0c13"><i class="fa fa-barcode"></i> Scan new product to Replace/Upgrade</span></center><br><br>
             <?php }elseif($doa_return_type==3){ ?>    
                <center><span style="color: #9b0c13"><i class="fa fa-user"></i> Manager will be responsible for FORCE DOA </span></center><br><br>
            <?php }else{ $title="Product Inward/Upgrade Form"; ?>
                <center><span style="color: #9b0c13"><i class="fa fa-barcode"></i> Inward received handset and Scan new product to Upgrade </span></center><br><br>                
                 <?php } ?>
            <div class="neucard shadow-inset border-light p-4">
                <div class="shadow-soft border-light rounded p-4" style="background-color: #fff">
                    <div style="background-image: linear-gradient(to right top, #510a0a, #750c11, #9b0c13, #c30e14, #eb1212);box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5);border-radius: 5px; margin-top: -45px">
                        <div style="padding: 8px; font-size: 17px; margin: 0px; color: #fff">
                            <center><i class="fa fa-clipboard"></i> <?php echo $title;?> </center>
                        </div>
                    </div><div class="clearfix"></div>
                    <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch'] ?>"/><br>
                    <input type="hidden" name="bfl_store_id" id="bfl_store_id" value="<?php echo $invoice_no->bfl_store_id ?>"/>
                    <input type="hidden" name="idstate" id="idstate" value="<?php echo $invoice_no->idstate ?>"/>
                    
                    <?php if($doa_return_type==1){ ?>
                        <div class="col-md-2 col-sm-4">DOA ID</div>
                        <div class="col-md-4 col-sm-4">
                            <input type="text" class="form-control input-sm doa_id" name="doa_id" id="doa_id" placeholder="Enter DOA ID" required="" />
                        </div>
                        <div class="col-md-2 col-sm-4">DOA Date</div> 
                        <div class="col-md-4 col-sm-4">
                            <input type="text" class="form-control input-sm doa_date" data-provide="datepicker" name="doa_date" id="doa_date" placeholder="Select Date" required="" autocomplete="off" onfocus="blur()" />
                        </div>         
                        <?php }else{ ?>    
                        <input type="hidden" class="form-control input-sm doa_id" name="doa_id" id="doa_id" value="" />                        
                        <input type="hidden" class="form-control input-sm doa_date" data-provide="datepicker" name="doa_date" id="doa_date" value="" />                        
                    <?php }
                    if($doa_return_type==2){                         
                        $brands = $this->General_model->get_active_brand_data(); ?>
                                <center><h5> Select product to inward new received handset </h5></center><hr>                                        
                                        <div class="col-md-2 col-sm-4">
                                                <span>Brand</span>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <select class="chosen-select form-control input-sm" required="" name="newidbrand" id="newidbrand">
                                                <option value="">Select Brand</option>';
                                                <?php foreach ($brands as $brand) { ?>
                                                    <option value="<?php echo $brand->id_brand; ?>"> <?php echo $brand->brand_name;?></option>
                                                 <?php   } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-sm-4">
                                                <span>Model</span>
                                        </div>                                        
                                        <div class="col-md-4 col-sm-4">
                                            <div class="idvariant">
                                            <select class="chosen-select form-control input-sm" required="" name="model" id="model">
                                                <option value="">Select Model </option>                                    
                                            </select>
                                            </div>
                                        </div>
                                    <div class="clearfix"></div><br>
                                    <div class="col-md-2 col-sm-4">
                                        <span>Scan IMEI To Inward</span>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control input-sm" required="" placeholder="Scan IMEI/SRNO/Barcode" id="new_enter_imei"/>
                                    </div>
                                    <div class="col-md-2 col-sm-4">
                                        <span>Inward Remark</span>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control input-sm" name="in_remark" placeholder="remark" id="in_remark"/>
                                    </div>
                                    <div class="clearfix"></div><hr>
                    <?php } ?>
                    <div class="clearfix"></div>
                    <div class="new_product">
                    <input type="hidden" name="sales_return_approved_by" value="" />
                     <center><h5> Scan new imei to upgrade </h5></center><br>   
                    <div class="col-md-2 col-sm-4" >
                        <span>Sales Promoter</span>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <select class="chosen-select form-control input-sm" name="idsalesperson" required="">
                            <option value="">Select Sales Promoter</option>
                            <?php foreach ($active_users_byrole as $user) { ?>
                                <option value="<?php echo $user->id_users ?>"><?php echo $user->user_name ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        Default Godown -> New Godown
                        <input type="hidden" id="idgodown" value="1"/>
                    </div><div class="clearfix"></div><br>
                    <div class="col-md-2 col-sm-4">
                        <span>Scan Replaced IMEI</span>
                    </div>
                    <div class="col-md-4 col-sm-4">
                        <input type="text" class="form-control input-sm" placeholder="Scan IMEI/SRNO/Barcode" id="enter_imei"/>
                    </div>
                    <div class="col-md-2 col-sm-4">
                        <span>Select Product</span>
                    </div>
                    <div class="col-md-4 col-sm-4">
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
                        <?php foreach ($payment_head as $head) { ?>
                           
                        <div class="col-md-2 col-sm-2 col-xs-6" style="font-family: Kurale; font-size: 15px; padding: 2px 5px;">
                            <label class="material-switch waves-block waves-effect waves-ripple" for="paymentmode<?php echo $head->payment_head ?>" style="font-weight: 100;box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.15);padding: 7px 12px;">
                                <input class="paymenthead" id="paymentmode<?php echo $head->payment_head ?>" type="checkbox" value="<?php echo $head->id_paymenthead ?>" selected_head="<?php echo $head->payment_head ?>" />
                                <label for="paymentmode<?php echo $head->payment_head ?>" class="label-primary" style="margin-bottom: 10px"></label> 
                                <span><?php echo $head->payment_head ?></span>
                            </label>
                        </div>
                        <?php } ?><div class="clearfix"></div>
                        <div id="modes_block0" class="modes_block modes_blockc0 thumbnail" style="margin-bottom: 5px; padding: 5px;">
                            <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                                <span style="font-size: 15px; font-family: Kurale">DOA Product</span>
                                <select class="form-control input-sm payment_type" name="payment_type[]">
                                    <option value="0">DOA Product</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                                Amount
                                <input type="number" class="form-control input-sm amount" id="amount1" name="amount[]" placeholder="Amount" readonly="" value="<?php echo $total_selected_cash ?>" min="<?php echo $total_selected_cash ?>" required="" />
                                <input type="hidden" class="idpaymenthead" name="idpaymenthead[]" value="0" />
                                <input type="hidden" class="headname" name="headname[]" value="DOA" />
                                <input type="hidden" class="credit_type" name="credit_type[]" value="0" />
                            </div>
                            <div class="col-md-2 col-sm-3">                            
                                <input type="hidden" class="form-control input-sm doa" id="doa" name="tranxid[]" value="<?php echo NULL; ?>" />
                            </div><div class="clearfix"></div>
                        </div><div class="clearfix"></div>
                        <div class="payment_modes" style="font-size: 12px"></div>
                        <div id="bfl_form"></div><hr>
                        <div class="col-md-2 col-sm-3 col-xs-4">
                            <a class="btn btn-warning gradient1 cancel_btn" >Cancel</a>
                        </div>
                        <div class="col-md-5 col-md-offset-3 col-sm-9 col-xs-8">
                            <input type="text" class="form-control input-sm" name="remark" placeholder="Enter Remark"/>
                        </div>
                        <div class="col-md-2 col-sm-3 col-xs-4">
                            <button type="submit" id="invoice_submit" class="btn btn-success btn-sub" formmethod="POST" formaction="<?php echo site_url('Service/save_product_doa_return_replace') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Submit</button>
                        </div><div class="clearfix"></div>
                    </div>
                    </div>
                </div><div class="clearfix"></div>
                <!--</form>-->
            </div><div class="clearfix"></div>
        </div><div class="clearfix"></div>
        <a href="<?php echo base_url('Sale/imei_tracker') ?>" target="_blank" class="simple-tooltip waves-effect waves-light" title="Track imei" id="floatingButton">
            <i class="mdi mdi-barcode-scan" style="font-size: 24px"></i>
        </a>
<?php }

   public function save_product_doa_return_replace_old() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
//        echo count($_POST);
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $this->load->model('Transfer_model');
        $this->load->model('Inward_model');
        $this->load->model('Purchase_model');
        $this->load->model('Sales_return_model');
        
        $total_return_cash = 0;
        $this->db->trans_begin();
        $inv_no = $this->input->post('inv_no');
        $idbranch = $this->input->post('idbranch');
        $date = date('Y-m-d');
        $sales_return_by=$this->session->userdata('id_users');                 
        $doa_return_type = $this->input->post('doa_return_type');
        $idservice = $this->input->post('idservice');
        $doa_id = $this->input->post('doa_id');
        $doa_date = $this->input->post('doa_date');
        $imei = $this->input->post('imei');
        $erp_type = $this->input->post('erp_type');
        
        $new_imeis = implode(', ', $imei); 
        $id_sale = $this->input->post('id_sale');
        $return_date = date('Y-m-d H:i:s');
        
//      $sales_return_invid = $this->input->post('sales_return_invid');
        $invid = $this->Sale_model->get_invoice_no_by_branch($idbranch); 
        $y = date('y', mktime(0, 0, 0, 9 + date('m')));
        $y1 = $y - 1;
        $next_srinv_no =  $invid->sales_return_invoice_no + 1;
        $sales_return_invid = 'SR'.$y1.$y . '/'. $invid->branch_code . '/' . sprintf('%04d', $next_srinv_no);
        $cust_fname = $this->input->post('cust_fname');
        $cust_lname = $this->input->post('cust_lname');
//      save sales return
        $selected_sale_products = $this->input->post('chk_return');
        $overall_basic = $this->input->post('selected_total_basic');
        $overall_discount_amt = $this->input->post('selected_total_discount');
        $overall_total_amt = $this->input->post('selected_total_amount');
        $old_landing = $this->input->post('old_landing');
        $data = array(
            'date' => $date,
            'idsale' => $id_sale,
            'doa_id' => $doa_id,
            'brand_doa_date' => $doa_date,
            'sales_return_invid' => $sales_return_invid,
            'sales_return_type' => 3, // doa return
            'doa_return_type' => $doa_return_type,
            'inv_no' => $inv_no,
            'inv_date' => $this->input->post('inv_date'),
            'idbranch' => $idbranch,
            'idcustomer' => $this->input->post('idcustomer'),
            'sales_return_approved_by'=> $this->input->post('sales_return_approved_by'),
            'sales_return_reason'=> "DOA return",
            'idsalesperson' => $this->input->post('idsalesperson'),
            'final_total' => $overall_total_amt,
            'discount_total' => $overall_discount_amt,
            'basic_total' => $overall_basic,
            'sales_return_by' => $sales_return_by,
        );
        // save sales return product
        $idsalereturn = $this->Sales_return_model->save_sales_return($data);
        $imeino = $this->input->post('imei_no'.$idservice);
        if($doa_return_type==2){
            $newidbrand = $this->input->post('newidbrand');
            $idv = $this->input->post('model');
            $new_enter_imei = $this->input->post('new_enter_imei');
            $id_sale = $this->input->post('id_sale');
            $inward_request = array(
                'date' => $date,
                'imei_no' => $new_enter_imei,
                'idvariant' => $idv,
                'idgodown' => 1,
                'idbrand' => $newidbrand,
                'idbranch' => $idbranch,
                'entry_time' => $return_date,
                'created_by' => $sales_return_by,
                'doa_imei' => $imeino,
                'status' => 0,
                'replaced_imei' => $new_imeis,
                'idservice' => $idservice
            );
             $this->Service_model->save_doa_inward($inward_request);
             $ary_service = array(
                    'closed' => $date,
                    'process_status' => 11,
                    'closure_remark' => 'Closed - received new handset',
                    'local_to_branch' => $return_date,
                    'sales_return_invid' => $sales_return_invid,
                );
             
        }elseif($doa_return_type==1){
            $ary_service = array(
                    'closed' => $date,
                    'process_status' => 11,
                    'closure_remark' => 'Closed -  received DOA letter',
                    'local_to_branch' => $return_date,
                    'sales_return_invid' => $sales_return_invid,
                );
        }elseif($doa_return_type==3){
            $ary_service = array(
                    'closed' => $date,
                    'process_status' => 11,
                    'closure_remark' => 'Closed -  Force DOA',
                    'local_to_branch' => $return_date,
                    'sales_return_invid' => $sales_return_invid,
                );
        }
        
        // update sale product
        $saleupdate = array(
            'sales_return_type' => 3, // doa return
            'sales_return_invid' => $sales_return_invid,
            'sales_return_by' => $sales_return_by,
            'sales_return_date' => $return_date,
        );
        
        //// update query at bottom line no 1782
//        $imei_history[] = array('nest'=>array());
        $imei_history = array();
        $saleproductupdate=array();
        $inward_stock=array();
        for($i = 0; $i < count($selected_sale_products); $i++){
            
            $qty = $this->input->post('qty');
            $previous_return_qty = 0;
            $idmodel = $this->input->post('idmodel'.$idservice);
//            die($total_qty);
            // update sale product
            
                    
            $selected_row_taxable = $this->input->post('taxable_amt');
            $selected_row_cgst_amt = $this->input->post('cgst_amt');
            $selected_row_sgst_amt = $this->input->post('sgst_amt');
            $selected_row_igst_amt = $this->input->post('igst_amt');
            $selected_row_tax = $this->input->post('tax');
            
                
                $sale_product = array(
                    'sales_return_type' => 3, // doa return
                    'doa_return_type' => $doa_return_type,
                    'date' => $date,
                    'imei_no' => $imeino,
                    'sales_return_invid' => $sales_return_invid,
                    'idskutype' => $this->input->post('skutype'.$idservice),
                    'idproductcategory' => $this->input->post('idproductcategory'.$idservice),
                    'idcategory' => $this->input->post('idcategory'.$idservice),
                    'idgodown' => 1,
                    'idvariant' => $this->input->post('idvariant'.$idservice),
                    'idmodel' => $idmodel,
                    'idbranch' => $idbranch,
                    'idbrand' => $this->input->post('idbrand'.$idservice),
                    'sales_return_by' => $sales_return_by,
                    'idsales_return' => $idsalereturn,
                    'product_name' => $this->input->post('ret_product_name'),
                    'price' => $this->input->post('price'.$idservice),
                    'inv_no' => $inv_no,
                    'qty' => 1,
                    'taxable_amt' => $selected_row_taxable,
                    'cgst_per' => $this->input->post('cgst_per'),
                    'sgst_per' => $this->input->post('sgst_per'),
                    'igst_per' => $this->input->post('igst_per'),
                    'cgst_amt' => $selected_row_cgst_amt,
                    'sgst_amt' => $selected_row_sgst_amt,
                    'igst_amt' => $selected_row_igst_amt,
                    'tax' => $selected_row_tax,
                    'basic' => $overall_basic,
                    'discount_amt' => $overall_discount_amt,
                    'total_amount' => $overall_total_amt,
                    'idsale_product' => $selected_sale_products[0],
                    'new_imei_against_doa' => $new_imeis,
                    'old_landing' => $old_landing
                );
//                die(print_r($sale_product));
                $idsalereturnproduct = $this->Sales_return_model->save_sales_return_product($sale_product);
//                $total_return_cash += $this->input->post('total_amt'.$product_id);
//              add into stock remaining    
                $inward_stock = array(                    
                    'created_by' => $sales_return_by,
                    'sales_return_by' => $sales_return_by,
                    'return_date' => $return_date,
                    'sales_return_type' => 3, 
                    'sale_date' => $this->input->post('inv_date'),
                    'idgodown' => 3,
                    'doa_return_type' => $doa_return_type,
                    'doa_id' => $doa_id,
                    'doa_date' => $doa_date
                    
                );              
                
                $imei_history[]=array(
                    'imei_no' => $imeino,
                    'entry_type' => 'DOA Return - Replace/Upgrade',
                    'entry_time' => $return_date,
                    'date' => $date,
                    'idbranch' => $idbranch,
                    'idgodown' => 3,
                    'idvariant' => $this->input->post('idvariant'.$idservice),                    
                    'idimei_details_link' => 9, // Sales Return from imei_details_link table
                    'idlink' => $idsalereturn,
                );
    
            }
        $srpayment = array();
        
// save sale       
//        $idbranch = $this->input->post('idbranch');
        $dcprint = $this->input->post('dcprint');
        $sinvid = $invid->invoice_no + 1; 
        if($dcprint[0] == 0){
            $sinv_no = $y1 .'-'. $y . '/'. $invid->branch_code . '/' . sprintf('%05d', $sinvid);
        }else{
            $sinv_no = 'DC'.$y1 .'-'. $y . '/'. $invid->branch_code . '/' . sprintf('%05d', $sinvid);
        }
        $remark=$this->input->post('remark');
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
            'remark' => $remark." - DOA Return amount Rs.".$overall_total_amt,
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
        
        if($headattr){
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
        }
        $parr=array();
        for($j=0; $j < count($idpaymenthead); $j++){
            $received_amount=0;$pending_amt=$amount[$j];$received_entry_time=NULL;$payment_receive=0;
            if($idpaymenthead[$j] == 1){     
                $received_amount = $amount[$j];
                $pending_amt=0;$received_entry_time=$datetime;$payment_receive=1;
                $srpayment[] = array(
                    'date' => $date,
                    'inv_no' => $inv_no,
                    'entry_type' => 2,
                    'idbranch' => $idbranch,
                    'idtable' => $idsale,
                    'table_name' => 'sale',
                    'amount' => $amount[$j],
                );
//                $this->Sale_model->save_daybook_cash_payment($srpayment);
            }
            if($idpaymenthead[$j] == 0){  
                $received_amount = $amount[$j];
                $pending_amt=0;$received_entry_time=$datetime;$payment_receive=1;
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
                'received_amount' => $received_amount,
                'received_entry_time'=>$received_entry_time,
                'payment_receive' => $payment_receive,
            );
            if(isset($vin[$j])>0){
                $payment = array_merge($payment, $vin[$j]); 
            }
            
            $id_sale_payment = $this->Sale_model->save_sale_payment($payment);
            if($credittype[$j] == 0){
                $npayment = array(
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
                    'received_amount' => $received_amount,
                    'pending_amt' => $pending_amt,
                    'received_entry_time'=>$received_entry_time,
                    'payment_receive' => $payment_receive,
                );
                if(isset($vin[$j])>0){
                    $npayment = array_merge($npayment, $vin[$j]); 
                }
                $this->Sale_model->save_payment_reconciliation($npayment);
            }
//            $parr[] = $payment;
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
        if($erp_type==1){
        $saleproductupdate = array(
                'sales_return_type' => 3, // doa return
                'sales_return_invid' => $sales_return_invid,
                'sales_return_by' => $sales_return_by,
                'sales_return_date' => $return_date,
                'sale_return_qty' => 1,
                'doa_imei_no' => $new_imeis,
            );
        }else{
        $saleproductupdate = array(
                'sales_return_type' => 3, // doa return
                'sales_return_invid' => $sales_return_invid,
                'doa_imei_no' => $new_imeis,
            );
        }
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
                'idsale_product_for_doa' => $selected_sale_products[0],
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
                    'idimei_details_link' => 4, // Sale from imei_details_link table
                    'idlink' => $idsale,
                );
            }
        }
        if(count($srpayment) > 0){
            $this->Sale_model->save_batch_daybook_cash_payment($srpayment);
        }
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
        
        $this->Service_model->update_service_stock($idservice, $ary_service);
        // Update Sale from sales return
        $this->Sale_model->update_sale($id_sale, $saleupdate);
        if($erp_type==1){
            $this->Sale_model->update_sale_product_byidsaleproduct($selected_sale_products[0], $saleproductupdate);     
        }else{
            $this->Service_model->update_sale_product_byidsaleproduct($selected_sale_products[0], $saleproductupdate);     
        }           
        $this->Service_model->update_stock($idservice,$inward_stock);
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
        if($dcprint[0] == 0){
            return redirect('Sale/invoice_print/'.$idsale);
        }else{
            return redirect('Sale/dc_print/'.$idsale);
        }
    }
    
   public function save_product_doa_return_replace() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
//        echo count($_POST);
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $this->load->model('Transfer_model');
        $this->load->model('Inward_model');
        $this->load->model('Purchase_model');
        $this->load->model('Sales_return_model');
        
        $total_return_cash = 0;
        $this->db->trans_begin();
        $inv_no = $this->input->post('inv_no');
        $idbranch = $this->input->post('idbranch');
        $date = date('Y-m-d');
        $sales_return_by=$this->session->userdata('id_users');                 
        $doa_return_type = $this->input->post('doa_return_type');
        $idservice = $this->input->post('idservice');
        $doa_id = $this->input->post('doa_id');
        $doa_date = $this->input->post('doa_date');
        $imei = $this->input->post('imei');
        $erp_type = $this->input->post('erp_type');
        
        $new_imeis = implode(', ', $imei); 
        $id_sale = $this->input->post('id_sale');
        $return_date = date('Y-m-d H:i:s');
        $imei_history = array();
//      $sales_return_invid = $this->input->post('sales_return_invid');
        $invid = $this->Sale_model->get_invoice_no_by_branch($idbranch); 
        $y = date('y', mktime(0, 0, 0, 9 + date('m')));
        $y1 = $y - 1;
        $next_srinv_no =  $invid->sales_return_invoice_no + 1;
        $sales_return_invid = 'SR'.$y1.$y . '/'. $invid->branch_code . '/' . sprintf('%04d', $next_srinv_no);
        $cust_fname = $this->input->post('cust_fname');
        $cust_lname = $this->input->post('cust_lname');
        //// new invoice no        
        $dcprint = $this->input->post('dcprint');
        $sinvid = $invid->invoice_no + 1; 
        if($dcprint[0] == 0){
            $sinv_no = $y1 .'-'. $y . '/'. $invid->branch_code . '/' . sprintf('%05d', $sinvid);
        }else{
            $sinv_no = 'DC'.$y1 .'-'. $y . '/'. $invid->branch_code . '/' . sprintf('%05d', $sinvid);
        }
//      save sales return
        $selected_sale_products = $this->input->post('chk_return');
        $overall_basic = $this->input->post('selected_total_basic');
        $overall_discount_amt = $this->input->post('selected_total_discount');
        $overall_total_amt = $this->input->post('selected_total_amount');
        $old_landing = $this->input->post('old_landing');
        $data = array(
            'date' => $date,
            'idsale' => $id_sale,
            'doa_id' => $doa_id,
            'brand_doa_date' => $doa_date,
            'sales_return_invid' => $sales_return_invid,
            'sales_return_type' => 3, // doa return
            'doa_return_type' => $doa_return_type,
            'inv_no' => $inv_no,
            'inv_date' => $this->input->post('inv_date'),
            'idbranch' => $idbranch,
            'idcustomer' => $this->input->post('idcustomer'),
            'sales_return_approved_by'=> $this->input->post('sales_return_approved_by'),
            'sales_return_reason'=> "DOA return",
            'idsalesperson' => $this->input->post('idsalesperson'),
            'final_total' => $overall_total_amt,
            'discount_total' => $overall_discount_amt,
            'basic_total' => $overall_basic,
            'sales_return_by' => $sales_return_by,
        );
        // save sales return product
        $idsalereturn = $this->Sales_return_model->save_sales_return($data);
        $imeino = $this->input->post('imei_no'.$idservice);
        
        $imei_history[]=array(
                    'imei_no' => $imeino,
                    'entry_type' => 'DOA Return - Replace/Upgrade',
                    'entry_time' => $return_date,
                    'date' => $date,
                    'idbranch' => $idbranch,
                    'idgodown' => 3,
                    'idvariant' => $this->input->post('idvariant'.$idservice),                    
                    'idimei_details_link' => 9, // Sales Return from imei_details_link table
                    'idlink' => $idsalereturn,
                );
        if($doa_return_type==2){
            $newidbrand = $this->input->post('newidbrand');
            $idv = $this->input->post('model');
            $var_data = $this->General_model->get_model_variant_data_byidvariant($idv);          
            $idmodel = $var_data->idmodel;
            $idskutype = $var_data->idsku_type;
            $idgodown = 1; 
            $idbrand = $var_data->idbrand;
            $idproductcategory = $var_data->idproductcategory;        
            $idcategory = $var_data->idcategory; 
            $new_enter_imei = $this->input->post('new_enter_imei');
            $id_sale = $this->input->post('id_sale');
            $inward_request = array(
                'date' => $date,
                'imei_no' => $new_enter_imei,
                'idvariant' => $idv,
                'idgodown' => 1,
                'idbrand' => $newidbrand,
                'idbranch' => $idbranch,
                'entry_time' => $return_date,
                'created_by' => $sales_return_by,
                'doa_imei' => $imeino,
                'status' => 1,
                'replaced_imei' => $new_imeis,
                'idservice' => $idservice,
                'sales_return_invid' => $sales_return_invid
            );
                $iddoainward=$this->Service_model->save_doa_inward($inward_request);
                $inward_stock = array(
                    'date' => $date,
                    'imei_no' => $new_enter_imei,
                    'idmodel' => $idmodel,
                    'created_by' => $sales_return_by, 
                    'idvariant' => $idv,
                    'idskutype' => $idskutype,
                    'idproductcategory' => $idproductcategory,
                    'idcategory' => $idcategory,
                    'idbrand' => $idbrand,
                    'idgodown' => $idgodown,
                    'idbranch' => $idbranch
                );
                if(count($inward_stock)>0){
                    $this->Inward_model->save_stock($inward_stock);                    
                }  
                $doa_reconciliation = array(   
                       'date' => $date,
                       'imei_no' => $imeino,
                       'idmodel' => $idmodel,
                       'created_by' => $sales_return_by, 
                       'idvariant' => $idv,
                       'idskutype' => $idskutype,
                       'idproductcategory' => $idproductcategory,
                       'idcategory' => $idcategory,
                       'idbrand' => $idbrand,
                       'idgodown' => $idgodown,
                       'idbranch' => $idbranch,
                        'created_by' => $sales_return_by,
                        'sales_return_by' => $sales_return_by,
                        'return_date' => $return_date,
                        'sales_return_type' => 3, 
                        'sale_date' => $this->input->post('inv_date'),
                        'idgodown' => 3,
                        'doa_return_type' => $doa_return_type,
                        'doa_id' => $doa_id,
                        'doa_date' => $doa_date,
                        'status' => 1,
                        'closure_type' => 1,
                        'cn_imei' => $new_enter_imei,
                        'closure_by' => $sales_return_by,
                        'iddoainward' => $iddoainward
                        
                );
                 if(count($doa_reconciliation)>0){
                    $this->Service_model->save_doa_reconciliation($doa_reconciliation);                    
                } 
                
                $imei_history[]=array(
                'imei_no' => $imeino,
                'entry_type' => 'DOA Closure with new Handset',
                'entry_time' => $return_date,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'idvariant' => $idv,                    
                'idimei_details_link' => 8, // Sales Return from imei_details_link table
                'idlink' => 0,
                );
                $imei_history[]=array(
                'imei_no' => $new_enter_imei,
                'entry_type' => 'Inward Againts DOA',
                'entry_time' => $return_date,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'idvariant' => $idv,                    
                'idimei_details_link' => 8, // Sales Return from imei_details_link table
                'idlink' => 0,
                );
            $ary_service = array(
                    'closed' => $date,
                    'process_status' => 11,
                    'closure_remark' => 'Closed - received new handset',
                    'local_to_branch' => $return_date,
                    'sales_return_invid' => $sales_return_invid,
            );            
        }elseif($doa_return_type==1){
            $ary_service = array(
                    'closed' => $date,
                    'process_status' => 11,
                    'closure_remark' => 'Closed -  received DOA letter',
                    'local_to_branch' => $return_date,
                    'sales_return_invid' => $sales_return_invid,
                );
        }elseif($doa_return_type==3){
            $ary_service = array(
                    'closed' => $date,
                    'process_status' => 3,
                    'closure_remark' => 'Closed -  Force DOA',
                    'local_to_branch' => $return_date,
                    'sales_return_invid' => $sales_return_invid,
                );
        }
        
        // update sale product
        $saleupdate = array(
            'sales_return_type' => 3, // doa return
            'sales_return_invid' => $sales_return_invid,
            'sales_return_by' => $sales_return_by,
            'sales_return_date' => $return_date,
        );
        


        $saleproductupdate=array();
        $inward_stock=array();
        for($i = 0; $i < count($selected_sale_products); $i++){
            
            $qty = $this->input->post('qty');
            $previous_return_qty = 0;
            $idmodel = $this->input->post('idmodel'.$idservice);
//            die($total_qty);
            // update sale product
            
                    
            $selected_row_taxable = $this->input->post('taxable_amt');
            $selected_row_cgst_amt = $this->input->post('cgst_amt');
            $selected_row_sgst_amt = $this->input->post('sgst_amt');
            $selected_row_igst_amt = $this->input->post('igst_amt');
            $selected_row_tax = $this->input->post('tax');
            
                
                $sale_product = array(
                    'sales_return_type' => 3, // doa return
                    'doa_return_type' => $doa_return_type,
                    'date' => $date,
                    'imei_no' => $imeino,
                    'sales_return_invid' => $sales_return_invid,
                    'idskutype' => $this->input->post('skutype'.$idservice),
                    'idproductcategory' => $this->input->post('idproductcategory'.$idservice),
                    'idcategory' => $this->input->post('idcategory'.$idservice),
                    'idgodown' => 1,
                    'idvariant' => $this->input->post('idvariant'.$idservice),
                    'idmodel' => $idmodel,
                    'idbranch' => $idbranch,
                    'idbrand' => $this->input->post('idbrand'.$idservice),
                    'sales_return_by' => $sales_return_by,
                    'idsales_return' => $idsalereturn,
                    'product_name' => $this->input->post('ret_product_name'),
                    'price' => $this->input->post('price'.$idservice),
                    'inv_no' => $inv_no,
                    'qty' => 1,
                    'taxable_amt' => $selected_row_taxable,
                    'cgst_per' => $this->input->post('cgst_per'),
                    'sgst_per' => $this->input->post('sgst_per'),
                    'igst_per' => $this->input->post('igst_per'),
                    'cgst_amt' => $selected_row_cgst_amt,
                    'sgst_amt' => $selected_row_sgst_amt,
                    'igst_amt' => $selected_row_igst_amt,
                    'tax' => $selected_row_tax,
                    'basic' => $overall_basic,
                    'discount_amt' => $overall_discount_amt,
                    'total_amount' => $overall_total_amt,
                    'idsale_product' => $selected_sale_products[0],
                    'new_imei_against_doa' => $new_imeis,
                    'old_landing' => $old_landing
                );

                $idsalereturnproduct = $this->Sales_return_model->save_sales_return_product($sale_product);

                 if($doa_return_type!=2){
                        $update_stock = array(                    
                        'created_by' => $sales_return_by,
                        'sales_return_by' => $sales_return_by,
                        'return_date' => $return_date,
                        'sales_return_type' => 3, 
                        'sale_date' => $this->input->post('inv_date'),
                        'idgodown' => 3,
                        'doa_return_type' => $doa_return_type,
                        'doa_id' => $doa_id,
                        'doa_date' => $doa_date,
                        'force_inv_no' => $sinv_no
                    );                    
                }
                
    
            }
        $srpayment = array();
        
// save sale       
//        $idbranch = $this->input->post('idbranch');
        
        $remark=$this->input->post('remark');
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
            'remark' => $remark." - DOA Return amount Rs.".$overall_total_amt,
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
        
        if($headattr){
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
        }
        $parr=array();
        for($j=0; $j < count($idpaymenthead); $j++){
            $received_amount=0;$pending_amt=$amount[$j];$received_entry_time=NULL;$payment_receive=0;
            if($idpaymenthead[$j] == 1){     
                $received_amount = $amount[$j];
                $pending_amt=0;$received_entry_time=$datetime;$payment_receive=1;
                $srpayment[] = array(
                    'date' => $date,
                    'inv_no' => $inv_no,
                    'entry_type' => 1,
                    'idbranch' => $idbranch,
                    'idtable' => $idsale,
                    'table_name' => 'sale',
                    'amount' => $amount[$j],
                );
//                $this->Sale_model->save_daybook_cash_payment($srpayment);
            }
            if($idpaymenthead[$j] == 0){  
                $received_amount = $amount[$j];
                $pending_amt=0;$received_entry_time=$datetime;$payment_receive=1;
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
                'received_amount' => $received_amount,
                'received_entry_time'=>$received_entry_time,
                'payment_receive' => $payment_receive,
            );
            if(isset($vin[$j])>0){
                $payment = array_merge($payment, $vin[$j]); 
            }
            
            $id_sale_payment = $this->Sale_model->save_sale_payment($payment);
            if($credittype[$j] == 0){
                $npayment = array(
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
                    'received_amount' => $received_amount,
                    'pending_amt' => $pending_amt,
                    'received_entry_time'=>$received_entry_time,
                    'payment_receive' => $payment_receive,
                );
                if(isset($vin[$j])>0){
                    $npayment = array_merge($npayment, $vin[$j]); 
                }
                $this->Sale_model->save_payment_reconciliation($npayment);
            }
//            $parr[] = $payment;
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
        if($erp_type==1){
        $saleproductupdate = array(
                'sales_return_type' => 3, // doa return
                'sales_return_invid' => $sales_return_invid,
                'sales_return_by' => $sales_return_by,
                'sales_return_date' => $return_date,
                'sale_return_qty' => 1,
                'doa_imei_no' => $new_imeis,
            );
        }else{
        $saleproductupdate = array(
                'sales_return_type' => 3, // doa return
                'sales_return_invid' => $sales_return_invid,
                'doa_imei_no' => $new_imeis,
            );
        }
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
                'entry_time' => $datetime
                
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
                    'idimei_details_link' => 4, // Sale from imei_details_link table
                    'idlink' => $idsale,
                );
            }
        }
        if(count($srpayment) > 0){
            $this->Sale_model->save_batch_daybook_cash_payment($srpayment);
        }
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
        
        $this->Service_model->update_service_stock($idservice, $ary_service);
        // Update Sale from sales return
        $this->Sale_model->update_sale($id_sale, $saleupdate);
        if($erp_type==1){
            $this->Sale_model->update_sale_product_byidsaleproduct($selected_sale_products[0], $saleproductupdate);     
        }else{
            $this->Service_model->update_sale_product_byidsaleproduct($selected_sale_products[0], $saleproductupdate);     
        }      
        if($doa_return_type!=2){
            $this->Service_model->update_stock($idservice,$update_stock);            
        }else{
            $this->Service_model->delete_idservice_from_stock($idservice);
        }
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
        if($dcprint[0] == 0){
            return redirect('Sale/invoice_print/'.$idsale);
        }else{
            return redirect('Sale/dc_print/'.$idsale);
        }
    } 
    
    
    public function product_noupgrade_form() {
        $idbranch = $_SESSION['idbranch'];
        ?>
        <br>
        <div class="col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1" style="padding: 0">
                       <div class="neucard shadow-inset border-light p-4">
                <div class="shadow-soft border-light rounded p-4" style="background-color: #fff">
                    <div style="background-image: linear-gradient(to right top, #510a0a, #750c11, #9b0c13, #c30e14, #eb1212);box-shadow: 4px 4px 15px rgba(0, 0, 0, 0.5);border-radius: 5px; margin-top: -45px">
                        <div style="padding: 8px; font-size: 17px; margin: 0px; color: #fff">
                            <center><i class="fa fa-clipboard"></i> New Handset Details </center>
                        </div>
                    </div><div class="clearfix"></div>
                    <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch'] ?>"/><br>
                                           
                        <?php $brands = $this->General_model->get_active_brand_data(); ?>
                                <center><h5> Select product to inward new received handset </h5></center><hr>                                        
                                        <div class="col-md-2 col-sm-4">
                                                <span>Brand</span>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <select class="chosen-select form-control input-sm" required="" name="newidbrand" id="newidbrand">
                                                <option value="">Select Brand</option>';
                                                <?php foreach ($brands as $brand) { ?>
                                                    <option value="<?php echo $brand->id_brand; ?>"> <?php echo $brand->brand_name;?></option>
                                                 <?php   } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-sm-4">
                                                <span>Model</span>
                                        </div>                                        
                                        <div class="col-md-4 col-sm-4">
                                            <div class="idvariant">
                                            <select class="chosen-select form-control input-sm" required="" name="model" id="model">
                                                <option value="">Select Model </option>                                    
                                            </select>
                                            </div>
                                        </div>
                                    <div class="clearfix"></div><br>
                                    <div class="col-md-2 col-sm-4">
                                        <span>Scan New IMEI </span>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control input-sm" required="" placeholder="Scan IMEI/SRNO/Barcode" id="new_enter_imei"/>
                                    </div>
                                    <div class="col-md-2 col-sm-4">
                                        <span>Inward Remark</span>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control input-sm" name="in_remark" placeholder="remark" id="in_remark"/>
                                    </div>
                                    <div class="clearfix"></div><hr>
                                    <div class="col-md-2 col-sm-3 col-xs-4 pull-right">
                                        <button type="submit" id="" class="btn btn-success btn-sub" formmethod="POST" formaction="<?php echo site_url('Service/save_product_doa_closure') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Submit</button>
                                    </div><div class="clearfix"></div>
                    <div class="clearfix"></div>
                   
                </div><div class="clearfix"></div>
                <!--</form>-->
            </div><div class="clearfix"></div>
        </div><div class="clearfix"></div>
       
<?php }

      
   public function save_product_doa_closure() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $this->load->model('Transfer_model');
        $this->load->model('Inward_model');
        $this->load->model('Purchase_model');
        $this->load->model('Sales_return_model');
        
        $total_return_cash = 0;
        $this->db->trans_begin();
        $inv_no = $this->input->post('inv_no');
        $idbranch = $this->input->post('idbranch');
        $date = date('Y-m-d');
        $sales_return_by=$this->session->userdata('id_users');                 
        $doa_return_type = $this->input->post('doa_return_type');
        $idservice = $this->input->post('idservice');
        $doa_id = $this->input->post('doa_id');
        $doa_date = $this->input->post('doa_date');
        $erp_type = $this->input->post('erp_type');
        $id_sale = $this->input->post('id_sale');
        $return_date = date('Y-m-d H:i:s');
        $imei_history = array();
        $selected_sale_products = $this->input->post('chk_return');
        
        $imeino = $this->input->post('imei_no'.$idservice);
        
            $newidbrand = $this->input->post('newidbrand');
            $idv = $this->input->post('model');
            $var_data = $this->General_model->get_model_variant_data_byidvariant($idv);          
            $idmodel = $var_data->idmodel;
            $idskutype = $var_data->idsku_type;
            $idgodown = 1; 
            $idbrand = $var_data->idbrand;
            $idproductcategory = $var_data->idproductcategory;        
            $idcategory = $var_data->idcategory; 
            $product_name=$var_data->full_name; 
            $new_enter_imei = $this->input->post('new_enter_imei');
            $id_sale = $this->input->post('id_sale');
            
                $doa_reconciliation = array(   
                       'date' => $date,
                       'imei_no' => $imeino,
                       'idmodel' => $idmodel,
                       'created_by' => $sales_return_by, 
                       'idvariant' => $idv,
                       'idskutype' => $idskutype,
                       'idproductcategory' => $idproductcategory,
                       'idcategory' => $idcategory,
                       'idbrand' => $idbrand,
                       'idgodown' => $idgodown,
                       'idbranch' => $idbranch,
                        'created_by' => $sales_return_by,
                        'sales_return_by' => $sales_return_by,
                        'return_date' => $return_date,
                        'sales_return_type' => 3, 
                        'sale_date' => $this->input->post('inv_date'),
                        'idgodown' => 3,
                        'doa_return_type' => $doa_return_type,
                        'doa_id' => $doa_id,
                        'doa_date' => $doa_date,
                        'status' => 1,
                        'closure_type' => 1,
                        'cn_imei' => $new_enter_imei,
                        'closure_by' => $sales_return_by                        
                );
                 if(count($doa_reconciliation)>0){
                    $this->Service_model->save_doa_reconciliation($doa_reconciliation);                    
                } 
                 $imei_history[]=array(
                'imei_no' => $new_enter_imei,
                'entry_type' => 'Inward Againts DOA',
                'entry_time' => $return_date,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'idvariant' => $idv,                    
                'idimei_details_link' => 8, // Sales Return from imei_details_link table
                'idlink' => 0,
                );
                 $imei_history[]=array(
                    'imei_no' => $new_enter_imei,
                    'entry_type' => 'Sale',
                    'entry_time' => $return_date,
                    'date' => $date,
                    'idbranch' => $idbranch,
                    'idgodown' => 1,
                    'idvariant' => $idv,                    
                    'idimei_details_link' => 4, // Sale from imei_details_link table
                    'idlink' => $id_sale,
                );
               
            $ary_service = array(
                    'closed' => $date,
                    'process_status' => 11,
                    'closure_remark' => 'Closed - received new handset',
                    'local_to_branch' => $return_date
            );   
            
        $saleupdate = array(
            'sales_return_type' => 3, // DOA
            'sales_return_by' => $sales_return_by,
            'sales_return_date' => $return_date,
        ); // Update Sale
        $this->Sale_model->update_sale($id_sale, $saleupdate);
        // update sale product
        $saleproductupdate = array( 
            'sales_return_type' => 3, 
            'sales_return_by' => $sales_return_by,
            'sales_return_date' => $return_date
        );
        $this->Sale_model->update_sale_product_byidsaleproduct($selected_sale_products[0], $saleproductupdate);
       
        $doa_sale_product_entry = array(
            'date' => $date,
            'idsale' => $id_sale,
            'idmodel' => $idmodel,
            'idsale_product_for_doa' => $selected_sale_products[0],
            'idvariant' => $idv,
             'product_name' => $product_name,
            'imei_no' => $new_enter_imei,
            'idskutype' =>$idskutype,
            'idproductcategory' => $idproductcategory,
            'idcategory' => $idcategory,
            'idgodown' => $idgodown,
            'idbrand' => $idbrand,
            'is_gst' => 1,
            'price' => 0,
            'landing' => 0,
            'mrp' => 0,
            'mop' => 0,
            'inv_no' => $inv_no,
            'qty' => 1,
            'idbranch' => $idbranch,
            'discount_amt' => 0,
            'salesman_price' => 0,
            'is_mop' => 0,
            'basic' => 0,
            'cgst_per' => 0,
            'sgst_per' => 0,
            'igst_per' => 0,
            'total_amount' => 0,
            'entry_time' => $return_date,
        );
        $idsaleproduct = $this->Sale_model->save_sale_product($doa_sale_product_entry);
        
        $this->Service_model->update_service_stock($idservice, $ary_service);
       if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
        $this->Service_model->delete_idservice_from_stock($idservice);
        
         if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Fail to receivce. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Service Closure Successfull');
        }
        
        return redirect('Sale/invoice_print/'.$id_sale);
    }
   

   public function ajax_receive_service_case(){
        $idservice=$this->input->post('idservice');
        $remark=$this->input->post('remark');
        $datetime = date('Y-m-d');
        $data = array(
            'process_status' => 11,
            'closed' => $datetime,
            'closure_remark' => $remark
        );
        $data=$this->Service_model->update_service_stock($idservice, $data);
        if($data){
            $this->Service_model->delete_idservice_from_stock($idservice);        
            echo 1;
        } else {
            echo 0;    
        }
   }
   public function ajax_variants_by_olderp_model(){
        $product_name=$this->input->post('product_name');        
        $data=$this->Service_model->get_variants_by_olderp_model($product_name);        
        if($data){                                
            echo '<input type="hidden" name="model" id="model" value="'.$data->idvariant.'" />';
        } else {
             $brands = $this->General_model->get_active_brand_data();
            $html='<center><h5> As this is billed in Old ERP we need to select new ERP Model </h5></center><hr>
                    <div class="col-md-2">Brand </div>
                            <div class="col-md-7">
                                <select class="chosen-select form-control input-sm" required="" name="idbrand" id="idbrand">
                                    <option value="">Select Brand</option>';
                                    foreach ($brands as $brand) { 
                                        $html .= '<option value="'.$brand->id_brand.'">'.$brand->brand_name.'</option>';
                                        }
                                $html .= '</select>
                            </div> 
                            <div class="clearfix"></div><br>  
                            <div class="col-md-2"> Model </div>
                            <div class="col-md-7">
                                <div class="idvariant">
                                <select class="chosen-select form-control input-sm" required="" name="model" id="model">
                                    <option value="">Select Model </option>                                    
                                </select>
                                </div>
                            </div>
                             <div class="clearfix"></div><br>';    
                                echo $html;
        }
   }
   
    public function ajax_verify_imei_presence(){
         $imei=$this->input->post('imei');
        $data=$this->Service_model->verify_imei_presence($imei);
        if(count($data)>0){
            $output = json_encode(array("result" => "true", "data" => "fail", "message" => ""));
            die($output);            
        }else{
            $output = json_encode(array("result" => "false", "data" => "success", "message" => ""));
            die($output);
        }
    } 
    
    
    
   public function force_doa_data(){   
        $idbranch=$this->session->userdata('idbranch');          
        $q['tab_active'] = '';
        $q['force_doa'] = $this->Service_model->get_force_doa_stock_by_PBB(0,0,$idbranch,0);             
        $this->load->view('service/force_doa_stock', $q);
    }
   
    public function force_doa_clerance($id){
        $q['tab_active'] = 'Reports';
        $q['service_data'] = $this->Service_model->get_service_details_byid($id);
        if($q['service_data'][0]->process_status==3){                      
            $this->load->view('service/force_doa_clerance', $q);
        }else{
            return redirect('Service/force_doa_data');
        }       
    }
    public function force_doa_clerance_form() {
        $idbranch = $_SESSION['idbranch'];
       
        $doa_return_type = $this->input->post('doa_return_type');
        
        ?>
        <br>
        <div class="col-md-10 col-md-offset-1 col-lg-10 col-lg-offset-1" style="padding: 0">
            <?php $title="Product Replace/Upgrade Form"; if($doa_return_type==1){ ?>
                <center><span style="color: #9b0c13"><i class="fa fa-phone"></i> Update DOA Details</span></center><br>
             <?php }else{ $title="Product Inward/Upgrade Form"; ?>
                <center><span style="color: #9b0c13"><i class="fa fa-barcode"></i> Inward New Handset </span></center><br>           
                 <?php } ?>
            <div class="thumbnail">
                <div class="border-light rounded p-4" style="background-color: #fff">                                                           
                    <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $_SESSION['idbranch'] ?>"/>
                    <?php if($doa_return_type==1){ ?>
                        <div class="col-md-2 col-sm-4">DOA ID</div>
                        <div class="col-md-4 col-sm-4">
                            <input type="text" class="form-control input-sm doa_id" name="doa_id" id="doa_id" placeholder="Enter DOA ID" required="" />
                        </div>
                        <div class="col-md-2 col-sm-4">DOA Date</div> 
                        <div class="col-md-4 col-sm-4">
                            <input type="text" class="form-control input-sm doa_date" data-provide="datepicker" name="doa_date" id="doa_date" placeholder="Select Date" required="" autocomplete="off" onfocus="blur()" />
                        </div>    
                        <div class="clearfix"></div><hr>
                        <?php }
                    if($doa_return_type==2){                         
                        $brands = $this->General_model->get_active_brand_data(); ?>
                                
                                        <div class="col-md-2 col-sm-4">
                                                <span>Brand</span>
                                        </div>
                                        <div class="col-md-4 col-sm-4">
                                            <select class="chosen-select form-control input-sm" required="" name="newidbrand" id="newidbrand">
                                                <option value="">Select Brand</option>';
                                                <?php foreach ($brands as $brand) { ?>
                                                    <option value="<?php echo $brand->id_brand; ?>"> <?php echo $brand->brand_name;?></option>
                                                 <?php   } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2 col-sm-4">
                                                <span>Model</span>
                                        </div>                                        
                                        <div class="col-md-4 col-sm-4">
                                            <div class="idvariant">
                                            <select class="chosen-select form-control input-sm" required="" name="model" id="model">
                                                <option value="">Select Model </option>                                    
                                            </select>
                                            </div>
                                        </div>
                                    <div class="clearfix"></div><br>
                                    <div class="col-md-2 col-sm-4">
                                        <span>Scan IMEI To Inward</span>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control input-sm" required="" placeholder="Scan IMEI/SRNO/Barcode" id="new_enter_imei"/>
                                    </div>
                                    <div class="col-md-2 col-sm-4">
                                        <span>Inward Remark</span>
                                    </div>
                                    <div class="col-md-4 col-sm-4">
                                        <input type="text" class="form-control input-sm" name="in_remark" placeholder="remark" id="in_remark"/>
                                    </div>
                                    <div class="clearfix"></div><hr>
                    <?php } ?>
                    <div class="clearfix"></div>
                    <div class="col-md-2 col-sm-3 col-xs-4 pull-right">
                            <button type="submit" id="invoice_submit" class="btn btn-success btn-sub" formmethod="POST" formaction="<?php echo site_url('Service/save_force_doa_closure') ?>" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">Submit</button>
                        </div><div class="clearfix"></div>
                </div><div class="clearfix"></div>
                <!--</form>-->
            </div><div class="clearfix"></div>
        </div><div class="clearfix"></div>
        
<?php }

   public function save_force_doa_closure() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
//        echo count($_POST);
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $this->load->model('Transfer_model');
        $this->load->model('Inward_model');
        
        $this->db->trans_begin();
        $inv_no = $this->input->post('inv_no');
        $idbranch = $this->input->post('idbranch');
        $date = date('Y-m-d');
        $sales_return_by=$this->session->userdata('id_users');                 
        $doa_return_type = $this->input->post('doa_return_type');
        $idservice = $this->input->post('idservice');
        $doa_id = $this->input->post('doa_id');
        $doa_date = $this->input->post('doa_date');
        $id_sale = $this->input->post('id_sale');
        $return_date = date('Y-m-d H:i:s');
        $imei_history = array();
        
        $imeino = $this->input->post('imei_no'.$idservice);
        $new_imeis = $this->input->post('new_imei_against_doa');
        $sales_return_invid = $this->input->post('sales_return_invid'.$idservice);        
        if($doa_return_type==2){
            $newidbrand = $this->input->post('newidbrand');
            $idv = $this->input->post('model');
            $var_data = $this->General_model->get_model_variant_data_byidvariant($idv);          
            $idmodel = $var_data->idmodel;
            $idskutype = $var_data->idsku_type;
            $idgodown = 1; 
            $idbrand = $var_data->idbrand;
            $idproductcategory = $var_data->idproductcategory;        
            $idcategory = $var_data->idcategory; 
            $new_enter_imei = $this->input->post('new_enter_imei');
            $id_sale = $this->input->post('id_sale');
            $inward_request = array(
                'date' => $date,
                'imei_no' => $new_enter_imei,
                'idvariant' => $idv,
                'idgodown' => 1,
                'idbrand' => $newidbrand,
                'idbranch' => $idbranch,
                'entry_time' => $return_date,
                'created_by' => $sales_return_by,
                'doa_imei' => $imeino,
                'status' => 1,
                'replaced_imei' => $new_imeis,
                'idservice' => $idservice,
                'sales_return_invid' => $sales_return_invid
            );
                $iddoainward=$this->Service_model->save_doa_inward($inward_request);
                $inward_stock = array(
                    'date' => $date,
                    'imei_no' => $new_enter_imei,
                    'idmodel' => $idmodel,
                    'created_by' => $sales_return_by, 
                    'idvariant' => $idv,
                    'idskutype' => $idskutype,
                    'idproductcategory' => $idproductcategory,
                    'idcategory' => $idcategory,
                    'idbrand' => $idbrand,
                    'idgodown' => $idgodown,
                    'idbranch' => $idbranch
                );
                if(count($inward_stock)>0){
                    $this->Inward_model->save_stock($inward_stock);                    
                }  
                $doa_reconciliation = array(   
                       'date' => $date,
                       'imei_no' => $imeino,
                       'idmodel' => $idmodel,
                       'created_by' => $sales_return_by, 
                       'idvariant' => $idv,
                       'idskutype' => $idskutype,
                       'idproductcategory' => $idproductcategory,
                       'idcategory' => $idcategory,
                       'idbrand' => $idbrand,
                       'idgodown' => $idgodown,
                       'idbranch' => $idbranch,
                        'created_by' => $sales_return_by,
                        'sales_return_by' => $sales_return_by,
                        'return_date' => $return_date,
                        'sales_return_type' => 3, 
                        'sale_date' => $this->input->post('inv_date'),
                        'idgodown' => 3,
                        'doa_return_type' => 3,
                        'doa_id' => $doa_id,
                        'doa_date' => $doa_date,
                        'status' => 1,
                        'closure_type' => 1,
                        'cn_imei' => $new_enter_imei,
                        'closure_by' => $sales_return_by,
                        'iddoainward' => $iddoainward
                        
                );
                 if(count($doa_reconciliation)>0){
                    $this->Service_model->save_doa_reconciliation($doa_reconciliation);                    
                } 
                
                $imei_history[]=array(
                'imei_no' => $imeino,
                'entry_type' => 'DOA Closure with new Handset',
                'entry_time' => $return_date,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'idvariant' => $idv,                    
                'idimei_details_link' => 8, // Sales Return from imei_details_link table
                'idlink' => 0,
                );
                $imei_history[]=array(
                'imei_no' => $new_enter_imei,
                'entry_type' => 'Inward Againts DOA',
                'entry_time' => $return_date,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'idvariant' => $idv,                    
                'idimei_details_link' => 8, // Sales Return from imei_details_link table
                'idlink' => 0,
                );
            $ary_service = array(
                    'closed' => $date,
                    'process_status' => 11,
                    'closure_remark' => 'Closed - Received new handset',
                    'local_to_branch' => $return_date,
                    'sales_return_invid' => $sales_return_invid,
            );            
        }elseif($doa_return_type==1){
            $ary_service = array(
                    'closed' => $date,
                    'process_status' => 11,
                    'closure_remark' => 'Closed -  Received DOA letter',
                    'local_to_branch' => $return_date,
                    'sales_return_invid' => $sales_return_invid,
                );
                $update_stock = array(                    
                        'sales_return_type' => 3, 
                        'idgodown' => 3,
                        'doa_return_type' => 1,
                        'doa_id' => $doa_id,
                        'doa_date' => $doa_date
                    ); 
        }

        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
               
        $this->Service_model->update_service_stock($idservice, $ary_service);
            
        if($doa_return_type==1){
            $this->Service_model->update_stock($idservice,$update_stock);            
        }else{
            $this->Service_model->delete_idservice_from_stock($idservice);
        }
         $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Fail to close force doa! Try again!');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Service Case Closed Successfully');
        }
        
        return redirect('Service/force_doa_data');
        
    }
    

   
   
    
}