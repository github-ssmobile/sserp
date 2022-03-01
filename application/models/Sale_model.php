<?php

class Sale_model extends CI_Model {
    public function get_imei_history($imei) {
        return $this->db->select('imei_history.*,branch.*,b.branch_name as branch_from,godown.*,mv.full_name,imei_details_link.*,u.user_name')
                        ->where('imei_history.imei_no', $imei)
                        ->where('imei_history.idbranch= branch.id_branch')->from('branch')
                        ->where('imei_history.idvariant= mv.id_variant')->from('model_variants mv') 
                        ->where('imei_history.idgodown= godown.id_godown')->from('godown')
                        ->join('users u','u.id_users=imei_history.iduser','left')
                        ->join('branch b','b.id_branch=imei_history.transfer_from','left')
                        ->where('imei_history.idimei_details_link = imei_details_link.id_imei_details_link')->from('imei_details_link')
                        ->order_by('imei_history.id_imei_history')
                        ->get('imei_history')->result();
    }
    public function get_invoice_no_by_branch($idbranch) {
        return $this->db->where('id_branch', $idbranch)->get('branch')->row();
    }
    public function get_customer_list() {
        return $this->db->where('customer.idbranch= branch.id_branch')
                        ->where('customer.idbranch',$_SESSION['idbranch'])
                        ->from('branch')->order_by('customer.customer_fname')
                        ->get('customer')->result();
    }
    public function get_customer_list_byidbranch($idbranch) {
//        if($idbranch == ''){
//            $branches = explode(',',$viewbranches);
//        }else{
//            $branches[] = $idbranch;
//        }
        
                if($idbranch){
                    $this->db->where('customer.idbranch', $idbranch);
                }
                return  $this->db->where('customer.idbranch= branch.id_branch')->from('branch')
                 ->order_by('customer.customer_fname')
                 ->get('customer')->result();
    }
//    public function get_sale_last_day_entry_byidbranch($idbranch) {
//        $date = date('Y-m-d');
//        return $this->db->where('idbranch', $idbranch)->where('date <',$date)->order_by('id_sale','desc')->get('sale',1)->result();    
//    }
    public function get_sale_last_day_entry_byidbranch($idbranch) {
        $date = date('Y-m-d');
        return $this->db->select('sum(amount) as sum_cash, max(date) as date')->where('idbranch', $idbranch)->where('date <',$date)->order_by('id_daybook_cash','desc')->get('daybook_cash',1)->result();
    }
    public function get_cash_closure_last_entry_byidbranch($idbranch) {
        $date = date('Y-m-d');
        return $this->db->select('max(closure_cash) as closure_cash, max(date) as date')->where('idbranch', $idbranch)->where('date <',$date)->group_by('date')->order_by('id_cash_closure','desc')->get('cash_closure',1)->result();    
    }
//    public function get_sale_last_date_entry_byidbranch($idbranch) {
//        $date = date('Y-m-d');
//        return $this->db->select('date')->where('idbranch', $idbranch)->where('date <',$date)->order_by('id_daybook_cash','desc')->get('daybook_cash',1)->result();
//    }
//    public function get_cash_closure_last_entry_byidbranch($idbranch) {
//        $date = date('Y-m-d');
//        return $this->db->where('idbranch', $idbranch)->where('date <',$date)->order_by('id_cash_closure','desc')->get('cash_closure',1)->result();    
//    }
    // for closure
    public function get_total_daybook_cash_sum_byidbranch($idbranch) {
        return $this->db->select('sum(amount) as sum_daybook_cash')->where('idbranch', $idbranch)->get('daybook_cash')->result();    
    }
    public function get_daybook_cash_sum_byid($idbranch) {
        return $this->db->select('SUM(amount) as sum_cash')->where('idbranch', $idbranch)->get('daybook_cash')->result();
    }
    public function get_customer_bycontact($cust_mobile) {
        $query = "SELECT customer_contact FROM customer WHERE customer_contact LIKE '{$cust_mobile}%' LIMIT 6";
        return $this->db->query($query)->result();
    }
    public function get_product_names($name) {
        $query = "SELECT full_name FROM model_variants WHERE full_name LIKE '{$name}%' LIMIT 6";
        return $this->db->query($query)->result();
    }
    public function ajax_get_customer_bycontact($contact) {
        return $this->db->like('customer_contact', $contact, 'after')->get('customer')->result();
    }
    public function save_customer($data) {
        $this->db->insert('customer', $data);
        return $this->db->insert_id();
    }
    public function update_sale_customer($idsale, $data) {
        $this->db->where('id_sale', $idsale)->update('sale', $data);
    }
    public function get_customer_byid($idcustomer) {
        return $this->db->where('id_customer', $idcustomer)->get('customer')->result();
    }
    public function get_state_bystate_name($state_name) {
        return $this->db->where('state_name', $state_name)->get('state')->row();
    }
    public function ajax_stock_data_byimei_branch($imei, $branch){
        return $this->db->where('imei_no', $imei)->where('idbranch', $branch)
//                        ->where('stock.idgodown = godown.id_godown')->from('godown')
                        ->where('stock.idcategory = category.id_category')->from('category')
                        ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
                        ->get('stock')->result();
    }
    public function ajax_stock_data_byimei_branch_variant($imei, $branch, $idvariant){
        return $this->db->where('stock.imei_no', $imei)->where('stock.idbranch', $branch)
//                        ->where('stock.idcategory = category.id_category')->from('category')
                        ->where('idvariant', $idvariant)->get('stock')->result();
    }
    public function ajax_get_variant_byid_branch_godown($variant, $idbranch, $idgodown) {
         return $this->db->where('stock.idvariant', $variant)
                        ->where('stock.idbranch', $idbranch)
                        ->where('stock.idgodown', $idgodown)
                        ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('stock.idcategory = category.id_category')->from('category')
//                        ->where('stock.idgodown = godown.id_godown')->from('godown')
                        ->get('stock', 1)->result();
    }
    public function ajax_get_variant_byid_branch_godown_saletype($variant, $idbranch, $idgodown, $sale_type) {
        if($sale_type == 2){
            return $this->db->select('mv.full_name as product_name,mv.id_variant as idvariant, mv.idsku_type as idskutype, mv.*, category.*')
                        ->where('mv.id_variant', $variant)
                       ->where('mv.idcategory = category.id_category')->from('category')
                       ->get('model_variants mv')->result();
        }else{
            return $this->db->where('stock.idvariant', $variant)
                       ->where('stock.idbranch', $idbranch)
                       ->where('stock.idgodown', $idgodown)
                       ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
                       ->where('stock.idcategory = category.id_category')->from('category')
                       ->get('stock', 1)->result();
        }
    }
    public function save_sale($data){
        $this->db->insert('sale', $data);
        return $this->db->insert_id();
    }
    public function save_sale_product($data) {
        $this->db->insert('sale_product', $data);
        return $this->db->insert_id();
    }
    public function save_bfl($data) {
        return $this->db->insert('bfl_file_customer', $data);
    }
    public function minus_stock_byidstock($idstock, $qty) {
        return $this->db->where('id_stock', $idstock)->set('qty', 'qty - ' . $qty, false)->update('stock');
    }
    public function save_sale_payment($data) {
        $this->db->insert('sale_payment', $data);
        return $this->db->insert_id();
    }
    public function save_payment_reconciliation($data) {
        $this->db->insert('payment_reconciliation', $data);
        return $this->db->insert_id();
    }
    public function get_sale_byid($idsale) {
        return $this->db->select('customer.*,sale.*, sale.customer_fname as sale_customer_fname, sale.customer_lname as sale_customer_lname, sale.entry_time as invoice_date,users.user_name,print_head.*,branch.*')->where('id_sale',$idsale)
                        ->where('branch.idprinthead = print_head.id_print_head')->from('print_head')
                        ->where('sale.idbranch=branch.id_branch')->from('branch')
                        ->where('sale.idcustomer=customer.id_customer')->from('customer')
                        ->where('sale.idsalesperson=users.id_users')->from('users')
                        ->order_by('id_sale','desc')
                        ->get('sale')->result();
    }
    public function get_sale_product_byid($idsale) {
        return $this->db->where('idsale',$idsale)->get('sale_product')->result();
    }
    public function get_sale_payment_byid($idsale) {
        return $this->db->where('idsale',$idsale)
                        ->where('sale_payment.idpayment_mode = payment_mode.id_paymentmode')->from('payment_mode')
                        ->where('sale_payment.idpayment_head = payment_head.id_paymenthead')->from('payment_head')
                        ->get('sale_payment')->result();
        
    }
    public function get_sale_payment_byid_invoice_edit($idsale) {
        return $this->db->select('payment_mode.*,payment_head.*,sale_payment.*,payment_reconciliation.payment_receive as reconciliation_status')->where('sale_payment.idsale',$idsale)
                        ->where('sale_payment.idpayment_mode = payment_mode.id_paymentmode')->from('payment_mode')
                        ->where('sale_payment.idpayment_head = payment_head.id_paymenthead')->from('payment_head')
                        ->join('payment_reconciliation', 'payment_reconciliation.idsale_payment = sale_payment.id_salepayment', 'left')
                -> group_by('`sale_payment`.`id_salepayment`')
                        ->get('sale_payment')->result();
        
    }
    public function get_financer_of_idsale($idsale) {
        return $this->db->select('sale_payment.transaction_id, payment_mode.payment_mode')
                        ->where('idsale',$idsale)
                        ->where('sale_payment.idpayment_mode = payment_mode.id_paymentmode')->from('payment_mode')
                        ->where('sale_payment.idpayment_head = 4')
                        ->get('sale_payment')->result();
    }
    public function get_sale_data() {
        return $this->db->where('sale.idbranch=branch.id_branch')->from('branch')
                        ->where('sale.idsalesperson=users.id_users')->from('users')
                        ->order_by('id_sale','desc')
                        ->get('sale', 100)->result();
    }
    public function get_sale_byidbranch($idbranch) {
        return $this->db->where('sale.idbranch',$idbranch)
                        ->where('sale.idbranch=branch.id_branch')->from('branch')
                        ->where('sale.idsalesperson=users.id_users')->from('users')
                        ->order_by('id_sale','desc')
                        ->get('sale', 100)->result();
    }
    public function ajax_get_sales_data_byidbranch($from, $to, $idbranch, $viewbranches) {
        if($idbranch == 0){
            $branches = explode(',',$viewbranches);
        }else{
            $branches[] = $idbranch;
        }
        
        return $this->db->where('sale.date >=', $from)
                        ->where('sale.date <=', $to)
                        ->where_in('sale.idbranch',$branches)
                        ->where('sale.idbranch=branch.id_branch')->from('branch')
                        ->where('sale.idsalesperson=users.id_users')->from('users')
                        ->order_by('id_sale','desc')
                        ->get('sale')->result();
    }
    
