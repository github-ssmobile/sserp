<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Master extends CI_Controller {
    public function __construct() {
        parent::__construct();
        if(!$this->session->userdata('userid')){ return redirect(base_url()); }
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Expense_wallet_model');
        $this->load->model('Costing_model');
        $this->load->model('Report_model');
        $this->load->model('common_model');
    }

    public function send_mail() {
        sendEmail('vg.gonjari@gmail.com', 'test', 'msg');
    }

    public function config_dashboard() {
        $q['tab_active'] = '';
        $q['type_data'] = $this->General_model->get_product_category_data();
        $q['category_data'] = $this->General_model->get_category_data();
        $q['brand_data'] = $this->General_model->get_brand_data();
        $q['model_data'] = $this->General_model->get_model_data();
        $this->load->view('dashboard_config', $q);
    }

    public function user_dashboard() {
        // Admin
        $q['tab_active'] = '';
        $q['menu_data'] = $this->General_model->get_menu_data();
        $q['branch_data'] = $this->General_model->get_branch_data();
        $q['model_data'] = $this->General_model->get_model_data();
        $q['vendor_data'] = $this->General_model->get_vendor_data();
        $q['user_data'] = $this->General_model->get_user_data();
        $q['type_data'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_brand_data();
        $q['godown_data'] = $this->General_model->get_godown_data();
        $this->load->view('dashboard_admin', $q);
    }

    public function menu_details() {
        $q['tab_active'] = 'Menus';
        $q['menu_data'] = $this->General_model->get_menu_data();
        $this->load->view('master/menu_details', $q);
    }

    public function ajax_get_address_bypincode($pincode) {
        $q['address_data'] = $this->General_model->ajax_get_address_bypincode($pincode);
        if(count($q['address_data'])){
            $q['result'] = 'Success';
        }else{
            $q['result'] = 'Failed';
        }
        echo json_encode($q);
    }
    
    public function save_menu() {
        $data = array(
            'menu' => $this->input->post('menu'),
            'url' => $this->input->post('url'),
            'font' => $this->input->post('font'),
        );
        $this->General_model->save_menu($data);
        $this->session->set_flashdata('save_data', 'Menu Created');
        return redirect('Master/menu_details');
    }

    public function edit_menu() {
        $id = $this->input->post('id');
        $data = array(
            'menu' => $this->input->post('menu'),
            'url' => $this->input->post('url'),
            'font' => $this->input->post('font'),
            'active' => $this->input->post('status'),
        );
        $this->General_model->edit_menu($id, $data);
        $this->session->set_flashdata('save_data', 'Menu Updated');
        return redirect('Master/menu_details');
    }

    public function submenu_details() {
        $q['tab_active'] = '';
        $q['menu_data'] = $this->General_model->get_menu_data();
        $q['submenu_data'] = $this->General_model->get_menu_submenu_data();
        $this->load->view('master/submenu_details', $q);
    }

    public function save_submenu() {
        $data = array(
            'idmenu' => $this->input->post('menu'),
            'submenu' => $this->input->post('submenu'),
            'url' => $this->input->post('url'),
            'font' => $this->input->post('font'),
        );
        $this->General_model->save_submenu($data);
        $this->session->set_flashdata('save_data', 'SubMenu Created');
        return redirect('Master/submenu_details');
    }

    public function edit_submenu() {
        $id = $this->input->post('id');
        $data = array(
            'idmenu' => $this->input->post('menu'),
            'submenu' => $this->input->post('submenu'),
            'url' => $this->input->post('url'),
            'font' => $this->input->post('font'),
            'active' => $this->input->post('status'),
        );
        $this->General_model->edit_submenu($id, $data);
        $this->session->set_flashdata('save_data', 'SubMenu Updated');
        return redirect('Master/submenu_details');
    }

    public function payment_head_details() {
        $q['tab_active'] = '';
        $q['payment_head'] = $this->General_model->get_payment_head_data();
        $q['payment_head_has_attributes'] = $this->General_model->get_payment_head_has_attributes();
        $this->load->view('master/payment_head_details', $q);
    }
    public function save_payment_head() {
        $data = array(
            'payment_head' => $this->input->post('payment_head'),
            'credit_type' => $this->input->post('credit_type'),
            'has_attribute' => isset($_POST['sel_attribute'])?1:0,
            'credit_receive_type' => isset($_POST['isrecon'])?1:0,
            'multiple_rows' => isset($_POST['multiple_rows'])?1:0,
            'payment_reconciliation' => isset($_POST['pay_rec'])?1:0,
            'bank_reconciliation' => isset($_POST['bank_rec'])?1:0,
            'valid_for_creadit_receive' => isset($_POST['receive_payment_mode'])?1:0,
            'amount_name' => $this->input->post('amount_name'),
            'corporate_sale' => $this->input->post('corporate_sale'),
        );
        $idhead = $this->General_model->save_payment_head($data);
        if($this->input->post('sel_attribute')){
            $attribute = explode(',', $this->input->post('sel_attribute'));
            $attribute_array[] = array('nest'=>array());
            foreach ($attribute as $attr){
                $attribute_array['nest'][] = array(
                    'idpayment_head' => $idhead,
                    'idpayment_attribute' => $attr[0],
                );
            }
//            die(print_r($attribute_array));
            $this->General_model->save_payment_head_has_attributes($attribute_array['nest']);
        }
        $this->session->set_flashdata('save_data', 'Payment Mode Created');
        return redirect('Master/payment_head_details');
    }
    public function payment_mode_details() {
        $q['tab_active'] = '';
        $q['payment_head_data'] = $this->General_model->get_payment_head_data();
        $q['payment_mode_data'] = $this->General_model->get_payment_mode_data();
        $q['payment_head_has_attributes'] = $this->General_model->get_payment_head_has_attributes();
        $this->load->view('master/payment_mode_details',$q);
    }
    public function save_payment_mode() {
        $data = array(
            'payment_mode' => $this->input->post('payment_mode'),
            'idpaymenthead' => $this->input->post('idpaymenthead'),
            'has_devices' => isset($_POST['hasdevices'])?1:0,
            'active' => $this->input->post('status'),
        );
        $this->General_model->save_payment_mode($data);
        $this->session->set_flashdata('save_data', 'Payment Mode Created');
        return redirect('Master/payment_mode_details');
    }
    public function edit_payment_mode() {
        $id = $this->input->post('id');
        $data = array(
            'payment_mode' => $this->input->post('payment_mode'),
            'idpaymenthead' => $this->input->post('idpaymenthead'),
            'tranxid_type' => $this->input->post('tranxid_type'),
            'has_devices' => isset($_POST['hasdevices'])?1:0,
            'active' => $this->input->post('status'),
        );
        $this->General_model->edit_payment_mode($id, $data);
        $this->session->set_flashdata('save_data', 'Payment Mode Updated');
        return redirect('Master/payment_mode_details');
    }
    public function role_details() {
        $q['tab_active'] = '';
        $q['user_role'] = $this->General_model->get_user_role();
        $this->load->view('master/role_details', $q);
    }

    public function role_has_menu($idrole) {
        $q['idrole'] = $idrole;
        $q['tab_active'] = '';
        $q['user_role'] = $this->General_model->get_user_role_byid($idrole);
        $q['menu_data'] = $this->General_model->get_all_menu_submenu();
        $q['userrole_has_menu'] = $this->General_model->get_userrole_has_menu_byid($idrole);
        $this->load->view('master/role_has_menu', $q);
    }

    public function user_details() {
        $q['tab_active'] = '';
        $q['user_role'] = $this->General_model->get_user_role();
        $q['user_data'] = $this->General_model->get_user_all_data();
        $this->load->view('master/user_details', $q);
    }
    public function edit_user_details($iduser) {
        $q['tab_active'] = '';
        $q['user_data'] = $this->General_model->get_user_all_data_byid($iduser);
//        die('<pre>'.print_r($q['user_data'],1).'</pre>');
        if($q['user_data'][0]->has_warehouse){
            $q['warehouse_data'] = $this->General_model->get_active_warehouse_data();
            $q['user_warehouse'] = $this->General_model->get_warehouse_by_user($iduser);
        }
        if($q['user_data'][0]->has_branch){
            $q['branch_data'] = $this->General_model->get_active_branch_data();
            $q['user_branch'] = $this->General_model->get_branches_by_user($iduser);
        }
        if($q['user_data'][0]->has_product_category){
            $q['product_category'] = $this->General_model->get_product_category_data(); 
            $q['user_product_cat'] = $this->General_model->get_product_category_by_user($iduser);
//            die('<pre>'.print_r($q['user_product_cat'],1).'</pre>');
        }
        if($q['user_data'][0]->has_brand){
            $q['brand_data'] = $this->General_model->get_active_brand_data(); 
            $q['user_brand'] = $this->General_model->get_brands_by_user($iduser);
//                        die('<pre>'.print_r($q['user_brand'],1).'</pre>');
        }
        if($q['user_data'][0]->has_paymentmode){
            $q['payment_mode'] = $this->General_model->get_active_payment_mode_head();
            $q['user_payment_mode'] = $this->General_model->get_payment_modes_by_user($iduser); 
//            die('<pre>'.print_r($q['user_payment_mode'],1).'</pre>');
        }
        if($q['user_data'][0]->has_expense_wallet){
            $q['wallet_data'] = $this->Expense_wallet_model->get_wallet_type_data();
            $q['user_wallet_type'] = $this->General_model->get_wallet_type_by_user($iduser); 
//            die('<pre>'.print_r($q['user_wallet_type'],1).'</pre>');
        }
        if($q['user_data'][0]->has_costing_header){
            $q['costing_data'] = $this->Costing_model->get_active_branch_costing_headers();
            $q['user_has_costing_header'] = $this->Costing_model->get_user_has_costing_header_by_user($iduser); 
//            die('<pre>'.print_r($q['user_has_costing_header'],1).'</pre>');
        }
        $this->load->view('master/edit_user_details', $q);
    }
    public function delete_user_has_warehouse($iduser, $id){
        $this->General_model->delete_user_has_warehouse($id);
        return redirect('master/edit_user_details/'.$iduser);
    }
    public function delete_user_has_product_category($iduser, $id){
        $this->General_model->delete_user_has_product_category($id);
        return redirect('master/edit_user_details/'.$iduser);
    }
    public function delete_user_has_brand($iduser, $id){
        $this->General_model->delete_user_has_brand($id);
        return redirect('master/edit_user_details/'.$iduser);
    }
    public function delete_user_has_payment_mode($iduser, $id){
        $this->General_model->delete_user_has_payment_mode($id);
        return redirect('master/edit_user_details/'.$iduser);
    }
    public function delete_user_has_wallet_type($iduser, $id){
        $this->General_model->delete_user_has_wallet_type($id);
        return redirect('master/edit_user_details/'.$iduser);
    }
     public function delete_user_has_costing_header($iduser, $id){
        $this->Costing_model->delete_user_has_costing_header($id);
        return redirect('master/edit_user_details/'.$iduser);
    }
    
     public function save_user() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        
        $datetime = date('Y-m-d H:i:s');
        $id_users = $this->input->post('id_users');
        $level = $this->input->post('level');
        if($level != 2){
            $idb = '';
        }else{
            $idb = $this->input->post('branches');
        }
        $data = array(
            'user_name' => $this->input->post('full_name'),
            'userid' => $this->input->post('name'),
            'user_contact' => $this->input->post('contact'),
            'user_password' => $this->input->post('password'),
            'iduserrole' => $this->input->post('role'),
            'idbranch' => $idb,
            'level' => $level,
            'active' => $this->input->post('status'),
        );
//        die('<pre>'.print_r($data,1).'</pre>');
        $iduser = $this->General_model->save_user($data);
        if($this->input->post('warehouses')){
            if($this->input->post('warehouses') == 'all'){
                $warehouse_data = $this->General_model->get_active_warehouse_data();
                 
                for($i=0; $i<count($warehouse_data); $i++){
                    $warehouse_d[] = array(
                        'iduser' => $iduser,
                        'idbranch' => $warehouse_data[$i]->id_branch,
                        'created_by' => $id_users,
                        'entry_time' => $datetime,
                    ); 
                }
                $this->General_model->save_user_has_branch($warehouse_d);
            }else{
                $warehouse = explode(',', $this->input->post('warehouses'));
                for($i=0; $i<count($warehouse);$i++){
                    $warehouse_array[] = array(
                        'iduser' => $iduser,
                        'idbranch' => $warehouse[$i],
                        'created_by' => $id_users,
                        'entry_time' => $datetime,
                    );
                }
                $this->General_model->save_user_has_branch($warehouse_array);
            }
        }
        if($this->input->post('branches')){
            if($level != 2){
                if($this->input->post('branches') == 'all'){
                   $branches_data = $this->General_model->get_active_branch_data();

                   for($i=0; $i<count($branches_data); $i++){
                       $branch_d[] = array(
                          'iduser' => $iduser,
                           'idbranch' => $branches_data[$i]->id_branch,
                           'created_by' => $id_users,
                           'entry_time' => $datetime,
                       ); 
                   }
                   $this->General_model->save_user_has_branch($branch_d);
               }else{
                   $branches = explode(',', $this->input->post('branches'));
                   for($i=0; $i<count($branches);$i++){
                       $branches_array[] = array(
                           'iduser' => $iduser,
                           'idbranch' => $branches[$i],
                           'created_by' => $id_users,
                           'entry_time' => $datetime,
                       );
                   }
                   $this->General_model->save_user_has_branch($branches_array);
               }
            }
        }
        if($this->input->post('brands')){
            if($this->input->post('brands') == 'all'){
                $brand_data = $this->General_model->get_active_brand_data();
                for($i=0; $i<count($brand_data); $i++){
                    $brand_d[] = array(
                       'iduser' => $iduser,
                        'idbrand' => $brand_data[$i]->id_brand,
                        'created_by' => $id_users,
                        'entry_time' => $datetime,
                    ); 
                }
                $this->General_model->save_user_has_brand($brand_d);
            }else{
            
                $brands = explode(',', $this->input->post('brands'));
                for($i=0; $i<count($brands);$i++){
                    $brands_array[] = array(
                        'iduser' => $iduser,
                        'idbrand' => $brands[$i],
                        'created_by' => $id_users,
                        'entry_time' => $datetime,
                    );
                }
                $this->General_model->save_user_has_brand($brands_array);
            }
        }
        if($this->input->post('product_cats')){
             if($this->input->post('product_cats') == 'all'){
                $product_category_data = $this->General_model->get_product_category_data();
                for($i=0; $i<count($product_category_data); $i++){
                    $category_d[] = array(
                        'iduser' => $iduser,
                        'idproductcategory' => $product_category_data[$i]->id_product_category,
                        'created_by' => $id_users,
                        'entry_time' => $datetime,
                    ); 
                }
                $this->General_model->save_user_has_product_category($category_d);
            }else{
            
                $product_cats = explode(',', $this->input->post('product_cats'));
                for($i=0; $i<count($product_cats);$i++){
                    $product_cats_array[] = array(
                        'iduser' => $iduser,
                        'idproductcategory' => $product_cats[$i],
                        'created_by' => $id_users,
                        'entry_time' => $datetime,
                    );
                }
                $this->General_model->save_user_has_product_category($product_cats_array);
            }
        }
        if($this->input->post('payment_modes')){
             if($this->input->post('payment_modes') == 'all'){
                $payment_mode_data = $this->General_model->get_active_payment_mode();
                for($i=0; $i<count($payment_mode_data); $i++){
                    $payment_d[] = array(
                        'iduser' => $iduser,
                        'idpaymentmode' => $payment_mode_data[$i]->id_paymentmode,
                        'created_by' => $id_users,
                        'entry_time' => $datetime,
                    ); 
                }
                $this->General_model->save_user_has_payment_mode($payment_d);
            }else{
            
                $payment_modes = explode(',', $this->input->post('payment_modes'));
                for($i=0; $i<count($payment_modes);$i++){
                    $payments_array[] = array(
                        'iduser' => $iduser,
                        'idpaymentmode' => $payment_modes[$i],
                        'created_by' => $id_users,
                        'entry_time' => $datetime,
                    );
                }
                $this->General_model->save_user_has_payment_mode($payments_array);
            }
        }
         if($this->input->post('wallet_types')){
             if($this->input->post('wallet_types') == 'all'){
                $wallet_data = $this->Expense_wallet_model->get_wallet_type_data();
                for($i=0; $i<count($wallet_data); $i++){
                    $wallet_d[] = array(
                        'idusers ' => $iduser,
                        'idwallet' => $wallet_data[$i]->id_wallet_type,
                        'user_created_by' => $id_users,
                    ); 
                }
                $this->General_model->save_user_has_wallet_type($wallet_d);
            }else{
            
                $wallet_data = explode(',', $this->input->post('wallet_types'));
                for($i=0; $i<count($wallet_data);$i++){
                    $wallet_array[] = array(
                        'idusers' => $iduser,
                        'idwallet' => $wallet_data[$i],
                        'user_created_by' => $id_users,
                    );
                }
                $this->General_model->save_user_has_wallet_type($wallet_array);
            }
        }
        if($this->input->post('costing_headers')){
             if($this->input->post('costing_headers') == 'all'){
                $costing_data = $this->Costing_model->get_active_branch_costing_headers();
                for($i=0; $i<count($costing_data); $i++){
                    $costing_d[] = array(
                        'iduser ' => $iduser,
                        'idcosting_header' => $costing_data[$i]->id_cost_header,
                        'created_by' => $id_users,
                    ); 
                }
                $this->General_model->save_user_has_costing_headers($costing_d);
            }else{
            
                $costing_data = explode(',', $this->input->post('costing_headers'));
                for($i=0; $i<count($costing_data);$i++){
                    $costing_array[] = array(
                        'iduser' => $iduser,
                        'idcosting_header' => $costing_data[$i],
                        'created_by' => $id_users,
                    );
                }
                $this->General_model->save_user_has_costing_headers($costing_array);
            }
        }
        $this->session->set_flashdata('save_data', 'User Created');
        return redirect('Master/user_details');
    }
    
    public function save_edit_user() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $iduser = $this->input->post('iduser');
         $datetime = date('Y-m-d H:i:s');
        
        $data = array(
           'user_name' => $this->input->post('full_name'), 
           'userid' => $this->input->post('name'), 
           'user_contact' => $this->input->post('contact'), 
           'user_password' => $this->input->post('password'), 
           'user_email' => $this->input->post('email'), 
           'active' => $this->input->post('status'), 
           'idbranch' => $this->input->post('idbranch'), 
        );
        $this->General_model->edit_user($iduser, $data);
        
         if($this->input->post('warehouses')){            
             $this->General_model->delete_user_has_warehouse_byiduser($iduser);
            if($this->input->post('warehouses') == 'all'){  
                    $warehouse_data = $this->General_model->get_active_warehouse_data();
                    for($i=0; $i<count($warehouse_data);$i++){
                        $warehouse_a[] = array(
                            'iduser' => $iduser,
                            'idbranch' => $warehouse_data[$i]->id_branch,
                            'created_by' => $iduser,
                            'entry_time' => $datetime,
                        );
                    }
                    $this->General_model->save_user_has_branch($warehouse_a);
            }else {
                $warehouse = explode(',', $this->input->post('warehouses'));                
                for($i=0; $i<count($warehouse);$i++){
                    $warehouse_array[] = array(
                        'iduser' => $iduser,
                        'idbranch' => $warehouse[$i],
                        'created_by' => $iduser,
                        'entry_time' => $datetime,
                    );
                }                
                $this->General_model->save_user_has_branch($warehouse_array);
            }
        }
        if($this->input->post('branches')){
            $this->General_model->delete_user_has_branch_byiduser($iduser);
            if($this->input->post('branches') == 'all'){     
                    $branches_data = $this->General_model->get_active_branch_data();
                    for($i=0; $i<count($branches_data); $i++){
                        $branch_a[] = array(
                            'iduser' => $iduser,
                            'idbranch' => $branches_data[$i]->id_branch,
                            'created_by' => $iduser,
                            'entry_time' => $datetime,
                        ); 
                    }
                    $this->General_model->save_user_has_branch($branch_a);               
            }else {
                $branches = explode(',', $this->input->post('branches'));
                for($i=0; $i<count($branches);$i++){
                    $branches_array[] = array(
                        'iduser' => $iduser,
                        'idbranch' => $branches[$i],
                        'created_by' => $iduser,
                        'entry_time' => $datetime,
                    );
                }
                $this->General_model->save_user_has_branch($branches_array);
            }
        }
        if($this->input->post('brands')){
            if($this->input->post('brands') == 'all'){
                if($this->General_model->delete_user_has_brand_byiduser($iduser)){
                    $brand_data = $this->General_model->get_active_brand_data();
                    for($i=0; $i<count($brand_data); $i++){
                        $brand_a[] = array(
                           'iduser' => $iduser,
                            'idbrand' => $brand_data[$i]->id_brand,
                            'created_by' => $iduser,
                            'entry_time' => $datetime,
                        ); 
                    }
                    $this->General_model->save_user_has_brand($brand_a);
                }
            } else {
                $brands = explode(',', $this->input->post('brands'));
                for($i=0; $i<count($brands);$i++){
                    $brands_array[] = array(
                        'iduser' => $iduser,
                        'idbrand' => $brands[$i],
                        'created_by' => $iduser,
                        'entry_time' => $datetime,
                    );
                }
                $this->General_model->save_user_has_brand($brands_array);
            }
        }
        if($this->input->post('product_cats')){
            if($this->input->post('product_cats') == 'all'){
                if($this->General_model->delete_user_has_product_category_byiduser($iduser)){
                    $product_category_data = $this->General_model->get_product_category_data();
                    for($i=0; $i<count($product_category_data); $i++){
                        $category_a[] = array(
                            'iduser' => $iduser,
                            'idproductcategory' => $product_category_data[$i]->id_product_category,
                            'created_by' => $iduser,
                            'entry_time' => $datetime,
                        ); 
                    }
                    $this->General_model->save_user_has_product_category($category_a);
                }
            }else{
                $product_cats = explode(',', $this->input->post('product_cats'));
                for($i=0; $i<count($product_cats);$i++){
                    $product_cats_array[] = array(
                        'iduser' => $iduser,
                        'idproductcategory' => $product_cats[$i],
                        'created_by' => $iduser,
                        'entry_time' => $datetime,
                    );
                }
                $this->General_model->save_user_has_product_category($product_cats_array);
            }
        }
        if($this->input->post('payment_modes')){
            if($this->input->post('payment_modes') == 'all'){
                if($this->General_model->delete_user_has_payment_mode_byiduser($iduser)){
                    $payment_mode_data = $this->General_model->get_active_payment_mode();
                    for($i=0; $i<count($payment_mode_data); $i++){
                        $payment_d[] = array(
                            'iduser' => $iduser,
                            'idpaymentmode' => $payment_mode_data[$i]->id_paymentmode,
                            'created_by' => $iduser,
                            'entry_time' => $datetime,
                        ); 
                    }
                    $this->General_model->save_user_has_payment_mode($payment_d);
                }
                
            }else{
                $payment_modes = explode(',', $this->input->post('payment_modes'));
                for($i=0; $i<count($payment_modes);$i++){
                    $payments_array[] = array(
                        'iduser' => $iduser,
                        'idpaymentmode' => $payment_modes[$i],
                        'created_by' => $iduser,
                        'entry_time' => $datetime,
                    );
                }
                $this->General_model->save_user_has_payment_mode($payments_array);
            }
        }
        if($this->input->post('wallet_types')){
             if($this->input->post('wallet_types') == 'all'){
                $wallet_data = $this->Expense_wallet_model->get_wallet_type_data();
                for($i=0; $i<count($wallet_data); $i++){
                    $wallet_d[] = array(
                        'idusers ' => $iduser,
                        'idwallet' => $wallet_data[$i]->id_wallet_type,
                        'user_created_by' => $id_users,
                    ); 
                }
                $this->General_model->save_user_has_wallet_type($wallet_d);
            }else{
            
                $wallet_data = explode(',', $this->input->post('wallet_types'));
                for($i=0; $i<count($wallet_data);$i++){
                    $wallet_array[] = array(
                        'idusers' => $iduser,
                        'idwallet' => $wallet_data[$i],
                        'user_created_by' => $id_users,
                    );
                }
                $this->General_model->save_user_has_wallet_type($wallet_array);
            }
        }
        if($this->input->post('costing_headers')){
            if($this->input->post('costing_headers') == 'all'){
                if($this->Costing_model->delete_user_has_costing_header_byiduser($iduser)){
                    $costing_data = $this->Costing_model->get_active_branch_costing_headers();
                    for($i=0; $i<count($costing_data); $i++){
                        $cost_a[] = array(
                            'iduser' => $iduser,
                            'idcosting_header' => $costing_data[$i]->id_cost_header,
                            'created_by' => $iduser,
                        ); 
                    }
                    $this->General_model->save_user_has_costing_headers($cost_a);
                }
            }else{
                $cost_data = explode(',', $this->input->post('costing_headers'));
                for($i=0; $i<count($cost_data);$i++){
                    $cost_array[] = array(
                        'iduser' => $iduser,
                        'idcosting_header' => $cost_data[$i],
                        'created_by' => $iduser,
                    );
                }
                $this->General_model->save_user_has_costing_headers($cost_array);
            }
        }
        $this->session->set_flashdata('save_data', 'User Data Updated');
        return redirect('Master/edit_user_details/'.$iduser);
      
    }

    public function vendor_has_branch() {
        $q['tab_active'] = '';
        $q['vendor_data'] = $this->General_model->get_vendor_data();
        $this->load->view('master/vendor_has_branch', $q);
    }
    
    public function save_role() {        
        $data = array(
            'role' => $this->input->post('role'),
            'level' => $this->input->post('level'),
            'home' => $this->input->post('home'),
            'role_type' => $this->input->post('role_type'),
            'has_warehouse' => isset($_POST['haswarehouse'])?1:0,
            'has_branch' => isset($_POST['hasbranch'])?1:0,
            'has_product_category' => isset($_POST['haspcategory'])?1:0,
            'has_brand' => isset($_POST['hasbrand'])?1:0,
            'has_wallet' => isset($_POST['haswallet'])?1:0,
            'has_paymentmode' => isset($_POST['has_paymentmode'])?1:0,
            'has_expense_wallet' => isset($_POST['has_expense_wallet'])?1:0,
            'has_costing_header' => isset($_POST['has_costing_header'])?1:0,
        );
        $this->General_model->save_role($data);
        $log = new Logging();
        $log->lfile('catlogs/User_Role.txt');
        $log->lwrite('Role: ' . $this->input->post('role') . '. Created by: ' . $this->session->userdata('id_users'));
        $log->lclose();
        $this->session->set_flashdata('save_data', 'User Role Created');
        return redirect('Master/role_details');
    }

    public function edit_role() {
        
        $id = $this->input->post('id');
        $data = array(
            'role' => $this->input->post('role'),
            'level' => $this->input->post('level'),
            'home' => $this->input->post('homeurl'),
            'role_type' => $this->input->post('role_type'),
            'has_warehouse' => isset($_POST['haswarehouse'])?1:0,
            'has_branch' => isset($_POST['hasbranch'])?1:0,
            'has_product_category' => isset($_POST['haspcategory'])?1:0,
            'has_brand' => isset($_POST['hasbrand'])?1:0,
            'has_wallet' => isset($_POST['haswallet'])?1:0,
            'has_paymentmode' => isset($_POST['has_paymentmode'])?1:0,
            'has_expense_wallet' => isset($_POST['has_expense_wallet'])?1:0,
            'has_costing_header' => isset($_POST['has_costing_header'])?1:0,
        );        
        $this->General_model->edit_role($id, $data);
        $this->session->set_flashdata('save_data', 'User Role Updated');
        return redirect('Master/role_details');
    }

    public function company_details() {
        $q['tab_active'] = '';
        $q['company_data'] = $this->General_model->get_company_data();
        $this->load->view('master/company_details', $q);
    }

    public function save_company_details() {
        $brand = $this->input->post('brand');
        $data = array(
            'company_name' => $this->input->post('company'),
            'company_address' => $this->input->post('address'),
            'company_pincode' => $this->input->post('pincode'),
            'company_city' => $this->input->post('city'),
            'company_state_name' => $this->input->post('state'),
            'company_gstin' => $this->input->post('gst'),
            'company_district' => $this->input->post('district'),
            'active' => $this->input->post('status'),
        );
        $this->General_model->save_db_company($data);
        $this->session->set_flashdata('save_data', 'Company Created');
        return redirect('Master/company_details');
    }

    public function edit_comapany() {
        $id = $this->input->post('id');
        $data = array(
            'company_name' => $this->input->post('company'),
            'company_address' => $this->input->post('address'),
            'company_pincode' => $this->input->post('pincode'),
            'company_city' => $this->input->post('city'),
            'company_state_name' => $this->input->post('state'),
            'company_gstin' => $this->input->post('gst'),
            'company_district' => $this->input->post('district'),
            'active' => $this->input->post('status'),
        );
        $this->General_model->edit_db_comapny($id, $data);
        $this->session->set_flashdata('save_data', 'Comapany Details Updated');
        return redirect('Master/company_details');
    }

    public function warehouse_details() {
        $q['tab_active'] = '';
        $q['branch_data'] = $this->General_model->get_warehouse_data();
        $this->load->view('master/warehouse_details', $q);
    }

    public function save_warehouse_details() {
        $data = array(
            'is_warehouse' => 1,
            'branch_name' => $this->input->post('branch'),
            'branch_code' => $this->input->post('branch_code'),
            'branch_address' => $this->input->post('address'),
            'branch_pincode' => $this->input->post('pincode'),
            'branch_city' => $this->input->post('city'),
            'branch_state_name' => $this->input->post('state'),
            'branch_district' => $this->input->post('district'),
            'branch_email' => $this->input->post('email'),
            'branch_contact' => $this->input->post('contact'),
            'branch_contact_person' => $this->input->post('contact_person'),
            'latitude' => $this->input->post('latitude'),
            'longitude' => $this->input->post('longitude'),
            'active' => $this->input->post('status'),
            'created_by' => $this->session->userdata('id_users'),
            'branch_lmb' => $this->session->userdata('id_users'),
            'po_approval' => $this->input->post('po_approval'),
        );
        $this->General_model->save_db_warehouse($data);
        $this->session->set_flashdata('save_data', 'Warehouse Created');
        return redirect('Master/warehouse_details');
    }

    public function edit_warehouse() {
        $id = $this->input->post('id');
        $data = array(
            'is_warehouse' => 1,
            'branch_name' => $this->input->post('branch'),
            'branch_code' => $this->input->post('branch_code'),
            'branch_address' => $this->input->post('address'),
            'branch_pincode' => $this->input->post('pincode'),
            'branch_city' => $this->input->post('city'),
            'branch_state_name' => $this->input->post('state'),
            'branch_district' => $this->input->post('district'),
            'branch_email' => $this->input->post('email'),
            'branch_contact' => $this->input->post('contact'),
            'branch_contact_person' => $this->input->post('contact_person'),
            'latitude' => $this->input->post('latitude'),
            'longitude' => $this->input->post('longitude'),
            'active' => $this->input->post('status'),
            'branch_lmt' => date('Y-m-d H:i:s'),
            'branch_lmb' => $this->session->userdata('id_users'),
            'po_approval' => $this->input->post('po_approval'),
        );
        $this->General_model->edit_db_warehouse($id, $data);
        $this->session->set_flashdata('save_data', 'Warehouse Details Updated');
        return redirect('Master/warehouse_details');
    }

    public function zone_details() {
        $q['tab_active'] = '';
        $q['zone_data'] = $this->General_model->get_zone_data();
        $q['warehouse_data'] = $this->General_model->get_active_warehouse_data();
        $this->load->view('master/zone_details', $q);
    }
    
    public function bank() {
        $q['tab_active'] = '';
        $q['bank_data'] = $this->General_model->get_bank_data();
        $this->load->view('master/bank_details', $q);
    }

    public function save_bank_details() {
        $data = array(
            'bank_name' => $this->input->post('bank_name'),
            'bank_branch' => $this->input->post('bank_branch'),
            'account_no' => $this->input->post('account_no'),
            'bank_ifsc' => $this->input->post('bank_ifsc'),
            'account_type' => $this->input->post('account_type'),
            'chq_return_charges' => $this->input->post('chq_return_charges'),
        );
        $this->General_model->save_db_bank($data);
        $this->session->set_flashdata('save_data', 'Bank Created');
        return redirect('Master/bank');
    }
    
    public function edit_bank() {
        $id = $this->input->post('id');
        $data = array(
            'bank_name' => $this->input->post('bank_name'),
            'bank_branch' => $this->input->post('bank_branch'),
            'account_no' => $this->input->post('account_no'),
            'bank_ifsc' => $this->input->post('bank_ifsc'),
            'account_type' => $this->input->post('account_type'),
            'chq_return_charges' => $this->input->post('chq_return_charges'),
            'active' => $this->input->post('status'),
        );
        $this->General_model->edit_db_bank($id, $data);
        $this->session->set_flashdata('save_data', 'Zone Details Updated');
        return redirect('Master/bank');
    }
    
    public function save_zone_details() {
        $data = array(
            'zone_name' => $this->input->post('zone_name'),
            'idwarehouse' => $this->input->post('warehouse'),
            'created_by' => $this->session->userdata('id_users'),
            'zone_lmb' => $this->session->userdata('id_users'),
            'active' => $this->input->post('status'),
        );
        $this->General_model->save_db_zone($data);
        $this->session->set_flashdata('save_data', 'Zone Created');
        return redirect('Master/zone_details');
    }

    public function edit_zone() {
        $id = $this->input->post('id');
        $data = array(
            'zone_name' => $this->input->post('zone_name'),
            'idwarehouse' => $this->input->post('warehouse'),
            'created_by' => $this->session->userdata('id_users'),
            'zone_lmb' => $this->session->userdata('id_users'),
            'active' => $this->input->post('status'),
            'zone_lmt' => date('Y-m-d H:i:s'),
        );
        $this->General_model->edit_db_zone($id, $data);
        $this->session->set_flashdata('save_data', 'Zone Details Updated');
        return redirect('Master/zone_details');
    }

    public function route_details() {
        $q['tab_active'] = '';
        $q['route_data'] = $this->General_model->get_route_data();
        $q['warehouse_data'] = $this->General_model->get_active_warehouse_data();
        $this->load->view('master/route_details', $q);
    }

    public function save_route_details() {
        $data = array(
            'route_name' => $this->input->post('route_name'),
            'idwarehouse' => $this->input->post('warehouse'),
            'created_by' => $this->session->userdata('id_users'),
            'route_lmb' => $this->session->userdata('id_users'),
            'active' => $this->input->post('status'),
        );
        $this->General_model->save_db_route($data);
        $this->session->set_flashdata('save_data', 'New Route Created');
        return redirect('Master/route_details');
    }

    public function edit_route() {
        $id = $this->input->post('id');
        $data = array(
            'route_name' => $this->input->post('route_name'),
            'idwarehouse' => $this->input->post('warehouse'),
            'created_by' => $this->session->userdata('id_users'),
            'route_lmb' => $this->session->userdata('id_users'),
            'active' => $this->input->post('status'),
            'route_lmt' => date('Y-m-d H:i:s'),
        );
        $this->General_model->edit_db_route($id, $data);
        $this->session->set_flashdata('save_data', 'Route Details Updated');
        return redirect('Master/route_details');
    }

    public function branch_category_details() {
        $q['tab_active'] = '';
        $q['branch_category_data'] = $this->General_model->get_branch_category_data();
        $this->load->view('master/branch_category_details', $q);
    }

    public function save_branch_category_details() {
        $data = array(
            'branch_category_name' => $this->input->post('category_name'),
            'created_by' => $this->session->userdata('id_users'),
            'bcategory_lmb' => $this->session->userdata('id_users'),
            'active' => $this->input->post('status'),
        );
        $this->General_model->save_db_branch_category($data);
        $this->session->set_flashdata('save_data', 'New Branch Category Created');
        return redirect('Master/branch_category_details');
    }

    public function edit_branch_category() {
        $id = $this->input->post('id');
        $data = array(
            'branch_category_name' => $this->input->post('category_name'),
            'created_by' => $this->session->userdata('id_users'),
            'bcategory_lmb' => $this->session->userdata('id_users'),
            'active' => $this->input->post('status'),
            'bcategory_lmt' => date('Y-m-d H:i:s'),
        );
        $this->General_model->edit_db_branch_category($id, $data);
        $this->session->set_flashdata('save_data', 'Branch Category Details Updated');
        return redirect('Master/branch_category_details');
    }

    public function branch_details() {
        $q['tab_active'] = '';
        $q['branch_data'] = $this->General_model->get_branch_data();
        $q['comapny_data'] = $this->General_model->get_active_comapny();
        $q['zone_data'] = $this->General_model->get_active_zone();
        $q['route_data'] = $this->General_model->get_active_route();
        $q['branch_category_data'] = $this->General_model->get_active_branch_category();
        $q['warehouse_data'] = $this->General_model->get_active_warehouse_data();
        $q['print_head_data'] = $this->General_model->get_print_head_data();
        $q['partner_type_data'] = $this->General_model->get_partner_type_data();
        $this->load->view('master/branch_details', $q);
    }

    public function save_branch_details() {
        $state_data = $this->General_model->get_state_by_name($this->input->post('state'));
        foreach($state_data as $state){
            $idstate = $state->id_state;
        }
        $data = array(
            'is_warehouse' => 0,
            'idstate' => $idstate,
            'branch_name' => $this->input->post('branch'),
            'branch_code' => $this->input->post('branch_code'),
            'branch_address' => $this->input->post('address'),
            'branch_pincode' => $this->input->post('pincode'),
            'branch_city' => $this->input->post('city'),
            'branch_state_name' => $this->input->post('state'),
            'branch_district' => $this->input->post('district'),
            'branch_email' => $this->input->post('email'),
            'branch_contact' => $this->input->post('contact'),
            'branch_contact_person' => $this->input->post('contact_person'),
            'latitude' => $this->input->post('latitude'),
            'longitude' => $this->input->post('longitude'),
            'active' => $this->input->post('status'),
            'created_by' => $this->session->userdata('id_users'),
            'branch_lmb' => $this->session->userdata('id_users'),
            'idcompany' => $this->input->post('company'),
            'idprinthead' => $this->input->post('print_head'),
            'idzone' => $this->input->post('zone'),
            'idroute' => $this->input->post('route'),
            'apple_store_id' => $this->input->post('apple_id'),
            'bfl_store_id' => $this->input->post('bfl_id'),
            'idpartner_type' => $this->input->post('partnertype'),
            'idbranchcategory' => $this->input->post('branch_category'),
            'branch_gstno' => $this->input->post('branch_gstno'),
            'idwarehouse' => $this->input->post('warehouse'),
            'allow_purchase_direct_inward' => $this->input->post('direct_inward'),
            'acc_branch_id' => $this->input->post('acc_branch_id'),
            'hrms_branch_id' => $this->input->post('hrms_branch_id'),
        );
        $lastid = $this->General_model->save_db_branch($data);
        if($lastid){
            $idzone = $this->input->post('zone');
            $users = $this->General_model->get_user_data_byidzone($idzone);
            foreach ($users as $usr){
                $user_data[] = array(
                    'iduser' => $usr->iduser,
                    'idbranch' => $lastid,
                    'created_by' => $_SESSION['id_users'],
                    'entry_time' => date('Y-m-d H:i:s'),
                );
            }
            $this->General_model->save_user_has_branch($user_data);
        }
        $this->session->set_flashdata('save_data', 'Branch Created');
        return redirect('Master/branch_details');
    }
    public function transport_vendor_details() {
        $q['tab_active'] = '';
        $q['vendor_data'] = $this->General_model->get_transport_vendor_data();
        $q['brand_data'] = $this->General_model->get_brand_data();
        $this->load->view('master/transport_vendor_details', $q);
    }
    public function save_transport_vendor() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $data = array(
            'transport_vendor_name' => $this->input->post('vendor'),
            'transport_vendor_gst' => $this->input->post('gst'),
            'delivery_days' => $this->input->post('delivery_days'),
            'transport_vendor_address' => $this->input->post('address'),
            'transport_vendor_contact' => $this->input->post('contact'),
            'transport_vendor_email' => $this->input->post('email'),
            'pincode' => $this->input->post('pincode'),
            'state' => $this->input->post('state'),
            'district' => $this->input->post('district'),
            'city' => $this->input->post('city'),
            'idbranch' => $_SESSION['idbranch'],
            'active' => $this->input->post('status'),
        );
        $this->General_model->save_transport_vendor($data);
        $this->session->set_flashdata('save_data', 'Transport Vendor Created');
        return redirect('Master/transport_vendor_details');
    }
      public function edit_transport_vendor() {
        $id = $this->input->post('id');
        $data = array(
            'transport_vendor_name' => $this->input->post('vendor'),
            'transport_vendor_address' => $this->input->post('address'),
            'transport_vendor_contact' => $this->input->post('contact'),
            'transport_vendor_gst' => $this->input->post('gst'),
            'transport_vendor_email' => $this->input->post('email'),
            'delivery_days' => $this->input->post('delivery_days'),
            'pincode' => $this->input->post('pincode'),
            'state' => $this->input->post('state'),
            'district' => $this->input->post('district'),
            'city' => $this->input->post('city'),
            'active' => $this->input->post('status'),
        );
        $this->General_model->edit_transport_vendor($id, $data);

        $this->session->set_flashdata('save_data', 'Vendor Created');
        return redirect('Master/transport_vendor_details');
    }
    public function vendor_details() {
        $q['tab_active'] = '';
        $q['vendor_data'] = $this->General_model->get_vendor_data();
        $q['brand_data'] = $this->General_model->get_brand_data();
        $q['sku_data'] = $this->General_model->get_vendor_sku_data();
//        die('<pre>'.print_r($q['vendor_data'],1).'</pre>');
        $this->load->view('master/vendor_details', $q);
    }
    public function vendor_details_edit($id) {
        $q['tab_active'] = '';
        $q['vendor_data'] = $this->General_model->get_vendor_byid($id);
        $q['brand_data'] = $this->General_model->get_brand_data();
        $q['sku_data'] = $this->General_model->get_vendor_sku_data();
        $this->load->view('master/vendor_details_edit', $q);
    }
    
