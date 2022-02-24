<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Hostbook_Model extends CI_Model {

	/*************** eway bill api ******************/
	function getLoginData($data, $Secret_Key) {
		$url="https://sandboxgst.hostbooks.in/ewbapi/api/ewb/logintally";
		$authorization = array('Secret-Key:'.$Secret_Key);
		$result = $this->rest->request($url, "POST", json_encode($data), 0, $authorization);

		$result = json_decode($result, true);
		return $result;
	}

	function getauthData($data, $Secret_Key) {
		$url="https://sandboxgst.hostbooks.in/ewbapi/api/ewb/generate-token";
		$authorization = $Secret_Key;
		$result = $this->rest->request($url, "POST", json_encode($data), 0, $authorization);

		$result = json_decode($result, true);
		return $result;
	}
	function getEwayBillData($data, $Secret_Key) {
		$url="https://sandboxgst.hostbooks.in/ewbapi/api/ewb/generate-bill";
		$authorization = $Secret_Key;
		$result = $this->rest->request($url, "POST", json_encode($data), 0, $authorization);

		$result = json_decode($result, true);
		return $result;
	}

	/*************** einvoice api ******************/
	function getLoginEinvData($data, $Secret_Key) {
		$url="http://sandboxgst.hostbooks.in/GSTTALLY/api/Login/signin";
		$authorization = array('Secret-Key:'.$Secret_Key);
		$result = $this->rest->request($url, "POST", json_encode($data), 0, $authorization);

		$result = json_decode($result, true);
		return $result;
	}
	function getAuthEinvData($data, $Secret_Key) {
		$url="http://sandboxgst.hostbooks.in/GSTTALLY/api/Login/authenticate";
		$authorization = array('Secret-Key:'.$Secret_Key);
		$result = $this->rest->request($url, "POST", json_encode($data), 0, $authorization);

		$result = json_decode($result, true);
		return $result;
	}
	function getAuthTokenEinvData($gstno,$Secret_Key) {
		$url="http://sandboxgst.hostbooks.in/GSTTALLY/api/Einvoice/GetAuthToken?gstin=27AADCK7940H006";
		$authorization = $Secret_Key;
		
		$result = $this->rest->request($url, "GET",'', 1, $authorization);

		$result = json_decode($result, true);
		return $result;
	}

	function hb_einv_irn_number($data, $Secret_Key) {
		$url="http://sandboxgst.hostbooks.in/GSTTALLY/api/Einvoice/GenerateIRN";
		// $authorization = array('Secret-Key:'.$Secret_Key);
		$result = $this->rest->request($url, "POST", json_encode($data), 0, $Secret_Key);

		$result = json_decode($result, true);
		return $result;
	}
	function hb_einv_eway_data($data, $Secret_Key) {
		$url="http://sandboxgst.hostbooks.in/GSTTALLY/api/Einvoice/GenerateEwayBill";
		// $authorization = array('Secret-Key:'.$Secret_Key);
		$result = $this->rest->request($url, "POST", json_encode($data), 0, $Secret_Key);

		$result = json_decode($result, true);
		return $result;
	}
	
	function getewaybillprint($ewbno,$Secret_Key,$eway_btype) {
		$url="http://sandboxgst.hostbooks.in/GSTTALLY/api/Einvoice/GeneratePrintEwbByEBNo?ebNo=".$ewbno."&isDetailed=false&ebFrom=".$eway_btype;
		$authorization = $Secret_Key;
		
		$result = $this->rest->request($url, "GET",'', 1, $authorization);

		$result = json_decode($result, true);
		return $result;
	}


	public function get_outword_byid($idsale) {
		return $this->db->select('o.*, o.dispatch_date as `invoice_date`,
			`users`.`user_name`, `print_head`.*,  `branch`.* ,`b`.id_branch buyer_id, `b`.branch_name buyer_name, `b`.branch_gstno buyer_gst, `b`.branch_address buyer_add, `b`.branch_pincode buyer_pin, 
			`b`.branch_state_name buyer_state, `b`.branch_district buyer_dist, `b`.branch_city buyer_city, `b`.branch_contact buyer_contact, `b`.branch_contact_person buyer_person')->where('o.id_outward',$idsale)
		->where('branch.idprinthead = print_head.id_print_head')->from('print_head')
		->where('o.idwarehouse=branch.id_branch')->from('branch')
		->where('o.idbranch=b.id_branch')->from('branch b')

		->where(' o.outward_by=users.id_users')->from('users')
		->order_by('id_outward','desc')
		->get('outward o')->result();
	}

	public function get_transfer_byid($idsale) {
		return $this->db->select('o.id_transfer id_outward, o.idbranch,o.gst_type, o.dispatch_date as `invoice_date`,
			`users`.`user_name`, `print_head`.*,  `branch`.* ,`b`.id_branch buyer_id, `b`.branch_name buyer_name, `b`.branch_gstno buyer_gst, `b`.branch_address buyer_add, `b`.branch_pincode buyer_pin, 
			`b`.branch_state_name buyer_state, `b`.branch_district buyer_dist, `b`.branch_city buyer_city, `b`.branch_contact buyer_contact, `b`.branch_contact_person buyer_person')->where('o.id_transfer',$idsale)
		->where('branch.idprinthead = print_head.id_print_head')->from('print_head')
		->where('o.transfer_from=branch.id_branch')->from('branch')
		->where('o.idbranch=b.id_branch')->from('branch b')

		->where(' o.approved_by=users.id_users')->from('users')
		->order_by('id_transfer','desc')
		->get('transfer o')->result();
	}

	public function get_sale_byid($idsale) {
		return $this->db->select('o.id_sale id_outward, o.idbranch,o.gst_type, o.date as `invoice_date, o.entry_time as invoice_date,users.user_name,print_head.*,branch.*,`c`.id_customer buyer_id, concat(c.customer_fname,"",c.customer_lname) buyer_name, c.customer_gst buyer_gst, c.customer_address buyer_add, c.customer_pincode buyer_pin, 
			c.customer_state buyer_state, c.customer_district buyer_dist, customer_city buyer_city, c.customer_contact buyer_contact, c.customer_fname buyer_person')->where('id_sale',$idsale)
		->where('branch.idprinthead = print_head.id_print_head')->from('print_head')
		->where('o.idbranch=branch.id_branch')->from('branch')
		->where('o.idcustomer=c.id_customer')->from('customer c')
		->where('o.idsalesperson=users.id_users')->from('users')
		->order_by('id_sale','desc')
		->get('sale o')->result();
	}
	
	function getSaleInvoiceData($fromdate,$todate,$idcompany,$sale_id){
		return $this->db->select('id_sale, inv_no, `date`, customer_fname, customer_lname, customer_gst')->where('`date` between "'.$fromdate.'" and "'.$todate.'"')
		->where(' customer_gst!=""')
		 ->where('sale.idbranch = branch.id_branch')->from('branch')
		 ->where('idcompany',$idcompany)
		->order_by('id_sale','desc')
		->get('sale')->result();
	}
	function getEInvoiceDataReport($fromdate,$todate,$idcompany,$sale_id){
		return $this->db->select('id_sale, inv_no, `date`, customer_fname, customer_lname, customer_gst')->where('`date` between "'.$fromdate.'" and "'.$todate.'"')
		->where('customer_gst!=""')
		->order_by('id_sale','desc')
		->get('sale')->result();
	}
	

	public function get_branch_outword_by_id($idallocation,$idoutward) {         
		$this->db->select('b.branch_name,od.date as all_date,b.idcompany,od.idbranch as id_branch,od.idwarehouse as id_warehouse,od.status as a_status,od.status as o_status,g.godown_name,mv.full_name,od.*,op.*,sum(op.qty) qty,op.price,op.cgst_per,op.idoutward,GROUP_CONCAT(op.imei_no) as imei');
		$this->db->from('outward od');        
		$this->db->where('g.id_godown=op.idgodown')->from('godown g');        
		$this->db->join('outward_product op','op.idoutward=od.id_outward','left');                                 
		$this->db->join('model_variants mv','mv.id_variant=op.idvariant');
		$this->db->where('od.idbranch=b.id_branch')->from('branch b'); 
		if($idoutward){
			$this->db->where('od.id_outward', $idoutward);            
		}
		$this->db->group_by('op.idgodown,op.idvariant');
		$query = $this->db->get(); 
		return $query->result(); 
		
	}
	public function get_branch_transfer_by_id($idallocation,$idoutward) {         
		$this->db->select('`b`.`branch_name`, `od`.`date` as `all_date`, `b`.`idcompany`, `od`.`idbranch` as `id_branch`, `od`.`transfer_from` as `id_warehouse`, `od`.`status` as `a_status`,`od`.`status` as `o_status`, `g`.`godown_name`, `mv`.`full_name`, `od`.*,op.*, sum(op.qty) qty,`op`.`price`, `op`.`cgst_per`, `op`.`idtransfer`,GROUP_CONCAT(op.imei_no) as imei');
		$this->db->from('transfer od');        
		$this->db->where('g.id_godown=op.idgodown')->from('godown g');        
		$this->db->join('transfer_product op','op.idtransfer=od.id_transfer','left');                                 
		$this->db->join('model_variants mv','mv.id_variant=op.idvariant');
		$this->db->where('od.idbranch=b.id_branch')->from('branch b'); 
		if($idoutward){
			$this->db->where('od.id_transfer', $idoutward);            
		}
		$this->db->group_by('op.idgodown,op.idvariant');
		$query = $this->db->get(); 
		return $query->result(); 
		
	}
	public function get_branch_sale_by_id($idallocation,$idoutward) {         
		$this->db->select('`b`.`branch_name`, `od`.`date` as `all_date`, `b`.`idcompany`, `od`.`idbranch` as `id_branch`, `od`.`idbranch` as `id_warehouse`, 1 as `a_status`,
			1 as `o_status`, `g`.`godown_name`, `mv`.`full_name`, `od`.*, `op`.*, sum(op.qty) qty, `op`.`price`, `op`.`cgst_per`, `op`.idsale idtransfer, 
			GROUP_CONCAT(op.imei_no) as imei');
		$this->db->from('sale od');        
		$this->db->where('g.id_godown=op.idgodown')->from('godown g');        
		$this->db->join('sale_product op','op.idsale=od.id_sale','left');                                 
		$this->db->join('model_variants mv','mv.id_variant=op.idvariant');
		$this->db->where('od.idbranch=b.id_branch')->from('branch b'); 
		if($idoutward){
			$this->db->where('od.id_sale', $idoutward);            
		}
		$this->db->group_by('op.idgodown,op.idvariant');
		$query = $this->db->get(); 
		return $query->result(); 
		// die(print_r($this->db->last_query()));
	}
	


	
	
}
?>