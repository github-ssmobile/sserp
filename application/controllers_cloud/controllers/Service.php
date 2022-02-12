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
        $q['tab_active'] = 'Service';      
        $this->load->view('service/service_inward', $q);
    } 
    public function counter_faulty_inward()
    {        
        $q['tab_active'] = 'Service';      
        $this->load->view('service/counter_faulty_inward', $q);
    } 
    public function counter_faulty_receipt_for_care($idservice)
    {        
        $q['tab_active'] = 'Service';
        $q['service_data'] = $this->Service_model->service_counter_faulty_details($idservice);
        $q['user_data'] = $this->Service_model->get_user_data_idbranch_byidrole($q['service_data'][0]->idbranch, 32);
        $this->load->view('service/counter_faulty_receipt_for_care', $q);
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
                            <div class="thumbnail" style="overflow: auto;"><br>
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
                            <div class="clearfix"></div><br>
                            </div>
                            <div class="thumbnail" style="overflow: auto;padding: 0;">
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
                        <div class="thumbnail col-md-12" style="margin-bottom: 400px">
                            <center><h4><i class="mdi mdi-clipboard-text"></i> Service Inward Form</h4></center><hr>   
                            <input type="hidden" name="erp" class="erp" value="1" />
                            <div class="col-md-1">Service Issues </div>
                            <div class="col-md-3">
                                <select class="chosen-select form-control input-sm" required="" name="idproblem" id="idproblem">
                                    <option value="">Select Issues</option>
                                    <?php foreach ($service_problems as $problems){ ?>
                                    <option value="<?php echo $problems->id; ?>"><?php echo $problems->problem; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <input type="hidden" id="problem" name="problem" class="problem" value="" />
                            <!--<div class="clearfix"></div><br>-->
                            <div class="col-md-1">Promoter </div>
                            <div class="col-md-3">
                                <select class="chosen-select  form-control input-sm" name="idsalesperson" required="" id="idsalesperson">
                                    <option value="">Select Sales Promoter</option>
                                    <?php foreach ($active_users_byrole as $user) { if($user->id_users != 0){ ?>
                                        <option value="<?php echo $user->id_users ?>"><?php echo $user->user_name ?></option>
                                    <?php }} ?>
                                </select>
                            </div>
                            <!--<div class="clearfix"></div><br>-->
                            <div class="col-md-1"> Remark </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control input-sm" name="remark" id="remark" required="" placeholder="Enter Remark" />
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
                            
                            <div class="col-md-2 pull-right">
                                <button type="submit" class="btn btn-primary waves-effect waves-light" id="btn_inward" formmethod="POST" formaction="<?php echo base_url('Service/save_service_inward') ?>"><span class="mdi mdi-cellphone-android fa-lg"></span> Inward </button>
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
    
    public function search_stock_byimei() {
        $branch = $this->input->post('branch');
        $level = $this->input->post('level');
        $imei = $this->input->post('imei');
        $models = $this->Sale_model->ajax_stock_data_byimei_branch($imei, $branch);
//        echo print_r($_POST);
        if(count($models)){
            $service_problems = $this->Service_model->get_service_problems();
            foreach($models as $model){
                if($model->idgodown != 1){
                    echo 'Product is Not in New Godown'; // Other that New Godown not accepted
                }else{
                    $amount_diff = $model->mop - $model->landing;
                    $active_users_byrole = $this->General_model->get_active_users_byrole_branch(17, $branch); ?>
                    <div class="col-md-8 col-md-offset-2" style="padding: 0;">
                        <header>
                            <div class="text-center">
                                <h1><?php echo $model->product_name; ?></h1>
                                <p><?php echo $imei; ?></p>
                            </div>
                        </header>
                    </div><div class="clearfix"></div><br>
                    <div class="thumbnail col-md-8 col-md-offset-2" style="margin-bottom: 400px">
                        <center><h4><i class="mdi mdi-clipboard-text"></i> Service Counter Faulty Form</h4></center><hr>
                        <?php // echo $model->id_stock ?>
                        <!--<div class="col-md-2">Model</div>-->
                        <!--<div class="col-md-5"><?php // echo $model->product_name; ?></div>-->
                        <!--<small class="pull-right" style="color:#1b6caa;font-family: Kurale;">New Godown</small>-->
                        <div class="col-md-4 col-md-offset-1" style="color: #113c63">MRP: <?php echo $model->mrp; ?></div>
                        <div class="col-md-4" style="color: #113c63">MOP: <?php echo $model->mop; ?></div>
                        <div class="col-md-3" style="color: #113c63">GST: <?php echo $model->igst ?>%</div>

                        <input type="hidden" id="idtype" class="form-control idtype" name="idtype" value="<?php echo $model->idproductcategory ?>" />
                        <input type="hidden" id="idcategory" class="form-control idcategory" name="idcategory" value="<?php echo $model->idcategory ?>" />
                        <input type="hidden" id="idbrand" class="form-control" name="idbrand" value="<?php echo $model->idbrand ?>" />
                        <input type="hidden" id="idvariant" class="form-control idvariant" name="idvariant" value="<?php echo $model->idvariant ?>" />
                        <input type="hidden" id="idmodel" class="form-control idmodel" name="idmodel" value="<?php echo $model->idmodel ?>" />
                        <input type="hidden" id="idgodown" class="form-control idgodown" name="idgodown" value="<?php echo $model->idgodown ?>" />
                        <input type="hidden" id="skutype" class="form-control skutype" name="skutype" value="<?php echo $model->idskutype ?>" />
                        <input type="hidden" id="product_name" class="form-control product_name" name="product_name" value="<?php echo $model->product_name; ?>" />
                        <input type="hidden" id="is_mop" class="form-control is_mop" name="is_mop" value="<?php echo $model->is_mop; ?>" />
                        <input type="hidden" id="hsn" class="form-control hsn" name="hsn" value="<?php echo $model->hsn; ?>" />
                        <input type="hidden" id="is_gst" class="form-control is_gst" name="is_gst" value="<?php echo $model->is_gst; ?>" />
                        <!--<div class="col-md-4">-->
                        <input type="hidden" id="imei" name="imei" class="imei" value="<?php echo $imei ?>" />
                            <?php // echo $imei; ?></b>
                        <!--</div><div class="clearfix"></div><hr>-->
                        <input type="hidden" id="readprice<?php echo $model->id_stock ?>" value="<?php echo $model->mop ?>" />
                        <input type="hidden" id="landing" name="landing" class="landing" value="<?php echo $model->landing ?>" />
                        <input type="hidden" id="mop" name="mop" class="mop" value="<?php echo $model->mop ?>" />
                        <input type="hidden" id="mrp" name="mrp" class="mrp" value="<?php echo $model->mrp ?>" />
                        <input type="hidden" id="salesman_price" name="salesman_price" class="salesman_price" value="<?php echo $model->salesman_price ?>" />
                        <input type="hidden" id="basic" name="basic" class="basic" placeholder="Basic" readonly="" value="<?php echo $model->mop ?>"/>
                        <input type="hidden" class="price_diff" id="price_diff" value="<?php echo $amount_diff ?>" />
                        <input type="hidden" id="idvendor" name="idvendor" class="idvendor" value="<?php echo $model->idvendor ?>"/>
                        <input type="hidden" id="cgst" name="cgst" class="form-control input-sm cgst" value="<?php echo $model->cgst; ?>" readonly=""/>
                        <input type="hidden" id="sgst" name="sgst" class="form-control input-sm sgst" value="<?php echo $model->sgst; ?>" readonly=""/>
                        <input type="hidden" id="igst" name="igst" class="form-control input-sm igst" value="<?php echo $model->igst; ?>" readonly=""/>
                        <input type="hidden" id="total_amt" name="total_amt" class="form-control total_amt input-sm" placeholder="Amount" value="<?php echo $model->mop ?>" required="" />
                            <!--<span id="sptotal_amt" class="sptotal_amt" name="sptotal_amt"><?php // echo $model->mop ?></span>-->
                            <!--<a class="btn btn-sm gradient1 btn-warning remove" name="remove" id="remove"><i class="fa fa-trash-o fa-lg"></i></a>-->
                            <!--<input type="hidden" name="rowid" id="rowid" class="rowid" value="<?php echo $model->id_stock ?>" />-->
                        <div class="clearfix"></div><hr>
                        <div class="col-md-2 col-md-offset-1">Service Issues </div>
                        <div class="col-md-7">
                            <select class="chosen-select form-control input-sm" required="" name="idproblem" id="idproblem">
                                <option value="">Select Issues</option>
                                <?php foreach ($service_problems as $problems){ ?>
                                <option value="<?php echo $problems->id; ?>"><?php echo $problems->problem; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="clearfix"></div><br>
                        <div class="col-md-2 col-md-offset-1">Promoter </div>
                        <div class="col-md-7">
                            <select class="chosen-select  form-control input-sm" name="idsalesperson" required="" id="idsalesperson">
                                <option value="">Select Sales Promoter</option>
                                <?php foreach ($active_users_byrole as $user) { if($user->id_users != 0){ ?>
                                    <option value="<?php echo $user->id_users ?>"><?php echo $user->user_name ?></option>
                                <?php }} ?>
                            </select>
                        </div>
                        <div class="clearfix"></div><br>
                        <input type="hidden" id="problem" name="problem" class="problem" value="" />
                        <div class="col-md-2 col-md-offset-1"> Remark </div>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="remark" id="remark" required="" placeholder="Enter Remark" />
                        </div>
                        <div class="clearfix"></div><hr>
                        <div class="col-md-2 pull-right">
                            <button type="submit" class="btn btn-primary waves-effect waves-light" id="btn_inward" formmethod="POST" formaction="<?php echo base_url('Service/save_counter_faulty_inward') ?>"><span class="mdi mdi-cellphone-android fa-lg"></span> Submit </button>
                        </div><div class="clearfix"></div>
                    </div><div class="clearfix"></div>
            <?php }}}else{
                echo '<center><h3><i class="mdi mdi-alert"></i> You Enter Wrong IMEI, Product Not in Your Branch Stock</h3>' .
                '<img src="' . base_url() . 'assets/images/highAlertIcon.gif" />'
                . '</center>';
            }
    }

    public function save_counter_faulty_inward() {
//        die(print_r($_POST));
        $date = date('Y-m-d');
        $inward_date = date('Y-m-d H:i:s');
        $this->db->trans_begin();
//        $idgodown = 4;
        $idgodown = 1; // For approval request
        $idbranch = $this->input->post('branch');
        $imei_no = $this->input->post('imei');
        $user_id = $this->session->userdata('id_users');
        $idproblem = $this->input->post('idproblem');
        $problem  = $this->input->post('problem');
        $data = array(
            'idbranch' => $idbranch,  
            'imei' => $imei_no,
            'counter_faulty' => 1,
            'erp_type' => 1,                   
            'idskutype' => $this->input->post('skutype'),
            'idgodown' => 1,
            'idproductcategory' => $this->input->post('idtype'),
            'idcategory' => $this->input->post('idcategory'),
            'idmodel' => $this->input->post('idmodel'),
            'idvariant' => $this->input->post('idvariant'),
            'idbrand' => $this->input->post('idbrand'),
            'branch_inwart' => $date,
            'idusers' => $user_id,
            'problem_id' => $idproblem,
            'problem' => $problem,
            'process_status' => 1,
            'entry_time' => $inward_date,
            'remark' => $this->input->post('remark'),
            'sold_amount' => $this->input->post('mop'),
        );
        // save service inward
        $idservice=$this->Service_model->save_service_inward($data);
        $update_stock = array(
            'idgodown' => $idgodown,
            'idservice' => $idservice
        );
        $this->Service_model->update_stock_byimei($imei_no,$update_stock);
        $imei_history=array();
        $imei_history[]=array(
            'imei_no' => $imei_no,
            'entry_type' => 'Service Inward - Counter Faulty(Pending For Approval)',
            'entry_time' => $inward_date,
            'date' => $date,
            'idbranch' => $idbranch,
            'idgodown' => $idgodown,
            'iduser' => $user_id,
            'idvariant' => $this->input->post('idvariant'),
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
        return redirect('Service/service_counter_faulty_details/'.$idservice);
    }
    
    public function approve_counter_faulty_inward() {
//        die(print_r($_POST));
        $date = date('Y-m-d');
        $inward_date = date('Y-m-d H:i:s');
        $this->db->trans_begin();
        $idbranch = $this->input->post('idbranch');
        $idservice = $this->input->post('idservice');
        $imei_no = $this->input->post('imei_no');
        $user_id = $this->input->post('iduser');
        $counter_faulty_btn = $this->input->post('counter_faulty_btn');
        if($counter_faulty_btn == 1){
            $idgodown = 4; // For approval request
            $update_stock = array('idgodown' => 4);
            $this->Service_model->update_stock_byimei($imei_no,$update_stock);
            $updata_service = array(
                'counter_faulty_approval' => 1,
                'counter_faulty_app_date' => $inward_date,
                'counter_faulty_app_remark' => $this->input->post('counter_faulty_approve_remark'),
            );
            $this->Service_model->update_service_stock($idservice, $updata_service);
        }else{
            $idgodown = 1; // For rejected request
            $updata_service = array(
                'process_status' => 11,
                'closed' => $date,
                'counter_faulty_approval' => 2,
                'counter_faulty_app_date' => $inward_date,
                'counter_faulty_app_remark' => $this->input->post('counter_faulty_approve_remark'),
            );
            $this->Service_model->update_service_stock($idservice, $updata_service);
        }
        
        if($counter_faulty_btn == 1){
            $imei_history[]=array(
                'imei_no' => $imei_no,
                'entry_type' => 'Service Inward - Counter Faulty(Approved by Co-ordinator)',
                'entry_time' => $inward_date,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 4,
                'iduser' => $user_id,
                'idvariant' => $this->input->post('idvariant'),
                'idimei_details_link' => 19, // Sales Return from imei_details_link table
                'idlink' => $idservice,
            );
        }else{
            $imei_history[]=array(
                'imei_no' => $imei_no,
                'entry_type' => 'Service case closed - Counter Faulty(Rejected by Co-ordinator)',
                'entry_time' => $inward_date,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'iduser' => $user_id,
                'idvariant' => $this->input->post('idvariant'),
                'idimei_details_link' => 19, // Sales Return from imei_details_link table
                'idlink' => $idservice,
            );
        }
        $this->General_model->save_batch_imei_history($imei_history);
        $this->db->trans_complete();
        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Failed to update service case. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Serive case updated Successfully');
        }
        return redirect('Service/service_counter_faulty_details/'.$idservice);
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
            'branch_inwart' => $date,
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
            'is_gst' => $this->input->post('is_gst'),
            'idvendor' => $this->input->post('idvendor'),
            'idgodown' => $idgodown,
            'idbranch' => $idbranch,
            'idservice' => $idservice
        );
        if (count($inward_stock) > 0) {
            $this->Inward_model->save_stock($inward_stock);
        }
        $imei_history = array();
        $imei_history[] = array(
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
        $q['service_process_status'] = $this->Service_model->get_service_process_status();
        $this->load->view('service/service_details', $q);
    }
    public function service_counter_faulty_details($id){
        $q['tab_active'] = 'Reports';
        $q['service_data'] = $this->Service_model->service_counter_faulty_details($id);
        $q['service_process_status'] = $this->Service_model->get_service_process_status();
        $this->load->view('service/service_counter_faulty_details', $q);
    }
    
    public function process_service_details($id){
        $q['tab_active'] = 'Reports';
        $q['service_data'] = $this->Service_model->get_service_details_byid($id);
        $idbranch = $_SESSION['idbranch'];
//        $q['total_selected_cash'] = $this->input->post('total_selected_sum');
        $q['invoice_no'] = $this->Sale_model->get_invoice_no_by_branch($idbranch);
        $q['payment_head'] = $this->General_model->get_active_payment_head();
        $q['payment_mode'] = $this->General_model->get_active_payment_mode();
        $q['payment_attribute'] = $this->General_model->get_payment_head_has_attributes();
        $q['state_data'] = $this->General_model->get_state_data();
        $q['active_users_byrole'] = $this->General_model->get_active_users_byrole_branch(17, $idbranch);
        $q['model_variant'] = $this->General_model->ajax_get_model_variant_byidskutype(4);
        
        $this->load->view('service/process_service_details', $q);
    }
    public function process_service_counter_faulty_details($id){
        $q['tab_active'] = 'Reports';
        $q['service_data'] = $this->Service_model->service_counter_faulty_details($id);
        $this->load->view('service/process_service_counter_faulty_details', $q);
    }
    public function receive_service_shipment($id){
        $q['tab_active'] = 'Reports';
        $q['service_data'] = $this->Service_model->service_shipment_at_ho($id);
        $this->load->view('service/receive_service_shipment', $q);
    }
    public function dc_receive_service_shipment($id){
        $q['tab_active'] = 'Reports';
        $q['service_data'] = $this->Service_model->service_shipment_at_ho($id);
        $this->load->view('service/dc_receive_service_shipment', $q);
    }
    public function receive_service_shipment_at_branch($id){
        $q['tab_active'] = 'Reports';
        $q['service_data'] = $this->Service_model->service_shipment_at_branch($id);
        $this->load->view('service/receive_service_shipment_at_branch', $q);
    }
    public function dc_receive_service_shipment_at_branch($id){
        $q['tab_active'] = 'Reports';
        $q['service_data'] = $this->Service_model->service_shipment_at_branch($id);
        $this->load->view('service/dc_receive_service_shipment_at_branch', $q);
    }
    
    public function service_report(){
        $user_id=$this->session->userdata('id_users'); 
        $role_type=$this->session->userdata('role_type');   
        $level=$this->session->userdata('level');   
        $q['tab_active'] = '';
        $q['service_process_status'] = $this->Service_model->get_service_process_status();
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
    public function doa_inward_list(){
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
        $this->load->view('service/doa_inward_list', $q);
    }
    public function verified_service_by_coordiantor(){
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
        $this->load->view('service/verified_service_by_coordiantor', $q);
    }
    
    public function send_to_branch_list(){
        $user_id=$this->session->userdata('id_users');
        $role_type=$this->session->userdata('role_type');
        $level=$this->session->userdata('level');
        $q['tab_active'] = '';
        $q['service_process_status'] = $this->Service_model->get_service_process_status();
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
        $this->load->view('service/send_to_branch_list', $q);
    }
    
    public function service_branch_send_to_ho(){
        $user_id=$this->session->userdata('id_users'); 
        $role_type=$this->session->userdata('role_type');   
        $level=$this->session->userdata('level');   
        $q['tab_active'] = '';
//        $q['service_process_status'] = $this->Service_model->get_service_process_status();
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
        $this->load->view('service/service_branch_send_to_ho', $q);
    }
    
    public function service_ho_send_to_branch(){
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
        $this->load->view('service/service_ho_send_to_branch', $q);
    }
    
    public function service_allocate_to_excecutive(){
        $user_id=$this->session->userdata('id_users');
        $role_type=$this->session->userdata('role_type');   
        $level=$this->session->userdata('level');   
        $q['tab_active'] = '';
//        $q['service_process_status'] = $this->Service_model->get_service_process_status();
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
        $this->load->view('service/service_allocate_to_excecutive', $q);
    }
    public function assigned_cases_to_excecutive(){
        $user_id=$this->session->userdata('id_users');
        $role_type=$this->session->userdata('role_type');   
        $level=$this->session->userdata('level');   
        $q['tab_active'] = '';
//        $q['service_process_status'] = $this->Service_model->get_service_process_status();
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
        $q['service_excecutive'] = $this->General_model->get_user_byidrole(39);
        $this->load->view('service/assigned_cases_to_excecutive', $q);
    }
    public function processed_list_by_excecutive(){
        $user_id=$this->session->userdata('id_users');
        $role_type=$this->session->userdata('role_type');   
        $level=$this->session->userdata('level');   
        $q['tab_active'] = '';
//        $q['service_process_status'] = $this->Service_model->get_service_process_status();
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
        $q['service_excecutive'] = $this->General_model->get_user_byidrole(39);
        $this->load->view('service/service_procesed_by_excecutive', $q);
    }
    public function force_doa_process_by_coord(){
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
        $this->load->view('service/force_doa_process_by_coord', $q);
    }
    public function processed_list_report_by_executive_on_exc(){
        $user_id=$this->session->userdata('id_users');
        $role_type=$this->session->userdata('role_type');   
        $level=$this->session->userdata('level');   
        $q['tab_active'] = '';
//        $q['service_process_status'] = $this->Service_model->get_service_process_status();
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
        $q['service_excecutive'] = $this->General_model->get_user_byidrole(39);
        $this->load->view('service/processed_list_report_by_executive_on_exc', $q);
    }
    public function branch_received_by_ho(){
        $user_id=$this->session->userdata('id_users');
        $role_type=$this->session->userdata('role_type');   
        $level=$this->session->userdata('level');   
        $q['tab_active'] = '';
//        $q['service_process_status'] = $this->Service_model->get_service_process_status();
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
        $q['service_excecutive'] = $this->General_model->get_user_byidrole(39);
        $this->load->view('service/branch_received_by_ho', $q);
    }
    
    public function ajax_get_service_stock_report(){
//        die(print_r($_POST));
        $brand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
        $status = $this->input->post('status');
        $product_category = $this->input->post('product_category');
        $warranty = $this->input->post('warranty');
        $service_stock = $this->Service_model->get_service_stock_report($brand, $product_category,$idbranch,$status, $warranty);
        if($service_stock){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>Case ID</th>
                    <th>Counter Faulty/ Sold</th>
                    <th>Branch</th>
                    <th>Branch Service Inward Date</th>                    
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Brand</th>
                    <th>Product name</th>
                    <th>IMEI/SRNO</th>
                    <th>Problem</th>
                    <th>Remark</th>                    
                    <th>Customer</th>
                    <th>Inward in HO</th>                    
                    <th>Pending Days</th>
                    <th>State</th>
                    <th>DOA Letter/Handset</th>
                    <th>Status</th>
                    <th>Info</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $stock->id_service; ?></td>
                        <td><?php if($stock->counter_faulty){
                            echo 'Counter Faulty'; 
                            if($stock->counter_faulty_approval == 0){
                                echo '<br><small class="red-text">Approval Pending</small>';
                            }elseif($stock->counter_faulty_approval == 2){
                                echo '<br><small class="red-text">Rejected</small>';
                            }
                        }else{ echo 'Sold'; } ?></td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td><?php if($stock->entry_time && $stock->entry_time != '0000-00-00'){ echo date('d-m-Y', strtotime($stock->entry_time)); } ?></td>
                        <td><?php echo $stock->inv_no ?></td>
                        <td><?php echo $stock->inv_date ?></td>
                        <td><?php echo $stock->brand_name ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->imei ?></td>
                        <td><?php echo $stock->problem ?></td>
                        <td><?php echo $stock->remark ?></td>
                        <td><?php echo $stock->customer_name.'-'.$stock->mob_number ?></td>
                        <td><?php if($stock->ho_inward_from_branch && $stock->ho_inward_from_branch != '0000-00-00'){ echo date('d-m-Y', strtotime($stock->ho_inward_from_branch)); } ?></td>
                        <td><?php 
                                if($stock->process_status == 11){
                                    $now = strtotime($stock->closed); // or your date as well
                                }else{
                                    $now = time(); // or your date as well
                                }
                                $your_date = strtotime($stock->entry_time);
                                $datediff = $now - $your_date;
                                echo round($datediff / (60 * 60 * 24));
                            ?>
                        </td>
                        <?php if($stock->warranty_status == 1){ ?>
                        <td>Repaired</td><td></td>
                        <?php }elseif($stock->warranty_status == 2){ ?>
                        <td>Rejected</td><td></td>
                        <?php }elseif($stock->warranty_status == 3){ ?>
                        <td>DOA Letter</td>
                        <td><a class="btn" target="_blank" href="<?php echo base_url('assets/doa_letter_file/'.$stock->doa_letter_path) ?>" style="color: #1b6caa"><i class="mdi mdi-note-text"></i> Letter</a></td>
                        <?php }elseif($stock->warranty_status == 4){ ?>
                        <td>DOA Handset</td>
                        <td><?php echo $stock->new_imei_against_doa ?></td>
                        <?php }else{ ?>
                        <td></td>
                        <td></td>
                        <?php } ?>
                        <td><?php echo $stock->delivery_status ?></td>
                        <td>
                            <?php if($stock->counter_faulty){ ?>
                            <a href="<?php echo base_url('Service/service_counter_faulty_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-info"></i></center></a>
                            <?php }else{ ?>
                            <a href="<?php echo base_url('Service/service_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-info"></i></center></a>
                            <?php } ?>
                        </td>
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
    public function ajax_get_doa_inward_list(){
//        die(print_r($_POST));
        $brand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
        $product_category = $this->input->post('product_category');
        $warranty = $this->input->post('warranty');
        $service_stock = $this->Service_model->ajax_get_doa_inward_list($brand,$idbranch,$warranty);
        if($service_stock){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>Case ID</th>
                    <th>Inward Date</th>
                    <th>Branch</th>
                    <th>DOA IMEI</th>
                    <th>Product name</th>
                    <th>New IMEI</th>
                    <th>Type</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $stock->idservice; ?></td>
                        <td><?php echo date('d-m-Y h:i a', strtotime($stock->entry_time)); ?></td>
                        <td><?php echo $stock->branch_name ?></td>
                        <td><?php echo $stock->doa_imei; ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->imei_no ?></td>
                        <?php if($stock->idvariant == 0){ ?>
                        <td>Letter</td>
                        <?php }else{ ?>
                        <td>Replaced Product</td>
                        <?php } ?>
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
    
    public function ajax_get_coordiantor_verified_service_stock(){
//        die(print_r($_POST));
        $brand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
        $status = $this->input->post('status');
        $product_category = $this->input->post('product_category');
        $warranty = $this->input->post('warranty');
        $service_stock = $this->Service_model->get_service_stock_report($brand, $product_category,$idbranch,$status, $warranty);
        if($service_stock){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>Case ID</th>
                    <th>Counter Faulty/ Sold</th>
                    <th>Branch</th>
                    <th>Inward Date</th>                    
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Product name</th>
                    <th>IMEI/SRNO</th>
                    <th>Problem</th>
                    <th>Remark</th>                    
                    <th>Customer</th>
                    <th>State</th>
                    <th>DOA Letter/Handset</th>
                    <th>Status</th>
                    <th>Info</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $stock->id_service; ?></td>
                        <td><?php if($stock->counter_faulty){ echo 'Counter Faulty'; }else{ echo 'Sold'; } ?></td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($stock->entry_time)); ?></td>
                        <td><?php echo $stock->inv_no ?></td>
                        <td><?php echo $stock->inv_date ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->imei ?></td>
                        <td><?php echo $stock->problem ?></td>
                        <td><?php echo $stock->remark ?></td>
                        <td><?php echo $stock->customer_name.'-'.$stock->mob_number ?></td>
                        <?php if($stock->warranty_status == 3){ ?>
                        <td>DOA Letter</td>
                        <td><a class="btn" target="_blank" href="<?php echo base_url('assets/doa_letter_file/'.$stock->doa_letter_path) ?>" style="color: #1b6caa"><i class="mdi mdi-note-text"></i> Letter</a></td>
                        <?php }elseif($stock->warranty_status == 4){ ?>
                        <td>DOA Handset</td>
                        <td><?php echo $stock->new_imei_against_doa ?></td>
                        <?php }else{ ?>
                        <td></td>
                        <td></td>
                        <?php } ?>
                        </td>
                        <td><?php echo $stock->delivery_status ?></td>
                        <td>
                            <?php 
                            if($stock->branch_process_enable){
                                if($stock->counter_faulty){ ?>
                                <a href="<?php echo base_url('Service/process_service_counter_faulty_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-sign-in"></i></center></a>
                                <?php }else{ ?>
                                <a href="<?php echo base_url('Service/process_service_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-sign-in"></i></center></a>
                            <?php }}else{
                                if($stock->counter_faulty){ ?>
                                <a href="<?php echo base_url('Service/service_counter_faulty_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-sign-in"></i></center></a>
                                <?php }else{ ?>
                                <a href="<?php echo base_url('Service/service_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-sign-in"></i></center></a>
                            <?php }} ?>
                        </td>
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
    
    public function ajax_get_service_send_to_branch_list(){
//        die(print_r($_POST));
        $brand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
        $status = $this->input->post('status');
        $product_category = $this->input->post('product_category');
        $warranty = $this->input->post('warranty');
        $service_stock = $this->Service_model->get_service_stock_report($brand, $product_category,$idbranch,$status, $warranty);
        if($service_stock){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Sr</th>
                    <th>Case ID</th>
                    <th>Counter Faulty/ Sold</th>
                    <th>Branch</th>
                    <th>Inward Date</th>                    
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Brand</th>
                    <th>Product</th>
                    <th>IMEI/SRNO</th>
                    <th>Problem</th>
                    <th>Remark</th>                    
                    <th>Customer</th>
                    <th>State</th>
                    <th>DOA Letter/Handset</th>
                    <th>Status</th>
                    <th>Send to Branch</th>
                    <th>Info</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $stock->id_service; ?></td>
                        <td><?php if($stock->counter_faulty){ echo 'Counter Faulty'; }else{ echo 'Sold'; } ?></td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($stock->entry_time)); ?></td>
                        <td><?php echo $stock->inv_no ?></td>
                        <td><?php echo $stock->inv_date ?></td>
                        <td><?php echo $stock->brand_name ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->imei ?></td>
                        <td><?php echo $stock->problem ?></td>
                        <td><?php echo $stock->remark ?></td>
                        <td><?php echo $stock->customer_name.'-'.$stock->mob_number ?></td>
                        <?php if($stock->warranty_status == 1){ ?>
                        <td>Repaired</td><td></td>
                        <?php }elseif($stock->warranty_status == 2){ ?>
                        <td>Rejected</td><td></td>
                        <?php }elseif($stock->warranty_status == 3){ ?>
                        <td>DOA Letter</td>
                        <td><a class="btn" target="_blank" href="<?php echo base_url('assets/doa_letter_file/'.$stock->doa_letter_path) ?>" style="color: #1b6caa"><i class="mdi mdi-note-text"></i> Letter</a></td>
                        <?php }elseif($stock->warranty_status == 4){ ?>
                        <td>DOA Handset</td>
                        <td><?php echo $stock->new_imei_against_doa ?></td>
                        <?php }else{ ?>
                        <td></td>
                        <td></td>
                        <?php } ?>
                        <td><?php echo $stock->delivery_status ?></td>
                        <td>
                            <center>
                                <input class="hide_checkbox sel_product" type="checkbox" name="checkrow[]" id="checkrow" value="<?php echo $stock->id_service; ?>">
                                <input class="row_branch" type="hidden" value="<?php echo $stock->idbranch; ?>">
                                <input class="row_branch_name" type="hidden" value="<?php echo $stock->branch_name; ?>">
                            </center>
                        </td>
                        <td>
                            <?php if($stock->counter_faulty){ ?>
                            <a href="<?php echo base_url('Service/service_counter_faulty_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-sign-in"></i></center></a>
                            <?php }else{ ?>
                            <a href="<?php echo base_url('Service/service_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-sign-in"></i></center></a>
                            <?php } ?>
                        </td>
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
    
    public function ajax_get_service_branch_send_to_ho_report(){
        $brand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
//        $status = $this->input->post('status');
        $product_category = $this->input->post('product_category');
        $service_stock = $this->Service_model->get_ho_shipment_service_stock_for_process($idbranch);
//        echo print_r($service_stock);
        if(count($service_stock)>0){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>DC No</th>
                    <th>Branch</th>
                    <th>Entry Time</th>                    
                    <th>Pending Days</th>                    
                    <th>Dispatch type</th>
                    <th>Courier name</th>
                    <th>PO LR No</th>                    
                    <th>No of Boxes</th>
                    <th>Shipment Remark</th>
                    <th>Receive Shipment</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo 'DC/SW/'.$stock->id_service_transfer; ?></td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td>'<?php echo date('d-m-Y H:i:s a', strtotime($stock->entry_time)); ?></td>
                        <td><?php $now = time(); // or your date as well
                                $your_date = strtotime($stock->entry_time);
                                $datediff = $now - $your_date;
                                echo round($datediff / (60 * 60 * 24)); ?></td>
                        <td><?php echo $stock->dispatch_type ?></td>
                        <td><?php echo $stock->courier_name ?></td>
                        <td><?php echo $stock->po_lr_no ?></td>
                        <td><?php echo $stock->no_of_boxes ?></td>
                        <td><?php echo $stock->shipment_remark ?></td>
                        <td>
                            <a href="<?php echo base_url('Service/receive_service_shipment/'.$stock->id_service_transfer) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-sign-in"></i></center></a>
                        </td>
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
    
    public function ajax_get_service_ho_send_to_branch_report(){
        $brand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
        $product_category = $this->input->post('product_category');
        $service_stock = $this->Service_model->get_service_ho_send_to_branch_process($brand, $product_category,$idbranch,9);
//        echo print_r($service_stock[0]);
        if(count($service_stock)>0){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>DC No</th>
                    <th>Sender</th>
                    <th>Entry Time</th>                    
                    <th>Pending Days</th>                    
                    <th>Dispatch type</th>
                    <th>Courier name</th>
                    <th>PO LR No</th>                    
                    <th>No of Boxes</th>
                    <th>Shipment Remark</th>
                    <th>Receive Shipment</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo 'DC/SW/'.$stock->id_service_transfer; ?></td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td>'<?php echo date('d-m-Y H:i:s a', strtotime($stock->entry_time)); ?></td>
                        <td><?php $now = time(); // or your date as well
                                $your_date = strtotime($stock->entry_time);
                                $datediff = $now - $your_date;
                                echo round($datediff / (60 * 60 * 24)); ?></td>
                        <td><?php echo $stock->dispatch_type ?></td>
                        <td><?php echo $stock->courier_name ?></td>
                        <td><?php echo $stock->po_lr_no ?></td>
                        <td><?php echo $stock->no_of_boxes ?></td>
                        <td><?php echo $stock->shipment_remark ?></td>
                        <td>
                            <a href="<?php echo base_url('Service/receive_service_shipment_at_branch/'.$stock->id_service_transfer) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-sign-in"></i></center></a>
                        </td>
                    </tr>
<!--                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $stock->id_service; ?></td>
                        <td><?php if($stock->counter_faulty){ echo 'Counter Faulty'; }else{ echo 'Sold'; } ?></td>
                        <td><?php echo $stock->sender_name; ?></td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($stock->entry_time)); ?></td>
                        <td><?php echo $stock->inv_no ?></td>
                        <td><?php echo $stock->inv_date ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->imei ?></td>
                        <td><?php echo $stock->problem ?></td>
                        <td><?php echo $stock->remark ?></td>
                        <td><?php echo $stock->customer_name.'-'.$stock->mob_number ?></td>
                        <?php if($stock->warranty_status == 1){ ?>
                        <td>Repaired</td><td></td>
                        <?php }elseif($stock->warranty_status == 2){ ?>
                        <td>Rejected</td><td></td>
                        <?php }elseif($stock->warranty_status == 3){ ?>
                        <td>DOA Letter</td>
                        <td><a class="btn" target="_blank" href="<?php echo base_url('assets/doa_letter_file/'.$stock->doa_letter_path) ?>" style="color: #1b6caa"><i class="mdi mdi-note-text"></i> Letter</a></td>
                        <?php }elseif($stock->warranty_status == 4){ ?>
                        <td>DOA Handset</td>
                        <td><?php echo $stock->new_imei_against_doa ?></td>
                        <?php }else{ ?>
                        <td>Pending</td>
                        <td></td>
                        <?php } ?>
                        <td><?php echo $stock->delivery_status ?></td>                        
                        <td>
                            <?php // if($stock->counter_faulty){ ?>
                            <a href="<?php // echo base_url('Service/receive_service_shipment/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-sign-in"></i></center></a>
                            <?php // }else{ ?>
                            <a href="<?php echo base_url('Service/receive_service_shipment_at_branch/'.$stock->idservice_transfer_send_to_branch) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-sign-in"></i></center></a>
                            <?php // } ?>
                        </td>
                    </tr>-->
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
    
    public function ajax_get_service_allocate_to_excecutive(){
        $brand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
//        $status = $this->input->post('status');
        $product_category = $this->input->post('product_category');
        $service_stock = $this->Service_model->get_service_stock_for_process($brand, $product_category,$idbranch,5);
        $service_excecutive = $this->General_model->get_user_byidrole(39);
        
//        echo print_r($service_stock);
        if(count($service_stock)>0){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>Case ID</th>
                    <th>Counter Faulty/ Sold</th>
                    <th>Branch</th>
                    <th>Inward Date</th>                    
                    <th>Pending Days</th>
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Brand</th>
                    <th>Product</th>
                    <th>IMEI/SRNO</th>
                    <th>Problem</th>
                    <th>Remark</th>                    
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Allocate</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $stock->id_service; ?></td>
                        <td><?php if($stock->counter_faulty){ echo 'Counter Faulty'; }else{ echo 'Sold'; } ?></td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($stock->entry_time)); ?></td>
                        <td><?php $now = time(); // or your date as well
                                $your_date = strtotime($stock->entry_time);
                                $datediff = $now - $your_date;
                                echo round($datediff / (60 * 60 * 24)); ?></td>
                        <td><?php echo $stock->inv_no ?></td>
                        <td><?php echo $stock->inv_date ?></td>
                        <td><?php echo $stock->brand_name ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->imei ?></td>
                        <td><?php echo $stock->problem ?></td>
                        <td><?php echo $stock->remark ?></td>
                        <td><?php echo $stock->customer_name.'-'.$stock->mob_number ?></td>
                        <td><?php echo $stock->delivery_status ?></td>                        
                        <td style="min-width: 250px">
                            <div class="col-md-8" style="padding: 2px">
                                <select class="form-control input-sm excecutive" name="excecutive">
                                    <option value="">Select Excecutive</option>
                                    <?php foreach ($service_excecutive as $excecutive){ ?>
                                    <option value="<?php echo $excecutive->id_users ?>"><?php echo $excecutive->user_name ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <input type="hidden" class="imei_no" name="imei_no" value="<?php echo $stock->imei ?>" />
                            <input type="hidden" class="idwarehouse" name="idwarehouse" value="<?php echo $stock->idwarehouse ?>" />
                            <input type="hidden" class="idvariant" name="idvariant" value="<?php echo $stock->idvariant ?>" />
                            <div class="col-md-4" style="padding: 2px">
                                <button class="btn btn-sm btn-primary submit_allocation" value="<?php echo $stock->id_service ?>" style="min-width: 50px; text-transform: capitalize">Allocate</button>
                            </div>
                        </td>
                    </tr>
                <?php $i++; } ?>
            </tbody>
        </table>
        <?php }else{ ?>
            <center class="red-text"><h4><i class="fa fa-exclamation-triangle"></i> Data Not Found</h4></center>
        <?php }
    }
    
    public function ajax_force_doa_process_by_coord(){
//        echo print_r($_POST);
        $brand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
        $product_category = $this->input->post('product_category');
        $service_stock = $this->Service_model->get_service_stock_report($brand, $product_category,$idbranch,3, 0);
        if(count($service_stock)>0){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>Case ID</th>
                    <th>Counter Faulty/ Sold</th>
                    <th>Branch</th>
                    <th>Inward Date</th>                    
                    <th>Force DOA Date</th>                    
                    <th>Pending Days</th>
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Brand</th>
                    <th>Product</th>
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
                        <td><?php if($stock->counter_faulty){ echo 'Counter Faulty'; }else{ echo 'Sold'; } ?></td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($stock->entry_time)); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($stock->force_doa_date)); ?></td>
                        <td><?php $now = time(); // or your date as well
                                $your_date = strtotime($stock->force_doa_date);
                                $datediff = $now - $your_date;
                                echo round($datediff / (60 * 60 * 24)); ?></td>
                        <td><?php echo $stock->inv_no ?></td>
                        <td><?php echo $stock->inv_date ?></td>
                        <td><?php echo $stock->brand_name ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->imei ?></td>
                        <td><?php echo $stock->problem ?></td>
                        <td><?php echo $stock->remark ?></td>
                        <td><?php echo $stock->customer_name.'-'.$stock->mob_number ?></td>
                        <td><?php echo $stock->delivery_status ?></td>
                        <td>
                            <?php if($stock->counter_faulty){ ?>
                            <a href="<?php echo base_url('Service/service_counter_faulty_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-info"></i></center></a>
                            <?php }else{ ?>
                            <a href="<?php echo base_url('Service/service_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-info"></i></center></a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php $i++; } ?>
            </tbody>
        </table>
        <?php }else{ ?>
            <center class="red-text"><h4><i class="fa fa-exclamation-triangle"></i> Data Not Found</h4></center>
        <?php }
    }
    
    public function ajax_get_assigned_cases_to_excecutive(){
//        die(print_r($_POST));
        $brand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
//        $status = $this->input->post('status');
        $product_category = $this->input->post('product_category');
        $iduser = $this->input->post('iduser');
        $service_stock = $this->Service_model->ajax_get_assigned_cases_to_excecutive($brand, $product_category,$idbranch,13,$iduser);
        
//        echo print_r($service_stock);
        if(count($service_stock)>0){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>Case ID</th>
                    <th>Counter Faulty/ Sold</th>
                    <th>Branch</th>
                    <th>Inward Time</th>
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Brand</th>
                    <th>Product</th>
                    <th>IMEI/SRNO</th>
                    <th>Problem</th>
                    <th>Remark</th>
                    <th>Customer</th>
                    <th>Status</th>
                    <th>Allocated Date</th>
                    <th>Pending Days</th>
                    <th>Process</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $stock->id_service; ?></td>
                        <td><?php if($stock->counter_faulty){ echo 'Counter Faulty'; }else{ echo 'Sold'; } ?></td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td><?php echo date('d-m-Y h:i a', strtotime($stock->entry_time)); ?></td>
                        <td><?php echo $stock->inv_no ?></td>
                        <td><?php echo $stock->inv_date ?></td>
                        <td><?php echo $stock->brand_name ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->imei ?></td>
                        <td><?php echo $stock->problem ?></td>
                        <td><?php echo $stock->remark ?></td>
                        <td><?php echo $stock->customer_name.'-'.$stock->mob_number ?></td>
                        <td><?php echo $stock->delivery_status ?></td>                        
                        <td><?php echo $stock->allocate_to_excecutive ?></td>                        
                        <td><?php $now = time(); // or your date as well
                                $your_date = strtotime($stock->allocate_to_excecutive);
                                $datediff = $now - $your_date;
                                echo round($datediff / (60 * 60 * 24)); ?>
                        </td>
                        <td>
                            <?php if($this->session->userdata('idrole') == 39){ ?>
                            <a class="thumbnail btn-sm btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $stock->id_service ?>" style="margin: 0" >
                                <span class="mdi mdi-share text-info fa-lg"></span>
                            </a>
                            <div class="modal fade" id="edit<?php echo $stock->id_service ?>">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <center><h4>Servicse Process - Generate Service State </h4></center><hr>
                                            <?php if($stock->counter_faulty){ ?>
                                            <center class="red-text"><i class="mdi mdi-flip-to-back fa-lg"></i> <?php echo 'Counter Faulty Product'; ?></center><hr>
                                            <?php }else{ ?>
                                            <center class="red-text"><i class="mdi mdi-flip-to-back fa-lg"></i> <?php echo 'Sold Product'; ?></center><hr>
                                            <?php } ?>
                                            <div class="action_form" style="line-height: 25px">
                                                <div class="col-md-3" style="font-weight: bold">Case ID: <?php echo $stock->id_service ?></div>
                                                <div class="col-md-9"><?php echo $stock->full_name ?> - <?php echo $stock->imei ?></div><div class="clearfix"></div>
                                                <div class="col-md-3">Branch</div>
                                                <div class="col-md-9"><?php echo $stock->branch_name; ?></div><div class="clearfix"></div>
                                                <div class="col-md-3">Problem</div>
                                                <div class="col-md-9"><?php echo $stock->problem; ?></div><div class="clearfix"></div>
                                                <div class="col-md-3">Remark</div>
                                                <div class="col-md-9"><?php echo $stock->remark; ?></div><div class="clearfix"></div>
                                                <div class="col-md-3">Customer</div>
                                                <div class="col-md-9"><?php echo $stock->customer_name.'-'.$stock->mob_number ?></div><div class="clearfix"></div>
                                                <div class="clearfix"></div><hr>
                                                <div class="col-md-3" style="padding: 2px">
                                                    <label class="form-check-label thumbnail" for="repaired<?php echo $stock->id_service ?>" style="padding: 5px; margin: 5px; font-weight: 100">
                                                        &nbsp; <input type="radio" class="repaired_ajax" name="state<?php echo $stock->id_service ?>" id="repaired<?php echo $stock->id_service ?>" />
                                                        &nbsp; Repaired
                                                    </label>
                                                </div>
                                                <div class="col-md-3" style="padding: 2px">
                                                    <label class="form-check-label thumbnail" for="rejected<?php echo $stock->id_service ?>" style="padding: 5px; margin: 5px; font-weight: 100">
                                                        &nbsp; <input type="radio" class="rejected_ajax" name="state<?php echo $stock->id_service ?>" id="rejected<?php echo $stock->id_service ?>" />
                                                        <!--&nbsp; <input type="radio" class="rejected_ajax" name="state<?php echo $stock->id_service ?>" id="rejected<?php echo $stock->id_service ?>" onclick="//return rejected_data<?php // echo $stock->id_service ?>()" />-->
                                                        &nbsp; Rejected
                                                    </label>
                                                </div>
                                                <div class="col-md-3" style="padding: 2px">
                                                    <label class="form-check-label thumbnail" for="doa_letter<?php echo $stock->id_service ?>" style="padding: 5px; margin: 5px; font-weight: 100">
                                                        &nbsp; <input type="radio" class="doa_letter_ajax" name="state<?php echo $stock->id_service ?>" id="doa_letter<?php echo $stock->id_service ?>" onclick="return doa_letter_data<?php echo $stock->id_service ?>()" />
                                                        &nbsp; DOA Letter
                                                    </label>
                                                </div>
                                                <div class="col-md-3" style="padding: 2px">
                                                    <label class="form-check-label thumbnail" for="doa_handset<?php echo $stock->id_service ?>" style="padding: 5px; margin: 5px; font-weight: 100">
                                                        &nbsp; <input type="radio" class="doa_handset_ajax" name="state<?php echo $stock->id_service ?>" id="doa_handset<?php echo $stock->id_service ?>" onclick="return doa_handset_data<?php echo $stock->id_service ?>()" />
                                                        &nbsp; Replacement Handset                                                </div><div class="clearfix"></div><br>
                                                <div class="service_state_form thumbnail"><center>Select Action</center></div>
                                                <input type="hidden" class="counter_faulty" name="counter_faulty" value="<?php echo $stock->counter_faulty ?>" />
                                                <input type="hidden" class="imei_no" name="imei_no" value="<?php echo $stock->imei ?>" />
                                                <input type="hidden" class="idwarehouse" name="idwarehouse" value="<?php echo $stock->idwarehouse ?>" />
                                                <input type="hidden" class="idservice" name="idservice" value="<?php echo $stock->id_service ?>" />
                                                <input type="hidden" class="idvariant" name="idvariant" value="<?php echo $stock->idvariant ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php }else{ ?>
                            Pending
                            <?php } ?>
                        </td>
                    </tr>
                <?php $i++; } ?>
            </tbody>
        </table>
        <?php }else{ ?>
            <center class="red-text"><h4><i class="fa fa-exclamation-triangle"></i> Data Not Found</h4></center>
        <?php }
    }
    
    public function ajax_get_processed_cases_by_excecutive(){
//        die(print_r($_POST));
        $brand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
//        $status = $this->input->post('status');
        $product_category = $this->input->post('product_category');
        $iduser = $this->input->post('iduser');
        $service_stock = $this->Service_model->ajax_get_assigned_cases_to_excecutive($brand, $product_category,$idbranch,14,$iduser);
//        echo print_r($service_stock);
        if(count($service_stock)>0){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>Case ID</th>
                    <th>Counter Faulty/ Sold</th>
                    <th>Branch</th>
                    <th>Inward Date</th>                    
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Brand</th>
                    <th>Product</th>
                    <th>IMEI/SRNO</th>
                    <th>Problem</th>
                    <th>Remark</th>                    
                    <th>Customer</th>
                    <th>Executive allocate date</th>
                    <th>Executive processed date</th>
                    <th>Days difference</th>
                    <th>State</th>
                    <th>DOA Letter/Handset</th>
                    <th>Status</th>
                    <th>Process</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $stock->id_service; ?></td>
                        <td><?php if($stock->counter_faulty){ echo 'Counter Faulty'; }else{ echo 'Sold'; } ?></td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td>'<?php echo date('d-m-Y', strtotime($stock->entry_time)); ?></td>
                        <td><?php echo $stock->inv_no ?></td>
                        <td><?php echo $stock->inv_date ?></td>
                        <td><?php echo $stock->brand_name ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->imei ?></td>
                        <td><?php echo $stock->problem ?></td>
                        <td><?php echo $stock->remark ?></td>
                        <td><?php echo $stock->customer_name.'-'.$stock->mob_number ?></td>
                        <td><?php echo $stock->allocate_to_excecutive ?></td>
                        <td><?php echo $stock->procesed_by_excecutive ?></td>
                        <td>
                            <?php $now = strtotime($stock->procesed_by_excecutive); // or your date as well
                                $your_date = strtotime($stock->allocate_to_excecutive);
                                $datediff = $now - $your_date;
                                echo round($datediff / (60 * 60 * 24)); ?>
                        </td>
                        <?php if($stock->warranty_status == 1){ ?>
                        <td>Repaired</td><td></td>
                        <?php }elseif($stock->warranty_status == 2){ ?>
                        <td>Rejected</td><td></td>
                        <?php }elseif($stock->warranty_status == 3){ ?>
                        <td>DOA Letter</td>
                        <td><a class="btn" target="_blank" href="<?php echo base_url('assets/doa_letter_file/'.$stock->doa_letter_path) ?>" style="color: #1b6caa"><i class="mdi mdi-note-text"></i> Letter</a></td>
                        <?php }elseif($stock->warranty_status == 4){ ?>
                        <td>DOA Handset</td>
                        <td><?php echo $stock->new_imei_against_doa ?></td>
                        <?php }else{ ?>
                        <td></td>
                        <td></td>
                        <?php } ?>
                        <td><?php echo $stock->delivery_status ?></td>
                        <td>
                            <?php if($stock->counter_faulty){ ?>
                            <a href="<?php echo base_url('Service/service_counter_faulty_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-sign-in"></i></center></a>
                            <?php }else{ ?>
                            <a href="<?php echo base_url('Service/service_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-sign-in"></i></center></a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php $i++; } ?>
            </tbody>
        </table>
        <?php }else{ ?>
            <center class="red-text"><h4><i class="fa fa-exclamation-triangle"></i> Data Not Found</h4></center>
        <?php }
    }
    
    public function ajax_get_processed_cases_by_excecutive_on_exe(){
//        die(print_r($_POST));
        $brand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
//        $status = $this->input->post('status');
        $product_category = $this->input->post('product_category');
        $iduser = $this->input->post('iduser');
        $service_stock = $this->Service_model->ajax_get_assigned_cases_to_excecutive_on_exe($brand, $product_category,$idbranch,$iduser);
//        echo print_r($service_stock);
        if(count($service_stock)>0){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>Case ID</th>
                    <th>Counter Faulty/ Sold</th>
                    <th>Branch</th>
                    <th>Inward Date</th>                    
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Brand</th>
                    <th>Product</th>
                    <th>IMEI/SRNO</th>
                    <th>Problem</th>
                    <th>Remark</th>                    
                    <th>Customer</th>
                    <th>State</th>
                    <th>DOA Letter/Handset</th>
                    <th>Status</th>
                    <th>Info</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $stock->id_service; ?></td>
                        <td><?php if($stock->counter_faulty){ echo 'Counter Faulty'; }else{ echo 'Sold'; } ?></td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($stock->entry_time)); ?></td>
                        <td><?php echo $stock->inv_no ?></td>
                        <td><?php echo $stock->inv_date ?></td>
                        <td><?php echo $stock->brand_name ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->imei ?></td>
                        <td><?php echo $stock->problem ?></td>
                        <td><?php echo $stock->remark ?></td>
                        <td><?php echo $stock->customer_name.'-'.$stock->mob_number ?></td>
                        <?php if($stock->warranty_status == 1){ ?>
                        <td>Repaired</td><td></td>
                        <?php }elseif($stock->warranty_status == 2){ ?>
                        <td>Rejected</td><td></td>
                        <?php }elseif($stock->warranty_status == 3){ ?>
                        <td>DOA Letter</td>
                        <td><a class="btn" target="_blank" href="<?php echo base_url('assets/doa_letter_file/'.$stock->doa_letter_path) ?>" style="color: #1b6caa"><i class="mdi mdi-note-text"></i> Letter</a></td>
                        <?php }elseif($stock->warranty_status == 4){ ?>
                        <td>DOA Handset</td>
                        <td><?php echo $stock->new_imei_against_doa ?></td>
                        <?php }else{ ?>
                        <td></td>
                        <td></td>
                        <?php } ?>
                        <td><?php echo $stock->delivery_status ?></td>
                        <td>
                            <?php if($stock->counter_faulty){ ?>
                            <a href="<?php echo base_url('Service/service_counter_faulty_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-info"></i></center></a>
                            <?php }else{ ?>
                            <a href="<?php echo base_url('Service/service_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-info"></i></center></a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php $i++; } ?>
            </tbody>
        </table>
        <?php }else{ ?>
            <center class="red-text"><h4><i class="fa fa-exclamation-triangle"></i> Data Not Found</h4></center>
        <?php }
    }
    
    public function ajax_get_branch_received_by_ho(){
//        die(print_r($_POST));
        $brand = $this->input->post('brand');
        $idbranch = $this->input->post('idbranch');
//        $status = $this->input->post('status');
        $product_category = $this->input->post('product_category');
        $iduser = $this->input->post('iduser');
        $service_stock = $this->Service_model->ajax_get_assigned_cases_to_excecutive($brand, $product_category,$idbranch,12,$iduser);
//        echo print_r($service_stock);
        if(count($service_stock)>0){ ?>
            <table class="table table-condensed table-striped table-bordered table-hover" style="margin-bottom: 0;">
                <thead class="fixedelementtop">
                    <th>Srno</th>
                    <th>Case ID</th>
                    <th>Counter Faulty/ Sold</th>
                    <th>Branch</th>
                    <th>Inward Date</th>                    
                    <th>Invoice Number</th>
                    <th>Invoice Date</th>
                    <th>Product name</th>
                    <th>IMEI/SRNO</th>
                    <th>Problem</th>
                    <th>Remark</th>                    
                    <th>Customer</th>
                    <th>State</th>
                    <th>DOA Letter/Handset</th>
                    <th>Status</th>
                    <th>Process</th>
                </thead>
                <tbody class="data_1">
                    <?php $i=1; foreach($service_stock as $stock){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $stock->id_service; ?></td>
                        <td><?php if($stock->counter_faulty){ echo 'Counter Faulty'; }else{ echo 'Sold'; } ?></td>
                        <td><?php echo $stock->branch_name; ?></td>
                        <td><?php echo date('Y-m-d', strtotime($stock->entry_time)); ?></td>
                        <td><?php echo $stock->inv_no ?></td>
                        <td><?php echo $stock->inv_date ?></td>
                        <td><?php echo $stock->full_name ?></td>
                        <td><?php echo $stock->imei ?></td>
                        <td><?php echo $stock->problem ?></td>
                        <td><?php echo $stock->remark ?></td>
                        <td><?php echo $stock->customer_name.'-'.$stock->mob_number ?></td>
                        <?php if($stock->warranty_status == 1){ ?>
                        <td>Repaired</td><td></td>
                        <?php }elseif($stock->warranty_status == 2){ ?>
                        <td>Rejected</td><td></td>
                        <?php }elseif($stock->warranty_status == 3){ ?>
                        <td>DOA Letter</td>
                        <td><a class="btn" target="_blank" href="<?php echo base_url('assets/doa_letter_file/'.$stock->doa_letter_path) ?>" style="color: #1b6caa"><i class="mdi mdi-note-text"></i> Letter</a></td>
                        <?php }elseif($stock->warranty_status == 4){ ?>
                        <td>DOA Handset</td>
                        <td><?php echo $stock->new_imei_against_doa ?></td>
                        <?php }else{ ?>
                        <td></td>
                        <td></td>
                        <?php } ?>
                        </td>
                        <td><?php echo $stock->delivery_status ?></td>
                        <td>
                            <?php if($stock->counter_faulty){ ?>
                            <a href="<?php echo base_url('Service/service_counter_faulty_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-sign-in"></i></center></a>
                            <?php }else{ ?>
                            <a href="<?php echo base_url('Service/service_details/'.$stock->id_service) ?>" class="btn btn-primary btn-floating waves-effect"><center><i class="fa fa-sign-in"></i></center></a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php $i++; } ?>
            </tbody>
        </table>
        <?php }else{ ?>
            <center class="red-text"><h4><i class="fa fa-exclamation-triangle"></i> Data Not Found</h4></center>
        <?php }
    }
    
    public function service_allocation_to_excecutive() {
//        die(print_r($_POST));
//      13  Allocate To Excecutive
        $this->db->trans_begin();
        $idservice=$this->input->post('idservice');
        $excecutive=$this->input->post('excecutive');
        $imei_no=$this->input->post('imei_no');
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $allocated_by = $this->session->userdata('id_users');
//      update service stock
        $updata_service = array(
            'process_status' => 13,
            'idservice_excecutive' => $excecutive,
            'allocate_to_excecutive' => $date,
        );
        $this->Service_model->update_service_stock($idservice, $updata_service);
        $imei_history[]=array(
            'imei_no' => $imei_no,
            'entry_type' => 'Service - Allocated to Excecutive',
            'entry_time' => $datetime,
            'date' => $date,
            'idbranch' => $this->input->post('idwarehouse'),
            'idgodown' => 4,
            'idvariant' => $this->input->post('idvariant'),
            'idimei_details_link' => 19, // Sale from imei_details_link table
            'idlink' => $idservice,
            'iduser' => $allocated_by,
        );
        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'Failed';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'Success';
        }
        echo json_encode($q);
    }
    
    public function service_process_by_excecutive_repaire() {
//        die(print_r($_POST));
//      14 Processed by Executive
        $this->db->trans_begin();
        $idservice=$this->input->post('idservice');
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $warranty_status=$this->input->post('warranty_status');
        $counter_faulty=$this->input->post('counter_faulty');
//      update service stock
        if($counter_faulty==0){
            $updata_service = array(
                'process_status' => 14,
                'procesed_by_excecutive' => $date,
                'warranty_status' => $warranty_status,
                'executive_remark' => $this->input->post('repaire_remark'),
            );
            $this->Service_model->update_service_stock($idservice, $updata_service);
            $imei_history[]=array(
                'imei_no' => $this->input->post('imei'),
                'entry_type' => 'Service - Processed by Excecutive (Repaired)',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $this->input->post('idwarehouse'),
                'idgodown' => 4,
                'idvariant' => $this->input->post('idvariant'),
                'idimei_details_link' => 19, // Sale from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $this->input->post('iduser'),
            );
        }else{
            $updata_service = array(
                'process_status' => 14,
                'procesed_by_excecutive' => $date,
                'closed' => $date,
                'warranty_status' => $warranty_status,
                'executive_remark' => $this->input->post('repaire_remark'),
            );
            $this->Service_model->update_service_stock($idservice, $updata_service);
            // repaired and close in warehouse
            $imei_history[]=array(
                'imei_no' => $this->input->post('imei'),
                'entry_type' => 'Service - Processed by Excecutive (Repaired)',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $this->input->post('idwarehouse'),
                'idgodown' => 4,
                'idvariant' => $this->input->post('idvariant'),
                'idimei_details_link' => 19, // Sale from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $this->input->post('iduser'),
            );
//            $imei_history[]=array(
//                'imei_no' => $this->input->post('imei'),
//                'entry_type' => 'Service - Closed by Excecutive',
//                'entry_time' => $datetime,
//                'date' => $date,
//                'idbranch' => $this->input->post('idwarehouse'),
//                'idgodown' => 1,
//                'idvariant' => $this->input->post('idvariant'),
//                'idimei_details_link' => 19, // Sale from imei_details_link table
//                'idlink' => $idservice,
//                'iduser' => $this->input->post('iduser'),
//            );
        }
        
        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'Failed';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'Success';
        }
        echo json_encode($q);
    }
    
    public function service_process_by_excecutive_reject() {
//        die(print_r($_POST));
//      14 Processed by Executive
        $this->db->trans_begin();
        $idservice=$this->input->post('idservice');
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $warranty_status=$this->input->post('warranty_status');
        $counter_faulty=$this->input->post('counter_faulty');
//      update service stock
        $updata_service = array(
            'process_status' => 14,
            'procesed_by_excecutive' => $date,
            'warranty_status' => $warranty_status,
            'executive_remark' => $this->input->post('repaire_remark'),
        );
        $this->Service_model->update_service_stock($idservice, $updata_service);
        $imei_history[]=array(
            'imei_no' => $this->input->post('imei'),
            'entry_type' => 'Service - Processed by Excecutive (Rejected)',
            'entry_time' => $datetime,
            'date' => $date,
            'idbranch' => $this->input->post('idwarehouse'),
            'idgodown' => 4,
            'idvariant' => $this->input->post('idvariant'),
            'idimei_details_link' => 19, // Sale from imei_details_link table
            'idlink' => $idservice,
            'iduser' => $this->input->post('iduser'),
        );
        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'Failed';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'Success';
        }
        echo json_encode($q);
    }
    
    public function service_process_by_excecutive_doa_letter_btn() {
//        die(print_r($_FILES));
//        die(print_r($_POST));
//      14 Processed by Executive
        $this->db->trans_begin();
        $idservice=$this->input->post('idservice');
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $warranty_status=$this->input->post('warranty_status');
        $counter_faulty=$this->input->post('counter_faulty');
//      update service stock
        
        $prodlink = 'assets/doa_letter_file/';
        $config = array(
        'image_library' => 'gd2',
        'upload_path' => $prodlink,
        'allowed_types' => 'jpg|jpeg|gif|png|jfif|pdf',
        'file_name' => $_FILES['doa_letter_file']['name'],
        'maintain_ratio' => TRUE,
        'create_thumb' => TRUE,
        );
        $this->load->library('upload',$config);
        $this->upload->initialize($config);
        if($this->upload->do_upload('doa_letter_file')){
            $uploadData = $this->upload->data();
            $img = $uploadData['file_name'];
        }else{
            $img = NULL;
        }   
        
        if($counter_faulty==0){
        // doa inward
//            $inward_request = array(
//                'date' => $date,
//                'idbranch' => $this->input->post('idwarehouse'),
//                'entry_time' => $datetime,
//                'created_by' => $this->input->post('iduser'),
//                'doa_imei' => $this->input->post('imei'),
//                'status' => 1,
//                'idservice' => $idservice,
//            );
//            $iddoainward=$this->Service_model->save_doa_inward($inward_request);
    //            doa_reconciliation
            $doa_reconciliation = array(
                'date' => $date,
                'imei_no' => $this->input->post('imei'),
                'created_by' => $this->input->post('iduser'), 
                'idbranch' => $this->input->post('idwarehouse'),
                'doa_id' => $this->input->post('doa_id'),
                'doa_date' => $this->input->post('doa_date'),
                'doa_letter_path' => $img,
                'idservice' => $idservice,
                'sales_return_type' => 3,
                'doa_return_type' => 1,
                'status' => 0,
//                'closure_type' => 0,
//                'iddoainward' => $iddoainward
            );
            $this->Service_model->save_doa_reconciliation($doa_reconciliation);   
            $updata_service = array(
                'process_status' => 14, // Processed by Executive
                'procesed_by_excecutive' => $date,
                'warranty_status' => $warranty_status,
                'doa_letter_path' => $img,
                'executive_remark' => $this->input->post('repaire_remark'),
                'doa_id' => $this->input->post('doa_id'),
                'doa_date' => $this->input->post('doa_date'),
            );
            $this->Service_model->update_service_stock($idservice, $updata_service);
            $imei_history[]=array(
                'imei_no' => $this->input->post('imei'),
                'entry_type' => 'Service - Processed by Executive (DOA Letter)',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $this->input->post('idwarehouse'),
                'idgodown' => 4,
                'idvariant' => $this->input->post('idvariant'),
                'idimei_details_link' => 19, // Sale from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $this->input->post('iduser'),
            );
        }else{
            // doa inward
//            $inward_request = array(
//                'date' => $date,
//                'idbranch' => $this->input->post('idwarehouse'),
//                'entry_time' => $datetime,
//                'created_by' => $this->input->post('iduser'),
//                'doa_imei' => $this->input->post('imei'),
//                'status' => 1,
//                'idservice' => $idservice,
//            );
//            $iddoainward=$this->Service_model->save_doa_inward($inward_request);
    //            doa_reconciliation
            $doa_reconciliation = array(   
                'date' => $date,
                'imei_no' => $this->input->post('imei'),
                'created_by' => $this->input->post('iduser'), 
                'idbranch' => $this->input->post('idwarehouse'),
                'doa_id' => $this->input->post('doa_id'),
                'doa_date' => $this->input->post('doa_date'),
                'doa_letter_path' => $img,
                'idservice' => $idservice,
                'sales_return_type' => 3,
                'doa_return_type' => 1,
                'status' => 0,
                'closure_type' => 0,
//                'iddoainward' => $iddoainward
            );
            $this->Service_model->save_doa_reconciliation($doa_reconciliation);                    
        
            $updata_service = array(
                'process_status' => 14, // Processed by Executive
                'procesed_by_excecutive' => $date,
                'warranty_status' => $warranty_status,
                'doa_letter_path' => $img,
                'executive_remark' => $this->input->post('repaire_remark'),
                'doa_id' => $this->input->post('doa_id'),
                'doa_date' => $this->input->post('doa_date'),
            );
            $this->Service_model->update_service_stock($idservice, $updata_service);
            // repaired and close in warehouse
            $imei_history[]=array(
                'imei_no' => $this->input->post('imei'),
                'entry_type' => 'Service - Processed by Executive (DOA Letter)',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $this->input->post('idwarehouse'),
                'idgodown' => 4,
                'idvariant' => $this->input->post('idvariant'),
                'idimei_details_link' => 19, // Sale from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $this->input->post('iduser'),
            );
//            $imei_history[]=array(
//                'imei_no' => $this->input->post('imei'),
//                'entry_type' => 'Service - Closed by Executive',
//                'entry_time' => $datetime,
//                'date' => $date,
//                'idbranch' => $this->input->post('idwarehouse'),
//                'idgodown' => 1,
//                'idvariant' => $this->input->post('idvariant'),
//                'idimei_details_link' => 19, // Sale from imei_details_link table
//                'idlink' => $idservice,
//                'iduser' => $this->input->post('iduser'),
//            );
        }
        
        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'Failed';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'Success';
        }
        echo json_encode($q);
    }
    public function service_send_to_local_counter_faulty_doa_letter_btn() {
//        die(print_r($_FILES));
//        die(print_r($_POST));
//      14 Processed by Executive
        $this->db->trans_begin();
        $idservice=$this->input->post('idservice');
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $warranty_status=$this->input->post('warranty_status');
        $counter_faulty=1;
//      update service stock
        
        $prodlink = 'assets/doa_letter_file/';
        $config = array(
        'image_library' => 'gd2',
        'upload_path' => $prodlink,
        'allowed_types' => 'jpg|jpeg|gif|png|jfif|pdf',
        'file_name' => $_FILES['doa_letter_file']['name'],
        'maintain_ratio' => TRUE,
        'create_thumb' => TRUE,
        );
        $this->load->library('upload',$config);
        $this->upload->initialize($config);
        if($this->upload->do_upload('doa_letter_file')){
            $uploadData = $this->upload->data();
            $img = $uploadData['file_name'];
        }else{
            $img = NULL;
        }   
        
        // doa inward
//        $inward_request = array(
//            'date' => $date,
//            'idbranch' => $this->input->post('idbranch'),
//            'entry_time' => $datetime,
//            'created_by' => $this->input->post('iduser'),
//            'doa_imei' => $this->input->post('imei'),
//            'status' => 1,
//            'idservice' => $idservice,
//        );
//        $iddoainward=$this->Service_model->save_doa_inward($inward_request);
//            doa_reconciliation
        $doa_reconciliation = array(   
            'date' => $date,
            'imei_no' => $this->input->post('imei'),
            'created_by' => $this->input->post('iduser'), 
            'idbranch' => $this->input->post('idbranch'),
            'doa_id' => $this->input->post('doa_id'),
            'doa_date' => $this->input->post('doa_date'),
            'doa_letter_path' => $img,
            'idservice' => $idservice,
            'sales_return_type' => 3,
            'doa_return_type' => 1,
            'status' => 0,
//            'closure_type' => 0,
//            'iddoainward' => $iddoainward
        );
        $this->Service_model->save_doa_reconciliation($doa_reconciliation);                    

        $updata_service = array(
            'process_status' => 11, // Processed by Executive
            'closed' => $date,
            'local_to_branch' => $date,
            'warranty_status' => $warranty_status,
            'doa_letter_path' => $img,
//            'executive_remark' => $this->input->post('repaire_remark'),
            'doa_id' => $this->input->post('doa_id'),
            'doa_date' => $this->input->post('doa_date'),
        );
        $this->Service_model->update_service_stock($idservice, $updata_service);

        $imei_history[]=array(
            'imei_no' => $this->input->post('imei'),
            'entry_type' => 'Service - Closed in Branch against DOA Letter',
            'entry_time' => $datetime,
            'date' => $date,
            'idbranch' => $this->input->post('idbranch'),
            'idgodown' => 3,
            'idvariant' => $this->input->post('idvariant'),
            'idimei_details_link' => 19, // Sale from imei_details_link table
            'idlink' => $idservice,
            'iduser' => $this->input->post('iduser'),
        );
        $update_stock = array(
            'idgodown' => 3, // DOA
        );
        $this->Service_model->update_stock_byimei($this->input->post('imei'),$update_stock);
        $this->General_model->save_batch_imei_history($imei_history);
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'Failed';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'Success';
        }
        echo json_encode($q);
    }
    
    public function service_send_to_local_counter_faulty_doa_handset_btn() {
//        die('<pre>'.print_r($_POST).'</pre>');
//      14 Processed by Executive
        $this->db->trans_begin();
        $idservice=$this->input->post('idservice');
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $warranty_status=$this->input->post('warranty_status');
        $counter_faulty=$this->input->post('counter_faulty');
        $idbranch = $this->input->post('idbranch');
        $new_enter_imei = $this->input->post('new_enter_imei');
        $imeino = $this->input->post('imei');
        $idv=$this->input->post('idvariant');
        $newidbrand = $this->input->post('newidbrand');
        $iduser = $this->input->post('iduser');
        
        $var_data = $this->General_model->get_model_variant_data_byidvariant($idv);          
        $idmodel = $var_data->idmodel;
        $idskutype = $var_data->idsku_type;
        $idgodown = 1; 
        $idbrand = $var_data->idbrand;
        $idproductcategory = $var_data->idproductcategory;        
        $idcategory = $var_data->idcategory; 
        $product_name = $var_data->full_name; 
//      update service stock
        $inward_request = array(
            'date' => $date,
            'imei_no' => $new_enter_imei,
            'idvariant' => $idv,
            'idgodown' => 1,
            'idbrand' => $idbrand,
            'idbranch' => $idbranch,
            'entry_time' => $datetime,
            'created_by' => $iduser,
            'doa_imei' => $imeino,
            'status' => 1,
            'replaced_imei' => $new_enter_imei,
            'idservice' => $idservice,
        );
//            die(print_r($updata_service));
        $iddoainward=$this->Service_model->save_doa_inward($inward_request);
        // save stock
        $inward_stock = array(
            'date' => $date,
            'imei_no' => $new_enter_imei,
            'idmodel' => $idmodel,
            'created_by' => $iduser, 
            'idvariant' => $idv,
            'product_name'=>$product_name,
            'idskutype' => $idskutype,
            'idproductcategory' => $idproductcategory,
            'idcategory' => $idcategory,
            'idbrand' => $idbrand,
            'idgodown' => $idgodown,
            'idbranch' => $idbranch
        );
        $this->Inward_model->save_stock($inward_stock);                    
//            doa_reconciliation
        $doa_reconciliation = array(
            'date' => $date,
            'imei_no' => $imeino,
            'idmodel' => $idmodel,
            'created_by' => $iduser, 
            'idvariant' => $idv,
            'idskutype' => $idskutype,
            'idservice'=>$idservice,
            'idproductcategory' => $idproductcategory,
            'idcategory' => $idcategory,
            'idbrand' => $idbrand,
            'idgodown' => $idgodown,
            'idbranch' => $idbranch,
            'sales_return_type' => 3, 
            'idgodown' => 3,
            'doa_return_type' => 3,
            'status' => 1,
            'closure_type' => 1,
            'cn_imei' => $new_enter_imei,
            'closure_by' => $iduser,
            'iddoainward' => $iddoainward
        );
         if(count($doa_reconciliation)>0){
            $this->Service_model->save_doa_reconciliation($doa_reconciliation);                    
        } 

        $updata_service = array(
            'process_status' => 11,
            'closed' => $date,
            'local_to_branch' => $date,
            'warranty_status' => $warranty_status,
            'new_imei_against_doa' => $new_enter_imei,
            'executive_remark' => $this->input->post('repaire_remark'),
        );
        $this->Service_model->update_service_stock($idservice, $updata_service);

        $imei_history[]=array(
            'imei_no' => $new_enter_imei,
            'entry_type' => 'New Inward Againts DOA(Replacement)',
            'entry_time' => $datetime,
            'date' => $date,
            'idbranch' => $idbranch,
            'idgodown' => 1,
            'idvariant' => $idv,                    
            'idimei_details_link' => 19, // Sales Return from imei_details_link table
            'idlink' => $idservice,
            'iduser' => $this->input->post('iduser'),
        );
        // old imei
        $imei_history[]=array(
            'imei_no' => $imeino,
            'entry_type' => 'Service case closed against New Handset',
            'entry_time' => $datetime,
            'date' => $date,
            'idbranch' => $idbranch,
            'idgodown' => 3,
            'idvariant' => $idv,
            'idimei_details_link' => 19, // Sale from imei_details_link table
            'idlink' => $idservice,
            'iduser' => $this->input->post('iduser'),
        );
        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
        $this->Service_model->delete_idservice_from_stock($idservice);
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'Failed';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'Success';
        }
        echo json_encode($q);
    }
    
    public function service_process_by_excecutive_doa_handset_btn_first() {
        die('<pre>'.print_r($_POST).'</pre>');
//      14 Processed by Executive
        $this->db->trans_begin();
        $idservice=$this->input->post('idservice');
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $warranty_status=$this->input->post('warranty_status');
        $counter_faulty=$this->input->post('counter_faulty');
        $idbranch = $this->input->post('idwarehouse');
        $new_enter_imei = $this->input->post('new_enter_imei');
        $imeino = $this->input->post('imei');
        $idv=$this->input->post('idvariant');
        $newidbrand = $this->input->post('newidbrand');
        $iduser = $this->input->post('iduser');
        $var_data = $this->General_model->get_model_variant_data_byidvariant($idv);          
        $idmodel = $var_data->idmodel;
        $idskutype = $var_data->idsku_type;
        $idgodown = 1; 
        $idbrand = $var_data->idbrand;
        $idproductcategory = $var_data->idproductcategory;        
        $idcategory = $var_data->idcategory; 
//      update service stock
        $imei_history = [];
        if($counter_faulty==0){
//            $id_sale = $this->input->post('id_sale');
            // doa inward
            $inward_request = array(
                'date' => $date,
                'imei_no' => $new_enter_imei,
                'idvariant' => $idv,
                'idgodown' => 1,
                'idbrand' => $newidbrand,
                'idbranch' => $idbranch,
                'entry_time' => $datetime,
                'created_by' => $iduser,
                'doa_imei' => $imeino,
                'status' => 1,
                'replaced_imei' => $new_enter_imei,
                'idservice' => $idservice,
            );
            $iddoainward=$this->Service_model->save_doa_inward($inward_request);
            // save stock
            $inward_stock = array(
                'date' => $date,
                'imei_no' => $new_enter_imei,
                'idmodel' => $idmodel,
                'created_by' => $iduser, 
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
//            doa_reconciliation
            $doa_reconciliation = array(   
                'date' => $date,
                'imei_no' => $imeino,
                'idmodel' => $idmodel,
                'created_by' => $iduser, 
                'idvariant' => $idv,
                'idskutype' => $idskutype,
                'idproductcategory' => $idproductcategory,
                'idcategory' => $idcategory,
                'idbrand' => $idbrand,
                'idservice'=>$idservice,
                'idgodown' => $idgodown,
                'idbranch' => $idbranch,
                'sales_return_type' => 3, 
                'sale_date' => $this->input->post('inv_date'),
                'idgodown' => 3,
                'doa_return_type' => 3,
                'status' => 1,
                'closure_type' => 1,
                'cn_imei' => $new_enter_imei,
                'closure_by' => $iduser,
                'iddoainward' => $iddoainward
            );
             if(count($doa_reconciliation)>0){
                $this->Service_model->save_doa_reconciliation($doa_reconciliation);                    
            } 
           
            $imei_history[]=array(
                'imei_no' => $new_enter_imei,
                'entry_type' => 'Inward against DOA Handset by Coordinator',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'idvariant' => $idv,                    
                'idimei_details_link' => 8, // Sales Return from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $this->input->post('iduser'),
            );
            
            $updata_service = array(
                'process_status' => 14,
                'procesed_by_excecutive' => $date,
                'warranty_status' => $warranty_status,
                'new_imei_against_doa' => $new_enter_imei,
                'executive_remark' => $this->input->post('repaire_remark'),
            );
            $this->Service_model->update_service_stock($idservice, $updata_service);
            
            // old imei
            $imei_history[]=array(
                'imei_no' => $imeino,
                'entry_type' => 'Service - DOA Closure with new Handset by Coordinator',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 4,
                'idvariant' => $idv,
                'idimei_details_link' => 19, // Sale from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $this->input->post('iduser'),
            );
            if(count($imei_history) > 0){
                $this->General_model->save_batch_imei_history($imei_history);
            }
        }else{
            
            // Sold
            $inward_request = array(
                'date' => $date,
                'imei_no' => $new_enter_imei,
                'idvariant' => $idv,
                'idgodown' => 1,
                'idbrand' => $idbrand,
                'idbranch' => $idbranch,
                'entry_time' => $datetime,
                'created_by' => $iduser,
                'doa_imei' => $imeino,
                'status' => 1,
                'replaced_imei' => $new_enter_imei,
                'idservice' => $idservice,
            );
//            die(print_r($updata_service));
            $iddoainward=$this->Service_model->save_doa_inward($inward_request);
            // save stock
            $inward_stock = array(
                'date' => $date,
                'imei_no' => $new_enter_imei,
                'idmodel' => $idmodel,
                'created_by' => $iduser, 
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
//            doa_reconciliation
            $doa_reconciliation = array(   
                'date' => $date,
                'imei_no' => $imeino,
                'idmodel' => $idmodel,
                'created_by' => $iduser, 
                'idvariant' => $idv,
                'idskutype' => $idskutype,
                'idservice'=>$idservice,
                'idproductcategory' => $idproductcategory,
                'idcategory' => $idcategory,
                'idbrand' => $idbrand,
                'idgodown' => $idgodown,
                'idbranch' => $idbranch,
                'sales_return_type' => 3, 
                'sale_date' => $this->input->post('inv_date'),
                'idgodown' => 3,
                'doa_return_type' => 3,
                'status' => 1,
                'closure_type' => 1,
                'cn_imei' => $new_enter_imei,
                'closure_by' => $iduser,
                'iddoainward' => $iddoainward
            );
             if(count($doa_reconciliation)>0){
                $this->Service_model->save_doa_reconciliation($doa_reconciliation);                    
            } 
           
            $updata_service = array(
                'process_status' => 14,
                'procesed_by_excecutive' => $date,
                'warranty_status' => $warranty_status,
                'new_imei_against_doa' => $new_enter_imei,
                'executive_remark' => $this->input->post('repaire_remark'),
            );
            $this->Service_model->update_service_stock($idservice, $updata_service);
            
            
            $imei_history[]=array(
                'imei_no' => $new_enter_imei,
                'entry_type' => 'New Inward Againts DOA',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'idvariant' => $idv,                    
                'idimei_details_link' => 8, // Sales Return from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $this->input->post('iduser'),
            );
            // old imei
            $imei_history[]=array(
                'imei_no' => $imeino,
                'entry_type' => 'Service Godown to DOA against New Handset by Executive',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 4,
                'idvariant' => $idv,
                'idimei_details_link' => 19, // Sale from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $this->input->post('iduser'),
            );
            if(count($imei_history) > 0){
                $this->General_model->save_batch_imei_history($imei_history);
            }
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'Failed';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'Success';
        }
        echo json_encode($q);
    }
    
    public function service_process_by_excecutive_doa_handset_btn() {
//        die('<pre>'.print_r($_POST).'</pre>');
//      14 Processed by Executive
        $this->db->trans_begin();
        $idservice=$this->input->post('idservice');
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $warranty_status=$this->input->post('warranty_status');
        $counter_faulty=$this->input->post('counter_faulty');
        $idbranch = $this->input->post('idwarehouse');
        $new_enter_imei = $this->input->post('new_enter_imei');
        $imeino = $this->input->post('imei');
        $idv=$this->input->post('idvariant');
        $newidbrand = $this->input->post('newidbrand');
        $iduser = $this->input->post('iduser');
        $var_data = $this->General_model->get_model_variant_data_byidvariant($idv);          
        $idmodel = $var_data->idmodel;
        $idskutype = $var_data->idsku_type;
        $idgodown = 1; 
        $idbrand = $var_data->idbrand;
        $idproductcategory = $var_data->idproductcategory;        
        $idcategory = $var_data->idcategory; 
//      update service stock
        $imei_history = [];
        if($counter_faulty==0){
//            $id_sale = $this->input->post('id_sale');
            // doa inward
            $inward_request = array(
                'date' => $date,
                'imei_no' => $new_enter_imei,
                'idvariant' => $idv,
                'idgodown' => 1,
                'idbrand' => $newidbrand,
                'idbranch' => $idbranch,
                'entry_time' => $datetime,
                'created_by' => $iduser,
                'doa_imei' => $imeino,
                'status' => 0, // pending for inward
                'replaced_imei' => $new_enter_imei,
                'idservice' => $idservice,
            );
            $iddoainward=$this->Service_model->save_doa_inward($inward_request);
            // save stock
//            $inward_stock = array(
//                'date' => $date,
//                'imei_no' => $new_enter_imei,
//                'idmodel' => $idmodel,
//                'created_by' => $iduser, 
//                'idvariant' => $idv,
//                'idskutype' => $idskutype,
//                'idproductcategory' => $idproductcategory,
//                'idcategory' => $idcategory,
//                'idbrand' => $idbrand,
//                'idgodown' => $idgodown,
//                'idbranch' => $idbranch
//            );
//            if(count($inward_stock)>0){
//                $this->Inward_model->save_stock($inward_stock);                    
//            }  
//            doa_reconciliation
            $doa_reconciliation = array(   
                'date' => $date,
                'imei_no' => $imeino,
                'idmodel' => $idmodel,
                'created_by' => $iduser, 
                'idvariant' => $idv,
                'idskutype' => $idskutype,
                'idservice'=>$idservice,
                'idproductcategory' => $idproductcategory,
                'idcategory' => $idcategory,
                'idbrand' => $idbrand,
                'idgodown' => $idgodown,
                'idbranch' => $idbranch,
                'sales_return_type' => 3, 
                'sale_date' => $this->input->post('inv_date'),
                'idgodown' => 3,
                'doa_return_type' => 3,
                'status' => 0, // Pending
                'closure_type' => 1, //handset
//                'cn_imei' => $new_enter_imei,
//                'closure_by' => $iduser,
                'iddoainward' => $iddoainward
            );
             if(count($doa_reconciliation)>0){
                $this->Service_model->save_doa_reconciliation($doa_reconciliation);                    
            } 
           
            $imei_history[]=array(
                'imei_no' => $new_enter_imei,
                'entry_type' => 'New Handset Againts DOA Handset by Executive<small>(Inward Pending from Service coordinator)</small>',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'idvariant' => $idv,                    
                'idimei_details_link' => 19, // Sales Return from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $this->input->post('iduser'),
            );
            
            $updata_service = array(
                'process_status' => 14,
                'procesed_by_excecutive' => $date,
                'warranty_status' => $warranty_status,
                'new_imei_against_doa' => $new_enter_imei,
                'executive_remark' => $this->input->post('repaire_remark'),
            );
            $this->Service_model->update_service_stock($idservice, $updata_service);
            $update_stock = array(
                'idgodown' => 3, // DOA
            );
            $this->Service_model->update_stock_byimei($imeino,$update_stock);
            // old imei
            $imei_history[]=array(
                'imei_no' => $imeino,
                'entry_type' => 'Service to DOA Godown against New Handset by Executive',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 3,
                'idvariant' => $idv,
                'idimei_details_link' => 19, // Sale from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $this->input->post('iduser'),
            );
            if(count($imei_history) > 0){
                $this->General_model->save_batch_imei_history($imei_history);
            }
        }else{
            // Sold
            $inward_request = array(
                'date' => $date,
                'imei_no' => $new_enter_imei,
                'idvariant' => $idv,
                'idgodown' => 1,
                'idbrand' => $idbrand,
                'idbranch' => $idbranch,
                'entry_time' => $datetime,
                'created_by' => $iduser,
                'doa_imei' => $imeino,
                'status' => 0,
                'replaced_imei' => $new_enter_imei,
                'idservice' => $idservice,
            );
//            die(print_r($updata_service));
            $iddoainward=$this->Service_model->save_doa_inward($inward_request);
            // save stock
//            $inward_stock = array(
//                'date' => $date,
//                'imei_no' => $new_enter_imei,
//                'idmodel' => $idmodel,
//                'created_by' => $iduser, 
//                'idvariant' => $idv,
//                'idskutype' => $idskutype,
//                'idproductcategory' => $idproductcategory,
//                'idcategory' => $idcategory,
//                'idbrand' => $idbrand,
//                'idgodown' => $idgodown,
//                'idbranch' => $idbranch
//            );
//            if(count($inward_stock)>0){
//                $this->Inward_model->save_stock($inward_stock);                    
//            }  
//            doa_reconciliation
            $doa_reconciliation = array(   
                'date' => $date,
                'idservice'=>$idservice,
                'imei_no' => $imeino,
                'idmodel' => $idmodel,
                'created_by' => $iduser, 
                'idvariant' => $idv,
                'idskutype' => $idskutype,
                'idproductcategory' => $idproductcategory,
                'idcategory' => $idcategory,
                'idbrand' => $idbrand,
                'idgodown' => $idgodown,
                'idbranch' => $idbranch,
                'sales_return_type' => 3, 
                'sale_date' => $this->input->post('inv_date'),
                'idgodown' => 3,
                'doa_return_type' => 3,
                'status' => 0,
                'closure_type' => 1,
//                'cn_imei' => $new_enter_imei,
//                'closure_by' => $iduser,
                'iddoainward' => $iddoainward
            );
             if(count($doa_reconciliation)>0){
                $this->Service_model->save_doa_reconciliation($doa_reconciliation);                    
            } 
           
            $updata_service = array(
                'process_status' => 14,
                'procesed_by_excecutive' => $date,
                'warranty_status' => $warranty_status,
                'new_imei_against_doa' => $new_enter_imei,
                'executive_remark' => $this->input->post('repaire_remark'),
            );
            $this->Service_model->update_service_stock($idservice, $updata_service);
            $update_stock = array(
                'idgodown' => 3, // DOA
            );
            $this->Service_model->update_stock_byimei($imeino,$update_stock);
            $imei_history[]=array(
                'imei_no' => $new_enter_imei,
                'entry_type' => 'New Handset Againts DOA Handset by Executive<small>(Inward Pending from Service coordinator)</small>',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'idvariant' => $idv,                    
                'idimei_details_link' => 19, // Sales Return from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $this->input->post('iduser'),
            );
            // old imei
            $imei_history[]=array(
                'imei_no' => $imeino,
                'entry_type' => 'Service to DOA Godown against New Handset by Executive',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 3,
                'idvariant' => $idv,
                'idimei_details_link' => 19, 
                'idlink' => $idservice,
                'iduser' => $this->input->post('iduser'),
            );
            if(count($imei_history) > 0){
                $this->General_model->save_batch_imei_history($imei_history);
            }
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'Failed';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'Success';
        }
        echo json_encode($q);
    }
    
    
    public function service_process_by_excecutive_doa_handset_btn_new() {
//        die(print_r($_POST));
//      14 Processed by Executive
        $this->db->trans_begin();
        $idservice=$this->input->post('idservice');
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $warranty_status=$this->input->post('warranty_status');
        $counter_faulty=$this->input->post('counter_faulty');
        $idbranch = $this->input->post('idwarehouse');
        $new_enter_imei = $this->input->post('new_enter_imei');
        $imeino = $this->input->post('imei');
        $idv=$this->input->post('idvariant');
        $newidbrand = $this->input->post('newidbrand');
        $iduser = $this->input->post('iduser');
        $var_data = $this->General_model->get_model_variant_data_byidvariant($idv);          
        $idmodel = $var_data->idmodel;
        $idskutype = $var_data->idsku_type;
        $idgodown = 1; 
        $idbrand = $var_data->idbrand;
        $idproductcategory = $var_data->idproductcategory;        
        $idcategory = $var_data->idcategory; 
//      update service stock
        $imei_history = [];
        if($counter_faulty==0){
//            $id_sale = $this->input->post('id_sale');
            // doa inward
            $inward_request = array(
                'date' => $date,
                'imei_no' => $new_enter_imei,
                'idvariant' => $idv,
                'idgodown' => 1,
                'idbrand' => $newidbrand,
                'idbranch' => $idbranch,
                'entry_time' => $datetime,
                'created_by' => $iduser,
                'doa_imei' => $imeino,
                'status' => 1,
                'replaced_imei' => $new_enter_imei,
                'idservice' => $idservice,
            );
            $iddoainward=$this->Service_model->save_doa_inward($inward_request);
            // save stock
            $inward_stock = array(
                'date' => $date,
                'imei_no' => $new_enter_imei,
                'idmodel' => $idmodel,
                'created_by' => $iduser, 
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
//            doa_reconciliation
            $doa_reconciliation = array(   
                'date' => $date,
                'imei_no' => $imeino,
                'idmodel' => $idmodel,
                'created_by' => $iduser, 
                'idvariant' => $idv,
                'idskutype' => $idskutype,
                'idproductcategory' => $idproductcategory,
                'idcategory' => $idcategory,
                'idbrand' => $idbrand,
                'idgodown' => $idgodown,
                'idbranch' => $idbranch,
                'sales_return_type' => 3, 
                'sale_date' => $this->input->post('inv_date'),
                'idgodown' => 3,
                'doa_return_type' => 3,
                'status' => 1,
                'closure_type' => 1,
                'cn_imei' => $new_enter_imei,
                'closure_by' => $iduser,
                'iddoainward' => $iddoainward
            );
             if(count($doa_reconciliation)>0){
                $this->Service_model->save_doa_reconciliation($doa_reconciliation);                    
            } 
           
            $imei_history[]=array(
                'imei_no' => $new_enter_imei,
                'entry_type' => 'Inward against DOA Handset by Coordinator',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'idvariant' => $idv,                    
                'idimei_details_link' => 8, // Sales Return from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $this->input->post('iduser'),
            );
            
            $updata_service = array(
                'process_status' => 14,
                'procesed_by_excecutive' => $date,
                'warranty_status' => $warranty_status,
                'new_imei_against_doa' => $new_enter_imei,
                'executive_remark' => $this->input->post('repaire_remark'),
            );
            $this->Service_model->update_service_stock($idservice, $updata_service);
            
            // old imei
            $imei_history[]=array(
                'imei_no' => $imeino,
                'entry_type' => 'Service - DOA Closure with new Handset by Executive',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 4,
                'idvariant' => $idv,
                'idimei_details_link' => 19, // Sale from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $this->input->post('iduser'),
            );
            if(count($imei_history) > 0){
                $this->General_model->save_batch_imei_history($imei_history);
            }
        }else{
            
            // Sold
            $inward_request = array(
                'date' => $date,
                'imei_no' => $new_enter_imei,
                'idvariant' => $idv,
                'idgodown' => 1,
                'idbrand' => $idbrand,
                'idbranch' => $idbranch,
                'entry_time' => $datetime,
                'created_by' => $iduser,
                'doa_imei' => $imeino,
                'status' => 1,
                'replaced_imei' => $new_enter_imei,
                'idservice' => $idservice,
            );
//            die(print_r($updata_service));
            $iddoainward=$this->Service_model->save_doa_inward($inward_request);
            // save stock
            $inward_stock = array(
                'date' => $date,
                'imei_no' => $new_enter_imei,
                'idmodel' => $idmodel,
                'created_by' => $iduser, 
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
//            doa_reconciliation
            $doa_reconciliation = array(   
                'date' => $date,
                'imei_no' => $imeino,
                'idmodel' => $idmodel,
                'created_by' => $iduser, 
                'idvariant' => $idv,
                'idskutype' => $idskutype,
                'idproductcategory' => $idproductcategory,
                'idcategory' => $idcategory,
                'idbrand' => $idbrand,
                'idgodown' => $idgodown,
                'idbranch' => $idbranch,
                'sales_return_type' => 3, 
                'sale_date' => $this->input->post('inv_date'),
                'idgodown' => 3,
                'doa_return_type' => 3,
                'status' => 1,
                'closure_type' => 1,
                'cn_imei' => $new_enter_imei,
                'closure_by' => $iduser,
                'iddoainward' => $iddoainward
            );
             if(count($doa_reconciliation)>0){
                $this->Service_model->save_doa_reconciliation($doa_reconciliation);                    
            } 
           
            $updata_service = array(
                'process_status' => 14,
                'procesed_by_excecutive' => $date,
                'warranty_status' => $warranty_status,
                'new_imei_against_doa' => $new_enter_imei,
                'executive_remark' => $this->input->post('repaire_remark'),
            );
            $this->Service_model->update_service_stock($idservice, $updata_service);
            
            
            $imei_history[]=array(
                'imei_no' => $new_enter_imei,
                'entry_type' => 'New Inward Againts DOA',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'idvariant' => $idv,                    
                'idimei_details_link' => 8, // Sales Return from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $this->input->post('iduser'),
            );
            // old imei
            $imei_history[]=array(
                'imei_no' => $imeino,
                'entry_type' => 'Service Godown to DOA against New Handset by Executive',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 4,
                'idvariant' => $idv,
                'idimei_details_link' => 19, // Sale from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $this->input->post('iduser'),
            );
            if(count($imei_history) > 0){
                $this->General_model->save_batch_imei_history($imei_history);
            }
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'Failed';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'Success';
        }
        echo json_encode($q);
    }
    
    // Coordinator process
    public function add_in_send_to_branch_list() {
//        die(print_r($_POST));
//      16 Prepare for send to branch
        $this->db->trans_begin();
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $idservice=$this->input->post('idservice');
        $idwarehouse = $this->input->post('idwarehouse');
        $imeino = $this->input->post('imei_no');
        $idvariant=$this->input->post('idvariant');
        $iduser = $this->input->post('iduser');
        $counter_faulty = $this->input->post('counter_faulty');
//      update service stock
        $imei_history[]=array(
            'imei_no' => $imeino,
            'entry_type' => 'Service - Prepare for Send to Branch by Coordinator',
            'entry_time' => $datetime,
            'date' => $date,
            'idbranch' => $idwarehouse,
            'idgodown' => 4,
            'idvariant' => $idvariant,                    
            'idimei_details_link' => 19, // Sales Return from imei_details_link table
            'idlink' => $idservice,
            'iduser' => $iduser,
        );
        $updata_service = array(
            'process_status' => 16,
            'verified_by_coordinator' => $date,
            'prepare_for_send_to_Branch' => $date,
        );
        $this->Service_model->update_service_stock($idservice, $updata_service);
        $this->General_model->save_batch_imei_history($imei_history);
        
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Failed to add please try again');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Added in send to branch list');
        }
        if($counter_faulty){
            return redirect('Service/service_counter_faulty_details/'.$idservice);
        }else{
            return redirect('Service/service_details/'.$idservice);
        }
    }
    
    public function verify_inward_in_ho_and_close() {
//        die(print_r($_POST));
//      15 verify, inward_in_ho and close
        
//        counter faulty
//        if(warranty_status == 1) {15, 11 verify, inward_in_ho and close} repair
//        if(warranty_status == 2) {15, 11 Generate Invoice
//        if(warranty_status == 3) {15, 11 Update DOA and close case in HO} letter
//        if(warranty_status == 4) {15, 11 Inward new Handset & close service in HO} handset
//        sold
//        if(warranty_status == 3) {15 Update DOA & Allow Replace/Upgrade Option to Branch} letter
//        if(warranty_status == 4) {15 Inward new Handset & Allow Replace/Upgrade Option to Branch} handset
        
        $this->db->trans_begin();
        
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $counter_faulty = $this->input->post('counter_faulty');
        $idservice=$this->input->post('idservice');
        $idwarehouse = $this->input->post('idwarehouse');
        $imeino = $this->input->post('imei_no');
        $idvariant=$this->input->post('idvariant');
        $iduser = $this->input->post('iduser');
        $warranty_status = $this->input->post('warranty_status');
        $idbranch = $this->input->post('idbranch');
        
//        if($counter_faulty){
        // counter faulty
            if($warranty_status == 1){ // Repair
//                15, verify, inward_in_ho and 11 close
                $updata_service = array(
                    'process_status' => 11, // Verify and close by cordinator
                    'verified_by_coordinator' => $date,
                    'closed' => $date,
                );
                $imei_history[]=array(
                    'imei_no' => $imeino,
                    'entry_type' => 'Service - Verify and Inward in HO by Coordinator',
                    'entry_time' => $datetime,
                    'date' => $date,
                    'idbranch' => $idwarehouse,
                    'idgodown' => 1,
                    'idvariant' => $idvariant,                    
                    'idimei_details_link' => 19, // Sales Return from imei_details_link table
                    'idlink' => $idservice,
                    'iduser' => $iduser,
                );
                $this->Service_model->update_service_stock($idservice, $updata_service);
                $update_stock = array(
                    'idgodown' => 1,
                );
                $this->Service_model->update_stock_byimei($imeino,$update_stock);
                $this->General_model->save_batch_imei_history($imei_history);
                
            }else if($warranty_status == 2){ // Reject
//                die('<pre>'.print_r($_POST).'</pre>');
                
                $updata_service = array(
                    'process_status' => 16, // Prepare for Send to Branch
                    'verified_by_coordinator' => $date,
                    'prepare_for_send_to_Branch' => $date,
                );
                $this->Service_model->update_service_stock($idservice, $updata_service);
                $imei_history[]=array(
                    'imei_no' => $imeino,
                    'entry_type' => 'Service - Prepare for Send to Branch by Coordinator',
                    'entry_time' => $datetime,
                    'date' => $date,
                    'idbranch' => $idwarehouse,
                    'idgodown' => 1,
                    'idvariant' => $idvariant,                    
                    'idimei_details_link' => 19, // Sales Return from imei_details_link table
                    'idlink' => $idservice,
                    'iduser' => $iduser,
                );
                
                $this->load->model('Purchase_model');
            //Generate Sale in credit
        //81425 live service customer
        //81336 testing service customer
//                '81336', 'Service', 'Rejected Customer', '1111111111', NULL, NULL, '416001', 'Karvir', 'Kolhapur', '1', 'Maharashtra', 'Kolhapur';
                $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);
                $branch_manager = $this->General_model->get_active_users_byrole_branch(32, $idbranch);
                if(count($branch_manager)){
                    $cust_fname = $branch_manager[0]->user_name;
                    $cust_contact = $branch_manager[0]->user_contact;
                }else{
                    $cust_fname = 'Service';
                    $cust_contact = '1111111111';
                }
                $invid = $invoice_no->invoice_no + 1; 
                $y = date('y', mktime(0, 0, 0, 9 + date('m')));
                $y1 = $y - 1;
                $inv_no = $y1.$y . '/'. $invoice_no->branch_code . '/' . sprintf('%05d', $invid);

                $idstate = $invoice_no->idstate;
                $cust_idstate = $idstate;
                $idcustomer = 81425;
                $cust_lname = ' ';
                $cust_pincode = $invoice_no->branch_pincode;
                $cust_address = $invoice_no->branch_city;
                $gst_type = 0; //cgst
                
                $models = $this->Sale_model->ajax_stock_data_byimei_branch($imeino, $idwarehouse);
                foreach($models as $model){
                    $idtype = $model->idproductcategory;
                    $idcategory = $model->idcategory;
                    $idbrand = $model->idbrand;
                    $idmodel = $model->idmodel;
    //                $idvariant = $this->input->post('idvariant');
                    $idgodown = 1;
                    $skutype = $model->idskutype;
                    $product_name = $model->product_name;
                    $imei = $imeino;
                    $qty = 1;
                    $rowid = $model->id_stock;
                    $hsn = $model->hsn; 
                    $is_gst = $model->is_gst; // price on invoice
                    $idvendor = $model->idvendor;
                    $price = $model->mop;
                    $basic = $model->mop;
                    $discount_amt = 0;
                    $total_amt = $model->mop;
                    $landing = $model->landing;
                    $mrp = $model->mrp;
                    $mop = $model->mop;
                    $salesman_price = $model->salesman_price;
                    $is_mop = $model->is_mop; // price on invoice
                    $igst = 0;
                    $cgst = $model->cgst;
                    $sgst = $model->sgst;
                }
                
                $sdata = array(
                    'date' => $date,
                    'inv_no' => $inv_no,
                    'idbranch' => $idbranch,
                    'idcustomer' => $idcustomer,
                    'customer_fname' => $cust_fname,
                    'customer_lname' => $cust_lname,
                    'customer_idstate' => $cust_idstate,
                    'customer_pincode' => $cust_pincode,
                    'customer_contact' => $cust_contact,
                    'customer_address' => $cust_address,
                    'idsalesperson' => $this->input->post('idsalesperson'),
                    'basic_total' => $total_amt,
                    'discount_total' => 0,
                    'final_total' => $total_amt,
                    'gst_type' => $gst_type,
                    'created_by' => $iduser,
                    'remark' => 'Customer Name: '.$cust_fname.'(Branch Manager), Invoice generated due to Service Case Rejection(Counter Faulty). Created by Service Coordinator',
                    'entry_time' => $datetime,
                    'dcprint' => 1,
                );
//                die('<pre>'.print_r($sdata).'</pre>');
                $idsale = $this->Sale_model->save_sale($sdata);
                $payment = array(
                    'date' => $date,
                    'idsale' => $idsale,
                    'amount' => $total_amt,
                    'idpayment_head' => 6,
                    'idpayment_mode' => 9,
                    'inv_no' => $inv_no,
                    'idcustomer' => $idcustomer,
                    'idbranch' => $idbranch,
                    'created_by' => $iduser,
                    'entry_time' => $datetime,
                    'received_amount' => 0,
                    'received_entry_time'=>NULL,
                    'payment_receive' => 0,
                    'approved_by' => 'Customer Name: '.$cust_fname.'(Branch Manager), Invoice generated due to Service Case Rejection(Counter Faulty). Created by Service Coordinator',
                );
                $id_sale_payment = $this->Sale_model->save_sale_payment($payment);
                $igst = 0;
                $sale_product = array(
                    'date' => $date,
                    'idsale' => $idsale,
                    'idmodel' => $idmodel,
                    'idvariant' => $idvariant,
                    'imei_no' => $imei,
                    'hsn' => $hsn,
                    'idskutype' => $skutype,
                    'idgodown' => $idgodown,
                    'idproductcategory' => $idtype,
                    'idcategory' => $idcategory,
                    'idbrand' => $idbrand,
                    'product_name' => $product_name,
                    'price' => $price,
                    'landing' => $landing,
                    'mrp' => $mrp,
                    'mop' => $mop,
                    'salesman_price' => $salesman_price,
                    'inv_no' => $inv_no,
                    'qty' => $qty,
                    'idbranch' => $idbranch,
                    'discount_amt' => $discount_amt,
                    'is_gst' => $is_gst,
                    'is_mop' => $is_mop,
                    'basic' => $basic,
                    'idvendor' => $idvendor,
                    'cgst_per' => $cgst,
                    'sgst_per' => $sgst,
                    'igst_per' => $igst,
                    'total_amount' => $total_amt,
                    'entry_time' => $datetime,
                );
                $idsaleproduct = $this->Sale_model->save_sale_product($sale_product);
                $this->Purchase_model->delete_stock_byidstock($rowid);
                // IMEI History
                $imei_history[]=array(
                    'imei_no' => $imei,
                    'entry_type' => 'Sale',
                    'entry_time' => $datetime,
                    'date' => $date,
                    'idbranch' => $idbranch,
                    'idgodown' => 1,
                    'idvariant' => $idvariant,
                    'idimei_details_link' => 4, // Sale from imei_details_link table
                    'idlink' => $idsale,
                    'iduser' => $iduser,
                );
                $invoice_data = array( 'invoice_no' => $invid );
                $this->General_model->edit_db_branch($idbranch, $invoice_data);
                $this->General_model->save_batch_imei_history($imei_history);
                
            }else if($warranty_status == 3){ // DOA Letter
//                11, Update DOA and close case in HO
                $updata_service = array(
                    'process_status' => 11, // Verify and close by cordinator
                    'verified_by_coordinator' => $date,
                    'closed' => $date,
                );
                $imei_history[]=array(
                    'imei_no' => $imeino,
                    'entry_type' => 'Service - Verify & transfer in DOA against Letter by Coordinator',
                    'entry_time' => $datetime,
                    'date' => $date,
                    'idbranch' => $idwarehouse,
                    'idgodown' => 3,
                    'idvariant' => $idvariant,                    
                    'idimei_details_link' => 19, // Sales Return from imei_details_link table
                    'idlink' => $idservice,
                    'iduser' => $iduser,
                );
                $this->Service_model->update_service_stock($idservice, $updata_service);
                $update_stock = array(
                    'idgodown' => 3, // DOA
                );
                $this->Service_model->update_stock_byimei($imeino,$update_stock);
                $this->General_model->save_batch_imei_history($imei_history);
            }else if($warranty_status == 4){ // DOA Handset
//               15, 11 Inward new Handset & close service in HO handset
//                die('<pre>'.print_r($_POST).'</pre>');
                $new_imei_against_doa = $this->input->post('new_imei_against_doa');
                $new_imei_data = $this->Service_model->doa_inward_reconciliation_data_byidservice($idservice);
                // save stock
                $inward_stock = array(
                    'date' => $date,
                    'imei_no' => $new_imei_against_doa,
                    'idmodel' => $new_imei_data->idmodel,
                    'created_by' => $iduser,
                    'idvariant' => $new_imei_data->idvariant,
                    'product_name' => $new_imei_data->full_name,
                    'idskutype' => $new_imei_data->idskutype,
                    'idproductcategory' => $new_imei_data->idproductcategory,
                    'idcategory' => $new_imei_data->idcategory,
                    'idbrand' => $new_imei_data->idbrand,
                    'idgodown' => 1,
                    'idbranch' => $new_imei_data->idbranch,
                );
                $this->Inward_model->save_stock($inward_stock);
                // New IMEI History
                $imei_history[]=array(
                    'imei_no' => $new_imei_against_doa,
                    'entry_type' => 'Inward against DOA Handset by Coordinator',
                    'entry_time' => $datetime,
                    'date' => $date,
                    'idbranch' => $idwarehouse,
                    'idgodown' => 1,
                    'idvariant' => $new_imei_data->idvariant,                    
                    'idimei_details_link' => 19, // Sales Return from imei_details_link table
                    'idlink' => $idservice,
                    'iduser' => $iduser,
                );
                // old imei history
                $imei_history[]=array(
                    'imei_no' => $imeino,
                    'entry_type' => 'Service(DOA) - Closure against New Handset by Coordinator',
                    'entry_time' => $datetime,
                    'date' => $date,
                    'idbranch' => $idwarehouse,
                    'idgodown' => 3,
                    'idvariant' => $idvariant,                    
                    'idimei_details_link' => 19, // Sales Return from imei_details_link table
                    'idlink' => $idservice,
                    'iduser' => $iduser,
                );
                $this->General_model->save_batch_imei_history($imei_history);
//                die(print_r($imei_history));
                $this->Service_model->delete_idservice_from_stock($idservice);
                // update doa inward
                $inward_request = array(
                    'status' => 1,
                    'approved_by'=> $iduser,
                );
                $this->Service_model->update_doa_inward($idservice, $inward_request);
                //            doa_reconciliation
                $doa_reconciliation = array(   
                    'status' => 1,
                    'closure_type' => 1,
                    'cn_imei' => $new_imei_against_doa,
                    'closure_by' => $iduser,
                );
                $this->Service_model->update_doa_reconciliation_byid($new_imei_data->id_doa_stock, $doa_reconciliation);                    
                $updata_service = array(
                    'process_status' => 11, // Verify and close by cordinator
                    'verified_by_coordinator' => $date,
                    'closed' => $date,
                );
                $this->Service_model->update_service_stock($idservice, $updata_service);
            }
//        }     
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Failed to submit entry');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Entry submitted successfully');
        }
        if($counter_faulty){
            if($warranty_status == 2){
                return redirect('Sale/invoice_print/'.$idsale);
            }else{
                return redirect('Service/service_counter_faulty_details/'.$idservice);
            }
        }else{
            return redirect('Service/service_details/'.$idservice);
        }
    }
    
    public function generate_invoice_force_doa_sold() {
//        die('<pre>'.print_r($_POST).'</pre>');
        
        $this->db->trans_begin();
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $idservice=$this->input->post('idservice');
        $idwarehouse = $this->input->post('idwarehouse');
        $imeino = $this->input->post('imei_no');
        $idvariant=$this->input->post('idvariant');
        $iduser = $this->input->post('iduser');
        $idbranch = $this->input->post('idbranch');
            $updata_service = array(
                'process_status' => 11, // Close process
                'verified_by_coordinator' => $date,
                'closed' => $date,
                'warranty_status' => 2, // reject due to force doa delay
            );
            $this->Service_model->update_service_stock($idservice, $updata_service);
            $imei_history[]=array(
                'imei_no' => $imeino,
                'entry_type' => 'Service Case Reject & Closed by Coordinator',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'idvariant' => $idvariant,                    
                'idimei_details_link' => 19, // Sales Return from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $iduser,
            );

            $this->load->model('Purchase_model');
        //Generate Sale in credit
        //81425 live service customer
        //81336 testing service customer
//                '81425', 'Service', 'Rejected Customer', '1111111111', NULL, NULL, '416001', 'Karvir', 'Kolhapur', '1', 'Maharashtra', 'Kolhapur';
            $invoice_no = $this->Sale_model->get_invoice_no_by_branch($idbranch);
            $branch_manager = $this->General_model->get_active_users_byrole_branch(32, $idbranch);
            if(count($branch_manager)){
                $cust_fname = $branch_manager[0]->user_name;
                $cust_contact = $branch_manager[0]->user_contact;
            }else{
                $cust_fname = 'Service';
                $cust_contact = '1111111111';
            }
            $invid = $invoice_no->invoice_no + 1; 
            $y = date('y', mktime(0, 0, 0, 9 + date('m')));
            $y1 = $y - 1;
            $inv_no = $y1.$y . '/'. $invoice_no->branch_code . '/' . sprintf('%05d', $invid);

            $idstate = $invoice_no->idstate;
            $cust_idstate = $idstate;
            $idcustomer = 81425;
            $cust_lname = ' ';
            $cust_pincode = $invoice_no->branch_pincode;
            $cust_address = $invoice_no->branch_city;
            $gst_type = 0; //cgst

            $models = $this->Sale_model->ajax_stock_data_byimei_branch($imeino, $idbranch);
            foreach($models as $model){
                $idtype = $model->idproductcategory;
                $idcategory = $model->idcategory;
                $idbrand = $model->idbrand;
                $idmodel = $model->idmodel;
    //                $idvariant = $this->input->post('idvariant');
                $idgodown = 1;
                $skutype = $model->idskutype;
                $product_name = $model->product_name;
                $imei = $imeino;
                $qty = 1;
                $rowid = $model->id_stock;
                $hsn = $model->hsn; 
                $is_gst = $model->is_gst; // price on invoice
                $idvendor = $model->idvendor;
                $price = $model->mop;
                $basic = $model->mop;
                $discount_amt = 0;
                $total_amt = $model->mop;
                $landing = $model->landing;
                $mrp = $model->mrp;
                $mop = $model->mop;
                $salesman_price = $model->salesman_price;
                $is_mop = $model->is_mop; // price on invoice
                $igst = 0;
                $cgst = $model->cgst;
                $sgst = $model->sgst;
            }

            $sdata = array(
                'date' => $date,
                'inv_no' => $inv_no,
                'idbranch' => $idbranch,
                'idcustomer' => $idcustomer,
                'customer_fname' => $cust_fname,
                'customer_lname' => $cust_lname,
                'customer_idstate' => $cust_idstate,
                'customer_pincode' => $cust_pincode,
                'customer_contact' => $cust_contact,
                'customer_address' => $cust_address,
                'idsalesperson' => $this->input->post('idsalesperson'), // sales coordinator
                'basic_total' => $total_amt,
                'discount_total' => 0,
                'final_total' => $total_amt,
                'gst_type' => $gst_type,
                'created_by' => $iduser,
                'remark' => 'Customer Name: '.$cust_fname.'(Branch Manager), Invoice generated due to Service Case Rejection(Delay in Force DOA closure). Created by Service Coordinator',
                'entry_time' => $datetime,
                'dcprint' => 1,
            );
//                die('<pre>'.print_r($sdata).'</pre>');
            $idsale = $this->Sale_model->save_sale($sdata);
            $payment = array(
                'date' => $date,
                'idsale' => $idsale,
                'amount' => $total_amt,
                'idpayment_head' => 6,
                'idpayment_mode' => 9,
                'inv_no' => $inv_no,
                'idcustomer' => $idcustomer,
                'idbranch' => $idbranch,
                'created_by' => $iduser,
                'entry_time' => $datetime,
                'received_amount' => 0,
                'received_entry_time'=>NULL,
                'payment_receive' => 0,
                'approved_by' => 'Customer Name: '.$cust_fname.'(Branch Manager), Invoice generate due to Service Case Rejection(Delay in Force DOA closure). Created by Service Coordinator'
            );
            $id_sale_payment = $this->Sale_model->save_sale_payment($payment);
            $igst = 0;
            $sale_product = array(
                'date' => $date,
                'idsale' => $idsale,
                'idmodel' => $idmodel,
                'idvariant' => $idvariant,
                'imei_no' => $imei,
                'hsn' => $hsn,
                'idskutype' => $skutype,
                'idgodown' => $idgodown,
                'idproductcategory' => $idtype,
                'idcategory' => $idcategory,
                'idbrand' => $idbrand,
                'product_name' => $product_name,
                'price' => $price,
                'landing' => $landing,
                'mrp' => $mrp,
                'mop' => $mop,
                'salesman_price' => $salesman_price,
                'inv_no' => $inv_no,
                'qty' => $qty,
                'idbranch' => $idbranch,
                'discount_amt' => $discount_amt,
                'is_gst' => $is_gst,
                'is_mop' => $is_mop,
                'basic' => $basic,
                'idvendor' => $idvendor,
                'cgst_per' => $cgst,
                'sgst_per' => $sgst,
                'igst_per' => $igst,
                'total_amount' => $total_amt,
                'entry_time' => $datetime,
            );
            $idsaleproduct = $this->Sale_model->save_sale_product($sale_product);
            $this->Purchase_model->delete_stock_byidstock($rowid);
            // IMEI History
            $imei_history[]=array(
                'imei_no' => $imei,
                'entry_type' => 'Sale',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'idvariant' => $idvariant,
                'idimei_details_link' => 4, // Sale from imei_details_link table
                'idlink' => $idsale,
                'iduser' => $iduser,
            );
            $invoice_data = array( 'invoice_no' => $invid );
            $this->General_model->edit_db_branch($idbranch, $invoice_data);
            $this->General_model->save_batch_imei_history($imei_history);
                
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Failed to submit entry');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Entry submitted successfully');
        }
        return redirect('Sale/invoice_print/'.$idsale);
    }
    
    public function verify_inward_in_ho_and_process_sold_prouct() {
//        die(print_r($_POST));
//      15 verify, inward_in_ho and close
//        sold
//        if(warranty_status == 3) {15 Update DOA & Allow Replace/Upgrade Option to Branch} letter
//        if(warranty_status == 4) {15 Inward new Handset & Allow Replace/Upgrade Option to Branch} handset
        
        $this->db->trans_begin();
        
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        $imeino = $this->input->post('imei_no');
        $idwarehouse = $this->input->post('idwarehouse');
        $idvariant = $this->input->post('idvariant');
        $idservice = $this->input->post('idservice');
        $warranty_status = $this->input->post('warranty_status');
        $iduser = $this->input->post('iduser');
        $counter_faulty = $this->input->post('counter_faulty');
        
//      sold
        if($warranty_status == 3){ // DOA Letter
//               15 verified_by_coordinator
            $updata_service = array(
                'process_status' => 15, // Verified by cordinator
                'verified_by_coordinator' => $date,
                'branch_process_enable' => 1,    // Enable to replace, upgrade to branch against doa letter
            );
            $imei_history[]=array(
                'imei_no' => $imeino,
                'entry_type' => 'Service - Verified & transfer to DOA Godown against DOA Letter by Coordinator',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idwarehouse,
                'idgodown' => 3,
                'idvariant' => $idvariant,                    
                'idimei_details_link' => 19, // Sales Return from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $iduser,
            );
            $this->Service_model->update_service_stock($idservice, $updata_service);
            $update_stock = array(
                'idgodown' => 3, // DOA
            );
            $this->Service_model->update_stock_byimei($imeino,$update_stock);
            $this->General_model->save_batch_imei_history($imei_history);
        }else if($warranty_status == 4){ // DOA Handset
//               15 verified_by_coordinator
            $new_imei_against_doa = $this->input->post('new_imei_against_doa');
            $new_imei_data = $this->Service_model->doa_inward_reconciliation_data_byidservice($idservice);
            
            // save stock
            $inward_stock = array(
                'date' => $date,
                'imei_no' => $new_imei_against_doa,
                'idmodel' => $new_imei_data->idmodel,
                'created_by' => $iduser,
                'product_name' => $new_imei_data->full_name,
                'idvariant' => $new_imei_data->idvariant,
                'idskutype' => $new_imei_data->idskutype,
                'idproductcategory' => $new_imei_data->idproductcategory,
                'idcategory' => $new_imei_data->idcategory,
                'idbrand' => $new_imei_data->idbrand,
                'idgodown' => 1,
                'idbranch' => $new_imei_data->idbranch,
            );
            $this->Inward_model->save_stock($inward_stock);

            //            die($new_imei_data->idmodel);
            $imei_history[]=array(
                'imei_no' => $new_imei_against_doa,
                'entry_type' => 'Inward against DOA Handset by Coordinator',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idwarehouse,
                'idgodown' => 1,
                'idvariant' => $new_imei_data->idvariant,                    
                'idimei_details_link' => 19, // Sales Return from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $iduser,
            );
            // old imei history
            $imei_history[]=array(
                'imei_no' => $imeino,
                'entry_type' => 'Service(DOA) - Closure against New Handset by Coordinator',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idwarehouse,
                'idgodown' => 3,
                'idvariant' => $idvariant,                    
                'idimei_details_link' => 19, // Sales Return from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $iduser,
            );
//            die('<pre>'.print_r($imei_history,1).'</pre>');
            $this->General_model->save_batch_imei_history($imei_history);
            
            
//            die(print_r($imei_history));
            // update doa inward
            $inward_request = array(
                'status' => 1,
                'approved_by'=> $iduser,
            );
            $this->Service_model->update_doa_inward($idservice, $inward_request);

//            doa_reconciliation
            $doa_reconciliation = array(   
                'status' => 1,
                'closure_type' => 1,
                'cn_imei' => $new_imei_against_doa,
                'closure_by' => $iduser,
            );
            $this->Service_model->update_doa_reconciliation_byid($new_imei_data->id_doa_stock, $doa_reconciliation);                    
            
            $updata_service = array(
                'process_status' => 15, // Verified by cordinator
                'verified_by_coordinator' => $date,
                'branch_process_enable' => 1,    // Enable to replace, upgrade to branch against doa letter
            );
            $this->Service_model->update_service_stock($idservice, $updata_service);
            $this->Service_model->delete_idservice_from_stock($idservice);
        }
        
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Failed to submit entry');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Entry submitted successfully');
        }
