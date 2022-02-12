<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_Model extends CI_Model {

    
    var $auth_key       = "89wrtg9qert589u29vn85u";

    public function check_auth_client(){
        
        $auth_key  = $this->input->get_request_header('Auth-Key', TRUE);        
        if( $auth_key == $this->auth_key){
            return true;
        } else {
            $j=json_encode(array('status' => 401,'message' => 'Unauthorized Access! Your session has been expired.'));
            die($j);
        }
    }

    public function login($username,$password,$session)
    {
        $q['login']=array();
        $this->db->select('*');
        $this->db->from('users'); 
        $this->db->join('branch b','users.idbranch = b.id_branch','left');
        $this->db->where('userid',$username)->where('user_password',$password);
        $this->db->where('users.active =1');
        $this->db->where('users.iduserrole = user_role.id_userrole')->from('user_role');
        $q['login'] = $this->db->get()->result();    
        
        $result=array();
       
        if(count($q['login'])==0){            			
				$result['status']=204;
				$result['message']='Username not found';
				$result['data']=$q;                 
				return $result;            
        } else {                
               $id              = $q['login'][0]->id_users;            
               $last_login = date('Y-m-d H:i:s');
               $token = crypt(substr( md5(rand()), 0, 7),$id);
              
               $q['login'][0]->token=$token;
                $is_session_aval = $this->db->where('users_id', $id)->get('users_authentication')->result();          
                if(count($is_session_aval)>0){                    
                    if($session==1){
                        $this->db->where('users_id',$id)->delete('users_authentication');  
                        $expired_at = date("Y-m-d H:i:s", strtotime('+12 hours'));
                        $this->db->trans_start();
                        $this->db->where('id_users',$id)->update('users',array('last_login' => $last_login));
                        $this->db->insert('users_authentication',array('users_id' => $id,'token' => $token,'expired_at' => $expired_at));
                        if ($this->db->trans_status() === FALSE){
                           $this->db->trans_rollback();
                           $result['status']=500;
                           $result['message']='Internal server error.';
                           $result['data']=$q;
                           return $result;
                        } else {
                           $this->db->trans_commit();
                           $result['status']=200;
                           $result['message']='Successfully login.';
                           $result['data']=$q;                 
                           return $result;
                        }
                    }else{
                        $result['status']=205;
                        $result['message']='You are already logged in on another device! Do you want to login here?';
                        $result['data']=$q;                 
                        return $result;
                    }
                }else{
                    $expired_at = date("Y-m-d H:i:s", strtotime('+12 hours'));
                        $this->db->trans_start();
                        $this->db->where('id_users',$id)->update('users',array('last_login' => $last_login));
                        $this->db->insert('users_authentication',array('users_id' => $id,'token' => $token,'expired_at' => $expired_at));
                        if ($this->db->trans_status() === FALSE){
                           $this->db->trans_rollback();
                           $result['status']=500;
                           $result['message']='Internal server error.';
                           $result['data']=$q;
                           return $result;
                        } else {
                           $this->db->trans_commit();
                           $result['status']=200;
                           $result['message']='Successfully login.';
                           $result['data']=$q;                 
                           return $result;
                        }
                }               
                           
        }
    }
    
    public function get_customer_bycontact($contact) {
       
        $q['customer'] = $this->db->where('customer_contact', $contact)->get('customer')->result();
          
        if(count($q['customer'])==0){            
            return array('status' => 204,'message' => 'Customer not found.', 'data' => $q );
        } else { 
            return array('status' => 200,'message' => 'Verification successfull', 'data' => $q );
        }
    }
    
    public function get_app_version() {
       
        $q['app_version'] = $this->db->get('app_version')->result();
          
        if(count($q['app_version'])==0){            
            return array('status' => 204,'message' => 'Customer not found.', 'data' => $q );
        } else { 
            return array('status' => 200,'message' => 'Verification successfull', 'data' => $q );
        }
    }
    
     public function get_lat_long_by_address($address) {
                $newurl = "http://maps.google.com/maps/api/geocode/json?address=".$address;
		$data = array();
		$result = $this->rest->request($newurl,"GET",$data);
		$result = json_decode($result, true);
		
		return $result;
     }
 
    public function logout()
    {
        $users_id  = $this->input->get_request_header('User-ID', TRUE);
        $token     = $this->input->get_request_header('Authorization', TRUE);
        $this->db->where('users_id',$users_id)->delete('users_authentication');
        $j=json_encode(array('status' => 200,'message' => 'Successfully logout.'));
        return $j;
        
    }

    public function auth()
    {
        $users_id  = $this->input->get_request_header('User-ID', TRUE);
        $token     = $this->input->get_request_header('Authorization', TRUE);
        $q  = $this->db->select('expired_at')->from('users_authentication')->where('users_id',$users_id)->where('token',$token)->get()->result();        
        if(count($q)==0){             
            $this->db->where('users_id',$users_id)->delete('users_authentication');
            $j=json_encode(array('status' => 401,'message' => 'Unauthorized Access! Your session has been expired.'));
              die($j);
        } else {
             
            if($q[0]->expired_at < date('Y-m-d H:i:s')){             
                $this->db->where('users_id',$users_id)->delete('users_authentication');
                 $j=json_encode(array('status' => 401,'message' => 'Unauthorized Access! Your session has been expired.'));
                  die($j);
            } else {                
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = date("Y-m-d H:i:s", strtotime('+2 hours'));
                $this->db->where('users_id',$users_id)->where('token',$token)->update('users_authentication',array('expired_at' => $expired_at,'updated_at' => $updated_at));
                return array('status' => 200,'message' => 'Authorized.');
            }
        }
        
    }
   
    public function ajax_token_byimei_branch($imei, $branch){
        return $this->db->where('st.status = 0')->where('st.id_sale_token= stp.idsaletoken')
                        ->where('stp.imei_no',$imei)->where('stp.idbranch', $branch)->from('sale_token st')
                        ->get('sale_token_product stp')->result();
    }
    
    public function ajax_token_byid_branch_godown($variant, $idbranch, $idgodown) {        
         return $this->db->select('sum(stp.qty) as token_qty')->where('st.status = 0')->where('stp.idvariant',$variant)->where('st.id_sale_token= stp.idsaletoken')
                        ->where('stp.idgodown',$idgodown)->where('stp.idbranch', $idbranch)->from('sale_token st')
                        ->get('sale_token_product stp')->result();                  
    }
    public function ajax_get_variant_byid_branch_godown($variant, $idbranch, $idgodown) {
        return $this->db->select('*,sum(stock.qty) as qty')->where('stock.idvariant', $variant)
                        ->where('stock.idbranch', $idbranch)
                        ->where('stock.idgodown', $idgodown)
                        ->where('stock.idvariant = model_variants.id_variant')->from('model_variants')
                        ->where('stock.idcategory = category.id_category')->from('category')
                        ->get('stock', 1)->result();
         
    }
    public function ajax_get_variant_for_saletype_2($variant, $idbranch, $idgodown) {
                        $this->db->select('stk.*,mv.*')->where('mv.id_variant', $variant)                                                
                        ->where('mv.idcategory = category.id_category')->from('category');
                         $this->db->join('(select * from stock WHERE idvariant='.$variant.' and idgodown='.$idgodown.' and idbranch='.$idbranch.') stk', 'stk.idvariant=mv.id_variant','left');                         
          return $this->db->get('model_variants mv', 1)->result();
    }
    public function save_stock($data) {
         $this->db->insert('stock', $data);
        return $this->db->insert_id();
    }
    public function delete_stock_byidstock($idstock) {
        return $this->db->where('id_stock',$idstock)->delete('stock');
    }
    public function minus_stock_by_idstock($data){           
        return $this->db->query($data);        
    }
    
    public function save_sale_token($data){
        $this->db->insert('sale_token', $data);
        return $this->db->insert_id();
    }
    public function save_sale_token_product($data) {
        $this->db->insert('sale_token_product', $data);
        return $this->db->insert_id();
    }
    public function save_sale_token_payment($data) {
        $this->db->insert('sale_token_payment', $data);
        return $this->db->insert_id();
    }
    
    public function get_current_promoter_target($idpromotor, $idbranch,$date) {        
        $year_mnt = date("Y-m",strtotime($date));        
        return $this->db->select('*')->where('idbranch', $idbranch)
                        ->where('idpromotor', $idpromotor)
                        ->where('month_year', $year_mnt)
                        ->where('id_targetslab = (select `id_target_slab` from target_slab where "'.$date.'" between `from_date` and `to_date` and `month_year`="'.$year_mnt.'")')                        
                        ->get('promotor_target_setup', 1)->result();    
//		die($this->db->last_query());				
    }    
    public function get_payment_summary_byidpromoter_date($idpromotor, $idbranch,$date) {
                $this->db->select('payment_head.payment_head,pr_data.amt');
                $this->db->join("(select pr.idpayment_head,sum(pr.amount) as amt from sale_payment pr,sale WHERE sale.idsalesperson='$idpromotor' and pr.idbranch='$idbranch'  and  pr.idsale = sale.id_sale and pr.date = '$date' group by pr.idpayment_head,pr.idbranch ) pr_data", 'pr_data.idpayment_head = payment_head.id_paymenthead', 'left');
        return  $this->db->where('payment_head.active', 1)->get('payment_head')->result();    
						
    }
    
    public function get_drr_promotor_sale_report_slab_byidpromoter($from,$from_slab,$idslab,$idpcat, $allpcats, $idbranch,$idpromoter,$type){ //type 1 = drr 2 = mtd
        $month_year = date('Y-m',strtotime($from));
        $c_from = $from_slab;
        $branches[] = $idbranch;
        
        if($idpcat == 0 ){
            $pcatss =  $allpcats;
             $pcats = explode(',',$allpcats);
        }else{
            $pcats[] = $idpcat;
            $pcatss = $idpcat;
        }        
        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.idpromotor,prom.pasp,prom.prevenue, csp.csale_qty,csp.ctotal,csp.clanding,  last_prom.last_pvolume,last_prom.last_pvalue,last_csp.last_csale_qty,last_csp.last_ctotal ');
        $this->db->where_in('b.id_branch', $branches);
        $this->db->where('b.is_warehouse',0);
        if($idslab != 0){
            $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.idpromotor='$idpromoter' and p.idbranch='$idbranch' and p.month_year = '$month_year' and p.id_targetslab = $idslab and p.idproductcategory in($pcatss) ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        }else{
            $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE  p.idpromotor='$idpromoter' and p.idbranch='$idbranch' and  p.month_year = '$month_year' and p.idproductcategory in($pcatss) ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        }
        
        if($type==1){
            $this->db->select('sp.sale_qty,sp.total,sp.landing');
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE sale.idsalesperson='$idpromoter' and sale.idbranch='$idbranch' and  s.idsale = sale.id_sale and s.date = '$from' and s.idproductcategory in($pcatss)  ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
            $this->db->join("(select sum(cs.qty) as csale_qty, sum(cs.total_amount) as ctotal,sum(cs.landing) as clanding, cs.idbranch,sale.idsalesperson from sale_product cs,sale WHERE  sale.idsalesperson='$idpromoter' and cs.idbranch='$idbranch' and cs.idsale = sale.id_sale and cs.date >= '$c_from' and cs.date < '$from' and cs.idproductcategory in($pcatss)  ) csp", 'csp.idbranch = b.id_branch and csp.idsalesperson = users.id_users', 'left');
        }else if($type==2){            
            $this->db->join("(select sum(cs.qty) as csale_qty, sum(cs.total_amount) as ctotal,sum(cs.landing) as clanding, cs.idbranch,sale.idsalesperson from sale_product cs,sale WHERE  sale.idsalesperson='$idpromoter' and cs.idbranch='$idbranch' and cs.idsale = sale.id_sale and cs.date >= '$c_from' and cs.date <= '$from' and cs.idproductcategory in($pcatss)  ) csp", 'csp.idbranch = b.id_branch and csp.idsalesperson = users.id_users', 'left');
        }
        $this->db->join("(select sum(last_p.volume) as last_pvolume, sum(last_p.value) as last_pvalue, last_p.idbranch,last_p.idpromotor from promotor_target_setup last_p WHERE last_p.idpromotor='$idpromoter' and last_p.idbranch='$idbranch'  and last_p.month_year = '$month_year' and last_p.to_slab < '$from_slab' and last_p.idproductcategory in($pcatss)  ) last_prom", 'last_prom.idbranch = b.id_branch and last_prom.idpromotor = users.id_users', 'left');
         $this->db->join("(select sum(cs.qty) as last_csale_qty, sum(cs.total_amount) as last_ctotal, cs.idbranch,sale.idsalesperson from sale_product cs,sale WHERE sale.idsalesperson='$idpromoter' and cs.idbranch='$idbranch'  and  cs.idsale = sale.id_sale and cs.date >= '$month_year-01' and cs.date < '$from_slab' and cs.idproductcategory in($pcatss)  ) last_csp", 'last_csp.idbranch = b.id_branch and last_csp.idsalesperson = users.id_users', 'left');
        
        $this->db->where('users.id_users', $idpromoter); 
        $this->db->where('users.iduserrole', 17);
        $this->db->where('users.active', 1);
        $this->db->where('b.id_branch = users.idbranch')->from('users');
        $this->db->where('b.idzone= zone.id_zone')->from('zone');
        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
		//die($this->db->last_query());
    }

    
    public function get_promotor_sale_report_byidbranch_idpromoter($from, $to, $idpcat,  $idbranch,$idpromoter){
        $month_year = date('Y-m',strtotime($from));                
        $pcatss = $idpcat;
        $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.pasp,prom.prevenue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing');
        $this->db->where('b.id_branch', $idbranch);
        $this->db->where('b.is_warehouse',0);
        $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.idpromotor='$idpromoter' and p.idbranch='$idbranch' and p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing,s.idbranch,sale.idsalesperson from sale_product s,sale WHERE sale.idsalesperson='$idpromoter' and s.idbranch='$idbranch' and s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
        $this->db->where('users.iduserrole', 17);
        $this->db->where('users.active', 1);
        $this->db->where('users.id_users', $idpromoter); 
        $this->db->where('b.id_branch = users.idbranch')->from('users');
        $this->db->where('b.idzone= zone.id_zone')->from('zone');
        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
        $this->db->from('branch b');
         $query = $this->db->get();  
        return $query->result();

    }
   
   
    
    public function get_lmtd_promotor_sale_report_byidbranch_idpromoter($month,$lastmonth, $idpcat,$idbranch,$idpromoter){
         $c_from = $month.'-01';
        $c_to = date('Y-m-d');
        $day = date('d');
        
        $last_from = $lastmonth.'-01';
        $last_to = $lastmonth.'-'.$day;
        $branches[] = $idbranch;
        $pcats = $idpcat;
        
        $this->db->select('users.id_users, users.user_name,b.id_branch,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,curr_sp.sale_qty,curr_sp.total as sale_total,curr_smart_sp.smart_sale_qty,curr_smart_sp.smart_total,last_sp.lsale_qty,last_sp.ltotal as last_sale_total,last_smart_sp.lsmart_sale_qty,last_smart_sp.lsmart_total,curr_sp.landing as sale_landing,curr_smart_sp.smart_landing,last_sp.llanding as last_sale_landing,last_smart_sp.lsmart_landing');
        $this->db->where_in('b.id_branch', $branches);
        $this->db->where('b.is_warehouse',0);
        $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing, s.idbranch,sale.idsalesperson from sale_product s,sale WHERE sale.idsalesperson='$idpromoter' and s.idbranch='$idbranch' and  s.idsale = sale.id_sale and s.date between '$c_from' and '$c_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch,sale.idsalesperson ) curr_sp", 'curr_sp.idbranch = b.id_branch and curr_sp.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(ss.qty) as smart_sale_qty, sum(ss.total_amount) as smart_total,sum(ss.landing) as smart_landing,ss.idbranch,sale.idsalesperson from sale_product ss,sale WHERE sale.idsalesperson='$idpromoter' and ss.idbranch='$idbranch' and ss.idsale = sale.id_sale and  ss.date between '$c_from' and '$c_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch,sale.idsalesperson) curr_smart_sp", 'curr_smart_sp.idbranch = b.id_branch and curr_smart_sp.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(s.qty) as lsale_qty, sum(s.total_amount) as ltotal,sum(s.landing) as llanding,s.idbranch,sale.idsalesperson from sale_product s,sale WHERE sale.idsalesperson='$idpromoter' and s.idbranch='$idbranch' and s.idsale = sale.id_sale and s.date between '$last_from' and '$last_to' and s.idproductcategory in($pcats) GROUP BY s.idbranch,sale.idsalesperson ) last_sp", 'last_sp.idbranch = b.id_branch and last_sp.idsalesperson = users.id_users', 'left');
        $this->db->join("(select sum(ss.qty) as lsmart_sale_qty, sum(ss.total_amount) as lsmart_total,sum(ss.landing) as lsmart_landing, ss.idbranch,sale.idsalesperson from sale_product ss,sale WHERE sale.idsalesperson='$idpromoter' and ss.idbranch='$idbranch' and ss.idsale = sale.id_sale and ss.date between '$last_from' and '$last_to' and ss.idcategory in(1,32) GROUP BY ss.idbranch,sale.idsalesperson ) last_smart_sp", 'last_smart_sp.idbranch = b.id_branch and last_smart_sp.idsalesperson = users.id_users', 'left');
        $this->db->where('users.iduserrole', 17);
        $this->db->where('users.active', 1);
        $this->db->where('users.id_users',$idpromoter)->where('b.id_branch = users.idbranch')->from('users');
        $this->db->where('b.idzone= zone.id_zone')->from('zone');
        $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
        $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
        $this->db->from('branch b');
        $query = $this->db->get();  
        return $query->result();
    }
    
      public function get_promotor_target_ach_byidbranch_idpromoter($monthyear, $idpcat, $idbranch, $idpromoter){
            $month_year = $monthyear;

            $day = date('d');
            $from = $month_year.'-01';
            $to = $month_year.'-'.$day;
            $branches[] = $idbranch;            
            $pcats[] = $idpcat;
            $pcatss = $idpcat;

            $this->db->select('users.id_users, users.user_name,b.branch_name,zone.zone_name,partner_type.partner_type,branch_category.branch_category_name,prom.pvolume,prom.pvalue,prom.pasp,prom.prevenue,prom.idpromotor,sp.sale_qty,sp.total,sp.landing,smartsp.smart_qty,rudram.rudram_qty,finance.finance_qty');
            $this->db->where_in('b.id_branch', $branches);
            $this->db->where('b.is_warehouse',0);
            $this->db->join("(select sum(p.volume) as pvolume, sum(p.value) as pvalue,sum(p.asp) as pasp,sum(p.revenue) as prevenue, p.idbranch,p.idpromotor from promotor_target_setup p WHERE p.idpromotor='$idpromoter' and p.idbranch='$idbranch' and  p.month_year = '$month_year' and p.idproductcategory in($pcatss) GROUP BY p.idbranch, p.idpromotor ) prom", 'prom.idbranch = b.id_branch and prom.idpromotor = users.id_users', 'left');
            $this->db->join("(select sum(s.qty) as sale_qty, sum(s.total_amount) as total,sum(s.landing) as landing,s.idbranch,sale.idsalesperson from sale_product s,sale WHERE sale.idsalesperson='$idpromoter' and s.idbranch='$idbranch' and  s.idsale = sale.id_sale and s.date between '$from' and '$to' and s.idproductcategory in($pcatss) GROUP BY s.idbranch, sale.idsalesperson ) sp", 'sp.idbranch = b.id_branch and sp.idsalesperson = users.id_users', 'left');
            $this->db->join("(select sum(ss.qty) as smart_qty,ss.idbranch,sale.idsalesperson from sale_product ss,sale WHERE  sale.idsalesperson='$idpromoter' and ss.idbranch='$idbranch' and ss.idsale = sale.id_sale and ss.date between '$from' and '$to' and  ss.idcategory in(1,32) and ss.idproductcategory in($pcatss) GROUP BY ss.idbranch, sale.idsalesperson ) smartsp", 'smartsp.idbranch = b.id_branch and smartsp.idsalesperson = users.id_users', 'left');
            $this->db->join("(select sum(sss.qty) as rudram_qty,sss.idbranch,sale.idsalesperson from sale_product sss,sale WHERE sale.idsalesperson='$idpromoter' and sss.idbranch='$idbranch' and  sss.idsale = sale.id_sale and sss.date between '$from' and '$to' and sss.idbrand = 29  GROUP BY sss.idbranch, sale.idsalesperson ) rudram", 'rudram.idbranch = b.id_branch and rudram.idsalesperson = users.id_users', 'left');
            $this->db->join("(select count(spay.id_salepayment) as finance_qty,spay.idbranch,sale.idsalesperson from sale_payment spay,sale_product, sale WHERE sale.idsalesperson='$idpromoter' and spay.idbranch='$idbranch' and spay.idsale = sale.id_sale and sale_product.idsale = spay.idsale   and spay.idpayment_head = 4 and spay.date between '$from' and '$to' and sale_product.idcategory in(1,32) and sale_product.idproductcategory in($pcatss) GROUP BY spay.idbranch, sale.idsalesperson ) finance", 'finance.idbranch = b.id_branch and finance.idsalesperson = users.id_users', 'left');
            $this->db->where('users.iduserrole', 17);
            $this->db->where('users.active', 1);
            $this->db->order_by('zone.id_zone,b.id_branch');
            $this->db->where('users.id_users',$idpromoter)->where('b.id_branch = users.idbranch')->from('users');
            $this->db->where('b.idzone= zone.id_zone')->from('zone');
            $this->db->where('b.idbranchcategory = branch_category.id_branch_category')->from('branch_category');
            $this->db->where('b.idpartner_type = partner_type.id_partner_type')->from('partner_type');
            $this->db->from('branch b');
             $query = $this->db->get();  
            return $query->result();
        }
        
        
        public function get_promotor_target_setup_data($monthyear, $idpcat,$idpromoter,$idbranch){       
                    $this->db->select('sum(pts.volume) as volume,sum(pts.value) as value, sum(pts.asp) as asp, sum(pts.revenue) as revenue, sum(pts.connect) as connect , brand.brand_name,branch.branch_name,users.user_name, target_slab.slab_name')                                                                
                        ->where('pts.idpromotor = users.id_users')->from('users')                        
                        ->where('pts.idbrand = brand.id_brand')->from('brand')
                        ->where('pts.idbranch = branch.id_branch')->from('branch'); 
                    $this->db->join('promotor_target_setup pts',"pts.id_targetslab = target_slab.id_target_slab and pts.idpromotor='$idpromoter' and pts.month_year='$monthyear' and pts.idproductcategory='$idpcat' and pts.idbranch ='$idbranch'",'left');      
                    return $this->db->group_by('target_slab.id_target_slab')
                        ->order_by('target_slab.id_target_slab')
                        ->get('target_slab')->result();
              
    }
    
       
        
     function short_url($url) {        
        $data = array("longDynamicLink" =>"https://ssmobile.page.link/?link=".$url);  
        $e=json_encode($data);
        $newurl = "https://firebasedynamiclinks.googleapis.com/v1/shortLinks?key=AIzaSyCsV4OaLPu_okJ0qZN8e7uh49oGc8T55go";
        $result = $this->rest->request($newurl, "POST", $e);
        $result = json_decode($result, true);          
        return $result;
    }
    public function find_sale_token_by_tokenuid($uid)
    {
        return $this->db->select('*')->where('token_uid',$uid)->get('sale_token')->result();
    }
    public function find_sale_by_tokenuid($uid)
    {
        return $this->db->select('*')->where('token_uid',$uid)->get('sale')->result();
    }
        public function ajax_get_promoter_sale_data_byfilter($from, $to, $idbranch, $idpcat, $idbrand,$idsalesperson){
                $this->db->select('sale.date,sale.entry_time,sale_product.idsale,sale_product.inv_no,sale.customer_fname,sale.customer_lname,sale.customer_contact,sale.customer_gst,sale_product.imei_no,product_category.product_category_name,brand.brand_name,sale_product.product_name,sale_product.total_amount,users.user_name, sale_product.landing,sale_product.mop,sale_product.mrp,model_variants.full_name,sale_product.nlc_price,users.id_users,sale_product.idbranch')
                    ->where('sale_product.idbranch', $idbranch);
                if($idpcat){
                    $this->db->where_in('sale_product.idproductcategory', $idpcat);                    
                }
                if($idbrand){
                    $this->db->where('sale_product.idbrand', $idbrand);
                }                    
        return $this->db->where('sale_product.date >=', $from)
                ->where('sale_product.date <=', $to)
                ->where('sale.corporate_sale', 0)
                ->where('sale.idsalesperson', $idsalesperson)                                
                ->join('sale','sale_product.idsale = sale.id_sale', 'left')
                ->join('users','sale.idsalesperson = users.id_users', 'left')
                ->join('product_category', 'sale_product.idproductcategory = product_category.id_product_category', 'left')
                ->join('brand', 'sale_product.idbrand = brand.id_brand', 'left')    
                 ->join('model_variants','sale_product.idvariant = model_variants.id_variant', 'left')
                ->order_by('sale_product.date','ASC')
                ->get('sale_product')->result();
    }
    

}
