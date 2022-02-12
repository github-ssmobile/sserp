<?php
class Scheme_model extends CI_Model{
    
    public function get_scheme_type(){
       return $this->db->where('status = 1')->order_by('sequence')->get('scheme_type')->result();
    }
    public function get_schemes_byidtype($idtype){
        return $this->db->select('s.*, b.brand_name, st.scheme_type, v.vendor_name')
                       ->where('idscheme_type',$idtype)
                       ->where('st.id_scheme_type',$idtype)->from('scheme_type st')
                       ->where('s.idbrand = b.id_brand')->from('brand b')
                       ->where('s.idvendor = v.id_vendor')->from('vendor v')
                       ->where('s.status = 1')->get('scheme s')->result();
    }
    public function get_all_active_schemes(){
       return $this->db->select('s.*, st.scheme_type')
                       ->where('st.id_scheme_type = s.idscheme_type')->from('scheme_type st')
                       ->where('s.status = 1')
                       ->order_by('s.id_scheme', 'desc')
                       ->get('scheme s')->result();
    }
    public function get_schemes_fordiscon_byidtype($idtype){
        return $this->db->select('s.*, b.brand_name, st.scheme_type, v.vendor_name, sd.scheme_code as dis_scheme_code, sd.scheme_name as dis_scheme_name, std.scheme_type as dis_scheme_type')
                       ->where('s.idscheme_type',$idtype)
                       ->where('st.id_scheme_type',$idtype)->from('scheme_type st')
                       ->where('s.idbrand = b.id_brand')->from('brand b')
                       ->where('s.idvendor = v.id_vendor')->from('vendor v')
                       ->where('s.discontinue_scheme_id = sd.id_scheme')->from('scheme sd')
                       ->where('std.id_scheme_type = sd.idscheme_type')->from('scheme_type std')
                       ->where('s.status = 1')->get('scheme s')->result();
    }
    public function get_scheme_type_byid($id){
       return $this->db->where('status = 1')->where('id_scheme_type', $id)->get('scheme_type')->row();
    }
    public function get_variants_by_idbrand($brand) {
        return $this->db->select('full_name, id_variant')->where('idbrand',$brand)
                        ->get('model_variants')->result();
    }
    public function get_variant_byid_for_price_drop($id) {
        return $this->db->select('mv.id_variant, mv.full_name,mv.last_purchase_price,mv.igst')
                        ->where('mv.id_variant',$id)  //online & new
//                        ->where('(s.idgodown = 1 or s.idgodown = 6) and mv.id_variant ='.$id)  //online & new
//                        ->where('s.idvariant = mv.id_variant')->from('stock s')
                        ->get('model_variants mv')->row();
    }
    public function create_scheme($data) {
        $this->db->insert('scheme', $data);
        return $this->db->insert_id();
    }
    public function save_batch_scheme_data($data) {
        return $this->db->insert_batch('scheme_data', $data);
    }
    public function save_scheme_data($data) {
        $this->db->insert('scheme_data', $data);
        return $this->db->insert_id();
    }
    public function save_scheme_foc_data($data) {
        $this->db->insert('scheme_foc_data', $data);
        return $this->db->insert_id();
    }
    public function save_batch_settlement_data($data) {
        return $this->db->insert_batch('scheme_settlement', $data);
    }
    public function get_variant_byid($id) {
        return $this->db->select('mv.id_variant, mv.full_name,mv.last_purchase_price,mv.igst')
                        ->where('mv.id_variant',$id)  //online & new
                        ->get('model_variants mv')->row();
    }
    public function get_scheme_byid($idscheme) {
        return $this->db->select('s.*, b.brand_name, v.vendor_name')
                        ->where('s.id_scheme', $idscheme)
                        ->where('s.idbrand = b.id_brand')->from('brand b')
                        ->where('s.idvendor = v.id_vendor')->from('vendor v')
                        ->get('scheme s')->row();
    }
    public function get_scheme_byid_fordiscontinue($idscheme) {
//        return $this->db->select('s.*, b.brand_name, v.vendor_name')
//                        ->where('s.id_scheme', $idscheme)
//                        ->where('s.idbrand = b.id_brand')->from('brand b')
//                        ->where('s.idvendor = v.id_vendor')->from('vendor v')
//                        ->get('scheme s')->row();
        return $this->db->select('s.*, b.brand_name, st.scheme_type, v.vendor_name, sd.scheme_code as dis_scheme_code, sd.scheme_name as dis_scheme_name, std.scheme_type as dis_scheme_type')
                       ->where('s.id_scheme',$idscheme)
                       ->where('st.id_scheme_type = s.idscheme_type')->from('scheme_type st')
                       ->where('s.idbrand = b.id_brand')->from('brand b')
                       ->where('s.idvendor = v.id_vendor')->from('vendor v')
                       ->where('s.discontinue_scheme_id = sd.id_scheme')->from('scheme sd')
                       ->where('std.id_scheme_type = sd.idscheme_type')->from('scheme_type std')
                       ->where('s.status = 1')->get('scheme s')->row();
    }
    