//    public function load_vendor_form() {
//        $q['tab_active'] = '';
//        $q['vendor_data'] = $this->General_model->get_vendor_data();
//        $q['brand_data'] = $this->General_model->get_brand_data();
//        $this->load->view('master/vendor_details', $q);
//    }
    public function ajax_create_vendor_form() {
        $brand_data = $this->General_model->get_brand_data(); ?>
        <div class="col-md-8 col-md-offset-2" >
            <article role="login" class="" style="padding: 15px; border-radius: 10px">
                <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Add Vendor</h4></center><hr>
                <label class="col-md-3 col-md-offset-1">Vendor</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="vendor" placeholder="Enter Vendor Name" required="" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Office Contact</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="contact" placeholder="Enter Contact Number" required=""/>
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Office Email</label>
                <div class="col-md-7">
                    <input type="email" class="form-control" name="email" placeholder="Enter Office Email" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">GSTIN</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="gst" id="gst" placeholder="Enter GSTIN" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Delivery Days</label>
                <div class="col-md-7">
                    <input type="number" class="form-control" name="delivery_days" placeholder="Enter Delivery Days" />
                    <input type="hidden" class="form-control" name="status" value="1" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Office Address</label>
                <div class="col-md-7">
                    <textarea type="text" class="form-control" name="address" placeholder="Enter Address" required="" rows="1" ></textarea>
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Office Pincode</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" id="pincode" name="pincode" placeholder="Enter Pincode" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Office State</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" id="state" readonly="" name="state" placeholder="State" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Office District</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" id="district" readonly="" name="district" placeholder="District" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Office City</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" id="city" readonly="" name="city" placeholder="City" />
                </div><div class="clearfix"></div><hr>
                <label class="col-md-3 col-md-offset-1">Godown Contact</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="gcontact" placeholder="Enter Contact Number" required=""/>
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Contact Person</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" name="person_name" placeholder="Enter Godown Person Name" required=""/>
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Office Email</label>
                <div class="col-md-7">
                    <input type="email" class="form-control" name="gemail" placeholder="Enter Godown Email" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Godown Address</label>
                <div class="col-md-7">
                        <textarea type="text" class="form-control" name="gaddress" placeholder="Enter Godown Address" required="" rows="1" ></textarea>
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Godown Pincode</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" id="gpincode" name="gpincode" placeholder="Enter Godown Pincode" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Godown State</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" id="gstate" readonly="" name="gstate" placeholder="State" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Godown District</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" id="gdistrict" readonly="" name="gdistrict" placeholder="District" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Godown City</label>
                <div class="col-md-7">
                    <input type="text" class="form-control" id="gcity" readonly="" name="gcity" placeholder="City" />
                </div><div class="clearfix"></div><br>
                <label class="col-md-3 col-md-offset-1">Brands</label>
                <div class="col-md-7">
                    <select data-placeholder="Select Multiple Brands" multiple class="chosen-select" required="" id="brand" style="min-width: 330px">
                        <?php foreach ($brand_data as $branch){ ?>
                        <option value="<?php echo $branch->id_brand ?>"><?php echo $branch->brand_name ?></option>
                        <?php } ?>
                    </select>
                </div><div class="clearfix"></div><hr>
                <input type="hidden" name="brands" id="brands" />
                <a class="btn btn-warning waves-effect simple-tooltip" data-toggle="collapse" data-target="#pay">Cancel</a>
                <button type="submit" formmethod="POST" formaction="<?php echo base_url('Master/save_vendor') ?>" class="pull-right btn btn-info waves-effect">Save</button>
                <div class="clearfix"></div>
            </article><div class="clearfix"></div><br>
        </div>
    <?php }
    
   public function save_vendor() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $data = array(
            'vendor_name' => $this->input->post('vendor'),
            'vendor_gst' => $this->input->post('gst'),
            'delivery_days' => $this->input->post('delivery_days'),
            'vendor_address' => $this->input->post('address'),
            'vendor_contact' => $this->input->post('contact'),
            'vendor_email' => $this->input->post('email'),
            'pincode' => $this->input->post('pincode'),
            'state' => $this->input->post('state'),
            'district' => $this->input->post('district'),
            'city' => $this->input->post('city'),
            'person_name' => $this->input->post('person_name'),
            'gaddress' => $this->input->post('gaddress'),
            'gcontact' => $this->input->post('gcontact'),
            'gemail' => $this->input->post('gemail'),
            'gpincode' => $this->input->post('gpincode'),
            'gstate' => $this->input->post('gstate'),
            'gdistrict' => $this->input->post('gdistrict'),
            'gcity' => $this->input->post('gcity'),
            'active' => $this->input->post('status'),
            'idvendors_sku' => $this->input->post('vendor_skus'),
            'vendor_created_by' => $this->session->userdata('id_users'),
            'entry_time' => date('Y-m-d H:i:s'),
        );
        $idvendor = $this->General_model->save_vendor($data);
        $brands = explode(',', $this->input->post('brands'));
        $first_nestarray = array();
        foreach ($brands as $brand) {
            $first_nestarray[] = array(
                'idvendor' => $idvendor,
                'idbrand' => $brand[0],
            );
        }
        if(count($first_nestarray) > 0){
            $this->General_model->save_vendor_has_branch($first_nestarray);
        }
        $this->session->set_flashdata('save_data', 'Vendor Created');
        return redirect('Master/vendor_details');
    }

    public function edit_vendor() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $id = $this->input->post('id');
        $vendorhas_brand = $this->General_model->get_vendor_has_brand_byidvendor($id);