    public function ajax_get_sales_data_byimei($imei) {
        return $this->db->where('sale_product.imei_no', $imei)
                        ->where('sale.idbranch=branch.id_branch')->from('branch')
                        ->where('sale.idsalesperson=users.id_users')->from('users')
                        ->where('sale_product.idsale = sale.id_sale')->from('sale')
                        ->order_by('sale_product.id_saleproduct','desc')
                        ->get('sale_product')->result();
    }
    
    public function ajax_get_sales_data_bycontact($contact_no) {
        return $this->db->where('sale.customer_contact', $contact_no)
                        ->where('sale.idbranch=branch.id_branch')->from('branch')
                        ->where('sale.idsalesperson=users.id_users')->from('users')
                        ->order_by('id_sale','desc')
                        ->get('sale')->result();
    }
    public function ajax_get_sales_data_byinvoice($invoice_no) {
        return $this->db->where('sale.inv_no', $invoice_no)
                        ->where('sale.idbranch=branch.id_branch')->from('branch')
                        ->where('sale.idsalesperson=users.id_users')->from('users')
                        ->order_by('id_sale','desc')
                        ->get('sale')->result();
    }
    
    public function ajax_get_sale_receivables_without_receive($idpayment_head, $idpayment_mode, $idbranch){
        $str1='';
        if($idpayment_mode != ''){
            if($idbranch == ''){
                $str1 = "SELECT sale_payment.*, customer.customer_fname,customer.customer_lname,customer.customer_contact, payment_mode.payment_mode, payment_head.valid_for_creadit_receive, branch.branch_name FROM sale_payment, payment_mode, branch, payment_head, customer where branch.id_branch = sale_payment.idbranch and customer.id_customer = sale_payment.idcustomer and payment_mode.id_paymentmode = idpayment_mode and idpayment_mode = ".$idpayment_mode." and payment_mode.idpaymenthead = payment_head.id_paymenthead order by id_salepayment desc";
            }else{
                $str1 = "SELECT sale_payment.*, customer.customer_fname,customer.customer_lname,customer.customer_contact, payment_mode.payment_mode, payment_head.valid_for_creadit_receive, branch.branch_name FROM sale_payment, payment_mode, branch, payment_head, customer where branch.id_branch = sale_payment.idbranch and customer.id_customer = sale_payment.idcustomer and payment_mode.id_paymentmode = idpayment_mode and sale_payment.idbranch = ".$idbranch." and idpayment_mode = ".$idpayment_mode." and payment_mode.idpaymenthead = payment_head.id_paymenthead order by id_salepayment desc";
            }
        }elseif($idpayment_head != ''){
            if($idbranch == ''){
                $str1 = "SELECT sale_payment.*, customer.customer_fname,customer.customer_lname,customer.customer_contact, payment_mode.payment_mode, payment_head.valid_for_creadit_receive, branch.branch_name FROM sale_payment, payment_mode, branch, payment_head, customer where branch.id_branch = sale_payment.idbranch and customer.id_customer = sale_payment.idcustomer and payment_mode.id_paymentmode = idpayment_mode and idpayment_head = ".$idpayment_head." and payment_mode.idpaymenthead = payment_head.id_paymenthead order by id_salepayment desc";
            }else{
                $str1 = "SELECT sale_payment.*, customer.customer_fname,customer.customer_lname,customer.customer_contact, payment_mode.payment_mode, payment_head.valid_for_creadit_receive, branch.branch_name FROM sale_payment, payment_mode, branch, payment_head, customer where branch.id_branch = sale_payment.idbranch and customer.id_customer = sale_payment.idcustomer and payment_mode.id_paymentmode = idpayment_mode and sale_payment.idbranch = ".$idbranch." and idpayment_head = ".$idpayment_head." and payment_mode.idpaymenthead = payment_head.id_paymenthead order by id_salepayment desc";
            }
        }
        return $this->db->query($str1)->result();
    }
    public function ajax_get_sale_receivables($idpayment_head, $idpayment_mode, $idbranch){
        $str1='';
        if($idpayment_mode != ''){
            if($idbranch == ''){
                $str1 = "SELECT sale_payment.*, customer.customer_fname,customer.customer_lname,customer.customer_contact, payment_mode.payment_mode, payment_head.valid_for_creadit_receive, branch.branch_name FROM sale_payment, payment_mode, branch, payment_head, customer where branch.id_branch = sale_payment.idbranch and customer.id_customer = sale_payment.idcustomer and payment_mode.id_paymentmode = idpayment_mode and idpayment_mode = ".$idpayment_mode." and payment_mode.idpaymenthead = payment_head.id_paymenthead and amount > received_amount order by id_salepayment desc";
            }else{
                $str1 = "SELECT sale_payment.*, customer.customer_fname,customer.customer_lname,customer.customer_contact, payment_mode.payment_mode, payment_head.valid_for_creadit_receive, branch.branch_name FROM sale_payment, payment_mode, branch, payment_head, customer where branch.id_branch = sale_payment.idbranch and customer.id_customer = sale_payment.idcustomer and payment_mode.id_paymentmode = idpayment_mode and sale_payment.idbranch = ".$idbranch." and idpayment_mode = ".$idpayment_mode." and payment_mode.idpaymenthead = payment_head.id_paymenthead and amount > received_amount order by id_salepayment desc";
            }
        }elseif($idpayment_head != ''){
            if($idbranch == ''){
                $str1 = "SELECT sale_payment.*, customer.customer_fname,customer.customer_lname,customer.customer_contact, payment_mode.payment_mode, payment_head.valid_for_creadit_receive, branch.branch_name FROM sale_payment, payment_mode, branch, payment_head, customer where branch.id_branch = sale_payment.idbranch and customer.id_customer = sale_payment.idcustomer and payment_mode.id_paymentmode = idpayment_mode and idpayment_head = ".$idpayment_head." and payment_mode.idpaymenthead = payment_head.id_paymenthead and amount > received_amount order by id_salepayment desc";
            }else{
                $str1 = "SELECT sale_payment.*, customer.customer_fname,customer.customer_lname,customer.customer_contact, payment_mode.payment_mode, payment_head.valid_for_creadit_receive, branch.branch_name FROM sale_payment, payment_mode, branch, payment_head, customer where branch.id_branch = sale_payment.idbranch and customer.id_customer = sale_payment.idcustomer and payment_mode.id_paymentmode = idpayment_mode and sale_payment.idbranch = ".$idbranch." and idpayment_head = ".$idpayment_head." and payment_mode.idpaymenthead = payment_head.id_paymenthead and amount > received_amount order by id_salepayment desc";
            }
        }
        return $this->db->query($str1)->result();
    }
    public function ajax_get_credit_receivable_report($idpayment_head, $idpayment_mode, $idbranch, $viewbranches, $from, $to){
        if($idbranch == ''){
            $branches = $viewbranches;
        }else{
            $branches = $idbranch;
        }
        $str1='';
        if($from !='' && $to != ''){
            if($idpayment_mode != ''){
                $str1 = "SELECT sale_payment.*, payment_mode.payment_mode, payment_head.valid_for_creadit_receive, branch.branch_name "
                        . "FROM sale_payment, payment_mode, branch, payment_head "
                        . "where branch.id_branch = sale_payment.idbranch and payment_mode.id_paymentmode = idpayment_mode and sale_payment.idbranch IN ($branches) and idpayment_mode = ".$idpayment_mode." and payment_mode.idpaymenthead = payment_head.id_paymenthead and amount > received_amount and sale_payment.date between '".$from."' and '".$to."' order by id_salepayment desc";
            }elseif($idpayment_head != ''){
                $str1 = "SELECT sale_payment.*, payment_mode.payment_mode, payment_head.valid_for_creadit_receive, branch.branch_name "
                        . "FROM sale_payment, payment_mode, branch, payment_head "
                        . "where branch.id_branch = sale_payment.idbranch and payment_mode.id_paymentmode = idpayment_mode and sale_payment.idbranch IN ($branches) and idpayment_head = ".$idpayment_head." and payment_mode.idpaymenthead = payment_head.id_paymenthead and amount > received_amount and sale_payment.date between '".$from."' and '".$to."' order by id_salepayment desc";
            }else{
                $str1 = "SELECT sale_payment.*, payment_mode.payment_mode, payment_head.valid_for_creadit_receive, branch.branch_name "
                        . "FROM sale_payment, payment_mode, branch, payment_head "
                        . "where branch.id_branch = sale_payment.idbranch and payment_mode.id_paymentmode = idpayment_mode and sale_payment.idbranch IN ($branches) and idpayment_head IN (6,7) and payment_mode.idpaymenthead = payment_head.id_paymenthead and amount > received_amount and sale_payment.date between '".$from."' and '".$to."' order by id_salepayment desc";
            }
        }else{
            if($idpayment_mode != ''){
                $str1 = "SELECT sale_payment.*, payment_mode.payment_mode, payment_head.valid_for_creadit_receive, branch.branch_name "
                        . "FROM sale_payment, payment_mode, branch, payment_head "
                        . "where branch.id_branch = sale_payment.idbranch and payment_mode.id_paymentmode = idpayment_mode and sale_payment.idbranch IN ($branches) and idpayment_mode = ".$idpayment_mode." and payment_mode.idpaymenthead = payment_head.id_paymenthead and amount > received_amount order by id_salepayment desc";
            }elseif($idpayment_head != ''){
                $str1 = "SELECT sale_payment.*, payment_mode.payment_mode, payment_head.valid_for_creadit_receive, branch.branch_name "
                        . "FROM sale_payment, payment_mode, branch, payment_head "
                        . "where branch.id_branch = sale_payment.idbranch and payment_mode.id_paymentmode = idpayment_mode and sale_payment.idbranch IN ($branches) and idpayment_head = ".$idpayment_head." and payment_mode.idpaymenthead = payment_head.id_paymenthead and amount > received_amount order by id_salepayment desc";
            }else{
                $str1 = "SELECT sale_payment.*, payment_mode.payment_mode, payment_head.valid_for_creadit_receive, branch.branch_name "
                        . "FROM sale_payment, payment_mode, branch, payment_head "
                        . "where branch.id_branch = sale_payment.idbranch and payment_mode.id_paymentmode = idpayment_mode and sale_payment.idbranch IN ($branches) and idpayment_head IN (6,7) and payment_mode.idpaymenthead = payment_head.id_paymenthead and amount > received_amount order by id_salepayment desc";
            }
        }
        return $this->db->query($str1)->result();
    }
    