    public function get_scheme_data_byid($idscheme) {
        return $this->db->select('sd.*, mv.full_name, mv.idproductcategory, mv.idcategory, mv.idmodel')
                        ->where('sd.idscheme', $idscheme)
                        ->join('model_variants mv','mv.id_variant = sd.idvariant','left')
                        ->get('scheme_data sd')->result();
    }
     public function get_schemedata_byid($idscheme) {
        return $this->db->select('sd.*')->where('sd.idscheme', $idscheme)->order_by('sd.max_target','desc')->get('scheme_data sd')->result();
    }
    public function get_scheme_settlement_byid($idscheme,$variants,$id_scheme_data=0) {
                $this->db->select('sd.*, GROUP_CONCAT(mv.full_name) as full_name')
                            ->where('sd.idscheme', $idscheme)
//                            ->where_in('sd.idvariant',$variants)
                            ->where_in('mv.id_variant',$variants)->from('model_variants mv');
                    if($id_scheme_data){
                        $this->db->where('sd.id_scheme_data', $id_scheme_data);
                    }
        $product_category = $this->db->get('scheme_data sd')->result();
        
//        die($this->db->last_query());
//        die('<pre>'.print_r($product_category,1).'</pre>');
        $category_array = array(); $i=0;
        foreach ($product_category as $category) {
            $category_array[$i]['id_scheme_data'] = $category->id_scheme_data;
            $category_array[$i]['idvariant'] = $category->idvariant;
            $category_array[$i]['full_name'] = $category->full_name;
            $category_array[$i]['min'] = $category->min_target;
            $category_array[$i]['max'] = $category->max_target;
            $category_array[$i]['payout_value'] = $category->payout_value;
            $category_array[$i]['payout_per'] = $category->payout_per;
            $category_array[$i][$category->id_scheme_data] = $this->db->select('st.*, mv.full_name as foc_model_name') 
                                                                ->where('st.idscheme_data', $category->id_scheme_data) 
                                                                ->where('mv.id_variant = st.idvariant')->from('model_variants mv')
                                                                ->get('scheme_foc_data st')->result();
            $category_array[$i]['ach_'.$category->id_scheme_data] = $this->db->select('count(imei_no) as ach,,sum(sale_price_mop) as mop,sum(purchase_basic) as basic')
                        ->where('sa.idscheme_data', $category->id_scheme_data)
                        ->get('scheme_achievement sa')->result();
            $category_array[$i]['total_ach_'.$category->idscheme] = $this->db->select('count(imei_no) as ach,sum(sale_price_mop) as mop,sum(purchase_basic) as basic')
                        ->where('sa.idscheme', $category->idscheme)
                        ->get('scheme_achievement sa')->result();
            $i++;
        }
        return $category_array;
    }
    