//        if($counter_faulty){
//            return redirect('Service/service_counter_faulty_details/'.$idservice);
//        }else{
            return redirect('Service/service_details/'.$idservice);
//        }
    }
    
    public function my_branch_inward_service(){
        $idbranch=$this->session->userdata('idbranch');   
        $q['tab_active'] = '';
        $q['dispatch_data'] = $this->General_model->get_dispatch_type();
        $q['warehouse_data'] = $this->General_model->get_warehouse_data();
        $q['transport_vendor'] = $this->General_model->get_transport_vendor_data_byidbranch($_SESSION['idbranch']);
        $q['service_stock'] = $this->Service_model->get_service_stock_report(0, 0,$idbranch,1,0);
        $this->load->view('service/my_service_stock', $q);
    }
    
    public function save_service_send_to_ho() {
//        die('<pre>'.print_r($_POST,1).'<pre>');
        // Send to HO = 4
        $this->db->trans_begin();
        $checkrow = $this->input->post('checkrow');
        $idbranch = $this->input->post('idbranch');
        $dispatch_date = $this->input->post('dispatch_date');
        $idwarehouse = $this->input->post('idwarehouse');
        $entry_by = $this->input->post('entry_by');
        $count = count($checkrow);
        $datetime = date('Y-m-d H:i:s');
        // Insert into service transfer
        $service_trasf = array(
            'date' => $dispatch_date,
            'transfer_from' => $idbranch,
            'idbranch' => $idwarehouse,
            'total_product' => $count,
            'entry_time' => $datetime,
            'created_by' => $entry_by,
            'status' => 0,
            'request_type' => 2,
            'dispatch_date' => $dispatch_date,
            'iddispatch_type' => $this->input->post('iddispatchtype'),
            'dispatch_type' => $this->input->post('dispatch_type'),
            'courier_name' => $this->input->post('courier_name'),
            'idtransport_vendor' => $this->input->post('idtvendors'),
            'po_lr_no' => $this->input->post('po_lr_no'),
            'no_of_boxes' => $this->input->post('no_of_boxes'),
            'shipment_remark' => $this->input->post('shipment_remark'),
            'shipment_remark' => $this->input->post('shipment_remark'),
            'transfer_process_status' => 4, // Branch to HO
        );
        $id_service_transfer = $this->Service_model->save_service_transfer($service_trasf);
        $imei_history = [];
        for($i=0; $i < $count; $i++){
            $service_row = $this->Service_model->get_service_row_byid($checkrow[$i]);
            $service_product = array(
                'idservice_transfer' => $id_service_transfer,
                'date' => $dispatch_date,
                'idservice_stock' => $service_row->id_service,
                'transfer_from' => $idbranch,
                'idbranch' => $idwarehouse,
                'idvariant' => $service_row->idvariant,
                'idmodel' => $service_row->idmodel,
                'idproductcategory' => $service_row->idproductcategory,
                'idcategory' => $service_row->idcategory,
                'idbrand' => $service_row->idbrand,
                'idgodown' =>  4, // Service godown
                'qty' =>  1,
                'imei_no' => $service_row->imei,
                'idskutype' => $service_row->idskutype,
                'price' => $service_row->sold_amount,
            );
            $this->Service_model->save_service_transfer_product($service_product);
            
            // update stock
            $update_stock = array(
                'idbranch' => 0,
                'transfer_from' => $idbranch,
                'temp_idbranch' => $idwarehouse,
            );
            $this->Service_model->update_stock_byimei($service_row->imei,$update_stock);
            
            // update service stock
            $updata_service = array(
                'process_status' => 4,
                'branch_sent_to_ho' => $dispatch_date,
                'idservice_transfer_send_to_ho' => $id_service_transfer,
                'idwarehouse' => $idwarehouse,
            );
            $this->Service_model->update_service_stock($checkrow[$i], $updata_service);
            
            $imei_history[] = array(
                'imei_no' => $service_row->imei,
                'entry_type' => 'Service - Send To HO',
                'entry_time' => $datetime,
                'date' => $dispatch_date,
                'idbranch' => $idwarehouse,
                'transfer_from' => $idbranch,
                'idgodown' => 4,
                'iduser' => $entry_by,
                'idvariant' => $service_row->idvariant,
                'idimei_details_link' => 18, // Sales Return from imei_details_link table
                'idlink' => $checkrow[$i],
            );
        }
        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
        $this->db->trans_complete();
        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Fail to inward service. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Service case send to HO Successfully');
        }
        return redirect('Service/dc_receive_service_shipment/'.$id_service_transfer);
    }
    
    public function ajax_open_service_send_to_branch_form() {
        $dispatch_data = $this->General_model->get_dispatch_type();
        $transport_vendor = $this->General_model->get_transport_vendor_data_byidbranch($_SESSION['idbranch']);
        $selected_branch = $this->input->post('selected_branch');
        $selected_branch_name = $this->input->post('selected_branch_name');
//        $branch_data = $this->General_model->get_active_branch_data(); ?>
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-info" style="box-shadow: 4px 4px 10px rgba(0, 51, 153, 0.3);padding-bottom: 0;">
                <div class="panel-body" style="min-width: 750px">
                    <h4><center style="color: #003399; margin-bottom: 10px"><i class="fa fa-handshake-o"></i> Service - Send to Branch</center></h4>
                    <div class="thumbnail">            
                        <div class="col-md-2">Dispatch Type</div>
                        <div class="col-md-4">
                            <select class="select form-control iddispatchtype" required="" name="iddispatchtype" id="iddispatchtype" onchange="$('#dispatch_type').val($('#iddispatchtype option:selected').text());" >
                                <option value="">Select Type</option>
                                <?php foreach ($dispatch_data as $dispatch){ ?>
                                <option value="<?php echo $dispatch->id_dispatch_type ?>"><?php echo $dispatch->dispatch_type?></option>
                                <?php } ?>
                            </select>
                            <input type="hidden" id="dispatch_type" name="dispatch_type">
                        </div>
                        <div class="col-md-3">Dispatch Date</div>
                        <div class="col-md-3"><input type="text" class="form-control" name="dispatch_date" value="<?php echo date('Y-m-d') ?>" readonly=""/></div><div class="clearfix"></div><br>
                        <div class="col-md-2">Courier/ Transport</div>
                        <div class="col-md-4">
                            <select class="select form-control idtvendors" required="" name="idtvendors" id="idtvendors" onchange="$('#courier_name').val($('#idtvendors option:selected').text());">
                                <option value="">Select Transport Vendor</option>
                                <?php foreach ($transport_vendor as $tvendors){ ?>
                                <option value="<?php echo $tvendors->id_transport_vendor ?>"><?php echo $tvendors->transport_vendor_name?></option>
                                <?php } ?>
                            </select>
                            <input type="hidden" id="courier_name" name="courier_name"/>
                        </div>
                        <div class="col-md-3">POP/LR Number</div>
                        <div class="col-md-3"><input type="text" class="form-control" id="po_lr_no" name="po_lr_no" placeholder="Enter POP/LR Number"/></div><div class="clearfix"></div><br>
                        <div class="col-md-2">Send To</div>
                        <div class="col-md-4">
                            <b><?php echo $selected_branch_name ?></b>
<!--                            <select class="chosen-select form-control input-sm" >
                                <option value="">Select Branch</option>   
                                <?php // foreach($branch_data as $branch){ ?>                
                                    <option value="<?php // echo $branch->id_branch ?>"><?php // echo $branch->branch_name ?></option>
                                <?php // } ?>
                            </select>-->
                        </div>
                        <input type="hidden" value="<?php echo $selected_branch ?>" name="receiver_branch" id="receiver_branch" />
                        <div class="col-md-3">No of Boxes</div>
                        <div class="col-md-3"><input type="text" class="form-control" id="no_of_boxes" name="no_of_boxes" placeholder="No of Boxes" required=""/></div><div class="clearfix"></div><br>
                        <div class="col-md-2">Remark</div>
                        <div class="col-md-10"><input type="text" class="form-control" required="" name="shipment_remark" placeholder="Enter Shipment Remark"/></div><div class="clearfix"></div><br>
                    </div>
                    <a class="btn btn-warning" onclick="if(confirm('Do you want to cancel')){$('#service_send_to_branch_form').html('');}">Cancel</a>
                    <button class="btn btn-primary pull-right" type="submit" onclick="if($('#receiver_branch').val() == ''){alert('Select receiver branch');return false;}
                        if($('#receiver_branch').val() != $('#idbranch').val()){alert('You select wrong sender branch!');return false;}
                        if(!confirm('Do you want to submit')){ return false;}" formmethod="POST" id="save_service_send_to_branch" formaction="<?php echo base_url('Service/save_service_send_ho_to_branch')?>">Send <span class="fa fa-send"></span></button>
                </div>
            </div>
        </div><div class="clearfix"></div>
    <?php 
    }
    
    public function save_service_send_ho_to_branch() {
//        die('<pre>'.print_r($_POST,1).'<pre>');
//        transfer_process_status
        // 9 = Send HO to Branch
        $this->db->trans_begin();
        $checkrow = $this->input->post('checkrow');
        $idbranch = $this->input->post('receiver_branch');
        $dispatch_date = $this->input->post('dispatch_date');
        $idwarehouse = $this->input->post('idwarehouse');
        $entry_by = $this->input->post('entry_by');
        $count = count($checkrow);
        $datetime = date('Y-m-d H:i:s');
        // Insert into service transfer
        $service_trasf = array(
            'date' => $dispatch_date,
            'transfer_from' => $idwarehouse,
            'idbranch' => $idbranch,
            'total_product' => $count,
            'entry_time' => $datetime,
            'created_by' => $entry_by,
            'status' => 0,
            'request_type' => 2,
            'dispatch_date' => $dispatch_date,
            'iddispatch_type' => $this->input->post('iddispatchtype'),
            'dispatch_type' => $this->input->post('dispatch_type'),
            'courier_name' => $this->input->post('courier_name'),
            'idtransport_vendor' => $this->input->post('idtvendors'),
            'po_lr_no' => $this->input->post('po_lr_no'),
            'no_of_boxes' => $this->input->post('no_of_boxes'),
            'shipment_remark' => $this->input->post('shipment_remark'),
            'transfer_process_status' => 9, //HO to Branch
        );
        $id_service_transfer = $this->Service_model->save_service_transfer($service_trasf);
        $imei_history = [];
        for($i=0; $i < $count; $i++){
            $service_row = $this->Service_model->get_service_row_byid($checkrow[$i]);
            $service_product = array(
                'idservice_transfer' => $id_service_transfer,
                'date' => $dispatch_date,
                'idservice_stock' => $service_row->id_service,
                'transfer_from' => $idwarehouse,
                'idbranch' => $idbranch,
                'idvariant' => $service_row->idvariant,
                'idmodel' => $service_row->idmodel,
                'idproductcategory' => $service_row->idproductcategory,
                'idcategory' => $service_row->idcategory,
                'idbrand' => $service_row->idbrand,
                'idgodown' =>  4, // Service godown
                'qty' =>  1,
                'imei_no' => $service_row->imei,
                'idskutype' => $service_row->idskutype,
                'price' => $service_row->sold_amount,
            );
            $this->Service_model->save_service_transfer_product($service_product);
            
            // update stock
            $update_stock = array(
                'idbranch' => 0,
                'transfer_from' => $idwarehouse,
                'temp_idbranch' => $idbranch,
            );
            $this->Service_model->update_stock_byimei($service_row->imei,$update_stock);
            
            // update service stock
            $updata_service = array(
                'process_status' => 9,
                'ho_sent_to_branch' => $dispatch_date,
                'idservice_transfer_send_to_branch' => $id_service_transfer,
            );
            $this->Service_model->update_service_stock($checkrow[$i], $updata_service);
            
            $imei_history[] = array(
                'imei_no' => $service_row->imei,
                'entry_type' => 'Service - HO Send To Branch',
                'entry_time' => $datetime,
                'date' => $dispatch_date,
                'idbranch' => $idbranch,
                'transfer_from' => $idwarehouse,
                'idgodown' => 4,
                'iduser' => $entry_by,
                'idvariant' => $service_row->idvariant,
                'idimei_details_link' => 18, // Sales Return from imei_details_link table
                'idlink' => $checkrow[$i],
            );
        }
        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
        $this->db->trans_complete();
        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Fail to inward service. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Serive case send to Branch Successfully');
        }
        return redirect('Service/dc_receive_service_shipment_at_branch/'.$id_service_transfer);
    }
    
    public function save_receive_servcice_at_ho_from_branch() {
//        die('<pre>'.print_r($_POST,1).'<pre>');
        // Received at HO from Branch = 5
        $this->db->trans_begin();
        $imei_no = $this->input->post('imei_no');
        $idservice_transfer = $this->input->post('idservice_transfer');
        $shipment_received_by = $this->input->post('shipment_received_by');
        $idbranch = $this->input->post('idbranch');
        $idservice = $this->input->post('idservice');
        $idvariant = $this->input->post('idvariant');
        $count = count($imei_no);
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        // Update service transfer
        $service_trasf = array(
            'shipment_received_remark' => $this->input->post('shipment_received_remark'),
            'shipment_received_by' => $shipment_received_by,
            'shipment_received_date' => $date,
            'shipment_received_entry_time' => $datetime,
            'status' => 1,
        );
        $this->Service_model->update_service_transfer($idservice_transfer, $service_trasf);
        $imei_history = [];
        for($i=0; $i < $count; $i++){
            // update stock
            $update_stock = array(
                'idbranch' => $idbranch,
                'temp_idbranch' => 0,
            );
            $this->Service_model->update_stock_byimei($imei_no[$i],$update_stock);
            // update service stock
            $updata_service = array(
                'process_status' => 5,
                'ho_inward_from_branch' => $date,
            );
            $this->Service_model->update_service_stock($idservice[$i], $updata_service);
            $imei_history[] = array(
                'imei_no' => $imei_no[$i],
                'entry_type' => 'Service - Received in HO from Branch',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 4,
                'iduser' => $shipment_received_by,
                'idvariant' => $idvariant[$i],
                'idimei_details_link' => 19, // Sales Return from imei_details_link table
                'idlink' => $idservice_transfer,
            );
        }
        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
        $this->db->trans_complete();
        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Fail to inward service. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Service case received at HO Successfully');
        }
        return redirect('Service/service_branch_send_to_ho/');
    }
    
    public function save_receive_servcice_at_branch_from_ho() {
//        die('<pre>'.print_r($_POST,1).'<pre>');
        // Received at Branch from HO = 12
        $this->db->trans_begin();
        $imei_no = $this->input->post('imei_no');
        $idservice_transfer = $this->input->post('idservice_transfer');
        $shipment_received_by = $this->input->post('shipment_received_by');
        $idbranch = $this->input->post('idbranch');
        $idservice = $this->input->post('idservice');
        $idvariant = $this->input->post('idvariant');
        $count = count($imei_no);
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        // Update service transfer
        $service_trasf = array(
            'shipment_received_remark' => $this->input->post('shipment_received_remark'),
            'shipment_received_by' => $shipment_received_by,
            'shipment_received_date' => $date,
            'shipment_received_entry_time' => $datetime,
            'status' => 1,
        );
        $this->Service_model->update_service_transfer($idservice_transfer, $service_trasf);
        $imei_history = [];
        for($i=0; $i < $count; $i++){
            // update stock
            $update_stock = array(
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'temp_idbranch' => 0,
            );
            $this->Service_model->update_stock_byimei($imei_no[$i],$update_stock);
            // update service stock
            $updata_service = array(
                'process_status' => 12,
                'branch_inwart_by_ho' => $date,
            );
            $this->Service_model->update_service_stock($idservice[$i], $updata_service);
            $imei_history[] = array(
                'imei_no' => $imei_no[$i],
                'entry_type' => 'Service - Received At Branch from HO',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'iduser' => $shipment_received_by,
                'idvariant' => $idvariant[$i],
                'idimei_details_link' => 19, // Sales Return from imei_details_link table
                'idlink' => $idservice_transfer,
            );
        }
        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
        $this->db->trans_complete();
        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Fail to inward service. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Service case received at Branch Successfully');
        }
        return redirect('Service/receive_service_shipment_at_branch/'.$idservice_transfer);
    }
    
    public function close_service_at_branch() {
//        die('<pre>'.print_r($_POST,1).'<pre>');
        // Close service in branch = 11
        $this->db->trans_begin();
        $imei_no = $this->input->post('imei_no');
        $closed_by = $this->input->post('iduser');
        $idbranch = $this->input->post('idbranch');
        $idservice = $this->input->post('idservice');
        $idvariant = $this->input->post('idvariant');
        $counter_faulty = $this->input->post('counter_faulty');
        $warranty_status = $this->input->post('warranty_status');
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        // Update service transfer
        $imei_history = [];
        // update service stock
        $updata_service = array(
            'process_status' => 11,
            'closed' => $date,
        );
        $this->Service_model->update_service_stock($idservice, $updata_service);
        $imei_history[] = array(
            'imei_no' => $imei_no,
            'entry_type' => 'Service - Closed in Branch',
            'entry_time' => $datetime,
            'date' => $date,
            'idbranch' => $idbranch,
            'idgodown' => 1,
            'iduser' => $closed_by,
            'idvariant' => $idvariant,
            'idimei_details_link' => 19, // Service imei_details_link table
            'idlink' => $idservice,
        );
        if(!$counter_faulty){
            $this->load->model('Purchase_model');
            if($warranty_status == 1){ 
                $this->Sale_model->delete_stock_byimei($imei_no);
                $status_name = 'Repaired'; 
                $imei_history[] = array(
                    'imei_no' => $imei_no,
                    'entry_type' => 'Returned to Customer due to Service - '.$status_name,
                    'entry_time' => $datetime,
                    'date' => $date,
                    'idbranch' => $idbranch,
                    'idgodown' => 1,
                    'iduser' => $closed_by,
                    'idvariant' => $idvariant,
                    'idimei_details_link' => 19, // Service imei_details_link table
                    'idlink' => $idservice,
                );
            }
            elseif($warranty_status == 2){
                $status_name = 'Rejected'; 
                $this->Sale_model->delete_stock_byimei($imei_no);
                $imei_history[] = array(
                    'imei_no' => $imei_no,
                    'entry_type' => 'Returned to Customer due to Service - '.$status_name,
                    'entry_time' => $datetime,
                    'date' => $date,
                    'idbranch' => $idbranch,
                    'idgodown' => 1,
                    'iduser' => $closed_by,
                    'idvariant' => $idvariant,
                    'idimei_details_link' => 19, // Service imei_details_link table
                    'idlink' => $idservice,
                );
            
            }
            elseif($warranty_status == 3){ 
                $status_name = 'DOA Letter'; 
                
            }
            elseif($warranty_status == 4){ 
                $status_name = 'DOA Handset'; 
                
            }
        }
        
        
        
        if(count($imei_history) > 0){
            $this->General_model->save_batch_imei_history($imei_history);
        }
        $this->db->trans_complete();
        if($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Fail to close service. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Service case closed in Branch Successfully');
        }
        if($counter_faulty){
            return redirect('Service/service_counter_faulty_details/'.$idservice);
        }else{
            return redirect('Service/service_details/'.$idservice);
        }
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
//                if($q['service_data'][0]->counter_faulty){
////                    $q['sale'] = $this->Service_model->get_sale_product_by_idsaleproduct($q['service_data'][0]->idsale_product,$q['service_data'][0]->inv_no);
//                    $q['sale'] = $this->Service_model->service_counter_faulty_details($q['service_data'][0]->id_service);
////                    $q['user_data'] = $this->Service_model->get_user_data_idbranch_byidrole($q['service_data'][0]->idbranch, 32);
//                }else{
                    $q['sale'] = $this->Service_model->get_sale_product_by_idsaleproduct($q['service_data'][0]->idsale_product,$q['service_data'][0]->inv_no);
//                }
            }