    function bfl($sfid, $partnerid) {
//        die($sfid.' '.$partnerid);
        $api_key='fd1a56a6fc4544a999f4a1bd9e1132a5';
//        $api_key='d466065d19464bb0bbe37250b989885b';
        $data = array();
        $dealid = $sfid;
        $partner = 'testpartner';
//        $dealid = $sfid;
//        $partner = $partnerid;
        $newurl = "https://bfl-api-dev.azure-api.net/POSReinventDOWS/DODetails?dealId=$dealid&partner=$partner";
//        $newurl = "https://prodapitm.bajajfinserv.in/POSReinventDOVSWS/DODetails?dealId=$dealid&partner=$partner";
//        $newurl = "https://prodapitm.bajajfinserv.in/POSReinventDOVSWS/DODetails?dealId={dealId}&partner=45473";
        $result = $this->rest->request($newurl, "GET", $data, $api_key);
        $result = json_decode($result, true);        
        return $result;
    }
    function bfl_integration($sfid, $partnerid) {
        $api_key='b62a9be9db544441926da01af6231a89';
        $data = array();
        $dealid = $sfid;
        $partner = $partnerid;
        $newurl = "https://prodapitm.bajajfinserv.in/POSReinventDOVSWS/DODetails?dealId=$dealid&partner=$partner";
        $result = $this->rest->request($newurl, "GET", $data, $api_key);
        $result = json_decode($result, true);        
        return $result;
    }
	
