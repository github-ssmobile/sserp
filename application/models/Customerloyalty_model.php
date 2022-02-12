<?php
class Customerloyalty_model extends CI_Model{
   
       public function save_cutomer_form_data($data){
          
           return $this->db->insert('customer_data_form', $data);
       }
       public function get_customer_form_data() {
           return $this->db->get('customer_data_form')->result();
       }
       public function edit_customer_form_data($id_data,$data) {
           return $this->db->where('id', $id_data)->update('customer_data_form', $data);
       }
       public function get_customer_formdata() {
           return $this->db->order_by("sequence", "asc")->where('status',1)->get('customer_data_form')->result();
       }
       public function get_state_bystate_name($state_name){
         $qy = "SELECT * FROM state WHERE state_name LIKE '%$state_name%'";
         // echo $str;die;
         $query = $this->db->query($qy);                
         return $query->result_array(); 
   
       }
       public function get_handset_customer_data_api($customer_contact) {
           $entrytime = date('Y-m-d H:i:s');
           $entrydate = date('Y-m-d');
           //print_r($entrydate);die;
           $data = $this->db->where('customer_contact',$customer_contact)->get('customer')->result();
           //echo '<pre>';
           $cust_fullname = $data[0]->customer_fname." ".$data[0]->customer_lname;
           $data1 = array(
           'cust_name' => $cust_fullname,
           'cust_fname' => $data[0]->customer_fname,
           'cust_lname' => $data[0]->customer_lname,  
           'cust_gst'   => $data[0]->customer_gst,
           'cust_mobile' => $data[0]->customer_contact,
           'idbranch' => $data[0]->idbranch,
           'cust_address' => $data[0]->customer_address,
           'cust_pincode' => $data[0]->customer_pincode,
           'cust_city' => $data[0]->customer_city,
           'cust_district' => $data[0]->customer_district,
           'cust_state' => $data[0]->customer_state,
           'cust_idstate' => $data[0]->idstate,
           'entry_date' => $entrydate,
           'entry_time' => $entrytime,
           'birth_date' => $data[0]->birth_date,  
           'customer_email' => $data[0]->customer_email,  
           'customer_gst' => $data[0]->customer_gst,
           'id_customer'=>$data[0]->id_customer 
           );
//           echo '<pre>';
//           print_r($data1);die;
            return $data1;
       }
       public function get_customer_contact($customer_contact) {
           $data = $this->db->where('customer_contact',$customer_contact)->get('customer')->result();
           $data_conut = count($data);
           return $data_conut;
       }
       public function save_customer_purchase_data($data,$branchid) {
           //print_r($data);die;
           $qy = "SELECT min_lab,max_lab,id_price_category_lab FROM price_category_lab";
           $query = $this->db->query($qy);
           $price_cat_array = $query->result_array();
//           echo '<pre>';
//           print_r($price_cat_array);die;
           $result = array();
           $id_price_category_lab ="";
           $sold_price = $data['sold_price'];
           //echo $sold_price;die;
           foreach ($price_cat_array as $value) {
            if($sold_price <= $value['max_lab'] && $value['min_lab'] <= $sold_price){
                $id_price_category_lab=$value['id_price_category_lab'];
           }
          }
          //echo $id_price_category_lab;die;
          $qy1 = "SELECT id_branch,idzone FROM branch where acc_branch_id=$branchid";
          $query1 = $this->db->query($qy1);
          $branch_id = $query1->result_array();
          //print_r($branch_id);die;
          $id_branch_acc =$branch_id[0]['id_branch'];
          $id_zone_acc =$branch_id[0]['idzone'];
          $price_cat = array('price_category' => $id_price_category_lab,'idbranch'=>$id_branch_acc,'idzone'=>$id_zone_acc);
          $result = array_merge($data, $price_cat);
          //print_r($result);die;
          return $this->db->insert('customer_purchase_data', $result);
       }
       public function save_customer_payment_data($data) {
//                    echo '<pre>';
//                    print_r($data);die;
             if($data['payment_type'] == "Partial Payment"){
                 if($data['cash_r'] > 0){
                     $payment_mode = 'Cash';
                     $data_payment = array(
                         'idcustomer'=>$data['custemr_id'],
                         'inv_no'=>$data['inv_no'],
                         'inv_date'=>$data['inv_date'], 
                         'payment_head'=> $payment_mode,
                         'payment_mode' => $payment_mode,
                         'amount' => $data['cash_r'],
                     );
                     $this->db->insert('customer_payment_history', $data_payment);
                 }
                 if($data['swipe_r'] > 0){
                    
                    $payment_mode = 'Swipe';
                    $data_payment = array(
                         'idcustomer'=>$data['custemr_id'],
                         'inv_no'=>$data['inv_no'],
                         'inv_date'=>$data['inv_date'], 
                         'payment_head'=> $payment_mode,
                         'payment_mode' => $payment_mode,
                         'amount' => $data['swipe_r'],
                         'idtransaction' => $data['rrn_number'],
                        );
                    
                     $this->db->insert('customer_payment_history', $data_payment);
                 }
                 if($data['credit_r'] > 0){
                    
                    $payment_mode = 'Credit';
                    $data_payment = array(
                         'idcustomer'=>$data['custemr_id'],
                         'inv_no'=>$data['inv_no'],
                         'inv_date'=>$data['inv_date'],
                         'payment_head'=> $payment_mode,
                         'payment_mode' => $payment_mode,
                         'amount' => $data['credit_r'],
                        );
                    
                     $this->db->insert('customer_payment_history', $data_payment);
                 }
                
                    
                    if($data['paytm_r']>0){
                        $payment_mode = 'Wallet';
                        $data_payment = array(
                         'idcustomer'=>$data['custemr_id'],
                         'inv_no'=>$data['inv_no'],
                         'inv_date'=>$data['inv_date'],
                         'payment_head'=> $payment_mode,
                         'payment_mode' => 'Paytm',
                         'amount' => $data['paytm_r'],
                         'idtransaction' => $data['paytm_transaction_id'],
                        );
                        $this->db->insert('customer_payment_history', $data_payment);
                    }
                    if($data['phonepay_r']>0){
                        $payment_mode = 'Wallet';
                        $data_payment = array(
                         'idcustomer'=>$data['custemr_id'],
                         'inv_no'=>$data['inv_no'],
                         'inv_date'=>$data['inv_date'],
                         'payment_head'=> $payment_mode,
                         'payment_mode' => 'PhonePe',
                         'amount' => $data['phonepay_r'],
                         'idtransaction' => $data['phone_transaction_id'],
                        );
                        $this->db->insert('customer_payment_history', $data_payment);
                    }
                    if($data['googlepay_r']>0){
                        $payment_mode = 'Wallet';
                         $data_payment = array(
                         'idcustomer'=>$data['custemr_id'],
                         'inv_no'=>$data['inv_no'],
                         'inv_date'=>$data['inv_date'],
                         'payment_head'=> $payment_mode,
                         'payment_mode' => 'Googlepay',
                         'amount' => $data['googlepay_r'],
                         'idtransaction' => $data['googlepay_transaction_id'],
                        );
                        $this->db->insert('customer_payment_history', $data_payment);
                    }
                    if($data['bajaj_r'] > 0){
                         $payment_mode = 'Finance';
                         $data_payment = array(
                         'idcustomer'=>$data['custemr_id'],
                         'inv_no'=>$data['inv_no'],
                         'inv_date'=>$data['inv_date'],
                         'payment_head'=> $payment_mode,
                         'payment_mode' => 'Bajaj',
                         'amount' => $data['bajaj_r'],
                         'idtransaction' => $data['sfid'],
                        );
                        $this->db->insert('customer_payment_history', $data_payment);
                    }
                    if($data['hdfc_r'] > 0){
                         $payment_mode = 'Finance';
                         $data_payment = array(
                         'idcustomer'=>$data['custemr_id'],
                         'inv_no'=>$data['inv_no'],
                         'inv_date'=>$data['inv_date'],
                         'payment_head'=> $payment_mode,
                         'payment_mode' => 'HDFC',
                         'amount' => $data['hdfc_r'],
                         'idtransaction' => $data['sfid'],    
                        );
                        $this->db->insert('customer_payment_history', $data_payment);
                    }
                    if($data['kissht_r'] > 0){
                         $payment_mode = 'Finance';
                         $data_payment = array(
                         'idcustomer'=>$data['custemr_id'],
                         'inv_no'=>$data['inv_no'],
                         'inv_date'=>$data['inv_date'],
                         'payment_head'=> $payment_mode,
                         'payment_mode' => 'IDFC_FIRST_Bank',
                         'amount' => $data['kissht_r'],
                         'idtransaction' => $data['sfid'],    
                        );
                        $this->db->insert('customer_payment_history', $data_payment);
                    }
                    if($data['hdb_r'] > 0){
                         $payment_mode = 'Finance';
                         $data_payment = array(
                         'idcustomer'=>$data['custemr_id'],
                         'inv_no'=>$data['inv_no'],
                         'inv_date'=>$data['inv_date'],
                         'payment_head'=> $payment_mode,
                         'payment_mode' => 'HDB',
                         'amount' => $data['hdb_r'],
                         'idtransaction' => $data['sfid'],    
                        );
                        $this->db->insert('customer_payment_history', $data_payment);
                    }
                    if($data['zest_money_r'] > 0){
                         $payment_mode = 'Finance';
                         $data_payment = array(
                         'idcustomer'=>$data['custemr_id'],
                         'inv_no'=>$data['inv_no'],
                         'inv_date'=>$data['inv_date'],
                         'payment_head'=> $payment_mode,
                         'payment_mode' => 'Zest_money',
                         'amount' => $data['zest_money_r'],
                         'idtransaction' => $data['sfid'],    
                        );
                        $this->db->insert('customer_payment_history', $data_payment);
                    }
                    if($data['samsung_sure_r'] > 0){
                         $payment_mode = 'Finance';
                         $data_payment = array(
                         'idcustomer'=>$data['custemr_id'],
                         'inv_no'=>$data['inv_no'],
                         'inv_date'=>$data['inv_date'],
                         'payment_head'=> $payment_mode,
                         'payment_mode' => 'Samsung_Sure',
                         'amount' => $data['samsung_sure_r'],
                         'idtransaction' => $data['sfid'],    
                        );
                        $this->db->insert('customer_payment_history', $data_payment);
                    }
                    if($data['cheque_r'] > 0){
                         $payment_mode = 'Cheque';
                         $data_payment = array(
                         'idcustomer'=>$data['custemr_id'],
                         'inv_no'=>$data['inv_no'],
                         'inv_date'=>$data['inv_date'],
                         'payment_head'=> $payment_mode,
                         'payment_mode' => 'Cheque',
                         'amount' => $data['cheque_r'],
                         'idtransaction' => $data['check_no'],    
                        );
                        $this->db->insert('customer_payment_history', $data_payment);
                    }
                 
             }else{
                  if($data['cash_r'] > 0){
                     $payment_mode = 'Cash';
                     $payment_head = 'Cash';
                     $amount = $data['cash_r'];
                     $idtransaction = "";
                  }
                  if($data['cheque_r'] > 0){
                     $payment_mode = 'Cheque';
                     $payment_head = 'Cheque';
                     $amount = $data['cheque_r'];
                     $idtransaction = "";
                  }
                   if($data['swipe_r'] > 0){
                     $payment_mode = 'Swipe';
                     $payment_head = 'Swipe';
                     $amount = $data['swipe_r'];
                     $idtransaction = $data['rrn_number'];
                  }
                  if($data['credit_r'] > 0){
                     $payment_mode = 'Credit';
                     $payment_head = 'Credit';
                     $amount = $data['credit_r'];
                     $idtransaction = "";
                  }
                  if($data['bajaj_r'] > 0){
                     $payment_mode = 'Finance';
                     $payment_head = 'Bajaj';
                     $amount = $data['bajaj_r'];
                     $idtransaction = $data['sfid'];
                  }
                  if($data['hdfc_r'] > 0){
                     $payment_mode = 'Finance';
                     $payment_head = 'HDFC';
                     $amount = $data['hdfc_r'];
                     $idtransaction = $data['sfid'];
                  }
                  if($data['kissht_r'] > 0){
                     $payment_mode = 'Finance';
                     $payment_head = 'IDFC_FIRST_Bank';
                     $amount = $data['kissht_r'];
                     $idtransaction = $data['sfid'];
                  }
                  if($data['hdb_r'] > 0){
                     $payment_mode = 'Finance';
                     $payment_head = 'HDB';
                     $amount = $data['hdb_r'];
                     $idtransaction = $data['sfid'];
                  }
                  if($data['zest_money_r'] > 0){
                     $payment_mode = 'Finance';
                     $payment_head = 'Zest_money';
                     $amount = $data['zest_money_r'];
                     $idtransaction = $data['sfid'];
                  }
                  if($data['samsung_sure_r'] > 0){
                     $payment_mode = 'Finance';
                     $payment_head = 'Samsung_Sure';
                     $amount = $data['samsung_sure_r'];
                     $idtransaction = $data['sfid'];
                  }
                  if($data['paytm_r'] > 0){
                     $payment_mode = 'Wallet';
                     $payment_head = 'Paytm';
                     $amount = $data['paytm_r'];
                     $idtransaction = $data['paytm_transaction_id'];
                  }
                  if($data['phonepay_r'] > 0){
                     $payment_mode = 'Wallet';
                     $payment_head = 'PhonePe';
                     $amount = $data['phonepay_r'];
                     $idtransaction = $data['phone_transaction_id'];
                  }
                  if($data['googlepay_r'] > 0){
                     $payment_mode = 'Wallet';
                     $payment_head = 'Googlepay';
                     $amount = $data['googlepay_r'];
                     $idtransaction = $data['googlepay_transaction_id'];
                  }
                 $data_payment = array(
                         'idcustomer'=>$data['custemr_id'],
                         'inv_no'=>$data['inv_no'],
                         'inv_date'=>$data['inv_date'],
                         'payment_head'=> $payment_mode,
                         'payment_mode' => $payment_head,
                         'amount' => $amount,
                         'idtransaction' => $idtransaction,    
                        );
                 $this->db->insert('customer_payment_history', $data_payment);
             }       
       }
       
