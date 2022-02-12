<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance_scheme extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('General_model');
        $this->load->model('Finance_model');
        $this->load->model('Stock_model');
    }
    
    
    public function finance_scheme() {
       $q['tab_active'] = '';                
        $q['brand_data'] = $this->General_model->get_active_brand_data();       
        $q['bank_data'] = $this->General_model->get_active_bank();
        $q['finance_data'] = $this->Finance_model->get_finance_scheme_data();
//        die('<pre>'.print_r($q['finance_data'],1).'</pre>');
        $this->load->view('finance_scheme/add_finance_scheme', $q);
    }
    public function ajax_variants_by_brand() {          
        $product_category = $this->input->post('product_category');        
        $brand = $this->input->post('brand');        
        $model_data = $this->General_model->get_active_variants_by_brand_product_category($product_category,$brand);
        echo '<select class="chosen-select form-control" name="model" id="model" required=""><option value="">Select Model</option>';
        foreach ($model_data as $model) { 
            echo '<option  value="'.$model->id_variant .'">'.$model->full_name.'</option>';
        }
    }
    public function ajax_modellist_by_brand() {          
        $product_category = $this->input->post('product_category');        
        $brand = $this->input->post('brand');        
        $model_data = $this->General_model->get_active_variants_by_brand_product_category($product_category,$brand);
        
        echo '<b>Model</b><select class="chosen-select form-control" name="idvariant" id="idvariant" required=""><option value="">Select Model</option><option value="0">All Model</option>';
        foreach ($model_data as $model) { 
            echo '<option  value="'.$model->id_variant .'">'.$model->full_name.'</option>';
        }
    }
    public function ajax_variants_by_brand_swipe() {          
        $product_category = $this->input->post('product_category');        
        $brand = $this->input->post('brand');        
        $model_data = $this->General_model->get_active_variants_by_brand_product_category($product_category,$brand);
        echo '<select class="chosen-select form-control" name="idvariant" id="idvariant" required=""><option value="">Select Model</option>';
        foreach ($model_data as $model) { 
            echo '<option  value="'.$model->id_variant .'">'.$model->full_name.'</option>';
        }
    }
    public function ajax_get_payment_mode_byidhead(){
       $idhead = $this->input->post('idhead');      
        $payment_mode = $this->General_model->ajax_get_payment_mode_byhead($idhead);
        echo '<select class="chosen-select form-control idpmodel" name="idpaymentmode" id="idpaymentmode" required=""><option value="">Select Finance Provider</option>';
        foreach ($payment_mode as $head) { 
            echo '<option  value="'.$head->id_paymentmode .'">'.$head->payment_mode.'</option>';
        } 
    }
    public function ajax_get_payment_mode_byidhead_edit(){
       $idhead = $this->input->post('idhead');      
        $payment_mode = $this->General_model->ajax_get_payment_mode_byhead($idhead);
        echo '<select class="chosen-select form-control idpmodel" name="idpmodel" id="idpmodel" required=""><option value="">Select Fnance Provider</option><option value="0">All Fnance Provider</option>';
        foreach ($payment_mode as $head) { 
            echo '<option  value="'.$head->id_paymentmode .'">'.$head->payment_mode.'</option>';
        } 
    }
    public function ajax_get_model_variant_data_byidvariant(){
       
        $idvariant = $this->input->post('idvariant');
        $model_variant = $this->General_model->get_model_variant_data_byidvariant($idvariant);
        if($model_variant){
            echo $model_variant->mop;
        }else{
            echo '0';
        }
    }
    
    public function save_finance_scheme(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
        
        $entry_time = date('Y-m-d h:i:s');
        if($this->input->post('scheme_type') == 1){ //Swipe Scheme
            $from = $this->input->post('from');
            $to = $this->input->post('to');
            $idbrand = $this->input->post('idbrand');
            $idvariant = $this->input->post('idvariant');
            $idpaymentmode = $this->input->post('idpaymentmode');
            $finance_mop = $this->input->post('finance_mop');
            $swipe_scheme = $this->input->post('swipe_scheme');
            $type_of_card = $this->input->post('type_of_card');
            $bank = $this->input->post('bank');
            $scheme = $this->input->post('scheme_type');
            
            $total_emi = $this->input->post('total_emi');
            $down_emi = $this->input->post('down_emi');
            $finance_emi = $this->input->post('finance_emi');
            $rate_interest = $this->input->post('rate_interest');
            $interest_mop = $this->input->post('mop_interest');
            $emi_amount = $this->input->post('emi_amount');
            $finance_emi_amount = $this->input->post('finance_emi_amount');
            $cashback_type = $this->input->post('cashback_type');
            $cashback = $this->input->post('cashback');
            
            $cnt_emi= count($total_emi);
            
            $model_variant = $this->General_model->get_model_variant_data_byidvariant($idvariant);
            $idpcat = $model_variant->idproductcategory;
            $idcategory = $model_variant->idcategory;
            $idmodel = $model_variant->idmodel;
            
            if($idpcat == 1){
                if($idcategory == 2 || $idcategory == 28  || $idcategory == 31){
                    $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byidmodel($idmodel,$idcategory);
                }else{
                   $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byid($idvariant,$idcategory);
                }

                foreach ($allvariants as $idvv){
                    $idva[] = $idvv->id_variant;
                }
            
                for($y=0; $y < $cnt_emi; $y++){
                    for($i=0; $i<count($idva); $i++){
                        $data = array(
                           'scheme' => $scheme,
                            'idbrand' => $idbrand,
                            'idvariant' => $idva[$i],
                            'scheme_type' => $swipe_scheme,
                            'idpayment_head' => 3,
                            'idpayment_mode' => $idpaymentmode,
                            'finance_mop' => $finance_mop,
                            'type_of_card' => $type_of_card,
                            'idbank' => $bank,
                            'created_by' => $_SESSION['id_users'],
                            'from_date' => $from[$y],
                            'to_date' => $to[$y],
                            'entry_time' => $entry_time,
                            'total_emi' => $total_emi[$y],
                            'emi_finance' => $finance_emi[$y],
                            'emi_downpayment' => $down_emi[$y],
                            'emi_amount' => $emi_amount[$y],
                            'finance_amount' => $finance_emi_amount[$y],
                            'cashback_amount' => $cashback[$y],
                            'cashback_type' => $cashback_type[$y],
                            'rate_of_interest' => $rate_interest[$y],
                            'interest_mop' => $interest_mop[$y],
                        );
                        $this->Finance_model->save_finance_scheme_data($data);
                    }
                }
            }else{
                for($y=0; $y < $cnt_emi; $y++){
                    $data = array(
                        'scheme' => $scheme,
                        'idbrand' => $idbrand,
                        'idvariant' => $idvariant,
                        'scheme_type' => $swipe_scheme,
                        'idpayment_head' => 3,
                        'idpayment_mode' => $idpaymentmode,
                        'finance_mop' => $finance_mop,
                        'type_of_card' => $type_of_card,
                        'idbank' => $bank,
                        'created_by' => $_SESSION['id_users'],
                        'from_date' => $from[$y],
                        'to_date' => $to[$y],
                        'entry_time' => $entry_time,
                        'total_emi' => $total_emi[$y],
                        'emi_finance' => $finance_emi[$y],
                        'emi_downpayment' => $down_emi[$y],
                        'emi_amount' => $emi_amount[$y],
                        'finance_amount' => $finance_emi_amount[$y],
                        'cashback_amount' => $cashback[$y],
                        'cashback_type' => $cashback_type[$y],
                        'rate_of_interest' => $rate_interest[$y],
                        'interest_mop' => $interest_mop[$y],
                       
                    );
                    $this->Finance_model->save_finance_scheme_data($data);
                }
            }
            $this->session->set_flashdata('save_data', 'Finance Scheme Saved Successfully');
            redirect('Finance_scheme/finance_scheme');
        }else{
            //*********Finance Scheme Save ********
            $from = $this->input->post('from');
            $to = $this->input->post('to');
            $idbrand = $this->input->post('idbrand');
            $idvariant = $this->input->post('model');
            $idpaymentmode = $this->input->post('idpaymentmode');
            $finance_mop = $this->input->post('finance_mop');
            $total_emi = $this->input->post('total_emi');
            $finance_emi = $this->input->post('finance_emi');
            $down_emi = $this->input->post('down_emi');
            $scheme_code = $this->input->post('scheme_code');
            $emi_amount = $this->input->post('emi_amount');
            $finance_emi_amount = $this->input->post('finance_emi_amount');
            $down_emi_amount = $this->input->post('down_emi_amount');
            $cashback = $this->input->post('cashback');
            $scheme = $this->input->post('scheme_type');
            $finance_scheme_type = $this->input->post('finance_scheme_type');
            $finance_interest_rate = $this->input->post('finance_interest_rate');
            $processng_fee = $this->input->post('processng_fee');
            $final_down_emi_amount = $this->input->post('final_down_emi_amount');
            
            $count_arr = count($total_emi);

            $model_variant = $this->General_model->get_model_variant_data_byidvariant($idvariant);
            $idpcat = $model_variant->idproductcategory;
            $idcategory = $model_variant->idcategory;
            $idmodel = $model_variant->idmodel;
            if($idpcat == 1){
                if($idcategory == 2 || $idcategory == 28  || $idcategory == 31){
                    $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byidmodel($idmodel,$idcategory);
                }else{
                   $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byid($idvariant,$idcategory);
                }

                foreach ($allvariants as $idvv){
                    $idva[] = $idvv->id_variant;
                }

                for($x=0; $x < $count_arr; $x++){
                    for($i=0; $i<count($idva); $i++){
                        $data  = array(
                             'scheme' => $scheme,
                            'idbrand' => $idbrand,
                            'idvariant' => $idva[$i],
                            'scheme_type' => 0,
                            'idpayment_head' => 4,
                            'idpayment_mode' => $idpaymentmode,
                            'finance_mop' => $finance_mop,
                            'created_by' => $_SESSION['id_users'],
                            'from_date' => $from[$x],
                            'to_date' => $to[$x],
                            'entry_time' => $entry_time,
                            'finance_scheme_type' => $finance_scheme_type,
                            'total_emi' => $total_emi[$x],
                            'emi_finance' => $finance_emi[$x],
                            'emi_downpayment' => $down_emi[$x],
                            'scheme_code' => $scheme_code[$x],
                            'emi_amount' => $emi_amount[$x],
                            'finance_amount' => $finance_emi_amount[$x],
                            'downpayment_amount' => $down_emi_amount[$x],
                            'cashback_amount' => $cashback[$x],
                            'processing_fee' => $processng_fee[$x],
                            'final_downpayment_amount' => $final_down_emi_amount[$x],
                            'rate_of_interest' => $finance_interest_rate[$x],
                        );
                        $this->Finance_model->save_finance_scheme_data($data);
                    }
                }
            
            }else{
                for($x=0; $x < $count_arr; $x++){
                    $data  = array(
                        'scheme' => $scheme,
                        'idbrand' => $idbrand,
                        'idvariant' => $idvariant,
                        'scheme_type' => 0,
                        'idpayment_head' => 4,
                        'idpayment_mode' => $idpaymentmode,
                        'finance_mop' => $finance_mop,
                        'created_by' => $_SESSION['id_users'],
                        'from_date' => $from[$x],
                        'to_date' => $to[$x],
                        'entry_time' => $entry_time,
                        'finance_scheme_type' => $finance_scheme_type,
                        'total_emi' => $total_emi[$x],
                        'emi_finance' => $finance_emi[$x],
                        'emi_downpayment' => $down_emi[$x],
                        'scheme_code' => $scheme_code[$x],
                        'emi_amount' => $emi_amount[$x],
                        'finance_amount' => $finance_emi_amount[$x],
                        'downpayment_amount' => $down_emi_amount[$x],
                        'cashback_amount' => $cashback[$x],
                        'processing_fee' => $processng_fee[$x],
                        'final_downpayment_amount' => $final_down_emi_amount[$x],
                        'rate_of_interest' => $finance_interest_rate[$x],
                    );
                    $this->Finance_model->save_finance_scheme_data($data);
                }
            }
            $this->session->set_flashdata('save_data', 'Finance Scheme Saved Successfully');
            redirect('Finance_scheme/finance_scheme');
        }
    }
    public function delete_finance_scheme_data(){
        $idfinance = $this->input->post('idfinance');
        $entry_time = $this->input->post('entry_time');
        $all = $this->input->post('all');
//        die($all);
        $finance_data = $this->Finance_model->get_finance_data_byid($idfinance);
        $res = 0;
        if($all == 1){ // All Color Variants
            
            $model_variant = $this->General_model->get_model_variant_data_byidvariant($finance_data->idvariant);
            
            $idpcat = $model_variant->idproductcategory;
            $idcategory = $model_variant->idcategory;
            $idmodel = $model_variant->idmodel;
            $idvariant = $model_variant->id_variant;
            if($idpcat == 1){
                if($idcategory == 2 || $idcategory == 28  || $idcategory == 31){
                    $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byidmodel($idmodel,$idcategory);
                }else{
                   $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byid($idvariant,$idcategory);
                }
                
                foreach ($allvariants as $idvv){
                    $idva[] = $idvv->id_variant;
                }
              
                if( $this->Finance_model->delete_finance_scheme_byidvariant($idva, $entry_time) ){
                    $res = 1;
                }else{ 
                    $res = 0;
                }
            
            }else{
                if( $this->Finance_model->delete_finance_scheme_byid($idfinance, $entry_time) ){
                    $res = 1;
                }else{ 
                    $res = 0;
                }
            }
        }else{ //Single variant
            if( $this->Finance_model->delete_finance_scheme_byid($idfinance, $entry_time) ){
                $res = 1;
            }else{ 
                $res = 0;
            }
        }
        
        echo $res;
    }
   
    public function edit_finance_scheme($idfinance,$scheme) {
       $q['tab_active'] = '';                
       if($scheme == 0){
           $idhead = 4;
       }else{
           $idhead = 3;
       }
           
        $q['brand_data'] = $this->General_model->get_active_brand_data();       
        $q['bank_data'] = $this->General_model->get_active_bank();
        $q['finance_data'] = $this->Finance_model->get_finance_data_byid($idfinance);
        $q['payment_mode'] = $this->General_model->ajax_get_payment_mode_byhead($idhead);
        $q['scheme'] = $scheme;
//        die('<pre>'.print_r( $q['finance_data'],1).'</pre>');
        $this->load->view('finance_scheme/edit_finance_scheme', $q);
    }
    
    public function update_finance_scheme_data(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $entry_time = $this->input->post('entry_time');
        $entrytime = date('Y-m-d h:i:s');
        if($this->input->post('scheme_type') == 1){  //Swipe Scheme
            $from = $this->input->post('from');
            $to = $this->input->post('to');
            $idbrand = $this->input->post('idbrand');
            $idvariant = $this->input->post('idmodel');
            $idpaymentmode = $this->input->post('idpaymentmode');
            $finance_mop = $this->input->post('finance_mop');
            $swipe_scheme = $this->input->post('swipe_scheme');
            $type_of_card = $this->input->post('type_of_card');
            $bank = $this->input->post('bank');
            $total_emi = $this->input->post('total_emi');
            $finance_emi = $this->input->post('finance_emi');
            $down_emi = $this->input->post('down_emi');
            $rate_interest = $this->input->post('rate_interest');
            $mop_interest = $this->input->post('mop_interest');
            $emi_amount = $this->input->post('emi_amount');
            $finance_emi_amount = $this->input->post('finance_emi_amount');
            $cashback_type = $this->input->post('cashback_type');
            $cashback = $this->input->post('cashback');
            $scheme = $this->input->post('scheme_type');
            
            if($swipe_scheme == 1) { // brand
                $cashback_type = '';
                $cashback = '';
                $rate_interest = '';
                $mop_interest = '';
                
            }
            if($swipe_scheme == 2){ //Bank
                $cashback_type = '';
                $cashback = '';
            }
            if($swipe_scheme == 3){ // Cashback
                $total_emi = '';
                $finance_emi = '';
                $down_emi = '';
                $rate_interest = '';
                $mop_interest = '';
                $emi_amount = '' ;
                $finance_emi_amount = '';
            }
           
            $model_variant = $this->General_model->get_model_variant_data_byidvariant($idvariant);
            $idpcat = $model_variant->idproductcategory;
            $idcategory = $model_variant->idcategory;
            $idmodel = $model_variant->idmodel;
            
            if($idpcat == 1){
                if($idcategory == 2 || $idcategory == 28  || $idcategory == 31){
                    $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byidmodel($idmodel,$idcategory);
                }else{
                   $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byid($idvariant,$idcategory);
                }

                foreach ($allvariants as $idvv){
                    $idva[] = $idvv->id_variant;
                }
                $data = array(
                    'scheme_type' => $swipe_scheme,
                    'idpayment_mode' => $idpaymentmode,
                    'finance_mop' => $finance_mop,
                    'total_emi' => $total_emi,
                    'emi_finance' => $finance_emi,
                    'emi_downpayment' => $down_emi,
                    'emi_amount' => $emi_amount,
                    'finance_amount' => $finance_emi_amount,
                    'cashback_amount' => $cashback,
                    'cashback_type' => $cashback_type,
                    'rate_of_interest' => $rate_interest,
                    'interest_mop' => $mop_interest,
                    'type_of_card' => $type_of_card,
                    'idbank' => $bank,
                    'created_by' => $_SESSION['id_users'],
                    'from_date' => $from,
                    'to_date' => $to,
                    'entry_time' => $entrytime,
                );
                $this->Finance_model->update_finance_scheme_data($data, $idva, $entry_time);
            
            }else{
                $data = array(
                    'scheme_type' => $swipe_scheme,
                    'idpayment_mode' => $idpaymentmode,
                    'finance_mop' => $finance_mop,
                    'total_emi' => $total_emi,
                    'emi_finance' => $finance_emi,
                    'emi_downpayment' => $down_emi,
                    'emi_amount' => $emi_amount,
                    'finance_amount' => $finance_emi_amount,
                    'cashback_amount' => $cashback,
                    'cashback_type' => $cashback_type,
                    'rate_of_interest' => $rate_interest,
                    'interest_mop' => $mop_interest,
                    'type_of_card' => $type_of_card,
                    'idbank' => $bank,
                    'created_by' => $_SESSION['id_users'],
                    'from_date' => $from,
                    'to_date' => $to,
                    'entry_time' => $entrytime,
                );
                $this->Finance_model->update_finance_scheme_data($data, $idvariant, $entry_time);
            }
            $this->session->set_flashdata('save_data', 'Finance Scheme Updated Successfully');
            redirect('Finance_scheme/finance_scheme');
        }else{ 
            
            //Finance Scheme
            
            $from = $this->input->post('from');
            $to = $this->input->post('to');
            $idbrand = $this->input->post('idbrand');
            $idvariant = $this->input->post('model');
            $idpaymentmode = $this->input->post('idpaymentmode');
            $finance_mop = $this->input->post('finance_mop');
            $total_emi = $this->input->post('total_emi');
            $finance_emi = $this->input->post('finance_emi');
            $down_emi = $this->input->post('down_emi');
            $scheme_code = $this->input->post('scheme_code');
            $emi_amount = $this->input->post('emi_amount');
            $finance_emi_amount = $this->input->post('finance_emi_amount');
            $down_emi_amount = $this->input->post('down_emi_amount');
            $cashback = $this->input->post('cashback');
            $scheme = $this->input->post('scheme_type');
            $processing_fee = $this->input->post('processng_fee');
            $final_downpayment_amount = $this->input->post('final_down_emi_amount');
            $rate_of_interest = $this->input->post('finance_interest_rate');
            
            $model_variant = $this->General_model->get_model_variant_data_byidvariant($idvariant);
            $idpcat = $model_variant->idproductcategory;
            $idcategory = $model_variant->idcategory;
            $idmodel = $model_variant->idmodel;
            if($idpcat == 1){
                if($idcategory == 2 || $idcategory == 28  || $idcategory == 31){
                    $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byidmodel($idmodel,$idcategory);
                }else{
                   $allvariants = $this->Stock_model->ajax_get_all_modelvariants_byid($idvariant,$idcategory);
                }

                foreach ($allvariants as $idvv){
                    $idva[] = $idvv->id_variant;
                }

                $data  = array(
                    'idpayment_mode' => $idpaymentmode,
                    'total_emi' => $total_emi,
                    'emi_finance' => $finance_emi,
                    'emi_downpayment' => $down_emi,
                    'scheme_code' => $scheme_code,
                    'emi_amount' => $emi_amount,
                    'finance_amount' => $finance_emi_amount,
                    'downpayment_amount' => $down_emi_amount,
                    'cashback_amount' => $cashback,
                    'created_by' => $_SESSION['id_users'],
                    'from_date' => $from,
                    'to_date' => $to,
                    'entry_time' => $entrytime,
                    'processing_fee' => $processing_fee,
                    'final_downpayment_amount' => $final_downpayment_amount,
                    'rate_of_interest' => $rate_of_interest,
                );
                $this->Finance_model->update_finance_scheme_data($data, $idva, $entry_time);
            }else{
                $data  = array(
                    'idpayment_mode' => $idpaymentmode,
                    'total_emi' => $total_emi,
                    'emi_finance' => $finance_emi,
                    'emi_downpayment' => $down_emi,
                    'scheme_code' => $scheme_code,
                    'emi_amount' => $emi_amount,
                    'finance_amount' => $finance_emi_amount,
                    'downpayment_amount' => $down_emi_amount,
                    'cashback_amount' => $cashback,
                    'created_by' => $_SESSION['id_users'],
                    'from_date' => $from,
                    'to_date' => $to,
                    'entry_time' => $entrytime,
                    'processing_fee' => $processing_fee,
                    'final_downpayment_amount' => $final_downpayment_amount,
                    'rate_of_interest' => $rate_of_interest,
                );
                $this->Finance_model->update_finance_scheme_data($data, $idvariant, $entry_time);
            }
            $this->session->set_flashdata('save_data', 'Finance Scheme Updated Successfully');
            redirect('Finance_scheme/finance_scheme');
        }
    }
    public function finance_scheme_report(){
        $q['tab_active'] = '';      
        $idhead= array(3,4);
        $q['payment_mode'] = $this->General_model->ajax_get_payment_mode_byhead($idhead);
        $q['brand_data'] = $this->General_model->get_active_brand_data();    
         $this->load->view('finance_scheme/finance_scheme_report', $q);
    }
    public function ajax_get_finance_scheme_byfilter(){
//        die(print_r($_POST));
        $from = $this->input->post('from');
//        $to = $this->input->post('to');
        $type = $this->input->post('type');
        $idmode = $this->input->post('idmode');
        $idbrand = $this->input->post('brand');
        $idvariant = $this->input->post('idvariant');
        
        $finance_data = $this->Finance_model->ajax_get_finance_scheme_data_byfilter($from, $idbrand,$idvariant,$type, $idmode);
//        die('<pre>'.print_r($finance_data,1).'</pre>');
        if($finance_data){ ?>
            <table class="table table-bordered table-condensed" id="finance_scheme_data">
                <thead style="background-color: #99ccff" class="fixheader">
                    <th>Sr.</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Brand</th>
                    <th>Model</th>
                    <th>Scheme Type</th>
                    <th>Payment Mode</th>
                    <th>Finance Mop</th>
                    <th>Scheme Code</th>
                    <th>Downpayment EMI</th>
                    <th>Finance EMI</th>
                    <th>Downpayment Amount</th>
                    <th>EMI Amount(per month)</th>
                    <th>Total EMI Amount</th>
                     <th>Processing Fee</th>
                    <th>Rate Of Interest</th>
                    <th>Mop With Interest</th>
                    <th>Cashback Type</th>
                    <th>Cashback Amount</th>
                     <?php if($_SESSION['level'] == 1){?>
                        <th>Entry Time</th>
                        <th>Action</th>
                        <th>Edit</th>
                    <?php } ?>
                        
<!--                    <th>Scheme</th>
                    <th>Payment Head</th>
                    <th>Total EMI</th>
                    <th>Type Of Card</th>
                    <th>Bank</th>
                    <th>Finance Scheme</th>-->
                </thead>
                <tbody class="data_1">
                    <?php $sr=1; foreach($finance_data as $fdata){ ?>
                    <tr>
                        <td><?php echo $sr++; ?></td>
                        <td><?php echo $fdata->from_date ?></td>
                        <td><?php echo $fdata->to_date ?></td>
                        <td><?php echo $fdata->brand_name?></td>
                        <td><?php echo $fdata->full_name;?></td>
                         <td><div class="finance_data"><?php if($fdata->scheme_type == 1){ echo 'Brand Scheme'; }elseif ($fdata->scheme_type == 2){ echo 'Bank Scheme'; }elseif ($fdata->scheme_type == 3){ echo 'Cashback Scheme'; }else{ echo 'Finance Scheme'; }?></div>
                            <div class="finance_edit"></div>
                        </td>
                        <td><?php echo $fdata->payment_mode?></td>
                        <td><?php echo $fdata->finance_mop?></td>
                        <td><div class="finance_data"><?php echo $fdata->scheme_code?></div>
                            <div class="finance_edit"></div>
                        </td>
                        <td><div class="finance_data"><?php echo $fdata->emi_downpayment?></div>
                            <div class="finance_edit"></div>
                        </td>
                         <td><div class="finance_data"><?php echo $fdata->emi_finance?></div>
                            <div class="finance_edit"></div>
                        </td>
                         <td><div class="finance_data"><?php echo round($fdata->downpayment_amount,2) ?></div>
                            <div class="finance_edit"></div>
                        </td>
                         <td><div class="finance_data"><?php echo round($fdata->emi_amount,2)?></div>
                            <div class="finance_edit"></div>
                        </td>
                    
                        <td><div class="finance_data"><?php echo round($fdata->finance_amount,2)?></div>
                            <div class="finance_edit"></div>
                        </td>
                          <td><div class="finance_data"><?php echo $fdata->processing_fee?></div>
                            <div class="finance_edit"></div>
                        </td>
                         <td><div class="finance_data"><?php echo $fdata->rate_of_interest?></div>
                            <div class="finance_edit"></div>
                        </td>
                         <td><div class="finance_data"><?php echo $fdata->interest_mop?></div>
                            <div class="finance_edit"></div>
                        </td>
                         <td><div class="finance_data"><?php if($fdata->cashback_amount > 0){ if($fdata->cashback_type == 1){ echo '90 Days'; }else{ echo 'Instant';} } ?></div>
                            <div class="finance_edit"></div>
                        </td>
                        <td><div class="finance_data"><?php echo $fdata->cashback_amount?></div>
                            <div class="finance_edit"></div>
                        </td>
                       
                        <?php if($_SESSION['level'] == 1){?>
                        <td><?php echo $fdata->entry_time ?></td>
                        <td>
                            <input type="hidden" id="ídfinance" value="<?php echo $fdata->id_finance_scheme?>">
                            <input type="hidden" id="entry_time" value="<?php echo $fdata->entry_time?>">
                            <a class="btn btn-floating btn-warning btndelete" id="btndelete"><span class="fa fa-trash"></span></a>
                        </td>
                        <td>
                            <a class="btn btn-floating btn-primary btnedit" href="<?php echo base_url()?>Finance_scheme/edit_finance_scheme/<?php echo $fdata->id_finance_scheme; ?>/<?php echo $fdata->scheme?>" ><span class="fa fa-pencil"></span></a>
                        </td>
                        <?php } ?>
                                                
<!--     <td><?php if($fdata->scheme == 1){ echo 'Swipe'; }else{ echo 'Finance';} ?></td>
                        <td><?php echo $fdata->payment_head?></td>
                              
 <td><div class="finance_data"><?php echo $fdata->total_emi?></div>
                            <div class="finance_edit"></div>
                        </td>
<td><div class="finance_data"><?php if($fdata->scheme == 1){ if($fdata->type_of_card == 1){ echo 'Credit Card'; } else { echo 'Debit Card'; } }?></div>
                            <div class="finance_edit"></div>
                        </td>
                        <td><div class="finance_data"><?php echo $fdata->bank_name?></div>
                            <div class="finance_edit"></div>
                        </td>
                        <td><div class="finance_data"><?php if($fdata->finance_scheme_type){ if($fdata->finance_scheme_type == 1){ echo 'vanilla';}else{ echo ' Non Vanilla'; } }?></div>
                            <div class="finance_edit"></div>
                        </td>-->
                      
                    
                      
                       
                        
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
            <script>
                $('.btndelete').click(function (){
                    var idfinance = $(this).closest('td').find('#ídfinance').val();
                    var entry_time = $(this).closest('td').find('#entry_time').val();
                    var all = 0;
                     if(!confirm("All Color Variants of This Model will be deleted. Do You Want To Delete ? ")){
//                         all = 0;
//                         $.ajax({
//                             url:"<?php echo base_url() ?>Finance_scheme/delete_finance_scheme_data",
//                             method:"POST",
//                             data:{idfinance : idfinance,all: all, entry_time: entry_time},
//                             success:function(data)
//                             {
//                                 if(data == '1' || data == 1 ){
//                                     alert("Finance Scheme Deleted successfully");
//                                     window.location.reload();
//                                 }else{
//                                     alert("Failed To delete");
//                                     return false;
//                                 }
//
//                             }
//                         });
                            return false;
                     }else{
                         all = 1;
                          $.ajax({
                             url:"<?php echo base_url() ?>Finance_scheme/delete_finance_scheme_data",
                             method:"POST",
                             data:{idfinance : idfinance,all: all, entry_time: entry_time},
                             success:function(data)
                             {
                                  if(data == '1' || data == 1 ){
                                     alert("Finance Scheme Deleted successfully");
                                     window.location.reload();
                                 }else{
                                     alert("Failed To delete");
                                     return false;
                                 }
                             }
                         });
                     }

                 });
            </script>
        <?php } else{
            echo 'Data Not Found';
        }
        
    }
}