    function upload_bfl($dataa) {
//      $api_key='d466065d19464bb0bbe37250b989885b';
        $api_key='fd1a56a6fc4544a999f4a1bd9e1132a5';
        
        $newurl = "https://bfl-api-dev.azure-api.net/POSISDDocsUploadWS/SubmitDocument";
//      $newurl = "https://prodapitm.bajajfinserv.in/POSISDDocsUploadWS/SubmitDocument";
        $result = $this->rest->request($newurl, "POST", json_encode($dataa), $api_key);
        $result = json_decode($result, true);                
        return $result;
    }
    public function update_credit_sale_payment_byid($id,$data) {
        return $this->db->where('id_salepayment', $id)->update('sale_payment', $data);
    }
    public function get_sale_reconciliation_byid($id){
        return $this->db->where('idsale', $id)
                        ->where('idpayment_mode = id_paymentmode')->from('payment_mode')
                        ->join('bank', 'bank.id_bank = payment_reconciliation.idbank', 'left')
                        ->get('payment_reconciliation')->result();
    }
    // sales return
    public function get_sale_byinvno($invno, $branch, $level){
        if($level == 1){
            return $this->db->where('inv_no', $invno)
                            ->where('idsalesperson = id_users')->from('users')
                            ->where('branch.id_branch', $branch)->from('branch')
                            ->get('sale', 1)->result();
        }else{
            return $this->db->where('inv_no', $invno)->where('sale.idbranch', $branch)
                            ->where('idsalesperson = id_users')->from('users')
                            ->where('branch.id_branch', $branch)->from('branch')
                            ->get('sale', 1)->result();
        }
    }
    public function get_sale_product_byinvno($invno, $branch, $level){
        if($level == 1){
            return $this->db->where('inv_no', $invno)
                            ->where('id_sku_type = idskutype')->from('sku_type')
                            ->get('sale_product')->result();
        }else{
            return $this->db->where('inv_no', $invno)->where('idbranch', $branch)
                            ->where('id_sku_type = idskutype')->from('sku_type')
                            ->get('sale_product')->result();
        }
    }
    // invoce edit by config
    public function get_config_sale_byinvno_for_edit($invno){
        return $this->db->select('branch.*, branch.idstate as branch_idstate, sale.*, customer.*, customer.idstate as customer_idstate,sale.entry_time')
                ->where('inv_no', $invno)
                ->where('sale.idbranch=branch.id_branch')->from('branch')
                ->where('sale.idcustomer=customer.id_customer')->from('customer')
                ->get('sale', 1)->result();
    }
    public function get_sale_payment_byinvno($invno, $branch, $level){
        if($level == 1){
            return $this->db->where('inv_no', $invno)->where('sales_return = 0')
                            ->where('idpayment_mode = id_paymentmode')->from('payment_mode')
                            ->get('sale_payment')->result();
        }else{
            return $this->db->where('inv_no', $invno)->where('sales_return = 0')->where('idbranch', $branch)
                        ->where('idpayment_mode = id_paymentmode')->from('payment_mode')
                        ->get('sale_payment')->result();
        }
    }
    public function save_daybook_cash_payment($data) {
        return $this->db->insert('daybook_cash', $data);
    }
    public function edit_daybook_cash_byidtable_entry_type($idtable, $entry_type, $data) {
//        return $this->db->where('entry_type = 1')->update_batch('daybook_cash', $data, 'idtable');
        return $this->db->where('idtable', $idtable)->where('entry_type',$entry_type)->update('daybook_cash', $data);
    }
    public function remove_daybook_cash_amount($idtable, $entry_type) {
        return $this->db->where('idtable', $idtable)->where('entry_type',$entry_type)->delete('daybook_cash');
    }
    public function save_batch_daybook_cash_payment($data) {
        return $this->db->insert_batch('daybook_cash',$data);
    }
    public function get_todays_cash_closure_byidbranch($idbranch) {
        return $this->db->where('idbranch',$idbranch)->where('date', date('Y-m-d'))->get('cash_closure')->result();
    }
    //update sales return
    public function update_sale($idsale, $data) {
        $this->db->where('id_sale', $idsale)->update('sale', $data);
    }
    public function update_sale_product_byidsale($idsale, $data) {
        $this->db->where('idsale', $idsale)->update('sale_product', $data);
    }
    public function update_sale_product_byidsale_customer($idsale, $gst_type) {
        $sale_product = $this->Sale_model->get_sale_product_byid($idsale);
        if($gst_type == 0){ // cgst
            foreach ($sale_product as $product) {
                $cgst = $product->igst_per/2;
                $igst = 0;
                $this->db->where('id_saleproduct', $product->id_saleproduct)
                        ->set('cgst_per', $cgst, false)
                        ->set('sgst_per', $cgst, false)
                        ->set('igst_per', $igst, false)
                        ->update('sale_product');
            }
        }else{ // igst
            foreach ($sale_product as $product) {
                $igst = $product->cgst_per + $product->cgst_per;
                $cgst = 0;
                $this->db->where('id_saleproduct', $product->id_saleproduct)
                        ->set('cgst_per', $cgst, false)
                        ->set('sgst_per', $cgst, false)
                        ->set('igst_per', $igst, false)
                        ->update('sale_product');
            }
        }
    }
    public function update_sale_product_byidsaleproduct($idsale, $data) {
        $this->db->where('id_saleproduct', $idsale)->update('sale_product', $data);
    }
    public function ajax_get_sale_data_byfilter($from, $to, $idbranch, $idpcat, $idbrand, $idsaletype, $idzone){
        $id = $_SESSION['id_users'];
        if($idzone != ''){
            if($idzone == 0){ 
                $branchs = $this->db->where('active', 1)->get('branch')->result();
            }else{
                $branchs = $this->db->where('idzone', $idzone)->get('branch')->result();
            }
            foreach ($branchs as $brn){
                $branchid[] = $brn->id_branch;
            }
        }else{
            if($idbranch == 0){ 
                if($this->session->userdata('level') == 1){  //admin all branch
                    $branches = $this->db->where('active', 1)->get('branch')->result();
                }elseif($this->session->userdata('level') == 2){   // Branch Accountant
                    $branches = $this->db->where('id_users', $_SESSION['id_users'])->where('idbranch = branch.id_branch')->from('branch')->get('users')->result();
                }elseif($this->session->userdata('level') == 3){  //Multiple branches
                    $branches = $this->db->select('b.*')
                                        ->where('b.active', 1)
                                        ->where(' ub.iduser', $id)
                                        ->where('b.is_warehouse', 0)
                                        ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
                                        ->get('branch b')->result();
                }
                foreach ($branches as $brn){
                    $branchid[] = $brn->id_branch;
                } 
            }
            else{
                 $branchid[] = $idbranch;
            }
        }
        
        if($idpcat == 0){
            $procat = $this->db->where('active', 1)->get('product_category')->result();
            foreach ($procat as $pcat){
                $productcatid[] = $pcat->id_product_category;
            }
        }else{
            $productcatid[] = $idpcat;
        } 
        
        if($idbrand == 0){
            $branddata = $this->db->where('active', 1)->get('brand')->result();
            foreach ($branddata as $bdata){
                $brandid[] = $bdata->id_brand;
            }
        }else{
            $brandid[] = $idbrand;
        } 
        
        return $this->db->select('sale.date,sale.idsaletoken,sale.entry_time,sale_product.idsale,sale_product.inv_no,branch.branch_name,sale.customer_fname,sale.customer_lname,sale.customer_contact,sale.customer_gst,sale_product.imei_no,product_category.product_category_name,brand.brand_name,sale_product.product_name,sale_product.total_amount,users.user_name, sale_product.landing,partner_type.partner_type,zone.zone_name,sale_product.mop,sale_product.mrp, branch_category.branch_category_name,model_variants.full_name,sale_product.nlc_price,users.id_users,sale_product.idbranch,sale_product.idcategory,sale_product.cgst_per,sale_product.sgst_per,sale_product.igst_per,sale.corporate_sale,sale_token.id_sale_token, category.category_name')
                        ->where_in('sale_product.idbranch', $branchid)
                        ->where_in('sale_product.idproductcategory', $productcatid)
                        ->where_in('sale_product.idbrand', $brandid)
                        ->where('sale_product.date >=', $from)
                        ->where('sale_product.date <=', $to)
                        ->where_in('sale.corporate_sale', $idsaletype)
                        ->join('sale_token','sale_product.idsale = sale_token.idsale', 'left')
                        ->join('branch','sale_product.idbranch = branch.id_branch', 'left')
                        ->join('branch_category','branch.idbranchcategory = branch_category.id_branch_category', 'left')
                        ->join('sale','sale_product.idsale = sale.id_sale', 'left')
                        ->join('users','sale.idsalesperson = users.id_users', 'left')
                        ->join('product_category', 'sale_product.idproductcategory = product_category.id_product_category', 'left')
                        ->join('brand', 'sale_product.idbrand = brand.id_brand', 'left')
                        ->join('category', 'sale_product.idcategory = category.id_category', 'left')
                        ->join('partner_type','branch.idpartner_type = partner_type.id_partner_type', 'left')
                        ->join('zone','branch.idzone = zone.id_zone', 'left')
                        ->join('model_variants','sale_product.idvariant = model_variants.id_variant', 'left')
                        ->order_by('sale_product.date,branch.idzone','ASC')
                        ->get('sale_product')->result();
        
//       die(print_r($str));
    }
    public function ajax_get_cluster_data($idbranch){
        $id = $_SESSION['id_users'];
        if($idbranch == 0){ 
            if($this->session->userdata('level') == 1){  //admin all branch
                $branches = $this->db->where('active', 1)->get('branch')->result();
            }elseif($this->session->userdata('level') == 2){   // Branch Accountant
                $branches = $this->db->where('id_users', $_SESSION['id_users'])->where('idbranch = branch.id_branch')->from('branch')->get('users')->result();
            }elseif($this->session->userdata('level') == 3){  //Multiple branches
                $branches = $this->db->select('b.*')
                                    ->where('b.active', 1)
                                    ->where(' ub.iduser', $id)
                                    ->where('b.is_warehouse', 0)
                                    ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
                                    ->get('branch b')->result();
            }
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            } 
        }
        else{
             $branchid[] = $idbranch;
        }
        