       public function save_customer_purchase($data,$idcustomer,$creted_by,$cnt) {
//           echo '<pre>';
//           print_r($data);die;
           extract($data);
           $qy = "SELECT user_name FROM users WHERE id_users = $creted_by";
           $query = $this->db->query($qy);
           $creted_name_arr = $query->result(); 
           $creted_name =  $creted_name_arr[0]->user_name;
           
           $data_purchase = array(
               'erp_type'=>'Handset',
               'idcustomer'=>$idcustomer,
               'idproductcategory'=>$idproductcategory,
               'idcategory'=>$idcategory,
               'idbrand'=>$idbrand,
               'idmodel'=>$idmodel,
               'idvariant'=>$idvariant,
               'idbranch'=>$idbranch,
               'mop'=>$mop,
               'landing'=>$landing,
               'mrp'=>$mrp,
               'sold_price'=>$total_amount,
               'inv_no'=>$inv_no,
               'inv_date'=>$date,
               'product_name'=>$product_name,
               'created_by'=>$creted_name,
               'idcreated_by'=>$creted_by,
               'product_cnt'=>$cnt
           );
           $qy = "SELECT min_lab,max_lab,id_price_category_lab FROM price_category_lab";
           $query = $this->db->query($qy);
           $price_cat_array = $query->result_array();
            foreach ($price_cat_array as $value) {
            if($total_amount <= $value['max_lab'] && $value['min_lab'] <= $total_amount){
                $id_price_category_lab=$value['id_price_category_lab'];
           }
          }
          
          $qy1 = "SELECT idzone FROM branch WHERE id_branch = $idbranch";
          $query = $this->db->query($qy1);
          $zone_data = $query->result();
          //print_r($zone_data[0]->idzone);die;
          $zone_id = $zone_data[0]->idzone; 
          //print_r($zone_id);die;
          $price_cat = array('price_category' => $id_price_category_lab,'idzone'=>$zone_id);
          $result = array_merge($data_purchase, $price_cat);
          //print_r($result);die;
           return $this->db->insert('customer_purchase_data', $result);
       }
       