//            die(print_r($q));
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
            $sinv_no = 'DC'.$y1.$y . '/'. $invid->branch_code . '/' . sprintf('%05d', $sinvid);
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
            $sinv_no = 'DC'.$y1.$y . '/'. $invid->branch_code . '/' . sprintf('%05d', $sinvid);
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
        
            $newidbrand = $this->input->post('newidbrand');
            $idv = $this->input->post('model');
            
            
            
        if($doa_return_type==2){            
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
                $doa_idvariant=$this->input->post('idvariant'.$idservice);
                $var_data_old = $this->General_model->get_model_variant_data_byidvariant($doa_idvariant);          
                $idmodel_doa = $var_data_old->idmodel;
                $idskutype_doa = $var_data_old->idsku_type;                
                $idbrand_doa = $var_data_old->idbrand;
                $idproductcategory_doa = $var_data_old->idproductcategory;        
                $idcategory_doa = $var_data_old->idcategory;                 
                $doa_reconciliation = array(   
                        'date' => $date,
                        'imei_no' => $imeino,
                        'idmodel' => $idmodel_doa,
                        'created_by' => $sales_return_by, 
                        'idvariant' => $doa_idvariant,
                        'idskutype' => $idskutype_doa,
                        'idproductcategory' => $idproductcategory_doa,
                        'idcategory' => $idcategory_doa,
                        'idbrand' => $idbrand_doa,
                        'idservice' => $idservice,
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
                'idgodown' => 3,
                'idvariant' => $doa_idvariant,                    
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
            $doa_idvariant=$this->input->post('idvariant'.$idservice);
            $var_data_old = $this->General_model->get_model_variant_data_byidvariant($doa_idvariant);          
            $idmodel_doa = $var_data_old->idmodel;
            $idskutype_doa = $var_data_old->idsku_type;                
            $idbrand_doa = $var_data_old->idbrand;
            $idproductcategory_doa = $var_data_old->idproductcategory;        
            $idcategory_doa = $var_data_old->idcategory;   
            $doa_reconciliation = array(   
                       'date' => $date,
                       'imei_no' => $imeino,
                       'idmodel' => $idmodel_doa,
                       'created_by' => $sales_return_by, 
                       'idvariant' => $doa_idvariant,
                       'idskutype' => $idskutype_doa,
                       'idproductcategory' => $idproductcategory_doa,
                       'idcategory' => $idcategory_doa,
                       'idbrand' => $idbrand_doa,                       
                       'idbranch' => $idbranch,
                        'created_by' => $sales_return_by,
                        'sales_return_by' => $sales_return_by,
                        'return_date' => $return_date,
                        'sales_return_type' => 3, 
                        'sale_date' => $this->input->post('inv_date'),
                        'idgodown' => 3,
                        'idservice' => $idservice,
                        'doa_return_type' => $doa_return_type,
                        'doa_id' => $doa_id,
                        'doa_date' => $doa_date,
                        'status' => 0
                );
                 if(count($doa_reconciliation)>0){
                    $this->Service_model->save_doa_reconciliation($doa_reconciliation);                    
                } 
            
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
                        'sale_date' => $this->input-> post('inv_date'),
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
        $warranty_state=$this->input->post('warranty_state');
        $counter_faulty=$this->input->post('counter_faulty');
        $iduser=$this->input->post('iduser');
        $idbranch=$this->input->post('idbranch');
        $idvariant=$this->input->post('idvariant');
        $imei_no=$this->input->post('imei_no');
        $date = date('Y-m-d');
        $datetime = date('Y-m-d H:i:s');
        if($warranty_state == 1){
            $warranty_state_name = 'Repaired';
        }else{
            $warranty_state_name = 'Rejected';
        }
        if($counter_faulty){
            $data = array(
                'process_status' => 11,
                'local_to_branch' => $date,
                'closed' => $date,
                'closure_remark' => $remark,
                'warranty_status' => $warranty_state,
                'actual_branch_status' => $warranty_state
            );
            $res=$this->Service_model->update_service_stock($idservice, $data);
            $imei_history[]=array(
                'imei_no' => $imei_no,
                'entry_type' => 'Service('.$warranty_state_name.') - Inward from Local Care',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'idvariant' => $idvariant,
                'idimei_details_link' => 19, // Sale from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $iduser,
            );
            if(count($imei_history) > 0){
                $this->General_model->save_batch_imei_history($imei_history);
            }
            if($res){
                $update_stock = array('idgodown' => 1);
                $this->Service_model->update_stock($idservice,$update_stock);
                echo 1;
            } else {
                echo 0;    
            }
        }else{
            $data = array(
                'process_status' => 11,
                'local_to_branch' => $date,
                'closed' => $date,
                'closure_remark' => $remark,
                'warranty_status' => $warranty_state,
                'actual_branch_status' => $warranty_state
            );
            $res=$this->Service_model->update_service_stock($idservice, $data);
            $imei_history[]=array(
                'imei_no' => $imei_no,
                'entry_type' => 'Service('.$warranty_state_name.') - Inward from Local Care & Given to Customer',
                'entry_time' => $datetime,
                'date' => $date,
                'idbranch' => $idbranch,
                'idgodown' => 1,
                'idvariant' => $idvariant,
                'idimei_details_link' => 19, // Sale from imei_details_link table
                'idlink' => $idservice,
                'iduser' => $iduser,
            );
            if(count($imei_history) > 0){
                $this->General_model->save_batch_imei_history($imei_history);
            }
            if($res){
                $this->Service_model->delete_idservice_from_stock($idservice);        
                echo 1;
            } else {
                echo 0;    
            }
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


    public function force_doa_handset_entry_form() {
        $idbranch = $_SESSION['idbranch']; ?>
        <center><h4>Replacement Handset Service State</h4></center><hr>
        <!--<div class="border-light rounded p-4" style="background-color: #fff">-->                                                           
            <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $idbranch ?>"/>
            <?php  $brands = $this->General_model->get_active_brand_data(); ?>
            <div class="col-md-2 col-sm-4">
                <span>Brand</span>
            </div>
            <div class="col-md-4 col-sm-4">
                <select class="chosen-select form-control input-sm newidbrand" required="" name="newidbrand" id="newidbrand">
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
                <div class="model_block">
                    <select class="chosen-select form-control input-sm model" required="" name="model" id="model">
                        <option value="">Select Model </option>                                    
                    </select>
                </div>
            </div><div class="clearfix"></div><br>
            <div class="col-md-2 col-sm-4">
                <span>Scan IMEI</span>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control input-sm new_enter_imei" required="" placeholder="Scan IMEI/SRNO/Barcode" id="new_enter_imei"/>
            </div><div class="clearfix"></div><br>
            <div class="col-md-2">
                <span>Remark</span>
            </div>
            <div class="col-md-10">
                <input type="text" class="form-control input-sm repaire_remark" placeholder="Remark" id="in_remark"/>
            </div>
            <div class="clearfix"></div><hr>
            <div class="col-md-4 pull-right">
                <button class="btn btn-info doa_handset_btn" value="4" style="background-image: linear-gradient(to right top, #051937, #173460, #28538d, #3773bc, #4596ee);">DOA Handset</button>
            </div><div class="clearfix"></div>
        <!--</div><div class="clearfix"></div>-->
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
    

    
    public function ajax_get_imei_details_for_doa_replace() {
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
    
    public function save_product_replace_upgrade_against_doa_return() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        
        $this->load->model('Transfer_model');
        $this->load->model('Purchase_model');
        $this->load->model('Sales_return_model');
        $total_return_cash = 0;
        $this->db->trans_begin();
        $inv_no = $this->input->post('inv_no');
        $idbranch = $this->input->post('idbranch');
        $date = date('Y-m-d');
        $sales_return_by = $this->input->post('iduser');
        $idservice = $this->input->post('idservice');
        $iddoa_return_type = $this->input->post('doa_return_type');
        if($iddoa_return_type == 3){ // letter
            $doa_return_type = 1;
        }elseif($iddoa_return_type == 4){ // handset
            $doa_return_type = 2;
        }
        $id_sale = $this->input->post('idsale');
        $return_date = date('Y-m-d H:i:s');
        
        $idcustomer = $this->input->post('idcustomer');
        $cust_data = $this->Sale_model->get_customer_byid($idcustomer);
        $cust_fname = $cust_data[0]->customer_fname;
        $cust_lname = $cust_data[0]->customer_lname;
        
        $invid = $this->Sale_model->get_invoice_no_by_branch($idbranch); 
        $y = date('y', mktime(0, 0, 0, 9 + date('m')));
        $y1 = $y - 1;
        $next_srinv_no =  $invid->sales_return_invoice_no + 1;
        $sales_return_invid = 'SR'.$y1.$y . '/'. $invid->branch_code . '/' . sprintf('%04d', $next_srinv_no);
        
// save sales return
        $overall_total_amt = $this->input->post('selected_total_amount');
        $data_sales_return = array(
            'date' => $date,
            'idsale' => $id_sale,
            'sales_return_invid' => $sales_return_invid,
            'sales_return_type' => 3, // doa return
            'doa_return_type' => $doa_return_type, // letter/ handset return
            'inv_no' => $inv_no,
            'inv_date' => $this->input->post('inv_date'),
            'idbranch' => $idbranch,
            'idcustomer' => $idcustomer,
            'sales_return_approved_by'=> $this->input->post('sales_return_approved_by'),
            'sales_return_reason'=> $this->input->post('sales_return_reason'),
            'idsalesperson' => $this->input->post('idsalesperson'),
            'brand_doa_date' => $this->input->post('doa_date'),
            'doa_id' => $this->input->post('doa_id'),
            'final_total' => $overall_total_amt,
            'discount_total' => 0,
            'basic_total' => $overall_total_amt,
            'sales_return_by' => $sales_return_by,
        );
        // save sales return product
        $idsalereturn = $this->Sales_return_model->save_sales_return($data_sales_return);
        // update sale product
        $saleupdate = array(
            'sales_return_type' => 3, // DOA return
            'sales_return_invid' => $sales_return_invid,
            'sales_return_by' => $sales_return_by,
            'sales_return_date' => $return_date,
        );
        //// update query at bottom line no 1782
        $imei_history = array();
        $product_id = $this->input->post('idsale_product'); //$selected_sale_products[$i];
        // update sale product
        $saleproductupdate = array(
            'sales_return_type' => 3, // DOA return
            'sales_return_invid' => $sales_return_invid,
            'sales_return_by' => $sales_return_by,
            'sales_return_date' => $return_date,
            'sale_return_qty' => 1,
        );
        $this->Sale_model->update_sale_product_byidsaleproduct($product_id, $saleproductupdate);

        $sale_product_data = $this->Service_model->get_sale_product_byidsaleproduct($product_id);
        $cgst_per = $sale_product_data[0]->cgst_per;
        $sgst_per = $sale_product_data[0]->sgst_per;
        $igst_per = $sale_product_data[0]->igst_per;
        $selected_row_cgst_amt=0;$selected_row_sgst_amt=0;$selected_row_igst_amt=0;$selected_row_taxable=0;
        if($igst_per != 0){
            $cal = ($sale_product_data[0]->igst_per + 100) / 100;
            $selected_row_taxable = $overall_total_amt / $cal;
            $selected_row_cgst_amt = $overall_total_amt - $selected_row_taxable;
        }else{
            $cal = ($sale_product_data[0]->cgst_per + $sale_product_data[0]->sgst_per + 100) / 100;
            $selected_row_taxable = $overall_total_amt / $cal;
            $cgst = $overall_total_amt - $selected_row_taxable;
            $selected_row_sgst_amt = $cgst / 2;
            $selected_row_igst_amt = $cgst / 2;
        }
        $selected_row_tax = $selected_row_cgst_amt+$selected_row_sgst_amt+$selected_row_igst_amt;

        $imeino = $this->input->post('imei_no');
        $sale_product = array(
            'sales_return_type' => 3, // DOA return
            'date' => $date,
            'imei_no' => $imeino,
            'sales_return_invid' => $sales_return_invid,
            'idskutype' => $sale_product_data[0]->idskutype,
            'idproductcategory' => $sale_product_data[0]->idproductcategory,
            'idcategory' => $sale_product_data[0]->idcategory,
            'idgodown' => $sale_product_data[0]->idgodown,
            'idvariant' => $sale_product_data[0]->idvariant,
            'idmodel' => $sale_product_data[0]->old_idmodel,
            'idbranch' => $idbranch,
            'idbrand' => $sale_product_data[0]->idbrand,
            'sales_return_by' => $sales_return_by,
            'idsales_return' => $idsalereturn,
            'product_name' => $sale_product_data[0]->product_name,
            'price' => $overall_total_amt,
            'inv_no' => $inv_no,
            'qty' => 1,
            'taxable_amt' => $selected_row_taxable,
            'cgst_per' => $cgst_per,
            'sgst_per' => $sgst_per,
            'igst_per' => $igst_per,
            'cgst_amt' => $selected_row_cgst_amt,
            'sgst_amt' => $selected_row_sgst_amt,
            'igst_amt' => $selected_row_igst_amt,
            'tax' => $selected_row_tax,
            'basic' => $overall_total_amt,
            'discount_amt' => 0,
            'total_amount' => $overall_total_amt,
            'idsale_product' => $product_id,
            'doa_return_type' => $doa_return_type, // letter/ handset return
            'old_landing' => $sale_product_data[0]->landing,
        );
        $idsalereturnproduct = $this->Sales_return_model->save_sales_return_product($sale_product);
        $imei_history[]=array(
            'imei_no' => $imeino,
            'entry_type' => 'Sales Return - Replace/Upgrade Against DOA',
            'entry_time' => $return_date,
            'date' => $date,
            'idbranch' => $idbranch,
            'idgodown' => 3,
            'idvariant' => $sale_product_data[0]->idvariant,
            'idimei_details_link' => 9, // Sales Return from imei_details_link table
            'idlink' => $idsalereturn,
            'iduser' => $sales_return_by
        );
        $srpayment = array();
//        $srpayment[] = array(
//            'date' => $date,
//            'inv_no' => $sales_return_invid,
//            'entry_type' => 3,
//            'idbranch' => $idbranch,
//            'idtable' => $idsalereturn,
//            'table_name' => 'sales_return',
//            'amount' => -$overall_total_amt,
//        );
//        $this->Sale_model->save_daybook_cash_payment($srpayment);
        
        
        
        
        
        
        
        
// save sale       
//        $idbranch = $this->input->post('idbranch');
        $dcprint = $this->input->post('dcprint');
        $sinvid = $invid->invoice_no + 1; 
        if($dcprint[0] == 0){
            $sinv_no = $y1 .'-'. $y . '/'. $invid->branch_code . '/' . sprintf('%05d', $sinvid);
        }else{
            $sinv_no = 'DC'.$y1.$y . '/'. $invid->branch_code . '/' . sprintf('%05d', $sinvid);
        }
        
        $datetime = date('Y-m-d H:i:s');
        $idstate = $this->input->post('idstate');
        $cust_idstate = $this->input->post('cust_idstate');
        $cust_pincode = $cust_data[0]->customer_pincode;
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
            'customer_contact' => $cust_data[0]->customer_contact,
            'customer_address' => $cust_data[0]->customer_address,
            'customer_gst' => $cust_data[0]->customer_gst,
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
        echo $headattr;
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
            if($idpaymenthead[$j] == 1){
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
                    'idimei_details_link' => 4, // Sale from imei_details_link table
                    'idlink' => $idsale,
                    'iduser' => $sales_return_by
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

        // Update Sale from sales return
        $this->Sale_model->update_sale($id_sale, $saleupdate);
        
        $invoice_data = array(
            'invoice_no' => $sinvid,
            'sales_return_invoice_no' => $next_srinv_no 
        );
        $this->General_model->edit_db_branch($idbranch, $invoice_data);
        
        // Service
        $imei_history[]=array(
            'imei_no' => $imeino,
            'entry_type' => 'Service - Closed(Replace/Upgrade against DOA)',
            'entry_time' => $datetime,
            'date' => $date,
            'idbranch' => $idbranch,
            'idgodown' => 3,
            'idvariant' => $sale_product_data[0]->idvariant,
            'idimei_details_link' => 19, // Sales Return from imei_details_link table
            'idlink' => $idservice,
            'iduser' => $this->input->post('iduser'),
        );
        $updata_service = array(
            'process_status' => 11,
            'closed' => $date,
            'actual_branch_status' => 4,
        );
        $this->Service_model->update_service_stock($idservice, $updata_service);

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
    
}