//        die(count($vendorhas_brand));
        $data = array(
            'vendor_name' => $this->input->post('vendor'),
            'vendor_address' => $this->input->post('address'),
            'vendor_contact' => $this->input->post('contact'),
            'vendor_gst' => $this->input->post('gst'),
            'vendor_email' => $this->input->post('email'),
            'delivery_days' => $this->input->post('delivery_days'),
            'pincode' => $this->input->post('pincode'),
            'state' => $this->input->post('state'),
            'district' => $this->input->post('district'),
            'city' => $this->input->post('city'),
            'active' => $this->input->post('status'),
            'idvendors_sku' => $this->input->post('vendor_skus'),
        );
//       die('<pre>'.print_r( $id,1).'</pre>');
        $this->General_model->edit_db_vendor($id, $data);
        if(count($vendorhas_brand)){
//            die(print_r($vendorhas_brand));
            $this->General_model->delete_vendor_has_brand($id);
        }
        $brandss = explode(',', $this->input->post('brands'));
//          die('<pre>'.print_r($brandss,1).'</pre>');
        $first_nestarray = array();
        for($i=0; $i < count($brandss); $i++) {
            $first_nestarray[] = array(
                'idvendor' => $id,
                'idbrand' => $brandss[$i],
            );
        }
//         die('<pre>'.print_r($first_nestarray,1).'</pre>');
        if(count($first_nestarray) > 0){
            $this->General_model->save_vendor_has_branch($first_nestarray);
        }
        $this->session->set_flashdata('save_data', 'Vendor Created');
        return redirect('Master/vendor_details');
    }

    public function edit_branch() {
        $id = $this->input->post('id');
        $state_data = $this->General_model->get_state_by_name($this->input->post('state'));
        foreach($state_data as $state){
            $idstate = $state->id_state;
        }
        $data = array(
            'is_warehouse' => 0,
            'idstate' => $idstate,
            'idwarehouse' => $this->input->post('warehouse'),
            'branch_name' => $this->input->post('branch'),
            'branch_code' => $this->input->post('branch_code'),
            'branch_address' => $this->input->post('address'),
            'branch_pincode' => $this->input->post('pincode'),
            'branch_city' => $this->input->post('city'),
            'branch_state_name' => $this->input->post('state'),
            'branch_district' => $this->input->post('district'),
            'branch_email' => $this->input->post('email'),
            'branch_contact' => $this->input->post('contact'),
            'branch_contact_person' => $this->input->post('contact_person'),
            'latitude' => $this->input->post('latitude'),
            'longitude' => $this->input->post('longitude'),
            'active' => $this->input->post('status'),
            'branch_lmt' => date('Y-m-d H:i:s'),
            'branch_lmb' => $this->session->userdata('id_users'),
            'idcompany' => $this->input->post('company'),
            'idprinthead' => $this->input->post('print_head'),
            'apple_store_id' => $this->input->post('apple_id'),
            'bfl_store_id' => $this->input->post('bfl_id'),
            'idpartner_type' => $this->input->post('partnertype'),
            'idzone' => $this->input->post('zone'),
            'idroute' => $this->input->post('route'),
            'idbranchcategory' => $this->input->post('branch_category'),
            'allow_purchase_direct_inward' => $this->input->post('direct_inward'),
            'branch_gstno' => $this->input->post('branch_gstno'),
            'acc_branch_id' => $this->input->post('acc_branch_id'),
            'hrms_branch_id' => $this->input->post('hrms_branch_id'),
        );
        $this->General_model->edit_db_branch($id, $data);
        $this->session->set_flashdata('save_data', 'Branch Details Updated');
        return redirect('Master/branch_details');
    }
    
    public function dispatch_type(){
        $q['tab_active'] = '';
        $q['dispatch_data'] = $this->General_model->get_dispatch_type();
        $this->load->view('master/dispatch_details', $q);
    }
    public function save_dispatch_type(){
        $data = array(
            'dispatch_type' => $this->input->post('type'),
        );
        $this->General_model->save_dispatch_type($data);
        $this->session->set_flashdata('save_data', 'Dispatch Type Created');
        return redirect('Master/dispatch_type');
    }
    public function edit_dispatch_type(){
        $id = $this->input->post('id');
        $data = array(
            'dispatch_type' => $this->input->post('type'),
            'status' => $this->input->post('status'),
        );
        $this->General_model->edit_dispatch_type($data, $id);
        $this->session->set_flashdata('save_data', 'Dispatch Type Updated');
        return redirect('Master/dispatch_type');
    }

    public function godown_details() {
        $q['tab_active'] = '';
        $q['godown_data'] = $this->General_model->get_godown_data();
        $this->load->view('master/godown_details', $q);
    }
   

    public function save_godown() {
        $data = array(
            'godown_name' => $this->input->post('name'),
            'active' => $this->input->post('status'),
        );
        $this->General_model->save_db_godown($data);

        $this->session->set_flashdata('save_data', 'Godown Created');
        return redirect('Master/godown_details');
    }

    public function edit_godown() {
        $id = $this->input->post('id');
        $data = array(
            'godown_name' => $this->input->post('name'),
            'active' => $this->input->post('status'),
        );
        $this->General_model->edit_db_godown($id, $data);

        $this->session->set_flashdata('save_data', 'Godown Updated');
        return redirect('Master/godown_details');
    }

    public function ajax_branch_details($id) {
        $branch = $this->General_model->get_branch_byid($id);
        $warehouse_data = $this->General_model->get_active_warehouse_data();
        $comapny_data = $this->General_model->get_active_comapny();
        $zone_data = $this->General_model->get_active_zone();
        $route_data = $this->General_model->get_active_route();
        $branch_category_data = $this->General_model->get_active_branch_category(); 
        $print_head_data = $this->General_model->get_print_head_data();
        $partner_type_data = $this->General_model->get_partner_type_data();
        ?>
        <form id="form-edit" class="collapse">
            <div class="col-md-8 thumbnail col-md-offset-2">
                <div class="">
                    <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Branch</h4></center><hr>
                    <label class="col-md-3">Branch Name</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" value="<?php echo $branch->branch_name; ?>" name="branch" required=""/>
                    </div><div class="clearfix"></div><br> 
                    <label class="col-md-3">Branch GSTIN</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" value="<?php echo $branch->branch_gstno; ?>" name="branch_gstno" required=""/>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">Branch Code</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" placeholder="Enter Branch Code" value="<?php echo $branch->branch_code ?>" name="branch_code" required=""/>
                    </div><div class="clearfix"></div><br> 
                    <label class="col-md-3">Address</label>
                    <div class="col-md-9">
                        <textarea type="text" class="form-control" name="address" required="" ><?php echo $branch->branch_address; ?></textarea>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">Pincode</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="pincode"  class="pincode"  name="pincode" value="<?php echo $branch->branch_pincode; ?>" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">State</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="state" readonly="" name="state" value="<?php echo $branch->branch_state_name; ?>" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">District</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="district" readonly="" name="district" value="<?php echo $branch->branch_district; ?>" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">City</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" id="city" readonly="" name="city" value="<?php echo $branch->branch_city; ?>" />
                    </div><div class="clearfix"></div><br>  
                    <label class="col-md-3">Branch Email</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="email" value="<?php echo $branch->branch_email; ?>" required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">Contact Person</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="contact_person" value="<?php echo $branch->branch_contact_person; ?>" required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">Contact</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="contact" value="<?php echo $branch->branch_contact; ?>" required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">Latitude </label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="latitude" value="<?php echo $branch->latitude; ?>" required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">Longitude</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="longitude" value="<?php echo $branch->longitude; ?>" required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">Apple Store Id</label>
                    <div class="col-md-9">
                       <input type="text" class="form-control" name="apple_id" placeholder="Enter apple Store Id" value="<?php echo $branch->apple_store_id; ?>" required="" />
                    </div><div class="clearfix"></div><br>                                        
                    <label class="col-md-3">Bfl Id</label>
                    <div class="col-md-9">
                        <input type="text" class="form-control" name="bfl_id" placeholder="Enter bfl Id" value="<?php echo $branch->bfl_store_id; ?>" required="" />
                    </div><div class="clearfix"></div><br> 
                     <label class="col-md-3">Partner Type</label>
                    <div class="col-md-9">
                        <select class=" form-control" name="partnertype" id="partnertype" required="">
                            <option value="">Select Partner Type</option>
                            <?php foreach ($partner_type_data as $ptype) {
                                if($ptype->id_partner_type == $branch->idpartner_type){ ?>
                                    <option selected="" value="<?php echo $ptype->id_partner_type; ?>"><?php echo $ptype->partner_type; ?></option>
                                <?php } else{  ?>
                                <option value="<?php echo $ptype->id_partner_type; ?>"><?php echo $ptype->partner_type; ?></option>
                                <?php  } } ?>
                        </select>                   
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-3">Company</label>
                    <div class="col-md-9">
                        <select class="form-control" name="company" id="company" required="">
                            <option value="">Select Company</option>
                            <?php foreach ($comapny_data as $comapny) {
                                    if ($comapny->company_id == $branch->idcompany) { ?>
                                        <option selected="" value="<?php echo $comapny->company_id; ?>"><?php echo $comapny->company_name; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $comapny->company_id; ?>"><?php echo $comapny->company_name; ?></option>
                                    <?php } ?>
                            <?php } ?>
                        </select>                   
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-3">Print Head</label>
                    <div class="col-md-9">
                        <select class="form-control" name="print_head" id="print_head" required="">
                            <option value="">Select Company</option>
                            <?php foreach ($print_head_data as $printhead) {
                                    if ($printhead->id_print_head == $branch->idprinthead) { ?>
                                        <option selected="" value="<?php echo $printhead->id_print_head; ?>"><?php echo $printhead->company_name; ?></option>
                                    <?php } else { ?>
                                        <option value="<?php echo $printhead->id_print_head; ?>"><?php echo $printhead->company_name; ?></option>
                                    <?php } ?>
                            <?php } ?>
                        </select>                   
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-3">Warehouse</label>
                    <div class="col-md-9">
                        <select class="form-control" name="warehouse" id="warehouse" required="">
                            <option value="">Select warehouse</option>
                                <?php foreach ($warehouse_data as $warehouse) {
                                    if ($warehouse->id_branch == $branch->idwarehouse) { ?>
                                    <option selected="" value="<?php echo $warehouse->id_branch; ?>"><?php echo $warehouse->branch_name; ?></option>
                                <?php } else { ?>
                                    <option value="<?php echo $warehouse->id_branch; ?>"><?php echo $warehouse->branch_name; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">Zone</label>
                    <div class="col-md-9">
                        <select class="form-control" name="zone" id="zone" required="">
                            <option value="">Select Zone</option>
                                <?php foreach ($zone_data as $zone) {
                                    if ($zone->id_zone == $branch->idzone) {
                                        ?>
                                    <option selected="" value="<?php echo $zone->id_zone; ?>"><?php echo $zone->zone_name; ?></option>
                                <?php } else { ?>
                                    <option value="<?php echo $zone->id_zone; ?>"><?php echo $zone->zone_name; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>                   
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-3">Route</label>
                    <div class="col-md-9">
                        <select class="form-control" name="route" id="route" required="">
                            <option value="">Select Route</option>
                            <?php foreach ($route_data as $route) {
                                if ($route->id_route == $branch->idroute) {
                                    ?>
                                    <option selected="" value="<?php echo $route->id_route; ?>"><?php echo $route->route_name; ?></option>
                                <?php } else { ?>
                                    <option value="<?php echo $route->id_route; ?>"><?php echo $route->route_name; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>                   
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-3">Branch Category</label>
                    <div class="col-md-9">
                        <select class="form-control" name="branch_category" id="branch_category" required="">
                            <option value="">Select Category</option>
                                <?php foreach ($branch_category_data as $branch_category) {
                                    if ($branch_category->id_branch_category == $branch->idbranchcategory) {
                                        ?>
                                    <option selected="" value="<?php echo $branch_category->id_branch_category; ?>"><?php echo $branch_category->branch_category_name; ?></option>
                                    <?php } else { ?>
                                    <option value="<?php echo $branch_category->id_branch_category; ?>"><?php echo $branch_category->branch_category_name; ?></option>
                                <?php } ?>
                            <?php } ?>
                        </select>                   
                    </div>
                    <div class="clearfix"></div><br>
                    <label class="col-md-3">Status</label>
                    <div class="col-md-9">
                        <select class="form-control" name="status">
                            <option value="<?php echo $branch->active ?>"><?php
                            if ($branch->active == 1) {
                                echo 'Active';
                            } elseif ($branch->active == 0) {
                                echo 'In Active';
                            }
                            ?></option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3">Purchase Direct Inward</label>
                    <div class="col-md-9">
                        <select class="select form-control" name="direct_inward">
                            <option value="<?php echo $branch->allow_purchase_direct_inward ?>"><?php
                            if ($branch->allow_purchase_direct_inward == 1) {
                                echo 'Allow';
                            } elseif ($branch->allow_purchase_direct_inward == 0) {
                                echo 'Dis Allow';
                            }
                            ?></option>
                            <option value="1">Allow</option>
                            <option value="0">Dis Allow</option>
                        </select>
                    </div>
                    <div class="clearfix"></div><br>
                   <label class="col-md-3 ">Accessories ID</label>
                    <div class="col-md-9">
                        <input type="number" class="form-control" name="acc_branch_id" placeholder="Enter Accessories Branch Id" value="<?php echo $branch->acc_branch_id ?>" />
                    </div><div class="clearfix"></div><br>  
                    <label class="col-md-3 ">Hrms ID</label>
                    <div class="col-md-9">
                        <input type="number" class="form-control" name="hrms_branch_id" placeholder="Enter HRMS Branch Id" value="<?php echo $branch->hrms_branch_id ?>" />
                    </div>
                    <div class="clearfix"></div><br>
                    <div class="clearfix"></div><hr>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div><hr>
                <button type="button" class="close-edit btn btn-warning pull-left waves-effect"><span class=""></span> Close</button>                
                <button type="submit" value="<?php echo $branch->id_branch ?>" name="id"  formmethod="POST" formaction="<?php echo base_url('Master/edit_branch') ?>" class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>

            </div>
        </form>            
        <?php
    }

    public function ajax_warehouse_details($id) {
        $branch = $this->General_model->get_branch_byid($id);
        ?>
        <form id="form-edit" class="collapse">
            <div class="col-md-8 thumbnail col-md-offset-2">
                <div class="">  
                    <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit Branch</h4></center><hr>
                    <label class="col-md-3 col-md-offset-1">Warehouse Name</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" value="<?php echo $branch->branch_name; ?>" name="branch" required=""/>
                    </div><div class="clearfix"></div><br>  
                    <label class="col-md-3 col-md-offset-1">Address</label>
                    <div class="col-md-7">
                        <textarea type="text" class="form-control" name="address" required="" > <?php echo $branch->branch_address; ?> </textarea>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Pincode</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" id="pincode"  class="pincode"  name="pincode" value="<?php echo $branch->branch_pincode; ?>" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">State</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" id="state" readonly="" name="state" value="<?php echo $branch->branch_state_name; ?>" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">District</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" id="district" readonly="" name="district" value="<?php echo $branch->branch_district; ?>" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">City</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" id="city" readonly="" name="city" value="<?php echo $branch->branch_city; ?>" />
                    </div><div class="clearfix"></div><br>  
                    <label class="col-md-3 col-md-offset-1">Branch Email</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="email" value="<?php echo $branch->branch_email; ?>" required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Contact Person</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="contact_person" value="<?php echo $branch->branch_contact_person; ?>" required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Contact</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="contact" value="<?php echo $branch->branch_contact; ?>" required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Latitude </label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="latitude" value="<?php echo $branch->latitude; ?>" required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Longitude</label>
                    <div class="col-md-7">
                        <input type="text" class="form-control" name="longitude" value="<?php echo $branch->longitude; ?>" required="" />
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">PO Approval</label>
                    <div class="col-md-7">
                        <select class="select form-control" name="po_approval">
                            <option value="<?php echo $branch->po_approval ?>">
                                <?php if ($branch->po_approval == 1) { echo 'Yes'; } elseif ($branch->po_approval == 0) { echo 'No'; } ?>
                            </option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div><div class="clearfix"></div><br>
                    <label class="col-md-3 col-md-offset-1">Status</label>
                    <div class="col-md-7">
                        <select class="select form-control" name="status">
                            <option value="<?php echo $branch->active ?>"><?php
                                if ($branch->active == 1) {
                                    echo 'Active';
                                } elseif ($branch->active == 0) {
                                    echo 'In Active';
                                }
                                ?></option>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div><div class="clearfix"></div><br>                                                    
                    <div class="clearfix"></div>
                </div>
                <button type="button" class="close-edit btn btn-warning pull-left waves-effect"><span class=""></span> Close</button>                
                <button type="submit" value="<?php echo $branch->id_branch ?>" name="id"  formmethod="POST" formaction="<?php echo base_url('Master/edit_warehouse') ?>" class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
            </div>
        </form>
    <?php
    }

    public function edit_user() {
        
        $id = $this->input->post('id');
        $data = array(
            'user_name' => $this->input->post('full_name'),
            'userid' => $this->input->post('name'),
            'user_contact' => $this->input->post('contact'),
            'user_password' => $this->input->post('password'),
            'active' => $this->input->post('status'),
        );
        $iduser = $this->General_model->edit_user($id, $data);
        $this->session->set_flashdata('save_data', 'User Updated');
        return redirect('Master/user_details');
    }

    public function save_userrole_menu() {
        $model_id = $this->input->post('model_id');
        $menu_sequence = $this->input->post('menu_sequence');        
        $submodel_id = $this->input->post('submodel_id');
        $submenu_sequence = $this->input->post('submenu_sequence');
        $idrole = $this->input->post('idrole');
        $count = count($submodel_id);
        $cnt = 1;
        $data = array();
        $update_data = array();
        $menu=array();

        for ($i = 0; $i < $count; $i++) {
            $update = 0;
            $create = 0;
            $delete = 0;
            $read_permission = 0;
            $idsubmodel = $submodel_id[$i];
            $idmenu = $model_id[$i];
            if (isset($_POST['submodel' . $cnt])) {
                if ($_POST['submodel' . $cnt] == 'on') {
                    $read_permission = 1;
                    if ($this->input->post('create' . $cnt) !== NULL) {
                        $create = $this->input->post('create' . $cnt);
                    }
                    if ($this->input->post('update' . $cnt) !== NULL) {
                        $update = $this->input->post('update' . $cnt);
                    }
                    if ($this->input->post('delete' . $cnt) !== NULL) {
                        $delete = $this->input->post('delete' . $cnt);
                    }
                    $data[$i] = array(
                        'iduserrole' => $idrole,
                        'idmenu' => $idmenu,
                        'idsubmenu' => $idsubmodel,
                        'menu_sequence' => $menu_sequence[$i],
                        'sequence' => $submenu_sequence[$i],
                        'read_permission' => $read_permission,
                        'create_permission' => $create,
                        'update_permission' => $update,
                        'delete_permission' => $delete,
                    );
                }
            }
            if (isset($_POST['model' . $cnt])) {

                if ($_POST['model' . $cnt] == 'on') {
                    $read_permission = 1;
                    if ($this->input->post('create' . $cnt) !== NULL) {
                        $create = $this->input->post('create' . $cnt);
                    }
                    if ($this->input->post('update' . $cnt) !== NULL) {
                        $update = $this->input->post('update' . $cnt);
                    }
                    if ($this->input->post('delete' . $cnt) !== NULL) {
                        $delete = $this->input->post('delete' . $cnt);
                    }
                    $data[$i] = array(
                        'iduserrole' => $idrole,
                        'idmenu' => $idmenu,
                        'idsubmenu' => $idsubmodel,
                        'menu_sequence' => $menu_sequence[$i],
                        'sequence' => $submenu_sequence[$i],
                        'read_permission' => $read_permission,
                        'create_permission' => $create,
                        'update_permission' => $update,
                        'delete_permission' => $delete,
                    );
                }
            }
            if (in_array($idmenu, $menu)){
                
            } else{
                 $update_data[] = array(  
                        'idmenu' => $idmenu,
                        'menu_sequence' => $menu_sequence[$i]
                    );
                 $menu[]=$idmenu;
            }
            $cnt++;
        }
        
        $this->General_model->save_userrole_menu($data, $idrole);
        $this->General_model->update_menu_sequence($update_data, $idrole);
        $this->session->set_flashdata('save_data', 'Menu Assign to User Role');
        return redirect('Master/role_has_menu/' . $idrole);
    }

    public function save_branch_godown() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $count = $this->input->post('count');
        $idbranch = $this->input->post('idbranch');
        for ($i = 1; $i <= $count; $i++) {
            if ($this->input->post('godown' . $i)) {
                $data[$i] = array(
                    'idbranch' => $idbranch,
                    'idgodown' => $this->input->post('godown' . $i),
                );
                $this->General_model->save_branch_godown($data[$i]);
            }
        }
        $this->session->set_flashdata('save_data', 'Godown Added to Branch');
        return redirect('Master/billing_godown/' . $idbranch);
    }
    
    public function ajax_get_role_mappings() {
        $role = $this->input->post('role');
        $role_data = $this->General_model->get_user_role_byid($role);
//        die(print_r($role_data));
//        die($role_data[0]->has_warehouse);
            if($role_data[0]->has_warehouse){
                $warehouse_data = $this->General_model->get_active_warehouse_data(); ?>
                <label class="col-md-4">Warehouse</label>
                <div class="col-md-8">
                    <select data-placeholder="Select Multiple Warehouse" multiple id="warehouse" class="chosen-select" required="" style="min-width: 100%">
                        <option value="all">All Warehouse</option>
                        <?php foreach ($warehouse_data as $warehouse){ ?>
                        <option value="<?php echo $warehouse->id_branch ?>"><?php echo $warehouse->branch_name ?></option>
                        <?php } ?>
                    </select>
                </div><div class="clearfix"></div><br>
        <?php }if($role_data[0]->has_branch){
            $branch_data = $this->General_model->get_active_branch_data();?>
            <label class="col-md-4">Branch</label>
            <div class="col-md-8">
            <?php if($role_data[0]->level == 2){ 
                if($this->session->userdata('level') == 2){ ?>
                    <select data-placeholder="Select Branch"  id="branch" class="chosen-select" required="" style="min-width: 100%">
                        <option value="">Select Branch</option>
                        <?php foreach ($branch_data as $branch){
                            if($_SESSION['idbranch'] == $branch->id_branch){ ?>
                                <option value="<?php echo $branch->id_branch ?>"><?php echo $branch->branch_name ?></option>
                        <?php } } ?>
                    </select>
                <?php } else {?>
                <select data-placeholder="Select Branch"  id="branch" class="chosen-select" required="" style="min-width: 100%">
                    <option value="all">Select Branch</option>
                    <?php foreach ($branch_data as $branch){ ?>
                    <option value="<?php echo $branch->id_branch ?>"><?php echo $branch->branch_name ?></option>
                    <?php } ?>
                </select>
                <?php } } else{ ?>
                <select data-placeholder="Select Multiple Branches" multiple id="branch" class="chosen-select" required="" style="min-width: 100%">
                    <option value="all">All Branch</option>
                    <?php foreach ($branch_data as $branch){ ?>
                    <option value="<?php echo $branch->id_branch ?>"><?php echo $branch->branch_name ?></option>
                    <?php } ?>
                </select>
            <?php }?>
            </div><div class="clearfix"></div><br>    
        <?php }if($role_data[0]->has_product_category){ 
            $product_category = $this->General_model->get_product_category_data(); ?>
            <label class="col-md-4">Product Category</label>
            <div class="col-md-8">
                <select data-placeholder="Select Multiple Category" multiple id="product_cat" class="chosen-select" required="" style="min-width: 100%">
                    <option value="all">All Product Category</option>
                    <?php foreach ($product_category as $category){ ?>
                    <option value="<?php echo $category->id_product_category ?>"><?php echo $category->product_category_name ?></option>
                    <?php } ?>
                </select>
            </div><div class="clearfix"></div><br>
        <?php }if($role_data[0]->has_brand){
//            $branch_data = $this->General_model->get_branch_data();
            $brand_data = $this->General_model->get_active_brand_data(); ?>
            <label class="col-md-4">Brand</label>
            <div class="col-md-8">
                <select data-placeholder="Select Multiple Brands" multiple id="brand" class="chosen-select" required="" style="min-width: 100%">
                    <option value="all">All Brand</option>
                    <?php foreach($brand_data as $brand){ ?>
                    <option value="<?php echo $brand->id_brand ?>"><?php echo $brand->brand_name ?></option>
                    <?php } ?>
                </select>
            </div><div class="clearfix"></div><br>
        <?php }if($role_data[0]->has_paymentmode){
            $payment_mode = $this->General_model->get_active_payment_mode(); ?>
            <label class="col-md-4">Payment Mode</label>
            <div class="col-md-8">
                <select data-placeholder="Select Multiple Payment Modes" multiple id="mode" class="chosen-select" required="" style="min-width: 100%">
                    <option value="all">All Payment Mode</option>
                    <?php foreach($payment_mode as $mode){ ?>
                    <option value="<?php echo $mode->id_paymentmode ?>"><?php echo $mode->payment_mode ?></option>
                    <?php } ?>
                </select>
            </div><div class="clearfix"></div><br>
              <?php } if($role_data[0]->has_expense_wallet){
            $wallet_data = $this->Expense_wallet_model->get_wallet_type_data(); ?>
            <label class="col-md-4">Wallet Type</label>
            <div class="col-md-8">
                <select data-placeholder="Select Multiple Wallet Type" multiple id="wallet" class="chosen-select" required="" style="min-width: 100%">
                    <option value="all">All Wallet Type</option>
                    <?php foreach($wallet_data as $wallet){ ?>
                    <option value="<?php echo $wallet->id_wallet_type ?>"><?php echo $wallet->wallet_type ?></option>
                    <?php } ?>
                </select>
            </div><div class="clearfix"></div><br>
        <?php } if($role_data[0]->has_costing_header){
            $costing_data = $this->Costing_model->get_active_branch_costing_headers(); ?>
            <label class="col-md-4">Branch Costing Headers</label>
            <div class="col-md-8">
                <select data-placeholder="Select Multiple Costing Headers" multiple id="costing" class="chosen-select" required="" style="min-width: 100%">
                    <option value="all">All Costing Headers</option>
                    <?php foreach($costing_data as $cdata){ ?>
                    <option value="<?php echo $cdata->id_cost_header ?>"><?php echo $cdata->cost_header_name ?></option>
                    <?php } ?>
                </select>
            </div><div class="clearfix"></div><br>
        <?php } ?>
            <input type="hidden" id="warehouses" name="warehouses" />
            <input type="hidden" id="branches" name="branches" />
            <input type="hidden" id="brands" name="brands" />
            <input type="hidden" id="product_cats" name="product_cats" />
            <input type="hidden" id="payment_modes" name="payment_modes" />
              <input type="hidden" id="wallet_types" name="wallet_types" />
              <input type="hidden" id="costing_headers" name="costing_headers" />
            <script>
                $(document).ready(function(){
                    $('#warehouse').change(function(){
                        $('#warehouses').val($(this).val());
                    });
                    $('#branch').change(function(){
                        $('#branches').val($(this).val());
                    });
                    $('#product_cat').change(function(){
                        $('#product_cats').val($(this).val());
                    });
                    $('#brand').change(function(){
                        $('#brands').val($(this).val());
                    });
                    $('#mode').change(function(){
                        $('#payment_modes').val($(this).val());
                    });
                    $('#wallet').change(function(){
                        $('#wallet_types').val($(this).val());
                    });
                    $('#costing').change(function(){
                        $('#costing_headers').val($(this).val());
                    });
                });
            </script>
        <?php 
    }
    
    public function ajax_get_payment_attributes() {
        $payment_attribute = $this->General_model->get_payment_attribute_data(); ?>
        <div class="clearfix"></div><br>
        <label class="col-md-4">Payment Attributes</label>
        <div class="col-md-8">
            <select data-placeholder="Select Multiple Attributes" multiple id="attribute" class="chosen-select" required="" style="min-width: 100%">
                <?php foreach ($payment_attribute as $attribute){ ?>
                <option value="<?php echo $attribute->id_payment_attribute ?>"><?php echo $attribute->attribute_name ?></option>
                <?php } ?>
            </select>
        </div>
        <input type="hidden" id="sel_attribute" name="sel_attribute" />
        <script>
            $(document).ready(function(){
                $('#attribute').change(function(){
                    $('#sel_attribute').val($(this).val());
                });
            });
        </script> <?php
    }
    public function payment_mode_has_devices($mode, $name, $paymenthead) {
        $q['mode'] = $mode;
        $q['name'] = $name;
        $q['paymenthead'] = $paymenthead;
        $q['tab_active'] = '';
        $q['devices_data'] = $this->General_model->get_devices_byidmode($mode);
        $q['branch_data'] = $this->General_model->get_active_branch_data();
        $this->load->view('master/payment_mode_has_devices', $q);
    }

    public function save_payment_mode_has_devices() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $idmode = $this->input->post('idmode');
        $mode_name = $this->input->post('mode_name');
        $paymenthead = $this->input->post('paymenthead');
        $data = array(
            'idpayment_mode' => $idmode,
            'idpayment_head' => $paymenthead,
            'device_id' => $this->input->post('device_id'),
            'idbranch' => $this->input->post('idbranch'),
            'status' => $this->input->post('status'),
        );
        $this->General_model->save_payment_mode_has_devices($data);
        $this->session->set_flashdata('save_data', 'Device added to Payment mode');
        return redirect('Master/payment_mode_has_devices/' . $idmode.'/'.$mode_name.'/'.$paymenthead);
    }
    
    public function edit_payment_mode_has_devices() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $idrow = $this->input->post('idrow');
        $idmode = $this->input->post('idmode');
        $mode_name = $this->input->post('mode_name');
        $paymenthead = $this->input->post('paymenthead');
        $data = array(
            'device_id' => $this->input->post('device_id'),
            'idbranch' => $this->input->post('idbranch'),
            'status' => $this->input->post('status'),
        );
        $this->General_model->edit_payment_mode_has_devices($idrow, $data);
        $this->session->set_flashdata('save_data', 'Device edited successfully');
        return redirect('Master/payment_mode_has_devices/' . $idmode.'/'.$mode_name.'/'.$paymenthead);
    }
    
    //    ------------------------------------------------------------------------------------
    // Edit
    public function fileUpload() {
        die(print_r($_FILES));
        if (!empty($_FILES['file']['name'])) {
            // Set preference
            $config['upload_path'] = 'uploads/';
            $config['allowed_types'] = 'jpg|jpeg|png|gif';
            $config['max_size'] = '1024'; // max_size in kb
            $config['file_name'] = $_FILES['file']['name'];
            //Load upload library
            $this->load->library('upload', $config);
            // File upload
            if ($this->upload->do_upload('file')) {
                // Get data about the file
                $uploadData = $this->upload->data();
            }
        }
    }

//    ------------------------------------------------------------------------------------
    // Remove
    public function remove_menu_role($id, $idrole) {
        $this->General_model->remove_menu_role($id);
        $this->session->set_flashdata('save_data', 'Menu Removed');
        return redirect('Master/role_has_menu/' . $idrole);
    }

    public function remove_branch_godown($id, $idbranch) {
        $this->General_model->remove_branch_godown($id);
        $this->session->set_flashdata('save_data', 'Godown Removed');
        return redirect('Master/billing_godown/' . $idbranch);
    }

    public function getbackup() {
        $this->load->dbutil();
        $prefs = array(
            'format' => 'zip',
            'filename' => 'my_db_backup.sql'
        );
        $backup = & $this->dbutil->backup($prefs);
        $db_name = 'erp backup-on-' . date("d-m-Y") . '.zip';
        $save = site_url() . 'DB_Backup/' . $db_name;
        $this->load->helper('file');
        write_file($save, $backup);
        $this->load->helper('download');
        force_download($db_name, $backup);
    }
    
    //=================== PRINT HEAD ==================================
    public function print_head(){
        $q['tab_active'] = '';
        $q['print_head_data'] = $this->General_model->get_print_head_data();
        $this->load->view('master/print_head', $q);
    }
    public function save_print_head(){
        $config = array(
            'image_library' => 'gd2',
            'upload_path' => 'assets/print_head',
            'allowed_types' =>'jpg|jpeg|gif|png|jfif|pdf|doc|docx',
            'file_name' => $_FILES['userfile']['name'],
        );
        $this->load->library('upload',$config);
        $this->upload->initialize($config);
        if($this->upload->do_upload('userfile')){
            $uploadData = $this->upload->data();
            $imgfile = $uploadData['file_name'] ;
        }else{
            $imgfile = NULL;
        }
        
        $data = array(
            'company_logo' => 'assets/print_head/'.$imgfile,
            'company_name' => $this->input->post('company_name'),
            'company_address' => $this->input->post('company_address'),
            'head_date' => date('Y-m-d'),
        );
        $this->General_model->save_print_head($data);
        $this->session->set_flashdata('save_data', 'Company Print Head Created');
        return redirect('Master/print_head');
        
    }
    public function edit_print_head(){
        $id = $this->input->post('idprinthead');
        if( $_FILES['userfile']['name'] != '' || $_FILES['userfile']['name'] != NULL){
            $config = array(
                'image_library' => 'gd2',
                'upload_path' => 'assets/print_head',
                'allowed_types' =>'jpg|jpeg|gif|png|jfif|pdf|doc|docx',
                'file_name' => $_FILES['userfile']['name'],
            );
            $this->load->library('upload',$config);
            $this->upload->initialize($config);
            if($this->upload->do_upload('userfile')){
                $uploadData = $this->upload->data();
                $imgfile = 'assets/print_head/'.$uploadData['file_name'] ;
                
                $path = $this->input->post('oldlogo');
                unlink($path);
            }else{
                $imgfile = NULL;
            }
        }else{
            $imgfile = $this->input->post('oldlogo');
        }
        
        $data = array(
            'company_logo' => $imgfile,
            'company_name' => $this->input->post('company_name'),
            'company_address' => $this->input->post('company_address'),
            'head_date' => date('Y-m-d'),
        );
        $this->General_model->update_print_head($data, $id);
        $this->session->set_flashdata('save_data', 'Company Print Head Updated');
        return redirect('Master/print_head');
    }
     public function apple_dms_report() {
        $q['tab_active'] = 'DMS Report';
        $this->load->view('report/dms_report', $q);
    }
    
    public function isl_file() {
        $this->load->helper('download');
        $this->load->helper('file');
        $fromdate = $this->input->post('fromdate');
        $f = date('Y-m-d', strtotime($fromdate));
        $today = date('Ymd', strtotime($fromdate));
        $uniq = date('Ymdhsm', strtotime($fromdate));
        $inward_data = $this->General_model->inward_product_bydate($f);
        if (file_exists('sale_files/2119880_EU_ISL_'.$today.'01.txt')) {
            $path = 'sale_files/2119880_EU_ISL_'.$today.'01.txt';
            unlink($path);
            $log = new Logging();
            $log->lfile('sale_files/2119880_EU_ISL_'.$today.'01.txt');
            $log->lwrite("CTRL\t2119880\t060704780001000\tISL\t" . $uniq . "\t" . $uniq . "\tSSCOMMUNICATION\tIN\tUTF-8");
            $log->lwrite("HDR\t" . $today . "\t2119880\tT2");
            $imei = "\t";
            $inward_date = "";
            $serial = "\t";
            foreach ($inward_data as $inw) {
                 if($inw->part_number ==NULL && $inw->part_number==''){
                    
                }else{
                if ($inw->idskutype == 1) {
                    $imei = $inw->imei_no;
                    $serial = "\t";
                } else {
                    $serial = trim(ltrim($inw->imei_no, 'S'));
                    $imei = "\t";
                }
                $receive_date = date('Ymd', strtotime($inw->date));
                $inv_date = date('Ymd', strtotime($inw->vendor_invoice_date));
                $log->lwrite("DTL\t" . $inw->part_number . "\t" . $imei . "\t" . $serial . "\tKOLHAPUR\t" . $inward_date . "\t" . $receive_date . "\tINV/" . $inw->financial_year . "/" . $inw->id_inward . "\t" . $inv_date);
            }
            }
            $log->lclose();
            $FileName = base_url().'sale_files/2119880_EU_ISL_'.$today.'01.txt';
            $name = '2119880_EU_ISL_'.$today.'01.txt';
            header('Content-disposition: attachment; filename="'.$name.'"');
            ob_end_clean();
            readfile($FileName);
//            exit;
        } else {
            $log = new Logging();
            $log->lfile('sale_files/2119880_EU_ISL_' . $today . '01.txt');
            $log->lwrite("CTRL\t2119880\t060704780001000\tISL\t" . $uniq . "\t" . $uniq . "\tSSCOMMUNICATION\tIN\tUTF-8");
            $log->lwrite("HDR\t" . $today . "\t2119880\tT2");
            $imei = "\t";
            $inward_date = "";
            $serial = "\t";
            foreach ($inward_data as $inw) {
                 if($sale->part_number ==NULL && $sale->part_number==''){
                    
                }else{
                if ($inw->idskutype == 1) {
                    $imei = $inw->imei_no;
                    $serial = "\t";
                } else {
                    $serial = trim(ltrim($inw->imei_no, 'S'));
                    $imei = "\t";
                }
                $receive_date = date('Ymd', strtotime($inw->date));
                $inv_date = date('Ymd', strtotime($inw->vendor_invoice_date));
                $log->lwrite("DTL\t" . $inw->part_number . "\t" . $imei . "\t" . $serial . "\tKOLHAPUR\t" . $inward_date . "\t" . $receive_date . "\tINV/" . $inw->financial_year . "/" . $inw->id_inward . "\t" . $inv_date);
            }
            }
            $log->lclose();
            $FileName = base_url() . 'sale_files/2119880_EU_ISL_' . $today . '01.txt';
            $name = '2119880_EU_ISL_' . $today . '01.txt';
            header('Content-disposition: attachment; filename="' . $name . '"');
            ob_end_clean();
            readfile($FileName);
        }
    }

    public function osl_file() {
        $fromdate = $this->input->post('fromdate');
        $f = date('Y-m-d', strtotime($fromdate));
        $today = date('Ymd', strtotime($fromdate));
        $uniq = date('Ymdhsm', strtotime($fromdate));
        $sale_data = $this->General_model->sale_product_bydate($f);
        if (file_exists('sale_files/2119880_EU_OSL_' . $today . '01.txt')) {
            $path = 'sale_files/2119880_EU_OSL_' . $today . '01.txt';
            unlink($path);
            $log = new Logging();
            $log->lfile('sale_files/2119880_EU_OSL_' . $today . '01.txt');
            $log->lwrite("CTRL\t2119880\t060704780001000\tOSL\t" . $uniq . "\t" . $uniq . "\tSSCOMMUNICATION\tIN\tUTF-8");
            $log->lwrite("HDR\t" . $today . "\t2119880\tT2");
            $imei = "\t";
            $serial = "\t";
            $orderdate = "";
            $shippingdata = "";
            $hqid = "";
            foreach ($sale_data as $sale) {
                if($sale->part_number ==NULL && $sale->part_number==''){
                    
                }else{
                    if ($sale->idskutype == 1) {
                        $imei = $sale->imei_no;
                        $serial = "\t";
                    } else {
                        $serial = trim(ltrim($sale->imei_no, 'S'));
                        $imei = "\t";
                    }
                    $inv_date = date('Ymd', strtotime($sale->date));
                    $log->lwrite("DTL\t" . $sale->part_number . "\t" . $imei . "\t" . $serial . "\t" . $orderdate . "\t" . $shippingdata . "\t" . $hqid . "\t" . $sale->inv_no . "\t" . $inv_date . "\t" . $sale->apple_store_id . "\t" . '9');
                }                
            }
            $log->lclose();
            $FileName = base_url() . 'sale_files/2119880_EU_OSL_' . $today . '01.txt';
            $name = '2119880_EU_OSL_' . $today . '01.txt';
            header('Content-disposition: attachment; filename="' . $name . '"');
            ob_end_clean();
            readfile($FileName);
        } else {
            $log = new Logging();
            $log->lfile('sale_files/2119880_EU_OSL_' . $today . '01.txt');
            $log->lwrite("CTRL\t2119880\t060704780001000\tOSL\t" . $uniq . "\t" . $uniq . "\tSSCOMMUNICATION\tIN\tUTF-8");
            $log->lwrite("HDR\t" . $today . "\t2119880\tT2");
            $imei = "\t";
            $serial = "\t";
            $orderdate = "";
            $shippingdata = "";
            $hqid = "";
            foreach ($sale_data as $sale) {
                 if($sale->part_number ==NULL && $sale->part_number==''){
                    
                }else{
                if ($sale->idskutype == 1) {
                    $imei = $sale->imei_no;
                    $serial = "\t";
                } else {
                    $serial = trim(ltrim($sale->imei_no, 'S'));
                    //$serial1 = trim($serial);
                    $imei = "\t";
                }
                $inv_date = date('Ymd', strtotime($sale->date));
                $log->lwrite("DTL\t" . $sale->part_number . "\t" . $imei . "\t" . $serial . "\t" . $orderdate . "\t" . $shippingdata . "\t" . $hqid . "\t" . $sale->inv_no . "\t" . $inv_date . "\t" . $sale->apple_store_id . "\t" . '9');
            }
            }
            $log->lclose();
            $FileName = base_url() . 'sale_files/2119880_EU_OSL_' . $today . '01.txt';
            $name = '2119880_EU_OSL_' . $today . '01.txt';
            header('Content-disposition: attachment; filename="' . $name . '"');
            ob_end_clean();
            readfile($FileName);
            
        }
    }

    public function sale_return_file() {
        $fromdate = $this->input->post('fromdate');
        $f = date('Y-m-d', strtotime($fromdate));
        $today = date('Ymd', strtotime($fromdate));
        $uniq = date('Ymdhsm', strtotime($fromdate));
        $salereturn_data = $this->General_model->sale_return_product_bydate($f);
        if (file_exists('sale_files/2119880_EU_RET_' . $today . '01.txt')) {
            $path = 'sale_files/2119880_EU_RET_' . $today . '01.txt';
            unlink($path);
            $log = new Logging();
            $log->lfile('sale_files/2119880_EU_RET_' . $today . '01.txt');
            $log->lwrite("CTRL\t2119880\t060704780001000\tRET\t" . $uniq . "\t" . $uniq . "\tSSCOMMUNICATION\tIN\tUTF-8");
            $log->lwrite("HDR\t" . $today . "\t2119880\tT2");
            $imei = "\t";
            $serial = "\t";
            $returndate = "\t";
            $doareturn = "\t";
            $purchase_return = "\t";
            foreach ($salereturn_data as $sale) {
                 if($sale->part_number ==NULL && $sale->part_number==''){
                    
                }else{
                if ($sale->idskutype == 1) {
                    //if (ctype_digit($sale->imei_no)) {
                    $imei = $sale->imei_no;
                    $serial = "\t";
                } else {
                    $serial = trim(ltrim($sale->imei_no, 'S'));
                    //$serial1 = trim($serial);
                    $imei = "\t";
                }
                $inv_date = date('Ymd', strtotime($sale->date));
                $log->lwrite("DTL\t" . $sale->part_number . "\t" . $imei . "\t" . $serial . "\t" . $returndate . "\t" . $doareturn . "\t" . $purchase_return . "\t" . $inv_date);
            }
            }
            $log->lclose();

            $FileName = base_url() . 'sale_files/2119880_EU_RET_' . $today . '01.txt';
            $name = '2119880_EU_RET_' . $today . '01.txt';
            header('Content-disposition: attachment; filename="' . $name . '"');
            ob_end_clean();
            readfile($FileName);
        } else {
            $log = new Logging();
            $log->lfile('sale_files/2119880_EU_RET_' . $today . '01.txt');
            $log->lwrite("CTRL\t2119880\t060704780001000\tRET\t" . $uniq . "\t" . $uniq . "\tSSCOMMUNICATION\tIN\tUTF-8");
            $log->lwrite("HDR\t" . $today . "\t2119880\tT2");
            $imei = "\t";
            $serial = "\t";
            $returndate = "\t";
            $doareturn = "\t";
            $purchase_return = "\t";
            foreach ($salereturn_data as $sale) {
                 if($sale->part_number ==NULL && $sale->part_number==''){
                    
                }else{
                if ($sale->idskutype == 1) {
                    //if (ctype_digit($sale->imei_no)) {
                    $imei = $sale->imei_no;
                    $serial = "\t";
                } else {
                    $serial = trim(ltrim($sale->imei_no, 'S'));
                    //$serial1 = trim($serial);
                    $imei = "\t";
                }
                $inv_date = date('Ymd', strtotime($sale->date));
                $log->lwrite("DTL\t" . $sale->part_number . "\t" . $imei . "\t" . $serial . "\t" . $returndate . "\t" . $doareturn . "\t" . $purchase_return . "\t" . $inv_date);
            }
            }
            $log->lclose();

            $FileName = base_url() . 'sale_files/2119880_EU_RET_' . $today . '01.txt';
            $name = '2119880_EU_RET_' . $today . '01.txt';
            header('Content-disposition: attachment; filename="' . $name . '"');
            ob_end_clean();
            readfile($FileName);
        }
    }
    
    public function partner_type(){
        $q['tab_active'] = '';
        $q['partner_type_data'] = $this->General_model->get_partner_type_data();
        $this->load->view('master/partner_type', $q);
    }
    public function save_partner_type(){
        $data = array(
            'partner_type' => $this->input->post('partner'),
        );
        $this->General_model->save_partner_type($data);
        $this->session->set_flashdata('save_data', 'Partner Type Created Successfully');
        redirect('Master/partner_type');
    }
    public function edit_partner_type(){
        $id = $this->input->post('id');
        $data = array(
            'partner_type' => $this->input->post('partner1'),
        );
        $this->General_model->edit_partner_type($data, $id);
        $this->session->set_flashdata('save_data', 'Partner Type Updated Successfully');
        redirect('Master/partner_type');
    }
    public function sale_time_slots(){
        $q['tab_active'] = '';
        $q['time_slots_data'] = $this->General_model->get_time_slot_data();
        $this->load->view('master/sale_time_slots', $q);
    }
    public function save_time_slots(){
        $data = array(
            'slot_name' => $this->input->post('slot_name'),
            'min_slot' => $this->input->post('from'),
            'max_slot' => $this->input->post('to'),
            'active' => $this->input->post('status'),
        );
        $this->General_model->save_time_slot($data);
        $this->session->set_flashdata('save_data', 'Time Slots Created Successfully');
        redirect('Master/sale_time_slots');
    }
     public function edit_time_slots(){
        $id = $this->input->post('id');
        $data = array(
            'slot_name' => $this->input->post('slot_name1'),
            'min_slot' => $this->input->post('from1'),
            'max_slot' => $this->input->post('to1'),
            'active' => $this->input->post('status1'),
        );
        $this->General_model->edit_time_slot($data, $id);
        
        $this->session->set_flashdata('save_data', 'Time Slots Updated Successfully');
        redirect('Master/sale_time_slots');
    }
    
    public function ajax_get_user_data_byidrole(){
        $idrole = $this->input->post('idrole');
        $user_data = $this->General_model->get_user_details_byidrole($idrole);
//        die(print_r($user_data)); 
        if($user_data){
        ?>
        <table id="user_data" class="table table-condensed table-full-width table-bordered table-responsive table-hover">
            <thead>
                <th>Sr</th>
                <th>User Name</th>
                <th>User Id</th>
                <th>Branch</th>
                <th>User Role</th>
                <th>User Contact</th>
                <th>Password</th>
                <th>Level<br><small>1=admin, 2=idbranch, 3=multiple Branches</small></th>
                <th>Status</th>
                <th>Edit</th>
            </thead>
            <tbody class="data_1">
                <?php $i=1; foreach ($user_data as $user){  
                    if($this->session->userdata('level') == 2){ 
                        if($user->iduserrole == 17 && $user->idbranch == $_SESSION['idbranch']) { ?>
                            <tr>
                                <td><?php echo $i++;?></td>
                                <td><?php echo $user->user_name; ?></td>
                                <td><?php echo $user->userid; ?></td>
                                <td><?php if($user->level==1){ echo 'All Branches'; }elseif($user->level==3){ echo 'Multi Branches'; }else{ echo $user->branch_name; } ?></td>
                                <td><?php echo $user->role; ?></td>
                                <td><?php echo $user->user_contact; ?></td>
                                <td><?php echo $user->user_password; ?></td>
                                <td><?php echo $user->level; ?></td>
                                <td><?php  if($user->active == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></td>
                                <td>
                                    <a class="thumbnail btn-link waves-effect" href="<?php echo base_url()?>Master/edit_user_details/<?php echo $user->id_users;?>"  style="margin: 0" >
                                        <span class="mdi mdi-pen text-primary fa-lg"></span>
                                    </a>
            <!--                        <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                                        <span class="mdi mdi-pen text-primary fa-lg"></span>
                                    </a>-->
                                    <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form>
                                                <div class="modal-body">
                                                    <div class="thumbnail">
                                                        <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit User</h4></center><hr>
                                                        <label class="col-md-4">Branch</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" disabled="" value="<?php echo $user->branch_name ?>" />
                                                        </div><div class="clearfix"></div><br>
                                                        <label class="col-md-4">User Role</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" disabled="" value="<?php echo $user->role ?>" />
                                                        </div><div class="clearfix"></div><br>
                                                        <label class="col-md-4">User Name</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" name="full_name" value="<?php echo $user->user_name ?>" placeholder="Enter User Name" required=""/>
                                                        </div><div class="clearfix"></div><br>
                                                        <label class="col-md-4">User Id</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" name="name" value="<?php echo $user->userid ?>"  placeholder="Enter User Id" required=""/>
                                                        </div><div class="clearfix"></div><br>
                                                        <label class="col-md-4">Password</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" name="password" value="<?php echo $user->user_password ?>" required="" placeholder="Enter Password" />
                                                        </div><div class="clearfix"></div><br>
                                                        <label class="col-md-4">Contact</label>
                                                        <div class="col-md-8">
                                                            <input type="text" class="form-control" name="contact" value="<?php echo $user->user_contact ?>"  placeholder="Enter Contact" required="" />
                                                        </div><div class="clearfix"></div><br>
                                                        <label class="col-md-4">Status</label>
                                                        <div class="col-md-8">
                                                            <select class="select form-control" name="status">
                                                                <option value="<?php echo $user->active ?>"><?php if($user->active == 1){ echo 'Active'; } elseif($user->active == 0){ echo 'In Active'; } ?></option>
                                                                <option value="1">Active</option>
                                                                <option value="0">Inactive</option>
                                                            </select>
                                                        </div><div class="clearfix"></div><hr>
                                                        <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                                    <button type="submit" value="<?php echo $user->id_users  ?>" name="id"  formmethod="POST" formaction="<?php echo base_url('Master/edit_user') ?>" class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
                                                    </div>

                                                </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php } }else{ ?>
                            <tr>
                                <td><?php echo $i++;?></td>
                                <td><?php echo $user->user_name; ?></td>
                                <td><?php echo $user->userid; ?></td>
                                <td><?php if($user->level==1){ echo 'All Branches'; }elseif($user->level==3){ echo 'Multi Branches'; }else{ echo $user->branch_name; } ?></td>
                                <td><?php echo $user->role; ?></td>
                                <td><?php echo $user->user_contact; ?></td>
                                <td><?php echo $user->user_password; ?></td>
                                <td><?php echo $user->level; ?></td>
                                <td><?php  if($user->active == 1){ echo 'Active'; } else{ echo 'In Active'; } ?></td>
                                <td>
                                    <a class="thumbnail btn-link waves-effect" href="<?php echo base_url()?>Master/edit_user_details/<?php echo $user->id_users;?>"  style="margin: 0" >
                                        <span class="mdi mdi-pen text-primary fa-lg"></span>
                                    </a>
            <!--                        <a class="thumbnail btn-link waves-effect" href="" data-toggle="modal" data-target="#edit<?php echo $i ?>" style="margin: 0" >
                                        <span class="mdi mdi-pen text-primary fa-lg"></span>
                                    </a>-->
                                    <div class="modal fade" id="edit<?php echo $i ?>" style="z-index: 999999;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form>
                                            <div class="modal-body">
                                                <div class="thumbnail">
                                                    <center><h4><span class="pe pe-7s-news-paper" style="font-size: 28px"></span> Edit User</h4></center><hr>
                                                    <label class="col-md-4">Branch</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" disabled="" value="<?php echo $user->branch_name ?>" />
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-4">User Role</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" disabled="" value="<?php echo $user->role ?>" />
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-4">User Name</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="full_name" value="<?php echo $user->user_name ?>" placeholder="Enter User Name" required=""/>
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-4">User Id</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="name" value="<?php echo $user->userid ?>"  placeholder="Enter User Id" required=""/>
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-4">Password</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="password" value="<?php echo $user->user_password ?>" required="" placeholder="Enter Password" />
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-4">Contact</label>
                                                    <div class="col-md-8">
                                                        <input type="text" class="form-control" name="contact" value="<?php echo $user->user_contact ?>"  placeholder="Enter Contact" required="" />
                                                    </div><div class="clearfix"></div><br>
                                                    <label class="col-md-4">Status</label>
                                                    <div class="col-md-8">
                                                        <select class="select form-control" name="status">
                                                            <option value="<?php echo $user->active ?>"><?php if($user->active == 1){ echo 'Active'; } elseif($user->active == 0){ echo 'In Active'; } ?></option>
                                                            <option value="1">Active</option>
                                                            <option value="0">Inactive</option>
                                                        </select>
                                                    </div><div class="clearfix"></div><hr>
                                                    <a href="#edit<?php echo $i ?>" class="pull-left btn btn-warning waves-effect waves-teal" data-toggle="modal">Close</a>
                                                <button type="submit" value="<?php echo $user->id_users  ?>" name="id"  formmethod="POST" formaction="<?php echo base_url('Master/edit_user') ?>" class="btn btn-info pull-right waves-effect"><span class=""></span> Save</button><div class="clearfix"></div>
                                                </div>

                                            </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                </td>
                            </tr>
                <?php } } ?>
            </tbody>
        </table>
        <?php 
        }
    }
    
    public function price_category_slabs(){
        $q['tab_active'] = '';
        $q['price_data'] = $this->Report_model->get_price_category_lab_data();
        $this->load->view('master/price_category_slab', $q);
    }
    public function save_price_category_slab(){
         
//        die(print_r($_POST));
        $data = array(
            'lab_name' => $this->input->post('slabname'),
            'min_lab' => $this->input->post('minslab'),
            'max_lab' => $this->input->post('maxslab'),
            'active' => $this->input->post('status'),
        );
        $this->General_model->save_price_category_slab($data);
        $this->session->set_flashdata('save_data', 'Price Category Slab Created Successfully');
        redirect('Master/price_category_slabs');
    }
    public function edit_price_category_slab(){
//        die(print_r($_POST));
        $id = $this->input->post('id');
        $data = array(
            'lab_name' => $this->input->post('slabname1'),
            'min_lab' => $this->input->post('minslab1'),
            'max_lab' => $this->input->post('maxslab1'),
            'active' => $this->input->post('status1'),
        );
        $this->General_model->update_price_category_slab($data, $id);
        
        $this->session->set_flashdata('save_data', 'Price Category Slab Updated Successfully');
        redirect('Master/price_category_slabs');
    }
    
    ///////// VENDOR SKUs        ////////
     
    public function vendors_sku_details() { 
        $q['tab_active'] = '';      
        $q['sku_data'] = $this->General_model->get_vendor_sku_data();
        $q['vendor_data'] = $this->General_model->get_vendor_data();
        $this->load->view('master/vendors_sku_details',$q);
    }
    public function save_vendors_sku() {
        $data = array(
            'vendor_name' => $this->input->post('sku_name'),
            'column_name' => $this->input->post('sku_col_name')            
        );
        $this->General_model->save_vendor_sku($data,$this->input->post('sku_col_name'));        
        $this->session->set_flashdata('save_data', 'Vendor SKU Created');
        return redirect('Master/vendors_sku_details');
    }
    
    public function model_vendor_sku_update(){   
        $q['tab_active'] = '';     
        $q['sku_data'] = $this->General_model->get_vendor_sku_data();
        $q['model_data'] = $this->General_model->get_recent_models();
        $q['product_category'] = $this->General_model->get_product_category_data();
        $q['brand_data'] = $this->General_model->get_active_brand_data();  
        
        $this->load->view('master/model_vendor_sku_update',$q);        
    }
    
    public function ajax_get_model_bycategory_sku() {
       $sku_column=$this->input->post('vendors_sku');
       
        $model_data = $this->General_model->ajax_get_active_model_by_PCB($this->input->post('category'),$this->input->post('brand'),$this->input->post('product_category'));
        
        ?>
        <thead>
            <th>Sr</th>            
            <th style="display: none">Idvariant</th>  
            <th>Product Type</th>            
            <th>Brand</th>
            <th>Model</th>            
                  <th>Part Number</th>            
            <th class="col-md-4">SKU</th>           
            <th>Action</th>
        </thead>
        <tbody class="data_1">
            <?php $i=1; foreach ($model_data as $model){ ?>
            <tr>
                <td><?php echo $i;?></td>                
                <td style="display: none"><?php echo $model->id_variant; ?></td>
                <td><?php echo $model->product_category_name; ?></td>                
                <td><?php echo $model->brand_name; ?></td>
                <td><?php echo $model->full_name; ?></td>
                 <td><?php echo $model->part_number; ?></td>
                <form class="model_price_submit_form">                    
                    <input type="hidden" name="ids" class="form-control input-sm" value="<?php echo $model->id_variant.'_'.$model->idmodel.'_'.$model->idbrand.'_'.$model->idcategory.'_'.$model->idproductcategory; ?>" />
                    <input type="hidden" name="sku_column" class="form-control input-sm" value="<?php echo $sku_column; ?>" />
                            
                    <td><div class="myDiv1" style="display: none"><input type="text" name="sku" class="sku form-control input-sm" value="<?php echo $model->$sku_column; ?>" /></div><div class="sku myDiv2"><?php echo $model->$sku_column; ?></div></td>
                    <td><div class="myDiv1" style="display: none"><button type="button" class="btn btn-sm btn-primary waves-effect waves-ripple save" name="id_model" value="<?php echo $model->idmodel ?>" style="margin: 0; text-transform: capitalize">Submit</button></div><div class="myDiv2"><a class="hide-btn btn btn-outline-info btn-sm waves-effect waves-ripple" style="margin: 0; text-transform: capitalize"> Edit</a></div></td>
                </form>
            </tr>
            <?php $i++; } ?>
        </tbody>
    
    <?php 
    }
    
    public function save_bulk_sku_update(){
        $sku_column=$this->input->post('vendors_sku');
        $q['tab_active'] = '';         
        $this->db->trans_begin();
        $timestamp = time();
        $i =0;
        $filename=$_FILES["uploadfile"]["tmp_name"];
        if($_FILES["uploadfile"]["size"] > 0){
            $updateArray = array();
            $file = fopen($filename, "r");
            while (($openingdata = fgetcsv($file, 10000, ",")) !== FALSE) {
                if($i > 0){ 
                    //Model_variant table
                    if($openingdata[5]!=''){
                        $updateArray[] = array(
                            'id_variant' => $openingdata[1],
                            $sku_column => $openingdata[5],                        
                            'm_variant_lmt' => $timestamp,
                            'm_variant_lmb' => $_SESSION['id_users']
                        );                    
                    }
                }
                $i++;
            }
            fclose($file);            
            $this->General_model->update_model_variants_byidvariant_bulk($updateArray);            
        }
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $this->session->set_flashdata('save_data', 'Excel Upload is aborted. Try again with same details');
        }else{
            $this->db->trans_commit();
            $this->session->set_flashdata('save_data', 'Model SKU update succesfully');
            redirect('master/model_vendor_sku_update');
        }
        
    }
    
    public function save_sku_update(){
        $this->db->trans_begin();
        $ids = explode("_", $this->input->post('ids'));
        $sku = $this->input->post('sku');
        $sku_column=  $this->input->post('sku_column');
        $updateArray = array();
        $timestamp = time();
        $id_variant = $ids[0];
        $updateArray[] = array(
                        'id_variant' => $id_variant,
                        $sku_column => $sku,                        
                        'm_variant_lmt' => $timestamp,
                        'm_variant_lmb' => $_SESSION['id_users']
                    ); 
        $this->General_model->update_model_variants_byidvariant_bulk($updateArray);
        if ($this->db->trans_status() === FALSE){
            $this->db->trans_rollback();
            $q['result'] = 'no';
        }else{
            $this->db->trans_commit();
            $q['result'] = 'yes';
        }
        echo json_encode($q);
    }
    
    ///////// BILING MODES ////////
    
    public function billing_mode_details() { 
        $q['tab_active'] = '';      
        $q['billing_mode_data'] = $this->General_model->get_billing_mode_data();        
        $this->load->view('master/billing_mode_details',$q);
    }
    public function save_billing_mode() {
        $data = array(
            'billing_mode_name' => $this->input->post('billing_mode_name'),
            'billing_mode_column_name' => $this->input->post('billing_mode_col_name')            
        );
        $this->General_model->save_billing_modes($data,$this->input->post('billing_mode_col_name'));        
        $this->session->set_flashdata('save_data', 'Billing Mode Created');
        return redirect('Master/billing_mode_details');
    }
    
    public function branch_billing_mode_configuration() {        
        $q['tab_active'] = '';
        $q['billing_mode_data'] = $this->General_model->get_billing_mode_data(); 
        $q['branch_data'] = $this->General_model->get_branchandwarehouse_data();
        
        $this->load->view('master/branch_billing_mode_configuration', $q);
    }
    public function save_billing_mode_configuration() {
        
        $mode = $this->input->post('mode');
        $count = count($mode);
        $cnt = 1;        
        $update_data = array();
        
        foreach ($mode as $key=>$value) {
            $d=null;
                $d['id_branch'] = $key;
                   
                foreach ($value as $k=>$v){
                    $d[$k] = 1;
                    
            }
            $update_data[]=$d;
            $cnt++;
        }
//        die('<pre>'.print_r($update_data,1).'</pre>');
        
        
        $this->General_model->update_branch_byid_branch_bulk($update_data);
        $this->session->set_flashdata('save_data', 'Billing Configuration Updated Successfully');
        return redirect('Master/branch_billing_mode_configuration');
    }
    
    
    //******* Apple WEBGDV Report ***************//
    
    public function apple_webgdv_report() {
        $q['tab_active'] = 'DMS Report';
        $this->load->view('report/apple_webgdv_report', $q);
    }
    public function ajax_apple_webgdv_report(){
        $from = $this->input->post('fromdate');
        $to = $this->input->post('todate');
        
        $report_data = $this->General_model->get_apple_webgdv_report_data($from, $to);
//        die('<pre>'.print_r($report_data,1).'</pre>');
        if($report_data){ ?> 
        <table class="table table-bordered" id="apple_webgdv_report">
            <thead style="background-color: #99ccff" class="fixheader"> 
                <th>Branch</th>
                <th>Store Code</th>
                <th>Model Name</th>
                <th>Model (SKU)</th>
                <th>Sale</th>
                <th>Return</th>
                <th>Stock</th>
            </thead>
            <tbody class="data_1">
                <?php $sale=0; $ret=0; $stk=0; 
                foreach ($report_data as $rdata){ 
                    if($rdata->sale_qty){ $sale = $rdata->sale_qty;}else{ $sale = 0;}
                    if($rdata->ret_qty){ $ret = $rdata->ret_qty;}else{ $ret = 0;}
                    if($rdata->stock_qty){ $stk = $rdata->stock_qty + $rdata->intrastock_qty;}else{ $stk = 0;}
                    if($sale != 0 || $stk != 0){?>
                        <tr>
                            <td><?php echo $rdata->branch_name; ?></td>
                            <td><?php echo $rdata->apple_store_id; ?></td>
                            <td><?php echo $rdata->full_name; ?></td>
                            <td><?php echo $rdata->part_number; ?></td>
                            <td><?php echo $sale; ?></td>
                            <td><?php echo $ret; ?></td>
                            <td><?php echo $stk; ?></td>
                        </tr>
                <?php } }?>
            </tbody>
        </table>
        <?php }
    }
    
    //Excel download apple webgdv report
     public function apple_webgdv_weekly_report() {
        $q['tab_active'] = 'Apple Webgdv Report';
        $this->load->view('report/apple_webgdv_weekly_report', $q);
    }
    public function ajax_apple_webgdv_weekly_report(){
        
        $fromdate = $this->input->post('fromdate');
        $f = date('d-m-Y', strtotime($fromdate));

        if (file_exists('apple_webgdv/apple_webgdv_report'.$f.'.csv')) {
            $FileName = base_url().'apple_webgdv/apple_webgdv_report'.$f.'.csv';
            $name = 'apple_webgdv_report'.$f.'.csv';
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-disposition: attachment; filename="'.$name.'"');
            header('Pragma: public');
            ob_end_clean();
            readfile($FileName);
            
        } else { 
               $this->session->set_flashdata('alert_dms_data', 'File Not Found');
                return redirect('Master/apple_webgdv_weekly_report');
        }
        
    }
    
    //****** Credit & Custudy ***************//
     public function branch_credit_limit() {
        $q['tab_active'] = '';
        $q['branch_data'] = $this->General_model->get_branch_data();
        $this->load->view('master/branch_credit_limit', $q);
    }
    public function update_branch_credit_limit(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $idbranch = $this->input->post('idbranch');
        $credit_days = $this->input->post('credit_days');
        $credit_limt = $this->input->post('credit_limt');
        for($i=0; $i<count($idbranch); $i++){
            $data = array(
                'credit_limit' => $credit_limt[$i],
                'credit_days' => $credit_days[$i],
            );
            $this->General_model->edit_db_branch($idbranch[$i],$data);
        }
        $this->session->set_flashdata('save_data', 'Branch Credit/Custudy data Updated Successfully');
        return redirect('Master/branch_credit_limit');
    }
    
    public function branch_payment_mode_onoff() {
        $q['tab_active'] = '';
        $q['branch_data'] = $this->General_model->get_branch_data();
        $q['paymenthead_data'] = $this->General_model->get_active_payment_head();
        $q['branch_paymenthead_data'] = $this->General_model->get_branch_payment_head_data();
        $this->load->view('master/branch_payment_mode_configuration', $q);
    }
    public function update_branch_paymenthead_configuration(){
        
        $pmodes = $this->General_model->get_active_payment_head();
        
        $mode = $this->input->post('mode');
        $idbranch = $this->input->post('idbranch');
        
        for($i=0; $i<count($idbranch); $i++){
            foreach ($pmodes as $pm){
                $idb = $idbranch[$i];
                if(isset($mode[$idb][$pm->payment_head])){
                    $data[] = array(
                        'idbranch' => $idbranch[$i],
                        'idhead' => $pm->id_paymenthead,
                        'date' => date('Y-m-d'),
                        'created_by' => $_SESSION['id_users']
                    );
                }
               
            }
        }
        if(count($data) > 0){
            $this->General_model->delete_branch_phead_configuration();
            $this->General_model->save_branch_phead_configuration($data);
        }
        
        $this->session->set_flashdata('save_data', 'Payment Mode Configuration Updated Successfully');
        return redirect('Master/branch_payment_mode_onoff');
    }
    public function vendor_create_interior(){
        if($this->uri->segment(2)){
            if($this->uri->segment(2)=='NEW'){
                $q['vendor_details']=2;
            }else{
                $q['vendor_details']  = $this->common_model->getSingleRow('vendor',array('vendor_type'=>2,'id_vendor'=>$this->uri->segment(2)));
            }
        }
        $q['vendor_data'] = $this->common_model->getRecords('vendor','*',array('vendor_type'=>2));

        $q['tab_active'] = '';
        $this->load->view('Master/vendor_create_interior',$q);
    }
 public function vendor_create_insurence(){
        if($this->uri->segment(2)){
            if($this->uri->segment(2)=='NEW'){
                $q['vendor_details']=2;
            }else{
                $q['vendor_details']  = $this->common_model->getSingleRow('vendor',array('vendor_type'=>3,'id_vendor'=>$this->uri->segment(2)));
            }
        }
        $q['vendor_data'] = $this->common_model->getRecords('vendor','*',array('vendor_type'=>3));
        $q['tab_active'] = '';
        $this->load->view('Master/vendor_create_insurence',$q);
    }

    public function vendor_create_store(){
        $postData=$this->input->post();
        $postData['vendor_created_by'] = $this->session->userdata('id_users');

        if(isset($postData['id_vendor']) && $postData['id_vendor'] != '')
        {
            $id = $postData['id_vendor'];
            $ins= $this->common_model->updateRow('vendor', $postData, array('id_vendor'=>$postData['id_vendor']));
        }else{
           $ins=$this->common_model->insertRow($postData, 'vendor');
        }
        if($ins){
            $response['status']=true;
            $response['message']='Record Save Successfully';
        }else{
            $response['status']=false;
            $response['message']='Please try again';
        }
        echo json_encode($response);
    }
}