        return $this->db->select('user_has_branch.idbranch as clust_branch, users.user_name as clust_name')
                        ->where_in('user_has_branch.idbranch', $branchid)
                        ->where('users.iduserrole',26)
                        ->where('user_has_branch.iduser = users.id_users')->from('users')
                        ->get('user_has_branch')->result();
    }
    
    public function ajax_get_brand_name_byiduser($id_users){
        return $this->db->select('brand.brand_name as user_brand_name')
                        ->where('user_has_brand.iduser', $id_users)
                        ->where('user_has_brand.idbrand = brand.id_brand')->from('brand')
                        ->get('user_has_brand')->row();
    }
    
    public function ajax_get_sale_return_data_byfilter($from, $to, $idbranch, $idpcat, $idbrand, $idsaletype, $idzone){
        $id = $_SESSION['id_users'];
        if($idzone != ''){
            if($idzone == 0){ 
                $branchs = $this->db->where('active', 1)->get('branch')->result();
            }else{
                $branchs = $this->db->where('idzone', $idzone)->get('branch')->result();
            }
            foreach ($branchs as $brn){
                $branchid[] = $brn->id_branch;
            }
        }else{
            if($idbranch == 0){ 
                if($this->session->userdata('level') == 1){  //admin all branch
                    $branches = $this->db->where('active', 1)->get('branch')->result();
                }elseif($this->session->userdata('level') == 2){   // Branch Accountant
                    $branches = $this->db->where('id_users', $_SESSION['id_users'])->where('idbranch = branch.id_branch')->from('branch')->get('users')->result();
                }elseif($this->session->userdata('level') == 3){  //Multiple branches
                    $branches = $this->db->select('b.*')
                                        ->where('b.active', 1)
                                        ->where(' ub.iduser', $id)
                                        ->where('b.is_warehouse', 0)
                                        ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
                                        ->get('branch b')->result();
                }
                foreach ($branches as $brn){
                    $branchid[] = $brn->id_branch;
                } 
            }
            else{
                 $branchid[] = $idbranch;
            }
        }
        if($idpcat == 0){
            $procat = $this->db->where('active', 1)->get('product_category')->result();
            foreach ($procat as $pcat){
                $productcatid[] = $pcat->id_product_category;
            }
        }else{
            $productcatid[] = $idpcat;
        } 
        
        if($idbrand == 0){
            $branddata = $this->db->where('active', 1)->get('brand')->result();
            foreach ($branddata as $bdata){
                $brandid[] = $bdata->id_brand;
            }
        }else{
            $brandid[] = $idbrand;
        } 
        return $this->db->select('sales_return_product.*,brand.*,product_category.*,customer.*,users.*,sales_return.*,sale_product.landing,sale_product.mop,sale_product.mrp,branch.*,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,model_variants.full_name,sale_product.nlc_price,sale.corporate_sale, category.category_name')
                        ->where_in('sales_return_product.idbranch', $branchid)
                        ->where_in('sales_return_product.idproductcategory', $productcatid)
                        ->where_in('sales_return_product.idbrand', $brandid)
                        ->where('sales_return_product.date >=', $from)
                        ->where('sales_return_product.date <=', $to)
                        ->where_in('sale.corporate_sale', $idsaletype)
                        ->join('branch','sales_return_product.idbranch = branch.id_branch', 'left')
                        ->join('branch_category','branch.idbranchcategory = branch_category.id_branch_category', 'left')
                        ->join('sale_product','sales_return_product.idsale_product = sale_product.id_saleproduct', 'left')                        
                        ->join('sales_return','sales_return_product.idsales_return = sales_return.id_salesreturn', 'left')
                        ->join('sale','sales_return.idsale = sale.id_sale', 'left')
                        ->join('users','sales_return.idsalesperson = users.id_users', 'left')
                        ->join('customer','sales_return.idcustomer = customer.id_customer', 'left')
                        ->join('partner_type','branch.idpartner_type = partner_type.id_partner_type', 'left')
                        ->join('zone','branch.idzone = zone.id_zone', 'left')
                        ->join('product_category', 'sales_return_product.idproductcategory = product_category.id_product_category', 'left')
                        ->join('brand', 'sales_return_product.idbrand = brand.id_brand', 'left')
                        ->join('category', 'sales_return_product.idcategory = category.id_category', 'left')
                        ->join('model_variants','sales_return_product.idvariant = model_variants.id_variant', 'left')
                        ->get('sales_return_product')->result();
//       die($this->db->last_query());
    }
    public function save_sale_edit_history_data($data){
        $this->db->insert('sale_edit_history', $data);
        return $this->db->insert_id();
    }
    public function edit_sale($idsale, $data){
        return $this->db->where('id_sale', $idsale)->update('sale', $data);
    }
    public function edit_sale_payment($id_salepayment, $data){
        return $this->db->where('id_salepayment', $id_salepayment)->update('sale_payment', $data);
    }
    public function edit_sale_reconciliation($id_salepayment, $data){
        return $this->db->where('idsale_payment', $id_salepayment)->update('payment_reconciliation', $data);
    }
    public function batch_edit_sale_payment($data){
//        return $this->db->where('id_salepayment', $id_salepayment)->update('sale_payment', $data);
        $this->db->update_batch('sale_payment', $data, 'id_salepayment'); 
    }
    public function batch_edit_sale_reconciliation($data){
//        return $this->db->where('idsale_payment', $id_salepayment)->update('payment_reconciliation', $data);
        $this->db->update_batch('payment_reconciliation', $data, 'idsale_payment'); 
    }
    public function save_sale_product_edit_history($data){
        return $this->db->insert_batch('sale_product_edit_history', $data);
    }
    public function save_sale_payment_edit_history($data){
//        return $this->db->insert_batch('sale_payment_edit_history', $data);
        return $this->db->insert('sale_payment_edit_history', $data);
    }
    public function edit_sale_product($data) {
//        $this->db->where('id_saleproduct', $id)->update('sale_product', $data);
        $this->db->update_batch('sale_product',$data, 'id_saleproduct'); 
    }
    public function edit_batch_insurance_recon($data) {
        $this->db->update_batch('insurance_reconciliation',$data, 'idsale_product'); 
    }
    public function edit_batch_imei_history($idtype,$idlink,$old_imei_no,$new_imei_no) {
//        die($idlink.' '.$edited_idsaleproduct.' '.$old_imei_no.' '.$new_imei_no);
        return $this->db->where('idlink', $idlink)
                        ->where('idimei_details_link', $idtype)
                        ->where('imei_no', $old_imei_no)
                        ->set('imei_no', $new_imei_no)
                        ->update('imei_history'); 
    }
    public function ajax_check_valid_imei($idbranch,$idgodown,$idvariant,$new_imei_no){
        return $this->db->where('idbranch', $idbranch)->where('idgodown', $idgodown)->where('idvariant', $idvariant)
                ->where('imei_no', $new_imei_no)->get('stock')->result();
//        die($this->db->last_query());
    }
    public function update_stock_byimei($new_imei_no, $old_imei_no) {
        $this->db->where('imei_no',$new_imei_no)->set('imei_no', $old_imei_no)->update('stock');
    }
    public function get_sale_payment_byidsale_payment($id) {
        $this->db->where('id_salepayment', $id)->get('sale_payment')->row();
    }
    public function remove_sale_payment($id) {
        $this->db->where_in('id_salepayment', $id)->delete('sale_payment');
    }
    public function remove_payment_reconciliation($id) {
        $this->db->where_in('idsale_payment', $id)->delete('payment_reconciliation');
    }
    public function ajax_get_credit_received_report_byfilter($idbranch, $datefrom, $dateto, $allbranches){
        if($idbranch == 0){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
        return $this->db->where('sale_edit_history.date >=', $datefrom)
                        ->where('sale_edit_history.date <=', $dateto)
                        ->where_in('sale_edit_history.idbranch', $branches)
//                        ->where('payment_reconciliation.idsale_payment = sale_payment.id_salepayment')->from('sale_payment')
                        ->where('sale_edit_history.idbranch = branch.id_branch')->from('branch')
//                        ->where('payment_reconciliation.idpayment_mode = payment_mode.id_paymentmode')->from('payment_mode')
//                        ->where('payment_reconciliation.idcustomer = customer.id_customer')->from('customer')
                        ->get('sale_edit_history')->result();
                        
    }
     public function update_stock_model_variant_data($stockdata, $imei){
        return $this->db->where('imei_no', $imei)->update('stock', $stockdata);
    }
    public function update_opening_model_variant_data($stockdata, $imei){
        return $this->db->where('imei_no', $imei)->update('opening_data', $stockdata);
    }
    public function update_inwardproduct_model_variant_data($stockdata, $imei){
        return $this->db->where('imei_no', $imei)->update('inward_product', $stockdata);
    }
    public function update_imei_histroy_model_variant_data($stockdata, $imei){
        return $this->db->where('imei_no', $imei)->update('imei_history', $stockdata);
    }
    
     public function ajax_get_e_invoice_sale_data_byfilter($from, $to, $idcompany, $idpcat, $idbrand){
        $id = $_SESSION['id_users'];
        if($idcompany == 0){ 
            $branches = $this->db->where('active', 1)->get('branch')->result();
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            }
        }
        else{
            $branches = $this->db->where('idcompany', $idcompany)->where('active', 1)->get('branch')->result();
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            }
        }
            
        if($idpcat == 0){
            $procat = $this->db->where('active', 1)->get('product_category')->result();
            foreach ($procat as $pcat){
                $productcatid[] = $pcat->id_product_category;
            }
        }else{
            $productcatid[] = $idpcat;
        } 
        
        if($idbrand == 0){
            $branddata = $this->db->where('active', 1)->get('brand')->result();
            foreach ($branddata as $bdata){
                $brandid[] = $bdata->id_brand;
            }
        }else{
            $brandid[] = $idbrand;
        } 
        
        return $this->db->select('sale.date,sale.entry_time,sale_product.qty as sqty,sale_product.basic,sale_product.total_amount,sale_product.cgst_per, sale_product.sgst_per, sale_product.igst_per,sale_product.idsale,sale_product.inv_no,sale_product.is_mop, sale_product.mop, branch.branch_name,sale.customer_fname,sale.customer_lname,sale.customer_contact,sale.customer_gst,sale_product.imei_no,product_category.product_category_name,brand.brand_name,sale_product.product_name,sale_product.total_amount,users.user_name,sale_product.landing,customer.customer_city,customer.customer_address,customer.customer_pincode,customer.customer_state,vendor.state as vstate,category.category_name, category.hsn,branch.branch_state_name,sale_product.price,sale_product.discount_amt,branch.branch_name,customer.customer_gst as cust_gst_no')
                        ->where_in('sale_product.idbranch', $branchid)
                        ->where_in('sale_product.idproductcategory', $productcatid)
                        ->where_in('sale_product.idbrand', $brandid)
                        ->where('sale_product.date >=', $from)
                        ->where('sale_product.date <=', $to)
                        ->where('customer.customer_gst != ','')
                        ->join('branch','sale_product.idbranch = branch.id_branch', 'left')
                        ->join('sale','sale_product.idsale = sale.id_sale', 'left')
                        ->join('vendor','vendor.id_vendor = sale_product.idvendor', 'left')
                        ->join('customer','customer.id_customer = sale.idcustomer', 'left')
                        ->join('users','sale.idsalesperson = users.id_users', 'left')
                        ->join('product_category', 'sale_product.idproductcategory = product_category.id_product_category', 'left')
                        ->join('category', 'sale_product.idcategory = category.id_category', 'left')
                        ->join('brand', 'sale_product.idbrand = brand.id_brand', 'left')
                        ->get('sale_product')->result();
        