       public function save_handset_payment_history($data) {
           return $this->db->insert('customer_payment_history', $data);
       }
       
       public function ajax_get_paymentmode_idhead() {
           $idphead = $_POST['id_payhead'];
           if($idphead == 'All'){
            $payment_modes = $this->General_model->get_active_payment_head();
            $id_payment_modes=array();
            
            foreach ($payment_modes as $value) {
                array_push($id_payment_modes,$value->id_paymenthead);
            }
             
            $implode_heads = implode(",",$id_payment_modes);
           
               $qy="SELECT * from payment_mode where idpaymenthead IN($implode_heads)";
           }else{
              $qy="SELECT * from payment_mode where idpaymenthead=$idphead"; 
           }
           
           //echo $qy;die;
           $query = $this->db->query($qy);
           $paymode_array = $query->result();

           $str = "";
           $idpaymentmodes=array();
           $str  ="<option value='-1'>Select Payment Mode</option>
                   <option value='All'>All Payment Mode</option>";  
           foreach($paymode_array as  $paymode){
           $str .= "<option value='".$paymode->payment_mode."'>".$paymode->payment_mode."</option>";
           array_push($idpaymentmodes,$paymode->payment_mode);
           }
           $implode_modes = implode($idpaymentmodes,"','");
           $str_modes = "'$implode_modes'";
           //print_r($str_modes);die;
           $enqdata['payment_modes'] =$str;
           $enqdata['array_modes'] =$str_modes;
            //echo $str;
           echo json_encode($enqdata);die;
       }
       