    public function get_sums_scheme_achievement_by_idscheme($idscheme) {
       return $this->db->select('count(imei_no) as qty,sum(sale_price_mop) as mop,sum(purchase_basic) as basic,sum(purchase_price) as purchase_total')
                        ->where('sa.idscheme', $idscheme)
                        ->get('scheme_achievement sa')->result();
    }
    
    public function get_scheme_settlement_byid_slabs($idscheme,$variants) {
        $product_category = $this->db->select('sd.*, GROUP_CONCAT(mv.full_name) as full_name')
                            ->where('sd.idscheme', $idscheme)                            
                            ->where_in('mv.id_variant',$variants)->from('model_variants mv')
                            ->group_by(' sd.id_scheme_data')
                            ->get('scheme_data sd')->result();
        
//        die($this->db->last_query());
//        die('<pre>'.print_r($product_category,1).'</pre>');
        $category_array = array(); $i=0;
        foreach ($product_category as $category) {
            $category_array[$i]['id_scheme_data'] = $category->id_scheme_data;
            $category_array[$i]['idvariant'] = $category->idvariant;
            $category_array[$i]['full_name'] = $category->full_name;
            $category_array[$i]['min'] = $category->min_target;
            $category_array[$i]['max'] = $category->max_target;
            $category_array[$i]['payout_value'] = $category->payout_value;
            $category_array[$i]['payout_per'] = $category->payout_per;
            $category_array[$i][$category->id_scheme_data] = $this->db->select('st.*, mv.full_name as foc_model_name') 
                                                                ->where('st.idscheme_data', $category->id_scheme_data) 
                                                                ->where('mv.id_variant = st.idvariant')->from('model_variants mv')
                                                                ->get('scheme_foc_data st')->result();
            $category_array[$i]['ach_'.$category->id_scheme_data] = $this->db->select('count(imei_no) as ach,sum(sale_price_mop) as mop,sum(purchase_basic) as basic')
                        ->where('sa.idscheme_data', $category->id_scheme_data)
                        ->get('scheme_achievement sa')->result();
            $i++;
        }
        return $category_array;
    }
    public function update_scheme($idscheme, $data) {
        return $this->db->where('id_scheme', $idscheme)->update('scheme', $data);
    }
    public function get_price_drop_data($idvariant, $date_from){
        return $this->db->select('imei_no, date, idvariant')->where_in('idvariant', $idvariant)
                        ->where('date', $date_from)->get('copy_daily_stock')->result();
        die($this->db->last_query());
    }
    public function save_scheme_achievement_stock($data) {
        return $this->db->insert_batch('scheme_achievement', $data);
    }
    public function delete_scheme_achievement_stock($idscheme) {
        $this->db->where('idscheme',$idscheme)->delete('scheme_achievement');
    }
    public function get_claim_product_data_byidscheme($idscheme) {
        return $this->db->select('mv.full_name,sa.*, s.inv_no, scd.payout_per')
                        ->where('sa.idscheme', $idscheme)
                        ->where('mv.id_variant = sa.idvariant')->from('model_variants mv')
                        ->join('scheme_data scd','scd.id_scheme_data = sa.idscheme_data', 'left')
                        ->join('sale s','sa.idlink = s.id_sale', 'left')
                        ->get('scheme_achievement sa')->result();
    }
    public function get_purchase_claim_product_data_byidscheme($idscheme) {
        return $this->db->select('mv.full_name,sa.imei_no,sa.new_price,sa.effective_price_change,sa.last_purchase_price,sa.date,sa.idlink,i.financial_year,sa.sale_price_mop,scd.payout_per,sa.purchase_basic')
                        ->where('sa.idscheme', $idscheme)
                        ->where('mv.id_variant = sa.idvariant')->from('model_variants mv')
                        ->join('scheme_data scd','scd.id_scheme_data = sa.idscheme_data', 'left')
                        ->join('inward i','sa.idlink = i.id_inward', 'left')
                        ->get('scheme_achievement sa')->result();
    }
    public function get_prebooking_data($idvariant, $min_val_per, $date_from, $date_to, $min_target_qty, $max_target_qty){
        if($min_target_qty == $max_target_qty){
            return $this->db->select('apr.idvariant')
                        ->where('apr.idvariant', $idvariant)
                        ->where('apr.date >=', $date_from)
                        ->where('apr.date <=', $date_to)
//                        ->having('count(apr.id_advance_payment_receive) >= '. $min_target_qty)
                        ->get('advance_payment_receive apr')->result();
        }else{
            return $this->db->select('apr.idvariant')
                        ->where('apr.idvariant', $idvariant)
                        ->where('apr.date >=', $date_from)
                        ->where('apr.date <=', $date_to)
//                        ->having('count(apr.id_advance_payment_receive) >= '. $min_target_qty)
//                        ->having('count(apr.id_advance_payment_receive) <= '. $max_target_qty)
                        ->get('advance_payment_receive apr')->result();
        }
    }
    public function get_prebooking_count_data($idvariant, $date_from, $date_to){
        return $this->db->select('count(*) as count_pre')
                        ->where('apr.idvariant', $idvariant)
                        ->where('apr.date >=', $date_from)
                        ->where('apr.date <=', $date_to)
                        ->get('advance_payment_receive apr')->result();
    }
    public function get_sale_activation_dataa($idvariant, $actdate_from, $actdate_to, $min_target_qty, $max_target_qty){
        if($min_target_qty == $max_target_qty){
            return $this->db->select('sp.idvariant, sp.imei_no,sp.date, sp.idsale,sp.mop')
                    ->where('sp.idvariant', $idvariant)
                    ->where('sp.date >=', $actdate_from)
                    ->where('sp.date <=', $actdate_to)
//                    ->having('count(sp.qty) >= '. $min_target_qty)
                    ->get('sale_product sp')->result();
        }else{
            return $this->db->select('sp.idvariant, sp.imei_no, sp.idsale, sp.mop')
                    ->where('sp.idvariant', $idvariant)
                    ->where('sp.date >=', $actdate_from)
                    ->where('sp.date <=', $actdate_to)
//                    ->having('count(sp.qty) >= '. $min_target_qty)
//                    ->having('count(sp.qty) <= '. $max_target_qty)
                    ->get('sale_product sp')->result();
        }
//        return $this->db->last_query();
    }
    public function get_purchase_data($idvariant, $actdate_from, $actdate_to, $min_target_qty, $max_target_qty){
        if($min_target_qty == $max_target_qty){
            return $this->db->select('sp.idvariant, sp.imei_no,sp.date,sp.idinward,sp.basic,sp.total_amount')
                    ->where_in('sp.idvariant', $idvariant)
                    ->where('sp.date >=', $actdate_from)
                    ->where('sp.date <=', $actdate_to)
//                    ->having('count(sp.qty) >= '. $min_target_qty)
                    ->get('inward_product sp')->result();
        }else{
            return $this->db->select('sp.idvariant, sp.imei_no, sp.idinward,sp.basic,ssp.total_amount')
                    ->where_in('sp.idvariant', $idvariant)
                    ->where('sp.date >=', $actdate_from)
                    ->where('sp.date <=', $actdate_to)
//                    ->having('count(sp.qty) >= '. $min_target_qty)
//                    ->having('count(sp.qty) <= '. $max_target_qty)
                    ->get('inward_product sp')->result();
        }
//        return $this->db->last_query();
    }
    public function get_sale_activation_count_data($idvariant, $actdate_from, $actdate_to){
        return $this->db->select('sp.product_name,sum(qty) as  sum_qty,sp.imei_no')
                    ->where('sp.idvariant', $idvariant)
                    ->where('sp.date >=', $actdate_from)
                    ->where('sp.date <=', $actdate_to)
                    ->get('sale_product sp')->result();
//        die($this->db->last_query());
    }
    public function get_purchase_count_data($idvariant, $date_from, $date_to){
        return $this->db->select('sum(qty) as sum_qty,imei_no')
                    ->where('ip.idvariant', $idvariant)
                    ->where('ip.date >=', $date_from)
                    ->where('ip.date <=', $date_to)
                    ->get('inward_product ip')->result();
//        die($this->db->last_query());
    }
    public function get_model_discontinue_data($idvariant, $iddiscon, $date_from, $date_to){
        return $this->db->select('imei_no, date, idvariant')
                        ->where_in('sa.idvariant', $idvariant)
                        ->where('sa.idscheme', $iddiscon)
                        ->where('sa.date >=', $date_from)
                        ->where('sa.date <=', $date_to)->get('scheme_achievement sa')->result();
    }
    public function get_color_variant_by_memory($idvariant) {
//        $var = $this->db->where('id_variant',$idvariant)->get('model_variants')->row();
        $var = $this->db->where('idattribute = 8')->where('idattribute = 9')
                        ->where('idvariant',$idvariant)->get('model_variants_attribute')->result();
        return $var;
//        die('<pre>'.print_r($var,1).'</pre>');
    }
    public function get_sale_activation_data($idvariant, $actdate_from, $actdate_to, $min_target_qty, $max_target_qty){
        if($min_target_qty == $max_target_qty){
            return $this->db->select('ip.basic,ip.total_amount,sp.idvariant, sp.imei_no,sp.date, sp.idsale,sp.mop')
                    ->where_in('sp.idvariant', $idvariant)
                    ->where('sp.date >=', $actdate_from)
                    ->where('sp.date <=', $actdate_to)
//                    ->having('count(sp.qty) >= '. $min_target_qty)
                    ->join('inward_product ip','ip.imei_no = sp.imei_no', 'left')
                    ->get('sale_product sp')->result();
        }else{
            return $this->db->select('ip.basic,ip.total_amount,sp.idvariant, sp.imei_no, sp.idsale, sp.mop')
                    ->where('sp.idvariant', $idvariant)
                    ->where('sp.date >=', $actdate_from)
                    ->where('sp.date <=', $actdate_to)
                    ->join('inward_product ip','ip.imei_no = sp.imei_no', 'left')
//                    ->having('count(sp.qty) >= '. $min_target_qty)
//                    ->having('count(sp.qty) <= '. $max_target_qty)
                    ->get('sale_product sp')->result();
        }
//        return $this->db->last_query();
    }
    public function get_scheme_foc_data($idscheme_data){
    return $this->db->select('st.*, mv.full_name as foc_model_name') 
            ->where('st.idscheme_data', $idscheme_data) 
            ->where('mv.id_variant = st.idvariant')->from('model_variants mv')
            ->get('scheme_foc_data st')->result();
    }
    public function update_scheme_foc_data($data) {
        return $this->db->update_batch('scheme_foc_data', $data, 'id_foc_data');
    }
    public function get_scheme_achievement_byidscheme_data($idscheme_data) {
        return $this->db->select('count(imei_no) as ach')
                        ->where('sa.idscheme_data', $idscheme_data)
                        ->get('scheme_achievement sa')->result();
    }
    public function get_max_slab_target($idscheme) {
        return $this->db->select('sd.*')
                        ->where('sd.max_target', '(SELECT max( `max_target`) FROM `scheme_data` WHERE `idscheme`='.$idscheme.')')
                        ->where('idscheme',$idscheme)
                        ->get('scheme_data sd')->row();
    }
    public function get_min_slab_target($idscheme) {
        return $this->db->select('sd.*')
                        ->where('sd.min_target', '(SELECT max( `min_target`) FROM `scheme_data` WHERE `idscheme`='.$idscheme.')')
                        ->where('idscheme',$idscheme)
                        ->get('scheme_data sd')->row();
    }
    
}
?> 