//       die(print_r($str));
    }
    public function get_invoice_correction_report($idbranch, $datefrom, $dateto, $allbranches){
        if($idbranch == 0){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
        return $this->db->select('sale_edit_history.*,branch.branch_name')
                        ->where('sale_edit_history.date >=', $datefrom)
                        ->where('sale_edit_history.date <=', $dateto)
                        ->where_in('sale_edit_history.idbranch', $branches)
//                        ->where('payment_reconciliation.idsale = sale.id_sale')->from('sale')
//                        ->where('payment_reconciliation.from_credit_buyback_received = 1')
//                        ->where('payment_reconciliation.idsale_payment = sale_payment.id_salepayment')->from('sale_payment')
                        ->where('sale_edit_history.idbranch = branch.id_branch')->from('branch')
//                        ->where('payment_reconciliation.idpayment_mode = payment_mode.id_paymentmode')->from('payment_mode')
                        ->order_by('sale_edit_history.id_sale_edit_history','desc')
                        ->get('sale_edit_history')->result();
    }
    public function get_invoice_edit_details_byid($id_sale_edit_history) {
             return $this->db->select('sale_edit_history.*,branch.branch_name')
                        ->where('id_sale_edit_history',$id_sale_edit_history)
                        ->where('sale_edit_history.idbranch = branch.id_branch')->from('branch')
                        ->get('sale_edit_history')->result();
    }
    public function get_sale_product_edit_history_byid($id_sale_edit_history) {
        return $this->db->where('idsale_edit_history',$id_sale_edit_history)->get('sale_product_edit_history')->result();
    }
    public function get_sale_payment_edit_history_byid($id_sale_edit_history) {
        return $this->db->select('sale_payment_edit_history.*, payment_mode.payment_mode, payment_head.payment_head')->where('idsale_edit_history',$id_sale_edit_history)
                        ->where('sale_payment_edit_history.idpayment_mode = payment_mode.id_paymentmode')->from('payment_mode')
                        ->where('sale_payment_edit_history.idpayment_head = payment_head.id_paymenthead')->from('payment_head')
                        ->get('sale_payment_edit_history')->result();
    }
    
    public function ajax_get_tally_sale_product_data_byfilter($from, $to, $idcompany, $idpcat, $idbrand){
        $id = $_SESSION['id_users'];
        
        $branchid = '';
        if($idcompany == 0){ 
            $branches = $this->db->where('active', 1)->get('branch')->result();
            foreach ($branches as $brn){
                $branchid .= $brn->id_branch.',';
            }
        }
        else{
            $branches = $this->db->where('idcompany', $idcompany)->where('active', 1)->get('branch')->result();
            foreach ($branches as $brn){
                $branchid .= $brn->id_branch.',';
            }
        }
        $branchid = trim($branchid,',');
        
        $productcatid = '';
        if($idpcat == 0){
            $procat = $this->db->where('active', 1)->get('product_category')->result();
            foreach ($procat as $pcat){
                $productcatid .= $pcat->id_product_category.',';
            }
            $productcatid = trim($productcatid,',');
        }else{
            $productcatid .= $idpcat;
        } 
        
        $brandid = '';
        if($idbrand == 0){
            $branddata = $this->db->where('active', 1)->get('brand')->result();
            foreach ($branddata as $bdata){
                $brandid .= $bdata->id_brand.',';
            }
            $brandid = trim($brandid,',');
        }else{
            $brandid .= $idbrand;
        } 
        
        $str = "Select distinct sale_product.imei_no, sale_product.inv_no, sale_product.qty as sqty,sale_product.basic,sale_product.total_amount,sale_product.cgst_per, sale_product.sgst_per, sale_product.igst_per,sale_product.idsale,sale_product.is_mop, sale_product.mop,sale_product.product_name,sale_product.total_amount,sale_product.landing,sale_product.price,sale_product.discount_amt, sale_product.date, sale_product.entry_time, customer.customer_gst as cust_gst_no,customer.customer_city,customer.customer_address,customer.customer_pincode,customer.customer_state,sale.customer_fname,sale.customer_lname,sale.customer_contact,sale.customer_gst,branch.branch_state_name,brand.brand_name from sale_product, sale, customer,branch,brand,sale_payment where sale_product.idbranch IN(63) and sale_product.idproductcategory IN(1,5) and sale_product.idbrand IN(1,29) and sale_product.date >= '$from' and sale_product.date <= '$to' and sale_product.idsale = sale.id_sale and customer.id_customer = sale.idcustomer and sale_product.idbranch = branch.id_branch and sale_product.idbrand = brand.id_brand ";
        return $this->db->query($str)->result();

    }
    
	public function get_tally_sale_report($from, $to, $idcompany,$dc )
    {
         $whr="";
        $whrr="";        
        if($idcompany==0){
            $whr.=" ";
             $whrr=" ";
        }else{
            $whr.=" and b.idcompany=".$idcompany." ";
            $whrr=" and branch.idcompany=".$idcompany." ";
        }
        
        $payment_mode_data = $this->db->get('payment_mode')->result();
//        $str = "SELECT s.id_sale,b.branch_name,ca.hsn,sp.product_name,sp.qty,sp.imei_no,sp.hsn,sp.is_mop,brd.brand_name,sp.discount_amt,sp.total_amount,sp.cgst_per,sp.sgst_per,sp.igst_per,s.dcprint,sp.mop,c.customer_fname,c.customer_lname,c.customer_contact,c.customer_gst,c.customer_state,u.user_name,t.inv_no, t.date,s.final_total as total_settlement,s.entry_time, ";
        $str = "SELECT s.final_total,s.id_sale,b.branch_name,ca.hsn,sp.product_name,sp.qty,sp.imei_no,sp.hsn,sp.is_mop,brd.brand_name,sp.discount_amt,sp.total_amount,sp.cgst_per,sp.sgst_per,sp.igst_per,s.dcprint,sp.mop,c.customer_fname,c.customer_lname,c.customer_contact,c.customer_gst,c.customer_state,u.user_name,t.inv_no, t.date,s.final_total as total_settlement,s.basic_total,s.entry_time,s.gst_type, ";
                    foreach ($payment_mode_data as $payment_mode){
                        $str .= "max(CASE t.idpayment_mode WHEN ".$payment_mode->id_paymentmode." THEN t.amount ELSE 0 END) AS ".$payment_mode->payment_mode." , ";
                         $str .= "max(CASE t.idpayment_mode WHEN ".$payment_mode->id_paymentmode." THEN t.tid ELSE '' END) AS ".$payment_mode->payment_mode."_transaction_id , ";
                    }
                    $str1 = rtrim($str, ', ');
        $str1 .= " FROM brand brd,category ca,sale_product sp,sale s join users u on u.id_users=s.idsalesperson join customer c on c.id_customer=s.idcustomer join branch b on b.id_branch=s.idbranch join (SELECT `inv_no`,`idpayment_head`,`idbranch`,`date`,`idpayment_mode`, sum(amount) as amount,GROUP_CONCAT(`transaction_id`) as tid FROM `sale_payment`, branch WHERE sale_payment.idbranch=branch.id_branch ".$whrr." and `date` between '$from' and '$to' GROUP by `inv_no`,`idpayment_mode`) t  on t.inv_no=s.inv_no"
                . " where  ((s.customer_gst!='' and sp.is_gst=0) or (s.customer_gst='' and sp.is_gst!=0) or sp.is_gst=1) and  sp.idsale=s.id_sale and sp.idcategory = ca.id_category and brd.id_brand=sp.idbrand and s.dcprint=".$dc." ".$whr." and s.`date` between '$from' and '$to' GROUP by sp.id_saleproduct ORDER by t.inv_no";
//        die($str1);
        return $this->db->query($str1)->result();
    }
    public function get_jio_tally_sale_report($from, $to, $idcompany,$dc ) ///new///
    {
         $whr="";
        $whrr="";        
        if($idcompany==0){
            $whr.=" ";
             $whrr=" ";
        }else{
            $whr.=" and b.idcompany=".$idcompany." ";
            $whrr=" and branch.idcompany=".$idcompany." ";
        }
        
        $payment_mode_data = $this->db->where('active = 1')->get('payment_mode')->result();
//        $str = "SELECT s.id_sale,b.branch_name,ca.hsn,sp.product_name,sp.qty,sp.imei_no,sp.hsn,sp.is_mop,brd.brand_name,sp.discount_amt,sp.total_amount,sp.cgst_per,sp.sgst_per,sp.igst_per,s.dcprint,sp.mop,c.customer_fname,c.customer_lname,c.customer_contact,c.customer_gst,c.customer_state,u.user_name,t.inv_no, t.date,s.final_total as total_settlement,s.entry_time, ";
        $str = "SELECT s.final_total,s.id_sale,b.branch_name,ca.hsn,sp.product_name,sp.qty,sp.imei_no,sp.hsn,sp.is_mop,brd.brand_name,sp.discount_amt,sp.total_amount,sp.cgst_per,sp.sgst_per,sp.igst_per,s.dcprint,sp.mop,c.customer_fname,c.customer_lname,c.customer_contact,c.customer_gst,c.customer_state,u.user_name,t.inv_no, t.date,s.final_total as total_settlement,s.basic_total,s.entry_time,s.gst_type, ";
                    foreach ($payment_mode_data as $payment_mode){
                        $str .= "max(CASE t.idpayment_mode WHEN ".$payment_mode->id_paymentmode." THEN t.amount ELSE 0 END) AS ".$payment_mode->payment_mode." , ";
                         $str .= "max(CASE t.idpayment_mode WHEN ".$payment_mode->id_paymentmode." THEN t.tid ELSE '' END) AS ".$payment_mode->payment_mode."_transaction_id , ";
                    }
                    $str1 = rtrim($str, ', ');
        $str1 .= " FROM brand brd,category ca,sale_product sp,sale s join users u on u.id_users=s.idsalesperson join customer c on c.id_customer=s.idcustomer join branch b on b.id_branch=s.idbranch join (SELECT `inv_no`,`idpayment_head`,`idbranch`,`date`,`idpayment_mode`, sum(amount) as amount,GROUP_CONCAT(`transaction_id`) as tid FROM `sale_payment`, branch WHERE sale_payment.idbranch=branch.id_branch ".$whrr." and `date` between '$from' and '$to' GROUP by `inv_no`,`idpayment_mode`) t  on t.inv_no=s.inv_no"
                . "   where sp.idsale=s.id_sale and sp.idcategory = ca.id_category and brd.id_brand=sp.idbrand and s.dcprint=".$dc." ".$whr." and s.`date` between '$from' and '$to' GROUP by sp.id_saleproduct ORDER by t.inv_no";
//        die($str1);
        return $this->db->query($str1)->result();
    }
	
    public function ajax_get_tally_sale_payment($datefrom, $dateto){
        $payment_mode_data = $this->db->get('payment_mode')->result();
        $str = "SELECT inv_no, sale_payment.date, branch.branch_name, sale_payment.idsale, ";
                foreach ($payment_mode_data as $payment_mode){
        $str .= "GROUP_CONCAT(CASE idpayment_mode WHEN ".$payment_mode->id_paymentmode." THEN transaction_id END ) AS trans".$payment_mode->payment_mode." ,";
        $str .= "SUM(CASE idpayment_mode WHEN ".$payment_mode->id_paymentmode." THEN amount END) AS ".$payment_mode->payment_mode." ,";
              }
                $str1 = rtrim($str, ',');
        $str1 .= " FROM sale_payment, branch where branch.id_branch = sale_payment.idbranch and sale_payment.date between '".$datefrom."' and '".$dateto."' group by idsale";
        return $this->db->query($str1)->result();
    }
    
     public function ajax_stock_analysis_report($idpcat, $idbrand, $allpcat, $allbrand){
        $productcatid = '';
        if($idpcat == 0){
           $procat = $this->db->where('active', 1)->get('product_category')->result();
            foreach ($procat as $pcat){
                $productcatid .= $pcat->id_product_category.',';
            }
            $productcatid = trim($productcatid,',');
        }else{
            $productcatid .= $idpcat;
        } 
        
        $brandid = '';
        if($idbrand == 0){
           $branddata = $this->db->where('active', 1)->get('brand')->result();
            foreach ($branddata as $bdata){
                $brandid .= $bdata->id_brand.',';
            }
            $brandid = trim($brandid,',');
        }else{
            $brandid .= $idbrand;
        } 
                       
        $str = "select sum(stock.qty) as stock_qty, sum(model_variants.last_purchase_price) as stock_amount, stock.idproductcategory, stock.idbrand from stock,model_variants where stock.idproductcategory in($productcatid) and stock.idbrand in($brandid) and (stock.idbranch > 0 or stock.temp_idbranch > 0) and stock.idvariant = model_variants.id_variant group by stock.idbrand, stock.idproductcategory";
        return $this->db->query($str)->result();
    }
    
    public function ajax_sale_analysis_report($first, $last, $idpcat, $idbrand, $allpcat, $allbrand){
        $productcatid = '';
        if($idpcat == 0){
           $procat = $this->db->where('active', 1)->get('product_category')->result();
            foreach ($procat as $pcat){
                $productcatid .= $pcat->id_product_category.',';
            }
            $productcatid = trim($productcatid,',');
        }else{
            $productcatid .= $idpcat;
        } 
        
        $brandid = '';
        if($idbrand == 0){
           $branddata = $this->db->where('active', 1)->get('brand')->result();
            foreach ($branddata as $bdata){
                $brandid .= $bdata->id_brand.',';
            }
            $brandid = trim($brandid,',');
        }else{
            $brandid .= $idbrand;
        } 
        
        $str = "select sum(sale_product.qty) as sale_qty, sum(sale_product.total_amount) as sale_amount, sale_product.idproductcategory, sale_product.idbrand  from sale_product where sale_product.date <= '$first' and sale_product.date >= '$last' and sale_product.idproductcategory in($productcatid) and sale_product.idbrand in($brandid) group by sale_product.idbrand, sale_product.idproductcategory";
        return $this->db->query($str)->result();
    }
    
     public function ajax_get_ageing_sale_data_byfilter($from, $to, $idbranch, $idpcat, $idbrand){
        $id = $_SESSION['id_users'];
        if($idbranch == 0){ 
            if($this->session->userdata('level') == 1){  //admin all branch
                $branches = $this->db->where('active', 1)->get('branch')->result();
            }elseif($this->session->userdata('level') == 2){   // Branch Accountant
                $branches = $this->db->where('id_users', $_SESSION['id_users'])->where('idbranch = branch.id_branch')->from('branch')->get('users')->result();
            }elseif($this->session->userdata('level') == 3){  //Multiple branches
                $branches = $this->db->select('b.*')
                                    ->where('b.active', 1)
                                    ->where(' ub.iduser', $id)
                                    ->where('b.is_warehouse', 0)
                                    ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
                                    ->get('branch b')->result();
            }
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            } 
        }
        else{
             $branchid[] = $idbranch;
        }
        
        if($idpcat == 0){
            $procat = $this->db->where('active', 1)->get('product_category')->result();
            foreach ($procat as $pcat){
                $productcatid[] = $pcat->id_product_category;
            }
        }else{
            $productcatid[] = $idpcat;
        } 
        
        if($idbrand == 0){
            $branddata = $this->db->where('active', 1)->get('brand')->result();
            foreach ($branddata as $bdata){
                $brandid[] = $bdata->id_brand;
            }
        }else{
            $brandid[] = $idbrand;
        } 
        
        return $this->db->select('sale.date,sale.entry_time,sale_product.idsale,sale_product.inv_no,branch.branch_name,sale.customer_fname,sale.customer_lname,sale.customer_contact,sale.customer_gst,sale_product.imei_no,product_category.product_category_name,brand.brand_name,sale_product.product_name,sale_product.total_amount,users.user_name, sale_product.landing,partner_type.partner_type,zone.zone_name,sale_product.mop,sale_product.mrp, branch_category.branch_category_name,model_variants.full_name,sale_product.nlc_price,users.id_users')
                        ->where_in('sale_product.idbranch', $branchid)
                        ->where_in('sale_product.idproductcategory', $productcatid)
                        ->where_in('sale_product.idbrand', $brandid)
                        ->where('sale_product.date >=', $from)
                        ->where('sale_product.date <=', $to)
                        ->where('sale_product.ageing ', 1)
                        ->join('branch','sale_product.idbranch = branch.id_branch', 'left')
                        ->join('branch_category','branch.idbranchcategory = branch_category.id_branch_category', 'left')
                        ->join('sale','sale_product.idsale = sale.id_sale', 'left')
                        ->join('users','sale.idsalesperson = users.id_users', 'left')
                        ->join('product_category', 'sale_product.idproductcategory = product_category.id_product_category', 'left')
                        ->join('brand', 'sale_product.idbrand = brand.id_brand', 'left')
                        ->join('partner_type','branch.idpartner_type = partner_type.id_partner_type', 'left')
                        ->join('zone','branch.idzone = zone.id_zone', 'left')
                        ->join('model_variants','sale_product.idvariant = model_variants.id_variant', 'left')
                        ->get('sale_product')->result();
        
//       die(print_r($str));
    }
    
    public function ajax_get_corporate_sale_data_byfilter($from, $to, $idbranch, $idpcat, $idbrand){
        $id = $_SESSION['id_users'];
        if($idbranch == 0){ 
            if($this->session->userdata('level') == 1){  //admin all branch
                $branches = $this->db->where('active', 1)->get('branch')->result();
            }elseif($this->session->userdata('level') == 2){   // Branch Accountant
                $branches = $this->db->where('id_users', $_SESSION['id_users'])->where('idbranch = branch.id_branch')->from('branch')->get('users')->result();
            }elseif($this->session->userdata('level') == 3){  //Multiple branches
                $branches = $this->db->select('b.*')
                                    ->where('b.active', 1)
                                    ->where(' ub.iduser', $id)
                                    ->where('b.is_warehouse', 0)
                                    ->where('b.id_branch = ub.idbranch')->from('user_has_branch ub')
                                    ->get('branch b')->result();
            }
            foreach ($branches as $brn){
                $branchid[] = $brn->id_branch;
            } 
        }
        else{
             $branchid[] = $idbranch;
        }
        
        if($idpcat == 0){
            $procat = $this->db->where('active', 1)->get('product_category')->result();
            foreach ($procat as $pcat){
                $productcatid[] = $pcat->id_product_category;
            }
        }else{
            $productcatid[] = $idpcat;
        } 
        
        if($idbrand == 0){
            $branddata = $this->db->where('active', 1)->get('brand')->result();
            foreach ($branddata as $bdata){
                $brandid[] = $bdata->id_brand;
            }
        }else{
            $brandid[] = $idbrand;
        } 
        
        return $this->db->select('sale.date,sale.entry_time,sale_product.idsale,sale_product.inv_no,branch.branch_name,sale.customer_fname,sale.customer_lname,sale.customer_contact,sale.customer_gst,sale_product.imei_no,product_category.product_category_name,brand.brand_name,sale_product.product_name,sale_product.total_amount,users.user_name, sale_product.landing,partner_type.partner_type,zone.zone_name,sale_product.mop,sale_product.mrp, branch_category.branch_category_name,model_variants.full_name,sale_product.nlc_price,users.id_users')
                        ->where_in('sale_product.idbranch', $branchid)
                        ->where_in('sale_product.idproductcategory', $productcatid)
                        ->where_in('sale_product.idbrand', $brandid)
                        ->where('sale_product.date >=', $from)
                        ->where('sale_product.date <=', $to)
                        ->where('sale.corporate_sale',1)
                        ->join('branch','sale_product.idbranch = branch.id_branch', 'left')
                        ->join('branch_category','branch.idbranchcategory = branch_category.id_branch_category', 'left')
                        ->join('sale','sale_product.idsale = sale.id_sale', 'left')
                        ->join('users','sale.idsalesperson = users.id_users', 'left')
                        ->join('product_category', 'sale_product.idproductcategory = product_category.id_product_category', 'left')
                        ->join('brand', 'sale_product.idbrand = brand.id_brand', 'left')
                        ->join('partner_type','branch.idpartner_type = partner_type.id_partner_type', 'left')
                        ->join('zone','branch.idzone = zone.id_zone', 'left')
                        ->join('model_variants','sale_product.idvariant = model_variants.id_variant', 'left')
                        ->order_by('sale_product.date,branch.idzone','ASC')
                        ->get('sale_product')->result();
        
    }
    
    
     public function get_customer_list_byidbranch_date($idbranch,$allbranches,$from,$to){
        if($idbranch == 0){
            $branches = explode(',',$allbranches);
        }else{
            $branches[] = $idbranch;
        }
        $fdate = $from.' 00:00:00';
        $tdate = $to.' 23:59:00';
        
        return $this->db->select('customer.*,branch.branch_name')
                        ->where_in('idbranch',$branches)
                        ->where('entry_time >=', $fdate)
                        ->where('entry_time <=', $tdate)
                        ->where('idbranch = branch.id_branch')->from('branch')
                        ->get('customer')->result();
                
    }
    public function get_customer_list_byidzone_date($idzone,$allzones,$from,$to){
        if($idzone == 0){
            $zones = explode(',',$allzones);
        }else{
            $zones[] = $idzone;
        }
        $fdate = $from.' 00:00:00';
        $tdate = $to.' 23:59:00';
        
        return $this->db->select('customer.*,branch.branch_name')
                        ->where('entry_time >=', $fdate)
                        ->where('entry_time <=', $tdate)
                        ->where_in('branch.idzone',$zones)
                        ->where('idbranch = branch.id_branch')->from('branch')
                        ->get('customer')->result();
                        
                
    }
    public function ajax_get_sale_data_by_idcustomer($idcustomer){
//        return $this->db->select('final_total')->where('sale.idcustomer',$idcustomer)->order_by('id_sale','DESC')->get('sale')->row();
        return $this->db->select('sale_product.total_amount as final_total,product_name')
                        ->where('sale_product.idsale=sale.id_sale')
                        ->where('sale.idcustomer',$idcustomer)->where('sale_product.idskutype != 4')->from('sale')
                        ->order_by('idsale','DESC')
                        ->get('sale_product')->row();
    }
    public function get_advanced_booking_byid_for_sale($id) {
        return $this->db->select('apr.*,model_variants.full_name,payment_mode.payment_mode,payment_head.payment_head,payment_head.tranxid_type,customer.*,users.user_name')
                        ->where('id_advance_payment_receive',$id)
                        ->where('apr.idcustomer = customer.id_customer')->from('customer')
                        ->where('apr.idpayment_mode = payment_mode.id_paymentmode')
                        ->where('apr.idpayment_head = payment_head.id_paymenthead')
                        ->from('payment_head')->from('payment_mode')
//                        ->where('branch.idprinthead = print_head.id_print_head')->from('print_head')
                        ->where('apr.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('apr.idsalesperson = users.id_users')->from('users')
                        ->get('advance_payment_receive apr')->row();
    }
    public function get_pending_sale_token($idbranch) {
        return $this->db->select('st.*,customer.customer_fname,customer.customer_lname,customer.customer_contact,users.user_name')
                        ->where('st.idbranch', $idbranch)
                        ->where('st.status = 0')
                        ->where('st.idcustomer = customer.id_customer')->from('customer')
                        ->where('st.idsalesperson = users.id_users')->from('users')
                        ->get('sale_token st')->result();
    }
    public function get_sale_token_byid($idtoken) {
        return $this->db->select('st.*,customer.*,users.user_name')
                        ->where('st.id_sale_token', $idtoken)
                        ->where('st.idcustomer = customer.id_customer')->from('customer')
                        ->where('st.idsalesperson = users.id_users')->from('users')
                        ->get('sale_token st')->row();
    }
    public function get_sale_token_product_byid($idtoken) {
        return $this->db->where('stp.idsaletoken', $idtoken)->get('sale_token_product stp')->result();
    }
    public function get_sale_token_payment_byid($idtoken) {
        return $this->db->where('stp.idsaletoken', $idtoken)
                        ->where('stp.idpayment_head = payment_head.id_paymenthead')->from('payment_head')
                        ->where('stp.idpayment_mode = payment_mode.id_paymentmode')->from('payment_mode')
                        ->get('sale_token_payment stp')->result();
    }
    public function update_sale_token_byid($idtoken, $data) {
        return $this->db->where('id_sale_token', $idtoken)->update('sale_token',$data);
    }
    function get_gstin_details($gstno) {
        $api_key='CMfRhG5DuTP1aVlfppLqyP9em762';
        $data=array();
        $newurl = "https://appyflow.in/api/verifyGST?gstNo=".$gstno."&key_secret=".$api_key;
        $result = $this->rest->request($newurl, "GET", $data);
        $result = json_decode($result, true);        
        return $result;
    }
    public function edit_customer_bycontact($contact, $data) {
        $this->db->where('customer_contact', $contact)->update('customer', $data);
    }
    public function save_batch_insurance_recon($data) {
        return $this->db->insert_batch('insurance_reconciliation', $data);
    }
    public function delete_stock_byimei($imei) {
        return $this->db->where('imei_no',$imei)->delete('stock');
    }
    
     public function get_imei_history_byimei($imei){
        return $this->db->where('imei_no', $imei)->get('imei_history')->row();
    }
    
    //online godown
    public function ajax_online_stock_data_byimei_branch($imei, $branch){
        return $this->db->where('imei_no', $imei)->where('idbranch', $branch)
                        ->where('stock.idgodown', 6)
                        ->where('stock.idgodown = godown.id_godown')->from('godown')
                        ->where('stock.idcategory = category.id_category')->from('category')
                        ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
                        ->get('stock')->result();
    }
    
    /**********  change imei ****************/
    
    public function get_imei_from_stock($imei){
        return $this->db->where('imei_no', $imei)->get('stock')->row();
    }
    public function get_inward_data_byimei($old_imei){
        $str = "SELECT * FROM `inward_data` WHERE `imei_srno` LIKE '%$old_imei%' ";
        return $this->db->query($str)->row();
    }
    public function update_stock_imei_no($data, $old_imei){
        return $this->db->where('imei_no', $old_imei)->update('stock', $data);
    }
    public function update_inward_product_imei_no($data, $old_imei){
        return $this->db->where('imei_no', $old_imei)->update('inward_product', $data);
    }
    public function update_inward_data_imei_no($data, $id){
        return $this->db->where('id_inward_data', $id)->update('inward_data', $data);
    }
    public function update_imei_history_imei_no($data, $old_imei){
        return $this->db->where('imei_no', $old_imei)->update('imei_history', $data);
    }
}