       public function ajax_get_crm_report_data() {
           //extract($_POST);
           //print_r($_POST);die;
           $idzone = -1;
           $idbranch = -1;
           $id_day_filter = -1;
           $date_filter1 = "";
           $date_filter = "";
           $datefrom = "";
           $dateto = "";
           $idpayment_mode = -1;
           
           extract($_REQUEST);
//           echo '<pre>';
//           print_r($_REQUEST);die;
           
           if($idpayment_mode != -1){
            $qy="SELECT cst.*,zn.zone_name,brc.branch_name,cpd.inv_date,
                  GROUP_CONCAT(DISTINCT cph.payment_mode SEPARATOR ', ') as concact_payment_mode,
                  cpd.inv_no as mode_invno,
                  GROUP_CONCAT(DISTINCT cpd.product_name SEPARATOR ', ') as concact_product ";   
           }else{
            $qy= "SELECT cst.*,zn.zone_name,brc.branch_name,cpd.product_name,cph.payment_mode,cpd.inv_no,cph.inv_no as history_inv,cpd.inv_date,
                    (
                         CASE 
                             WHEN cpd.erp_type = 'Handset' THEN br.brand_name 
                             WHEN cpd.erp_type = 'accessories' THEN 'Accessories'
                         END
                     )AS erp_brand ";  
           }
           $qy.="FROM customer as cst
                   JOIN customer_purchase_data as cpd ON (cpd.idcustomer = cst.id_customer)
                   JOIN customer_payment_history as cph ON (cph.inv_no = cpd.inv_no)
                   JOIN branch AS brc ON (brc.id_branch = cpd.idbranch) 
                   JOIN zone AS zn ON (zn.id_zone = brc.idzone) 
                   JOIN brand AS br ON (br.id_brand = cpd.idbrand) 
                  WHERE"; 
      
            if($id_category == "All"){
             $qy.=" cpd.idproductcategory IN ($categorys)";   
            }else{
             $qy.=" cpd.idproductcategory='$id_category'";   
            }
           if($idbranch != -1){
            if($idbranch == "All"){
             $qy.=" AND cpd.idbranch IN ($branches)";   
            }else{
             $qy.=" AND cpd.idbranch=$idbranch";   
            }
           }
           if($idbrand != -1){
           if($idbrand == "All"){
            $qy.=" AND cpd.idbrand IN ($brands)";
           }else{
            $qy.=" AND cpd.idbrand='$idbrand'";   
           }
           }
           if($idzone != -1){
           if($idzone == "All"){
            $qy.=" AND cpd.idzone IN ('$zones')";   
           }else{
            $qy.=" AND cpd.idzone='$idzone'";   
           }
           }
           if($id_price_category != -1){
           if($id_price_category == "All"){
            $qy.=" AND cpd.price_category IN ($price_categorys)";   
           }else{
            $qy.=" AND cpd.price_category='$id_price_category'";   
           }
           }
           if($date_filter1 != "" && $date_filter !="" ){
            $qy.=" AND 	cph.inv_date BETWEEN '$date_filter1' AND '$date_filter'";   
           }
           if($pincode !="" ){
            $qy.=" AND cst.customer_pincode = '$pincode'";   
           }
           if($city !="" ){
            $qy.=" AND cst.customer_city = '$city'";   
           }
           if($datefrom != "" && $dateto !="" ){
            $qy.=" AND 	cst.birth_date BETWEEN '$datefrom' AND '$dateto'";   
           }
           if($id_day_filter != -1 ){
            $qy.=" AND cpd.inv_date BETWEEN CURDATE() - INTERVAL $id_day_filter DAY AND CURDATE()";   
           }
           if($idpayment_mode != -1){
             if($idpayment_mode == "All"){
             $qy.=" AND cph.payment_mode IN ($payment_modes)";
             $qy.=" GROUP BY
                    cph.inv_no ORDER BY cph.inv_date ";
             }else{
             $qy.=" AND cph.payment_mode LIKE '$idpayment_mode'";
             $qy.=" GROUP BY
                    cph.inv_no ORDER BY cph.inv_date ";
            }  
           }else{
              if($datefrom != "" && $dateto !="" ){
                 $qy.=" GROUP BY cst.customer_contact ";  
              }else{
                 $qy.=" GROUP BY
                 cpd.inv_no,
                 cpd.product_cnt ORDER BY cpd.inv_date ";  
              } 
              
           }

           //echo $qy;die;
           $query = $this->db->query($qy);
           $creted_name_arr = $query->result(); 
           //print_r($creted_name_arr);die;
           
            $msg="<table id='crm_table' class='table table-condensed table-bordered table-responsive' style='margin: 0'>
                <thead class='fixedelement'>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Contact No.</th>
                        <th>Branch Name</th>
                        ";
                        if($idpayment_mode == -1){
                        $msg.="<th>Birth Date</th>";
                        if($datefrom == "" && $dateto =="" ){       
                        $msg.="<th>Product Name</th>
                               <th>Inv No</th>
                               <th>Inv Date</th>";
                       
                        $msg.="<th>Brand</th>";
                        $msg.="<th>Zone</th>";
                        }   
                        $msg.="<th>Customer City</th> 
                               <th>Customer Pincode</th>";
                        }else{
                        $msg.="<th>Product Name</th>
                               <th>Payment Mode</th>";
                        $msg.="<th>Inv No</th>
                               <th>Inv Date</th>";
                        } 
                    $msg.="</thead><tbody class='data_1'>";
                $id=1;
                foreach($creted_name_arr as $d){
                    $birthdate = date("d-m-Y", strtotime($d->birth_date));
                    $inv_date = date("d-m-Y", strtotime($d->inv_date));
                $msg.="<tr>
                    <td>".$id."</td>
                    <td>".$d->customer_fname." ".$d->customer_lname."</td>
                    <td>".$d->customer_contact."</td>
                    <td>".$d->branch_name."</td>";
                    if($idpayment_mode == '-1'){    
                    $msg.="<td>".$birthdate."</td>";
                    if($datefrom == "" && $dateto =="" ){
                    $msg.="<td>".$d->product_name."</td>
                           <td>".$d->inv_no."</td>
                           <td>".$inv_date."</td>";
                   
                    $msg.="<td>".$d->erp_brand."</td>";
                    $msg.="<td>".$d->zone_name."</td>";
                    }
                    $msg.="<td>".$d->customer_city."</td>    
                           <td>".$d->customer_pincode."</td>";
                    }else{
                    $msg.="<td>".$d->concact_product."</td>";    
                    $msg.="<td>".$d->concact_payment_mode."</td>";
                    $msg.="<td>".$d->mode_invno."</td>";
                    $msg.="<td>".$inv_date."</td>";
                    }
                  $msg.="</tr>";
                    $id++;
                }
               $msg.="</tbody>
            </table>";
           $result= $msg;
           echo json_encode($result);die;
       }
}
?> 