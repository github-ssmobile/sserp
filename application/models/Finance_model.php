<?php
class Finance_model extends CI_Model{
    
    public function save_finance_scheme_data($data){
        return $this->db->insert('finance_scheme',$data);
    }
    public function get_finance_scheme_data(){
        return $this->db->select('finance_scheme.*,brand.brand_name,model_variants.full_name,payment_head.payment_head,payment_mode.payment_mode,bank.bank_name')
                        ->join('brand','finance_scheme.idbrand = brand.id_brand','left')
                        ->join('model_variants','finance_scheme.idvariant = model_variants.id_variant','left')
                        ->join('payment_head','finance_scheme.idpayment_head = payment_head.id_paymenthead','left')
                        ->join('payment_mode','finance_scheme.idpayment_mode = payment_mode.id_paymentmode','left')
                        ->join('bank','finance_scheme.idbank = bank.id_bank','left')
                        ->get('finance_scheme')->result();
    }
    public function ajax_get_finance_scheme_data_byfilter($from, $idbrand,$idvariant,$type, $idmode){
//        return $this->db->select('finance_scheme.*,brand.brand_name,model_variants.full_name,payment_head.payment_head,payment_mode.payment_mode,bank.bank_name')
//                        ->where('finance_scheme.from_date <=', $from)
//                        ->where('finance_scheme.to_date >=', $from)
//                        ->where('finance_scheme.idpayment_mode', $idmode)
//                        ->where('finance_scheme.idbrand', $idbrand)
//                        ->where('finance_scheme.idvariant', $idvariant)
//                        ->join('brand','finance_scheme.idbrand = brand.id_brand','left')
//                        ->join('model_variants','finance_scheme.idvariant = model_variants.id_variant','left')
//                        ->join('payment_head','finance_scheme.idpayment_head = payment_head.id_paymenthead','left')
//                        ->join('payment_mode','finance_scheme.idpayment_mode = payment_mode.id_paymentmode','left')
//                        ->join('bank','finance_scheme.idbank = bank.id_bank','left')
//                        ->get('finance_scheme')->result();
        
         if($type == 0){
            $types = array(4,3);
        }else{
            $types = $type;
        }
        
        if($idmode == 0){
            $pmodes = $this->db->where_in('idpaymenthead', $types)->get('payment_mode')->result();
//            die(print_r($pmodes));
            foreach ($pmodes as $pmode){
                $idpmode[] = $pmode->id_paymentmode; 
            }
        }else{
            $idpmode = $idmode;
        }
        if($idvariant == 0){
            return $this->db->select('finance_scheme.*,brand.brand_name,model_variants.full_name,payment_head.payment_head,payment_mode.payment_mode,bank.bank_name')
                        ->where('finance_scheme.from_date <=', $from)
                        ->where('finance_scheme.to_date >=', $from)
                        ->where_in('finance_scheme.idpayment_mode', $idpmode)
                        ->where('finance_scheme.idbrand', $idbrand)
                        ->join('brand','finance_scheme.idbrand = brand.id_brand','left')
                        ->join('model_variants','finance_scheme.idvariant = model_variants.id_variant','left')
                        ->join('payment_head','finance_scheme.idpayment_head = payment_head.id_paymenthead','left')
                        ->join('payment_mode','finance_scheme.idpayment_mode = payment_mode.id_paymentmode','left')
                        ->join('bank','finance_scheme.idbank = bank.id_bank','left')
                        ->get('finance_scheme')->result();
        }else{
             return $this->db->select('finance_scheme.*,brand.brand_name,model_variants.full_name,payment_head.payment_head,payment_mode.payment_mode,bank.bank_name')
                        ->where('finance_scheme.from_date <=', $from)
                        ->where('finance_scheme.to_date >=', $from)
                        ->where_in('finance_scheme.idpayment_mode', $idpmode)
                        ->where('finance_scheme.idbrand', $idbrand)
                        ->where('finance_scheme.idvariant', $idvariant)
                        ->join('brand','finance_scheme.idbrand = brand.id_brand','left')
                        ->join('model_variants','finance_scheme.idvariant = model_variants.id_variant','left')
                        ->join('payment_head','finance_scheme.idpayment_head = payment_head.id_paymenthead','left')
                        ->join('payment_mode','finance_scheme.idpayment_mode = payment_mode.id_paymentmode','left')
                        ->join('bank','finance_scheme.idbank = bank.id_bank','left')
                        ->get('finance_scheme')->result();
        }
    }
    public function get_finance_data_byid($idfinance){
        return $this->db->select('finance_scheme.*,brand.brand_name,model_variants.full_name,payment_head.payment_head,payment_mode.payment_mode,bank.bank_name')
                        ->where('finance_scheme.id_finance_scheme', $idfinance)
                        ->join('brand','finance_scheme.idbrand = brand.id_brand','left')
                        ->join('model_variants','finance_scheme.idvariant = model_variants.id_variant','left')
                        ->join('payment_head','finance_scheme.idpayment_head = payment_head.id_paymenthead','left')
                        ->join('payment_mode','finance_scheme.idpayment_mode = payment_mode.id_paymentmode','left')
                        ->join('bank','finance_scheme.idbank = bank.id_bank','left')
                        ->get('finance_scheme')->row();
    }
    public function delete_finance_scheme_byid($idfinance, $entry_time){
        return $this->db->where_in('id_finance_scheme', $idfinance)
                        ->where('entry_time', $entry_time)
                        ->delete('finance_scheme');
    }
    public function delete_finance_scheme_byidvariant($idvariant, $entry_time){
        return $this->db->where_in('idvariant', $idvariant)
                        ->where('entry_time', $entry_time)
                        ->delete('finance_scheme');
    }
    
    public function update_finance_scheme_data($data, $idva, $entry_time){
        return $this->db->where_in('idvariant', $idva)
                        ->where('entry_time', $entry_time)
                        ->update('finance_scheme', $data);
    }
    
}
?>