<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Scheme extends CI_Controller
{
    public function __construct() {
        parent::__construct();
        date_default_timezone_set('Asia/Kolkata');
        $this->load->model('Scheme_model');
        $this->load->model('Purchase_model');
    }
    public function create_scheme($type=0){
        $q['tab_active'] = '';
        $q['scheme_type'] = $this->Scheme_model->get_scheme_type();
        if($type){
            if($type == 6){ // discontinue
                $q['all_schemes'] = $this->Scheme_model->get_all_active_schemes();
                $q['schemetype'] = $this->Scheme_model->get_scheme_type_byid($type);
                $q['vendor_data'] = $this->General_model->get_active_vendor_data();
                $q['brand_data'] = $this->General_model->get_active_brand_data();
                $q['schemes'] = $this->Scheme_model->get_schemes_fordiscon_byidtype($type);
            }else{
                $q['schemetype'] = $this->Scheme_model->get_scheme_type_byid($type);
                $q['vendor_data'] = $this->General_model->get_active_vendor_data();
                $q['brand_data'] = $this->General_model->get_active_brand_data();
                $q['schemes'] = $this->Scheme_model->get_schemes_byidtype($type);
            }
        }
        $this->load->view('scheme/create_scheme', $q);
    }
    public function scheme_details($idtype,$idscheme){
        $q['tab_active'] = '';
        $q['schemetype'] = $this->Scheme_model->get_scheme_type_byid($idtype);
        $q['scheme_data'] = $this->Scheme_model->get_scheme_data_byid($idscheme);
        
        if($idtype == 6){ // discontinue
            $q['scheme'] = $this->Scheme_model->get_scheme_byid_fordiscontinue($idscheme);
        }else{
            $q['scheme'] = $this->Scheme_model->get_scheme_byid($idscheme);
            if($idtype == 2){ // sellout 
                $sale_actdata = array();
                $b=0;
                foreach ($q['scheme_data'] as $d){ 
                    $variants=array();
                    $date_from = $q['scheme']->date_from;
                    $date_to = $q['scheme']->date_to;  

                    $vids= explode(",",$d->idvariant);
                    $all_colors=explode(",",$d->all_colors);
                    
                    $l=0;
                    foreach ($vids as $idvariant){   
                        $variants=array();
                        if($all_colors[$l]==1){ 
                                $var_data = $this->General_model->get_active_variants_id($idvariant);
                                if($var_data->idproductcategory==1){ 
                                    $idcategory=$var_data->idcategory;
                                    if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33 || $idcategory==39 || $idcategory==38 || $idcategory==36){                                
                                        $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,1);                                                                  
                                    }else{                                      
                                        $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,$var_data->idmodel,0);
                                    }   
                                    foreach ($variantss as $v){
                                        $variants[]=$v->id_variant;
                                    } 
                                }else{                         
                                    $variants[]=$idvariant;
                                }
                            }else{                         
                                $variants[]= $idvariant;
                            }  
                            $l++;
                        }                        
                        $q['settlement_data'][] = $this->Scheme_model->get_scheme_settlement_byid($idscheme,$variants,$d->id_scheme_data);                        
                        } 
//                        die('<pre>'.print_r($q['settlement_data'],1).'</pre>');
                        
                $q['sale_count'] = $sale_actdata;
            }elseif($idtype == 4){ // prebooking
                foreach ($q['scheme_data'] as $d){
                    $date_from = $q['scheme']->date_from;
                    $date_to = $q['scheme']->date_to;
                    $actdate_from = $q['scheme']->activate_date_from;
                    $actdate_to = $q['scheme']->activate_date_to;
                    $stock_prebook[$d->idvariant] = $this->Scheme_model->get_prebooking_count_data($d->idvariant, $date_from, $date_to);
                    $sale_actdata[$d->idvariant] = $this->Scheme_model->get_sale_activation_count_data($d->idvariant, $actdate_from, $actdate_to);
                }
                $q['prebook_count'] = $stock_prebook;
                $q['sale_count'] = $sale_actdata;
                
            }elseif($idtype == 3){ // purchase
                $sale_actdata = array();
               $sale_actdata = array();
                $b=0;
                foreach ($q['scheme_data'] as $d){ 
                    $variants=array();
                    $date_from = $q['scheme']->date_from;
                    $date_to = $q['scheme']->date_to;  

                    $vids= explode(",",$d->idvariant);
                    $all_colors=explode(",",$d->all_colors);
                    
                    $l=0;
                    foreach ($vids as $idvariant){                        
                        if($all_colors[$l]==1){ 
                                $var_data = $this->General_model->get_active_variants_id($idvariant);
                                if($var_data->idproductcategory==1){ 
                                    $idcategory=$var_data->idcategory;
                                    if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33 || $idcategory==39 || $idcategory==38 || $idcategory==36){                                
                                        $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,1);                                                                  
                                    }else{                                      
                                        $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,$var_data->idmodel,0);
                                    }   
                                    foreach ($variantss as $v){
                                        $variants[]=$v->id_variant;
                                    } 
                                }else{                         
                                    $variants[]=$idvariant;
                                }
                            }else{                         
                                $variants[]= $idvariant;
                            }  
                            $l++;
                        }                        
                        $q['settlement_data'][] = $this->Scheme_model->get_scheme_settlement_byid($idscheme,$variants,$d->id_scheme_data);                        
                        }
//                         die('<pre>'.print_r($q['settlement_data'],1).'</pre>');
                    }
                /*
                foreach ($q['scheme_data'] as $d){
                    $date_from = $q['scheme']->date_from;
                    $date_to = $q['scheme']->date_to;
                    $sale_actdata[$d->idvariant] = $this->Scheme_model->get_purchase_count_data($d->idvariant, $date_from, $date_to);
                }
                $q['sale_count'] = $sale_actdata;
                $q['settlement_data'] = $this->Scheme_model->get_scheme_settlement_byid($idscheme);
            */
        }
        $this->load->view('scheme/'.$q['schemetype']->scheme_type.'_details.php', $q);
//        $this->load->view('scheme/scheme_details'.$idtype, $q);
    }
    public function ajax_variants_by_brand_multiselect() {
        $brand = $this->input->post('brand');
        $model_data = $this->Scheme_model->get_variants_by_idbrand($brand);
        
        echo '<select data-placeholder="Select Multiple Branches" name="selmodel" multiple id="selmodel" class="chosen-select" required="">'
//        echo '<select class="chosen-select form-control" name="selmodel" id="selmodel" required="">'
                . '<option value="">Select Model</option>';
        foreach ($model_data as $model) {
            echo '<option  value="'.$model->id_variant .'">'.$model->full_name.'</option>';
        }
        echo '</select>';
    }
    public function ajax_variants_by_brand() {
        $brand = $this->input->post('brand');
        $model_data = $this->Scheme_model->get_variants_by_idbrand($brand);
        
//        echo '<select data-placeholder="Select Multiple Branches" name="selmodel" multiple id="selmodel" class="chosen-select" required="">'
        echo '<select class="chosen-select form-control" name="selmodel" id="selmodel" required="">'
                . '<option value="">Select Model</option>';
        foreach ($model_data as $model) {
            echo '<option  value="'.$model->id_variant .'">'.$model->full_name.'</option>';
        }
        echo '</select>';
    }
    public function get_variant_byid_for_price_drop() {
        $id = $this->input->post('id');
        $gst_selected_type = $this->input->post('gst_selected_type');
        $variant = $this->Scheme_model->get_variant_byid_for_price_drop($id);?>
        <tr>
            <td>
                <?php echo $variant->id_variant; ?>
            </td>
            <td>
                <input type="hidden" class="idvariant" name="idvariant[]" value="<?php echo $variant->id_variant; ?>" />
                <input type="hidden" class="igst" name="igst[]" value="<?php echo $variant->igst; ?>" />
                <?php echo $variant->full_name; ?>
            </td>
            <td><input type="checkbox" name="all_variants[]" checked></td>
            <input type="hidden" class="allvariants" name="allvariants[]"  value="1">
            <!--<td><?php // echo ($variant->st_qty) ? $variant->st_qty : 0 ?></td>-->
            <td><input type="number" class="form-control input-sm last_purchase_price" name="last_purchase_price[]" placeholder="Enter Last Purchase Price" min="1" required="" style="width: 120px" value="<?php echo round($variant->last_purchase_price) ?>" step="0.001" /></td>
            <td><input type="number" class="form-control input-sm new_price" name="new_price[]" placeholder="Enter New Price" min="1" required="" style="width: 120px" step="0.001" /></td>
            <td><input type="number" class="form-control input-sm price_drop" name="price_drop[]" placeholder="Enter Price Amount" min="1"  required="" style="width: 120px" step="0.001" />
             <?php if($gst_selected_type){ ?>  <?php } ?>
                <input type="hidden" class="igst" name="igst[]" value="<?php echo $variant->igst; ?>" />
                <input type="hidden" class="excluding_gst_amt" name="excluding_gst_amt[]" />
                <!--<span class="spexcluding_gst_amt"></span> [<?php echo $variant->igst ?>%]-->
            </td>
            <td <?php if($gst_selected_type){ ?> class="text-muted" <?php } ?>>
                <input type="hidden" class="taxable_gst_price" name="taxable_gst_price[]" />
                <span class="sptaxable_gst_price"></span>
            </td>
            <td><a class="btn btn-sm btn-warning waves-effect waves-light remove_btn" id="remove_btn">Remove <i class="fa fa-trash-o fa-lg"></i></a></td>
        </tr>
        <?php 
    }
    public function get_variant_byid_for_model_discontinue() {
        $id = $this->input->post('id');
        $variant = $this->Scheme_model->get_variant_byid_for_price_drop($id);?>
        <tr>
            <td>
                <?php echo $variant->id_variant; ?>
            </td>
            <td>
                <input type="hidden" class="idvariant" name="idvariant[]" value="<?php echo $variant->id_variant; ?>" />
                <input type="hidden" class="igst" name="igst[]" value="<?php echo $variant->igst; ?>" />
                <?php echo $variant->full_name; ?>
            </td>
            <td><a class="btn btn-sm btn-warning waves-effect waves-light remove_btn" id="remove_btn">Remove <i class="fa fa-trash-o fa-lg"></i></a></td>
        </tr>
        <?php 
    }
    public function create_price_drop() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $entry_time = date('Y-m-d H:i:s');
        $create_by = $this->input->post('create_by');
        $date_from = $this->input->post('date_from');
//        $date_to = $this->input->post('date_to');
        $idbrand = $this->input->post('idbrand');
        $idvendor = $this->input->post('idvendor');
        
        $data = array(
            'idscheme_type' => $this->input->post('idscheme_type'),
            'scheme_code' => $this->input->post('scheme_code'),
            'scheme_name' => $this->input->post('scheme_name'),
            'idbrand' => $idbrand,
            'idvendor' => $idvendor,
            'is_gst_include' => $this->input->post('gst_selected_type'),
            'entry_time' => $entry_time,
            'create_by' => $create_by,
            'date_from' => $date_from,
//            'date_to' => $date_to,
        );
//        die(print_r($data));
        $idscheme = $this->Scheme_model->create_scheme($data);
        $idvariant = $this->input->post('idvariant');
        for($i=0;$i<count($idvariant);$i++){
            $data_product[] = array(
                'idvariant' => $idvariant[$i],
                'last_purchase_price' => $this->input->post('last_purchase_price['.$i.']'),
                'price' => $this->input->post('price_drop['.$i.']'),
                'excluding_gst_amt' => $this->input->post('excluding_gst_amt['.$i.']'),
                'igst_per' => $this->input->post('igst['.$i.']'),
                'taxable_gst_price' => $this->input->post('taxable_gst_price['.$i.']'),
                'new_price' => $this->input->post('new_price['.$i.']'),
                'idscheme_type' => $this->input->post('idscheme_type'),
                'idscheme' => $idscheme,
                'all_colors' => $this->input->post('allvariants['.$i.']'),
            );
        }
//        die(print_r($data_product));
        $this->Scheme_model->save_batch_scheme_data($data_product);
        return redirect('Scheme/create_scheme/'.$this->input->post('idscheme_type'));
    }
    public function create_discontinue_scheme() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $entry_time = date('Y-m-d H:i:s');
        $create_by = $this->input->post('create_by');
        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');
        $idbrand = $this->input->post('idbrand');
        $iddiscon_scheme = $this->input->post('discon_scheme');
        $dis_scheme = $this->Scheme_model->get_scheme_byid($iddiscon_scheme);
        $data = array(
            'idscheme_type' => $this->input->post('idscheme_type'),
            'scheme_code' => $this->input->post('scheme_code'),
            'scheme_name' => $this->input->post('scheme_name'),
            'discontinue_scheme_id' => $iddiscon_scheme,
            'idvendor' => $dis_scheme->idvendor,
            'idbrand' => $idbrand,
            'entry_time' => $entry_time,
            'create_by' => $create_by,
            'date_from' => $date_from,
            'date_to' => $date_to,
        );
//        die(print_r($data));
        $idscheme = $this->Scheme_model->create_scheme($data);
        $idvariant = $this->input->post('idvariant');
        for($i=0;$i<count($idvariant);$i++){
            $data_product[] = array(
                'idvariant' => $idvariant[$i],
                'idscheme_type' => $this->input->post('idscheme_type'),
                'idscheme' => $idscheme,
            );
        }
        $this->Scheme_model->save_batch_scheme_data($data_product);
        return redirect('Scheme/create_scheme/'.$this->input->post('idscheme_type'));
    }
    
    
    public function get_variant_byid_for_prebooking() {
        $id = $this->input->post('id');
        $variant = $this->Scheme_model->get_variant_byid($id); ?>
        <tr>
            <td><?php echo $variant->id_variant; ?></td>
            <td>
                <input type="hidden" class="idvariant" name="idvariant[]" value="<?php echo $variant->id_variant; ?>" />
                <?php echo $variant->full_name; ?>
            </td>
            <td><input type="checkbox" name="all_variants[]" checked></td>
            <td><input type="number" class="form-control input-sm min_val" name="min_val[]" id="min_val" placeholder="Advanced booking percentage" style="width: 100px"></td>
            <td><input type="number" class="form-control input-sm min_qty" name="min_qty[]" placeholder="Min Target Qty" min="1" required="" style="width: 100px" /></td>
            <td><input type="number" class="form-control input-sm max_qty" name="max_qty[]" placeholder="Max Target Qty" min="1" required="" style="width: 100px" /></td>
            <td><input type="number" class="form-control input-sm price_per_unit" name="price_per_unit[]" placeholder="Price Per Unit" min="1" required="" style="width: 150px" /></td>
            <td><a class="btn btn-sm btn-warning waves-effect waves-light remove_btn" id="remove_btn">Remove <i class="fa fa-trash-o"></i></a></td>
        </tr>
        <?php 
    }
    
    public function create_pre_booking() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $entry_time = date('Y-m-d H:i:s');
        $create_by = $this->input->post('create_by');
        $date_from = $this->input->post('bdate_from');
        $date_to = $this->input->post('bdate_to');
        $idbrand = $this->input->post('idbrand');
        $idvendor = $this->input->post('idvendor');
        
        $data = array(
            'idscheme_type' => $this->input->post('idscheme_type'),
            'scheme_code' => $this->input->post('scheme_code'),
            'scheme_name' => $this->input->post('scheme_name'),
            'idbrand' => $idbrand,
            'idvendor' => $idvendor,
            'entry_time' => $entry_time,
            'create_by' => $create_by,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'activate_date_from' => $this->input->post('adate_from'),
            'activate_date_to' => $this->input->post('adate_to'),
        );
//        die(print_r($data));
        $idscheme = $this->Scheme_model->create_scheme($data);
        $idvariant = $this->input->post('idvariant');
        for($i=0;$i<count($idvariant);$i++){
            $data_product[] = array(
                'idvariant' => $idvariant[$i],
                'price' => $this->input->post('price_per_unit['.$i.']'),
                'min_target' => $this->input->post('min_qty['.$i.']'),
                'max_target' => $this->input->post('max_qty['.$i.']'),
                'idscheme_type' => $this->input->post('idscheme_type'),
                'idscheme' => $idscheme,
                'min_val_per_for_booking' => $this->input->post('min_val['.$i.']'),
            );
        }
//        die(print_r($data_product));
        $this->Scheme_model->save_batch_scheme_data($data_product);
        return redirect('Scheme/create_scheme/'.$this->input->post('idscheme_type'));
    }
    
    public function get_variant_byid_for_foc() {
        $id = $this->input->post('id');
        $brand = $this->input->post('brand');
        $settlement_type_val = $this->input->post('settlement_type_val');
        $variant = $this->Scheme_model->get_variant_byid($id); ?>
            <tr>
                <td><?php echo $variant->id_variant; ?></td>
                <td>
                    <input type="hidden" class="idvariant" name="idvariant[]" value="<?php echo $variant->id_variant; ?>" />
                    <?php echo $variant->full_name; ?>
                </td>
                <td><input type="checkbox" name="all_variants[]" checked></td>
                <td><input type="number" class="form-control input-sm min_qty" name="min_qty[]" placeholder="Min Target Qty" min="1" required="" style="width: 100px" /></td>
                <td><input type="number" class="form-control input-sm max_qty" name="max_qty[]" placeholder="Max Target Qty" min="1" required="" style="width: 100px" /></td>
                <td>
                    <?php if($settlement_type_val == 0){ 
                        $model_data = $this->Scheme_model->get_variants_by_idbrand($brand); ?>
                        <select class="chosen-select form-control focmodel" name="focmodel" id="focmodel" required="" onchange="call_by_foc(this)">
                            <option value="">Select FOC Model</option>;
                            <?php foreach ($model_data as $model) {
                                echo '<option  value="'.$model->id_variant .'">'.$model->full_name.'</option>';
                            } ?>
                        </select><div class="clearfix"></div>
                        <div class="thumbnail" style="margin-top: 5px; font-size: 12px">
                            <div class="fochead" style="display: none">
                                <div class="col-md-7" style="padding: 2px;text-align: center;">FOC Product</div>
                                <div class="col-md-3" style="padding: 2px;text-align: center;">FOC Unit</div>
                                <div class="col-md-2" style="padding: 2px;text-align: center;">Remove</div><div class="clearfix"></div>
                            </div>
                            <div class="focdata"></div>
                        </div>
                    <?php }else if($settlement_type_val == 1){ ?>
                        <input type="number" class="form-control input-sm payout_value" name="payout_value[]" placeholder="Payout Value" min="1" required="" />
                    <?php }else{ ?>
                        <input type="number" class="form-control input-sm payout_value" name="payout_per[]" placeholder="Payout Percentage" min="1" required="" />
                    <?php } ?>
                </td>
                <td><a class="btn btn-sm btn-warning waves-effect waves-light remove_btn" id="remove_btn">Remove <i class="fa fa-trash-o"></i></a></td>
            </tr>
        <script>
            $(document).ready(function(){
                $(document).on('click', '.remove_foc_btn', function () {
                    $(this).closest('div').parent('div').remove();
                });
            });
        </script>
        <?php 
    }
    public function get_variants_byid_for_foc() {
        $variant_ids = $this->input->post('variant_ids');
        
        $brand = $this->input->post('brand');
        $settlement_type_val = $this->input->post('settlement_type_val');
        $has_slabs = $this->input->post('has_slabs');
        if($has_slabs==1){
            foreach ($variant_ids as $idvarient){
                $variant = $this->Scheme_model->get_variant_byid($idvarient); ?>
                 <tr>
                <td><?php echo $variant->id_variant; ?></td>
                <td>
                    <input type="hidden" class="idvariant" name="idvariant[]" value="<?php echo $variant->id_variant; ?>" />
                    <?php echo $variant->full_name; ?>
                </td>    
                <td>
                    <input type="checkbox" class="all_variants" name="all_variants[]" checked>
                    <input type="hidden" class="allvariants" name="allvariants[]"  value="1">
                </td>
                <td><a class="btn btn-sm btn-warning waves-effect waves-light remove_btn" id="remove_btn">Remove <i class="fa fa-trash-o"></i></a></td>
            </tr>
                
           <?php  }            
        }else{
             foreach ($variant_ids as $idvarient){
                    $variant = $this->Scheme_model->get_variant_byid($idvarient); 
                    $slabname=$variant->id_variant;
        ?>
            <tr>
                <td><?php echo $variant->id_variant; ?></td>
                <td>
                    <input type="hidden" class="idvariant" name="idvariant[]" value="<?php echo $variant->id_variant; ?>" />
                    <?php echo $variant->full_name; ?>
                    <input type="hidden" class="slabname" name="slabname[]" value="<?php echo $slabname ?>" />
                </td>
                <td>
                    <input type="checkbox" class="all_variants" name="all_variants[]" checked >
                    <input type="hidden" class="allvariants" name="allvariants[]"  value="1">
                </td>
                <input type="hidden" class="form-control input-sm min_qty" name="<?php echo $slabname ?>_min_qty[]" value="1" required="" style="width: 100px" /> <!-- <td></td> -->
                <input type="hidden" class="form-control input-sm max_qty" name="<?php echo $slabname ?>_max_qty[]" value="1" required="" style="width: 100px" /> <!--<td></td>-->
                <td>
                    <?php if($settlement_type_val == 0){ 
                        $model_data = $this->Scheme_model->get_variants_by_idbrand($brand); ?>
                        <select class="chosen-select form-control focmodel" name="focmodel" id="focmodel" required="" onchange="call_by_foc(this)">
                            <option value="">Select FOC Model</option>;
                            <?php foreach ($model_data as $model) {
                                echo '<option slabname="'.$slabname.'"  value="'.$model->id_variant .'">'.$model->full_name.'</option>';
                            } ?>
                        </select><div class="clearfix"></div>
                        <div class="thumbnail" style="margin-top: 5px; font-size: 12px">
                            <div class="fochead" style="display: none">
                                <div class="col-md-7" style="padding: 2px;text-align: center;">FOC Product</div>
                                <div class="col-md-3" style="padding: 2px;text-align: center;">FOC Unit</div>
                                <div class="col-md-2" style="padding: 2px;text-align: center;">Remove</div><div class="clearfix"></div>
                            </div>
                            <div class="focdata"></div>
                        </div>
                    <?php }else if($settlement_type_val == 1){ ?>
                        <input type="number" class="form-control input-sm payout_value" name="payout_value[]" placeholder="Payout Value" min="1"  required="" />
                    <?php }else{ ?>
                        <input type="number" class="form-control input-sm payout_value" name="payout_per[]" placeholder="Payout Percentage" step="0.1" min="0.1" required="" />
                    <?php } ?>
                </td>
                <td><a class="btn btn-sm btn-warning waves-effect waves-light remove_btn" id="remove_btn">Remove <i class="fa fa-trash-o"></i></a></td>
            </tr>
        <script>
            $(document).ready(function(){
                $(document).on('click', '.remove_foc_btn', function () {
                    $(this).closest('div').parent('div').remove();
                });
            });
        </script>
        <?php 
        }
        }
     } 
    
    public function get_slabs() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $brand = $this->input->post('brand');
        $has_slabs = $this->input->post('has_slabs');
        $settlement_type_val = $this->input->post('settlement_type_val');
        $variants = $this->input->post('variants');
        $ids= implode("_",$variants);
        $slabname=date("Y_m_d_h_i_s");
        ?>
        <div id="modes_block" class="modes_block modes_blockc  thumbnail" style="margin-bottom: 5px; padding: 5px;">
            <input type="hidden" class="slabname" name="slabname[]" value="<?php echo $slabname ?>" />
            <div class="col-md-1" style="padding: 20px 5px;text-align: center;">
                <a class="btn btn-sm btn-warning remove_slab_btn" id="remove_slab_btn"><i class="fa fa-trash-o fa-lg"></i></a>
            </div>
            <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                <b>Min</b>
                <input type="number" class="form-control input-sm min_qty" name="<?php echo $slabname ?>_min_qty[]" placeholder="Min Target Qty" min="1" required="" style="width: 100px" />
            </div>
            <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                <b>Max</b>
                <input type="number" class="form-control input-sm max_qty" name="<?php echo $slabname ?>_max_qty[]" placeholder="Max Target Qty" min="1" required="" style="width: 100px" />
            </div>
            
            <?php if($has_slabs == 1 && $settlement_type_val==0){ 
                $model_data = $this->Scheme_model->get_variants_by_idbrand($brand); ?>
            <div class="col-md-2 col-sm-3" style="padding: 2px 5px">    
                <b>FOC Models</b>
                <select class="chosen-select form-control focmodel" name="focmodel" id="focmodel" required="" onchange="call_by_foc(this)">
                    <option value="">Select FOC Model</option>;
                    <?php foreach ($model_data as $model) {
                        echo '<option slabname="'.$slabname.'" value="'.$model->id_variant .'">'.$model->full_name.'</option>';
                    } ?>
                </select><div class="clearfix"></div>
             </div>
            <div class="col-md-5 col-sm-6 foc_block" style="padding: 2px 5px">    
                <b>FOC Products</b>
                <div class="thumbnail" style="font-size: 12px;padding: 5px !important;">
                    <div class="fochead" style="display: block">
                        <div class="col-md-7" style="padding: 2px;text-align: center;"><b>FOC Product</b></div>
                        <div class="col-md-3" style="padding: 2px;text-align: center;"><b>FOC Unit</b></div>
                        <div class="col-md-2" style="padding: 2px;text-align: center;"><b>Remove</b></div><div class="clearfix"></div>
                    </div>
                    <div class="focdata"></div>
                </div> 
            </div>
            <?php }else if($settlement_type_val == 1){ ?>
                <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                    Payout Value
                    <input type="number" class="form-control input-sm payout_value" name="payout_value[]" placeholder="Payout Value" min="1" required="" />
                </div>
            <?php }else{ ?>
                <div class="col-md-2 col-sm-3" style="padding: 2px 5px">
                    Payout Percentage(%)
                    <input type="number" class="form-control input-sm payout_value" name="payout_per[]" placeholder="Payout Percentage" min="1" required="" />
                </div>
            <?php } ?>
            
            
            <div class="clearfix"></div>
            </div>
        <div class="clearfix"></div>
        <?php
    }
    public function create_sell_out_scheme() {
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $entry_time = date('Y-m-d H:i:s');
        $create_by = $this->input->post('create_by');
        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');
        $idbrand = $this->input->post('idbrand');
        $idvendor = $this->input->post('idvendor');
        $has_slabs = $this->input->post('hasslabs');
        $slabname = $this->input->post('slabname');
        $all_variants = $this->input->post('allvariants');
        $claim_target = $this->input->post('claim_target_val'); // 0=Qty, 1=Value , Volume=2
        $settlement_type_val = $this->input->post('settlement_type_val'); // 0=FOC,1=Payout,2=Percentage
         
        
        $data = array(
            'idscheme_type' => $this->input->post('idscheme_type'),
            'scheme_code' => $this->input->post('scheme_code'),
            'scheme_name' => $this->input->post('scheme_name'),
            'has_slabs' => $has_slabs,
            'overall_target_value' => $this->input->post('overall_value'),
            'overall_target_volume' => $this->input->post('overall_volume'),
            'idbrand' => $idbrand,
            'claim_target' => $claim_target,
            'settlement_type' => $settlement_type_val,
            'idvendor' => $idvendor,
            'entry_time' => $entry_time,
            'create_by' => $create_by,
            'date_from' => $date_from,
            'date_to' => $date_to,
        );
//        die(print_r($data));
        $idscheme = $this->Scheme_model->create_scheme($data);
        $idvariant = $this->input->post('idvariant');
        
        $variants=implode(",",$idvariant);
        $variants_color=implode(",",$this->input->post('allvariants'));
        
        if($has_slabs==1){ 
            for($i=0;$i<count($slabname);$i++){
                
                $data_product = array(
                'idvariant' => $variants,
                'settlement_type' => $settlement_type_val,
                'min_target' => $this->input->post($slabname[$i].'_min_qty[0]'),
                'max_target' => $this->input->post($slabname[$i].'_max_qty[0]'),
                'idscheme_type' => $this->input->post('idscheme_type'),
                'idscheme' => $idscheme,
                'payout_value' => $this->input->post('payout_value['.$i.']'),
                'payout_per' => $this->input->post('payout_per['.$i.']'),
                'all_colors' => $variants_color,
            );
            $idscheme_data = $this->Scheme_model->save_scheme_data($data_product);   
            $foc_models=$this->input->post($slabname[$i].'_foc_unit');
                if($settlement_type_val==0){
                    foreach ($foc_models as $foc=>$unit){                
                        $foc_data = array(  
                            'idscheme_data' => $idscheme_data,
                            'idvariant' => $foc,
                            'foc_units' => $unit[0],
                        );
                        $this->Scheme_model->save_scheme_foc_data($foc_data);
                    }
                }
            } 
            
              
        }else{
            
            for($i=0;$i<count($idvariant);$i++){            
            $idvariant_sel = $idvariant[$i];
            $min_target=$this->input->post($idvariant_sel.'_min_qty');
            $max_target=$this->input->post($idvariant_sel.'_max_qty');
                $data_product = array(
                    'idvariant' => $idvariant_sel,
                    'settlement_type' => $settlement_type_val,
                    'min_target' => $min_target[0],
                    'max_target' => $max_target[0],
                    'idscheme_type' => $this->input->post('idscheme_type'),
                    'idscheme' => $idscheme,
                    'payout_value' => $this->input->post('payout_value['.$i.']'),
                    'payout_per' => $this->input->post('payout_per['.$i.']'),
                    'all_colors' => $this->input->post('allvariants['.$i.']'),
                );
                $idscheme_data = $this->Scheme_model->save_scheme_data($data_product);
                $foc_models=$this->input->post($idvariant_sel.'_foc_unit');
                if($settlement_type_val==0){
                    foreach ($foc_models as $foc=>$unit){                
                        $foc_data = array(  
                            'idscheme_data' => $idscheme_data,
                            'idvariant' => $foc,
                            'foc_units' => $unit[0],
                        );
                        $this->Scheme_model->save_scheme_foc_data($foc_data);
                    }
                }
                
            }
        }
        /*
        for($i=0;$i<count($idvariant);$i++){
            
            $idvariant_sel = $idvariant[$i];
            $data_product = array(
                'idvariant' => $idvariant_sel,
                'settlement_type' => $settlement_type_val,
                'min_target' => $this->input->post('min_qty['.$i.']'),
                'max_target' => $this->input->post('max_qty['.$i.']'),
                'idscheme_type' => $this->input->post('idscheme_type'),
                'idscheme' => $idscheme,
                'payout_value' => $this->input->post('payout_value['.$i.']'),
                'payout_per' => $this->input->post('payout_per['.$i.']'),
            );
            $idscheme_data = $this->Scheme_model->save_scheme_data($data_product);
            for($j=0;$j<count($idvariant);$j++){
                if($settlement_type_val == 0){ // foc
                    $settlement_data[] = array(
                        'idscheme' => $idscheme,
                        'idscheme_type' => $this->input->post('idscheme_type'),
                        'foc_model' => $this->input->post('foc_model['.$idvariant_sel.']['.$j.']'),
                        'foc_unit' => $this->input->post('foc_unit['.$idvariant_sel.']['.$j.']'),
                        'idscheme_data' => $idscheme_data,
                    );
                }else if($settlement_type_val == 1){ 
                    
                }
//                else if($settlement_type_val == 1){ //payout
//                    $settlement_data[] = array(
//                        'idscheme' => $idscheme,
//                        'idscheme_type' => $this->input->post('idscheme_type'),
//                        'payout_value' => $this->input->post('payout_value['.$idvariant_sel.']['.$j.']'),
//                    );
//                }else{ // percentage
//                    $settlement_data[] = array(
//                        'idscheme' => $idscheme,
//                        'idscheme_type' => $this->input->post('idscheme_type'),
//                        'payout_per' => $this->input->post('payout_per['.$idvariant_sel.']['.$j.']'),
//                    );
//                }
            }
        }
        */
//        if($settlement_type_val == 0){
//            $this->Scheme_model->save_batch_settlement_data($settlement_data);
//        }
//        die(print_r($data_product));
        return redirect('Scheme/create_scheme/'.$this->input->post('idscheme_type'));
    }
    public function generate_price_drop_claim() {
//                die('<pre>'.print_r($_POST,1).'</pre>');
        $this->db->trans_begin();
        $idscheme = $this->input->post('idscheme');
        $scheme = $this->Scheme_model->get_scheme_byid($idscheme);
        $scheme_data = $this->Scheme_model->get_schemedata_byid($idscheme);        
        $idscheme_type = $scheme->idscheme_type; 
        $date_from = $scheme->date_from;
         $regen = $this->input->post('regen');
         if($regen){
            $this->delete_scheme_achievement_stock($idscheme);
         } 
        
         $data = array();
         $count=0;
        foreach ($scheme_data as $sche_var){
                        $variants=array();
                        if($sche_var->all_colors==1){
                        $var_data = $this->General_model->get_active_variants_id($sche_var->idvariant);
                        if($var_data->idproductcategory==1){            
                                $idcategory=$var_data->idcategory;
                                if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33 || $idcategory==39 || $idcategory==38 || $idcategory==36){
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,1);
                                }else{
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,$var_data->idmodel,0);
                                }   
                                foreach ($variantss as $v){
                                    $variants[]=$v->id_variant;
                                } 
                            }else{
                                $variants[]=$sche_var->idvariant;
                            }
                        }else{
                            $variants[]=$sche_var->idvariant;
                        }
                         $last_date = date('Y-m-d', strtotime($date_from . " -1 days"));
                        $stock_data = $this->Scheme_model->get_price_drop_data($variants, $last_date);
                //        die('<pre>'.print_r($stock_data,1).'</pre>');
                        $count += count($stock_data);
                        if(count($stock_data)){
                            foreach ($stock_data as $stock){
                                $data[] = array(
                                    'idvariant' => $stock->idvariant,
                                    'idscheme' => $idscheme,
                                    'idscheme_type' => $idscheme_type,
                                    'date' => $last_date,
                                    'imei_no' => $stock->imei_no,
                                    'last_purchase_price' => $sche_var->last_purchase_price,
                                    'effective_price_change' => $sche_var->price,
                                    'new_price' => $sche_var->new_price,
                                );
                            } 
                        }
                        
        } 
        if(count($data)>0){
             $this->Scheme_model->save_scheme_achievement_stock($data);
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $d['result'] = 'Failed';
            }else{
                $this->db->trans_commit();
                $this->update_scheme($idscheme,$count);
                $d['result'] = 'Success';
                $d['count'] = $count;
            }
        }else{
             $d['result'] = 'Failed';
        }
           
        
        echo json_encode($d);
    }
    
    public function update_scheme($idscheme,$count) {
        $upscheme = array(
            'claim_status' => 1,
            'total_achivement_count' => $count,
            'generated_on' => date('Y-m-d H:i:s'),
        );
        $this->Scheme_model->update_scheme($idscheme, $upscheme);
    }
    public function delete_scheme_achievement_stock($idscheme) {
        $this->Scheme_model->delete_scheme_achievement_stock($idscheme);
    }
    
    public function generate_price_increase_claim() {
//        die(print_r($_POST));
        $this->db->trans_begin();
        $idscheme = $this->input->post('idscheme');        
        $scheme = $this->Scheme_model->get_scheme_byid($idscheme);
        $scheme_data = $this->Scheme_model->get_schemedata_byid($idscheme);        
        $idscheme_type = $scheme->idscheme_type; 
        $date_from = $scheme->date_from;
         $regen = $this->input->post('regen');
         if($regen){
            $this->delete_scheme_achievement_stock($idscheme);
         } 
         $data = array();
         $count=0;
        foreach ($scheme_data as $sche_var){
                        $variants=array();
                        if($sche_var->all_colors==1){
                        $var_data = $this->General_model->get_active_variants_id($sche_var->idvariant);
                        if($var_data->idproductcategory==1){            
                                $idcategory=$var_data->idcategory;
                                if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33 || $idcategory==39 || $idcategory==38 || $idcategory==36){
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,1);
                                }else{
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,$var_data->idmodel,0);
                                }   
                                foreach ($variantss as $v){
                                    $variants[]=$v->id_variant;
                                } 
                            }else{
                                $variants[]=$sche_var->idvariant;
                            }
                        }else{
                            $variants[]=$sche_var->idvariant;
                        }
                         $last_date = date('Y-m-d', strtotime($date_from . " -1 days"));
                        $stock_data = $this->Scheme_model->get_price_drop_data($variants, $last_date);
                //        die('<pre>'.print_r($stock_data,1).'</pre>');
                        $count += count($stock_data);
                        if(count($stock_data)){
                            foreach ($stock_data as $stock){
                                $data[] = array(
                                    'idvariant' => $stock->idvariant,
                                    'idscheme' => $idscheme,
                                    'idscheme_type' => $idscheme_type,
                                    'date' => $last_date,
                                    'imei_no' => $stock->imei_no,
                                    'last_purchase_price' => $sche_var->last_purchase_price,
                                    'effective_price_change' => $sche_var->price,
                                    'new_price' => $sche_var->new_price,
                                );
                            } 
                        }
                        
        }
        if(count($data)>0){
            $this->Scheme_model->save_scheme_achievement_stock($data);
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $d['result'] = 'Failed';
            }else{
                $this->db->trans_commit();
                $this->update_scheme($idscheme,$count);
                $d['result'] = 'Success';
                $d['count'] = $count;
            }
            }else{ 
            $d['result'] = 'Failed';
        }
         
        echo json_encode($d);
    }
    public function generate_prebooking_claim() {
//        die(print_r($_POST));
        $this->db->trans_begin();
        $idscheme = $this->input->post('idscheme');
        $idscheme_type = $this->input->post('idscheme_type');
        $idvariant = $this->input->post('idvariant');
        $date_from = $this->input->post('bookdate_from');
        $date_to = $this->input->post('bookdate_to');
        $actdate_from = $this->input->post('actdate_from');
        $actdate_to = $this->input->post('actdate_to');
        $min_val_per = $this->input->post('min_val_per_for_booking');
        
        $min_target_qty = $this->input->post('min_target');
        $max_target_qty = $this->input->post('max_target');
        $price = $this->input->post('price');
        $regen = $this->input->post('regen');
        if($regen){
            $this->delete_scheme_achievement_stock($idscheme);
        }
        $data = array();$total_count=0;
        for($i=0; $i < count($idvariant); $i++){
            $stk_pre = $this->Scheme_model->get_prebooking_data($idvariant[$i], $min_val_per[$i], $date_from, $date_to, $min_target_qty[$i], $max_target_qty[$i]);
            $sl_act = $this->Scheme_model->get_sale_activation_data($idvariant[$i], $actdate_from, $actdate_to, $min_target_qty[$i], $max_target_qty[$i]);
//            die('<pre>'.print_r($stk_pre,1).'</pre>');
//            die('<pre>'.print_r($sl_act,1).'</pre>');
            $count_stk_pre = count($stk_pre);
            $count_sl_act = count($sl_act);
            $min_count = min($count_stk_pre,$count_sl_act);
            for($j=0; $j<$min_count; $j++){
                $data[] = array(
                    'idvariant' => $idvariant[$i],
                    'idscheme' => $idscheme,
                    'idscheme_type' => $idscheme_type,
                    'imei_no' => $sl_act[$j]->imei_no,
                    'date' => $sl_act[$j]->date,
                    'new_price' => $price[$idvariant[$i]],
                );
            }
            $total_count += $min_count;
        }
        if($total_count){
            $this->Scheme_model->save_scheme_achievement_stock($data);
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $d['result'] = 'Failed';
            }else{
                $this->db->trans_commit();
                $this->update_scheme($idscheme,$total_count);
                $d['result'] = 'Success';
                $d['count'] = $total_count;
            }
        }else{
            $d['result'] = 'Failed';
            $d['count'] = $total_count;
        }
        echo json_encode($d);
    }
    public function generate_model_discontinue_claim() {
//        die(print_r($_POST));
        $this->db->trans_begin();
        $idscheme = $this->input->post('idscheme');
        $idscheme_type = $this->input->post('idscheme_type');
        $idvariant = $this->input->post('idvariant');
        $iddiscon = $this->input->post('iddiscon');
        $date_from = $this->input->post('date_from');
        $date_to = $this->input->post('date_to');
        $regen = $this->input->post('regen');
        if($regen){
            $this->delete_scheme_achievement_stock($idscheme);
        }
        $data = array();
        $stock_data = $this->Scheme_model->get_model_discontinue_data($idvariant,$iddiscon,$date_from,$date_to);
//        die(print_r($stock_data));
        $count = count($stock_data);
        if(count($stock_data)){
            foreach ($stock_data as $stock){
                $data[] = array(
                    'idvariant' => $stock->idvariant,
                    'idscheme' => $idscheme,
                    'idscheme_type' => $idscheme_type,
                    'imei_no' => $stock->imei_no,
                );
            }
            $this->Scheme_model->save_scheme_achievement_stock($data);
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $d['result'] = 'Failed';
            }else{
                $this->db->trans_commit();
                $this->update_scheme($idscheme,$count);
                $d['result'] = 'Success';
                $d['count'] = $count;
            }
        }else{
            $d['result'] = 'Failed';
            $d['count'] = $count;
        }
        echo json_encode($d);
    }
    public function generate_sell_out_claim(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $this->db->trans_begin();
        $idscheme = $this->input->post('idscheme');        
        $idvariant = $this->input->post('idvariant');        
        $min_qty = $this->input->post('min_qty');
        $max_qty = $this->input->post('max_qty');
        
        $regen = $this->input->post('regen');
        if($regen){
            $this->delete_scheme_achievement_stock($idscheme);
        }
        $scheme = $this->Scheme_model->get_scheme_byid($idscheme);
        $scheme_data = $this->Scheme_model->get_schemedata_byid($idscheme);
        
        $idscheme_type = $scheme->idscheme_type; 
        $date_from = $scheme->date_from;
        $date_to = $scheme->date_to;        
        $settlement_type = $scheme->settlement_type;
        $claim_target = $scheme->claim_target;
        $has_slabs = $scheme->has_slabs;
        
        
        $stock_data = array();$data = array();$overall_count=0;$focdata=array();
        if($claim_target==0){
            if($settlement_type==0){
                if($has_slabs==0){
                    foreach ($scheme_data as $sche_var){
                        $variants=array();
                        if($sche_var->all_colors==1){
                            $var_data = $this->General_model->get_active_variants_id($sche_var->idvariant);
                            if($var_data->idproductcategory==1){            
                                $idcategory=$var_data->idcategory;
                                if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33 || $idcategory==39 || $idcategory==38 || $idcategory==36){
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,1);
                                }else{
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,$var_data->idmodel,0);
                                }   
                                foreach ($variantss as $v){
                                    $variants[]=$v->id_variant;
                                } 
                            }else{
                                $variants[]=$sche_var->idvariant;
                            }
                        }else{
                            $variants[]=$sche_var->idvariant;
                        }                        
                        $stock_data = $this->Scheme_model->get_sale_activation_data($variants, $date_from, $date_to, $sche_var->min_target, $sche_var->min_target);                        
                        $foc_data = $this->Scheme_model->get_scheme_foc_data($sche_var->id_scheme_data);                        
                        $count = count($stock_data);
                        if($count){
                            foreach ($stock_data as $stock){
                                $data[] = array(
                                    'idvariant' => $stock->idvariant,
                                    'idscheme' => $idscheme,
                                    'idscheme_type' => $idscheme_type,
                                    'imei_no' => $stock->imei_no,
                                    'date' => $stock->date,
                                    'idscheme_data' => $sche_var->id_scheme_data,
                                    'idlink' => $stock->idsale,
                                    'sale_price_mop' => $stock->mop,
                                );
                            }
                            $overall_count += $count;
                            foreach ($foc_data as $foc){
                                $units=$count*($foc->foc_units);
                                $focdata[] = array(
                                    'id_foc_data' => $foc->id_foc_data, 
                                    'foc_settlement' => $units,
                                );
                            }
                        }            
                    }                    
                     
                }else{    //QTY_FOC_SLABS
                    
                        $variants=array();
                        $vids= explode(",",$scheme_data[0]->idvariant);
                        $all_colors=explode(",",$scheme_data[0]->all_colors);

                        $l=0;
                    foreach ($vids as $idvariant){ 
                        if($all_colors[0]==1){
                            $var_data = $this->General_model->get_active_variants_id($idvariant);
                            if($var_data->idproductcategory==1){            
                                $idcategory=$var_data->idcategory;
                                if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33 || $idcategory==39 || $idcategory==38 || $idcategory==36){
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,1);
                                }else{
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,$var_data->idmodel,0);
                                }   
                                foreach ($variantss as $v){
                                    $variants[]=$v->id_variant;
                                } 
                            }else{
                                $variants[]=$idvariant;
                            }
                        }else{
                            $variants[]=$idvariant;
                        }   
                    }
                            $stock_data = $this->Scheme_model->get_sale_activation_data($variants, $date_from, $date_to, 0, 0);                        

                            $count = count($stock_data); 
                            $sale_count=$count; 
                             foreach ($scheme_data as $sche_var){
                                 if($sale_count>0){
                                    $foc_cnt=0;
                                    $foc_cnt= intval(($sale_count/$sche_var->max_target));  
                                     if($foc_cnt>0){
                                        $foc_data = $this->Scheme_model->get_scheme_foc_data($sche_var->id_scheme_data);  
                                        foreach ($foc_data as $foc){
                                               $units=$foc_cnt*($foc->foc_units);
                                               $focdata[] = array(
                                                   'id_foc_data' => $foc->id_foc_data, 
                                                   'foc_settlement' => $units,
                                               );
                                           }
                                        $sale_count=($sale_count-($foc_cnt*$sche_var->max_target));
                                     }
                                   
                                 }
                            }
//                        }      
//                            die('<pre>'.print_r($focdata,1).'</pre>');
                        
                        if($count){
                            foreach ($stock_data as $stock){
                                $data[] = array(
                                    'idvariant' => $stock->idvariant,
                                    'idscheme' => $idscheme,
                                    'idscheme_type' => $idscheme_type,
                                    'imei_no' => $stock->imei_no,
                                    'date' => $stock->date,                                    
                                    'idlink' => $stock->idsale,
                                    'sale_price_mop' => $stock->mop,
                                );
                            }
                            $overall_count += $count; 
                        }            
                           
                }
            }elseif($settlement_type==1){
                if($has_slabs==0){ 
                    foreach ($scheme_data as $sche_var){
                        $variants=array();
                        if($sche_var->all_colors==1){
                            $var_data = $this->General_model->get_active_variants_id($sche_var->idvariant);
                            if($var_data->idproductcategory==1){            
                                $idcategory=$var_data->idcategory;
                                if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33 || $idcategory==39 || $idcategory==38 || $idcategory==36){
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,1);
                                }else{
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,$var_data->idmodel,0);
                                }   foreach ($variantss as $v){
                                    $variants[]=$v->id_variant;
                                } 
                            }else{
                                $variants[]=$sche_var->idvariant;
                            }
                        }else{
                            $variants[]=$sche_var->idvariant;
                        }   
                        
                        $stock_data = $this->Scheme_model->get_sale_activation_data($variants, $date_from, $date_to, $sche_var->min_target, $sche_var->min_target);                        
                        $count = count($stock_data);
                        if($count){
                            foreach ($stock_data as $stock){
                                $data[] = array(
                                    'idvariant' => $stock->idvariant,
                                    'idscheme' => $idscheme,
                                    'idscheme_type' => $idscheme_type,
                                    'imei_no' => $stock->imei_no,
                                    'date' => $stock->date,
                                    'idscheme_data' => $sche_var->id_scheme_data,
                                    'idlink' => $stock->idsale,
                                    'sale_price_mop' => $stock->mop,
                                );
                            }
                            $overall_count += $count;
                        }
            //            die(print_r($stock_data)); 
                    } 
                }else{
                    /// QTY_PAYOUT_SLABS
                    
                   $variants=array();
                        $vids= explode(",",$scheme_data[0]->idvariant);
                        $all_colors=explode(",",$scheme_data[0]->all_colors);

                        $l=0;
                    foreach ($vids as $idvariant){ 
                        if($all_colors[$l]==1){
                            $var_data = $this->General_model->get_active_variants_id($idvariant);
                            if($var_data->idproductcategory==1){            
                                $idcategory=$var_data->idcategory;
                                if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33 || $idcategory==39 || $idcategory==38 || $idcategory==36){
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,1);
                                }else{
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,$var_data->idmodel,0);
                                }   
                                foreach ($variantss as $v){
                                    $variants[]=$v->id_variant;
                                } 
                            }else{
                                $variants[]=$idvariant;
                            }
                        }else{
                            $variants[]=$idvariant;
                        }   
                        $l++;
                    }
                    $stock_data = $this->Scheme_model->get_sale_activation_data($variants, $date_from, $date_to, 0, 0);                        
                    $sale_count = count($stock_data);  
                            
                        if($sale_count){
                            foreach ($stock_data as $stock){
                                $data[] = array(
                                    'idvariant' => $stock->idvariant,
                                    'idscheme' => $idscheme,
                                    'idscheme_type' => $idscheme_type,
                                    'imei_no' => $stock->imei_no,
                                    'date' => $stock->date,                                    
                                    'idlink' => $stock->idsale,
                                    'sale_price_mop' => $stock->mop,
                                );
                            }
                            $overall_count += $sale_count; 
                        }  
  
                            
                }
            }elseif($settlement_type==2){
                if($has_slabs==0){
                    $p=0;
                    foreach ($scheme_data as $sche_var){
                        $variants=array();
                        if($sche_var->all_colors==1){
                            $var_data = $this->General_model->get_active_variants_id($sche_var->idvariant);
                            if($var_data->idproductcategory==1){            
                                $idcategory=$var_data->idcategory;
                                if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33 || $idcategory==39 || $idcategory==38 || $idcategory==36){
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,1);
                                }else{
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,$var_data->idmodel,0);
                                }   foreach ($variantss as $v){
                                    $variants[]=$v->id_variant;
                                } 
                            }else{
                                $variants[]=$sche_var->idvariant;
                            }
                        }else{
                            $variants[]=$sche_var->idvariant;
                        }                       
                        $stock_data = $this->Scheme_model->get_sale_activation_data($variants, $date_from, $date_to, $sche_var->min_target, $sche_var->min_target);                        
                        $count = count($stock_data);
                        if($count){
                            foreach ($stock_data as $stock){
                                $data[] = array(
                                    'idvariant' => $stock->idvariant,
                                    'idscheme' => $idscheme,
                                    'idscheme_type' => $idscheme_type,
                                    'imei_no' => $stock->imei_no,
                                    'date' => $stock->date,
                                    'idscheme_data' => $sche_var->id_scheme_data,
                                    'idlink' => $stock->idsale,
                                    'sale_price_mop' => $stock->mop,
                                    'purchase_basic' => $stock->basic,
                                    'purchase_price' => $stock->total_amount,
                                );
                            }
                            $overall_count += $count;
                        }
                       $p++; 
                    }                    
                }else{
                    //QTY_PER_SLAB
                     
                   $variants=array();
                        $vids= explode(",",$scheme_data[0]->idvariant);
                        $all_colors=explode(",",$scheme_data[0]->all_colors);

                        $l=0;
                    foreach ($vids as $idvariant){ 
                        if($all_colors[$l]==1){
                            $var_data = $this->General_model->get_active_variants_id($idvariant);
                            if($var_data->idproductcategory==1){            
                                $idcategory=$var_data->idcategory;
                                if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33 || $idcategory==39 || $idcategory==38 || $idcategory==36){
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,1);
                                }else{
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,$var_data->idmodel,0);
                                }   
                                foreach ($variantss as $v){
                                    $variants[]=$v->id_variant;
                                } 
                            }else{
                                $variants[]=$idvariant;
                            }
                        }else{
                            $variants[]=$idvariant;
                        }   
                        $l++;
                    }
                    $stock_data = $this->Scheme_model->get_sale_activation_data($variants, $date_from, $date_to, 0, 0);                                            
                    $sale_count = count($stock_data);  
                            
                        if($sale_count){
                            foreach ($stock_data as $stock){
                                $data[] = array(
                                    'idvariant' => $stock->idvariant,
                                    'idscheme' => $idscheme,
                                    'idscheme_type' => $idscheme_type,
                                    'imei_no' => $stock->imei_no,
                                    'date' => $stock->date,                                    
                                    'idlink' => $stock->idsale,
                                    'sale_price_mop' => $stock->mop,
                                    'purchase_basic' => $stock->basic,
                                    'purchase_price' => $stock->total_amount,
                                );
                            }
                            $overall_count += $sale_count; 
                        }  
                }
            }
        }if($claim_target==1){
            if($settlement_type==0){
                if($has_slabs==0){
                    
                }else{
                     //OVAL_FOC_SLAB
                    $variants=array();
                        $vids= explode(",",$scheme_data[0]->idvariant);
                        $all_colors=explode(",",$scheme_data[0]->all_colors);

                        $l=0;
                    foreach ($vids as $idvariant){ 
                        if($all_colors[0]==1){
                            $var_data = $this->General_model->get_active_variants_id($idvariant);
                            if($var_data->idproductcategory==1){            
                                $idcategory=$var_data->idcategory;
                                if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33 || $idcategory==39 || $idcategory==38 || $idcategory==36){
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,1);
                                }else{
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,$var_data->idmodel,0);
                                }   
                                foreach ($variantss as $v){
                                    $variants[]=$v->id_variant;
                                } 
                            }else{
                                $variants[]=$idvariant;
                            }
                        }else{
                            $variants[]=$idvariant;
                        }   
                    }
                        $stock_data = $this->Scheme_model->get_sale_activation_data($variants, $date_from, $date_to, 0, 0);                        
                        $count = count($stock_data); 
                        $basic = 0;$mop=0;
                        if($count){
                            foreach ($stock_data as $stock){
                                $data[] = array(
                                    'idvariant' => $stock->idvariant,
                                    'idscheme' => $idscheme,
                                    'idscheme_type' => $idscheme_type,
                                    'imei_no' => $stock->imei_no,
                                    'date' => $stock->date,                                    
                                    'idlink' => $stock->idsale,
                                    'sale_price_mop' => $stock->mop,
                                    'purchase_basic' => $stock->basic,
                                    'purchase_price' => $stock->total_amount,
                                );
                                $basic += $stock->basic;
                                $mop += $stock->mop;
                            }
                            $overall_count += $count; 
                        }  
                        
                             foreach ($scheme_data as $sche_var){
                                 if($mop>0){
                                    $foc_cnt=0;
                                     if($mop > $sche_var->min_target){
                                        $foc_cnt= 1;       
                                     }
                                     if($foc_cnt>0){
                                        $foc_data = $this->Scheme_model->get_scheme_foc_data($sche_var->id_scheme_data);  
                                        foreach ($foc_data as $foc){
                                               $units=$foc_cnt*($foc->foc_units);
                                               $focdata[] = array(
                                                   'id_foc_data' => $foc->id_foc_data, 
                                                   'foc_settlement' => $units,
                                               );
                                           }                                        
                                     }
                                   
                                 }
                            }
                }
            }elseif($settlement_type==1){
                if($has_slabs==0){
                    
                }else{
                      //OVAL_Payout_SLAB
                    $variants=array();
                        $vids= explode(",",$scheme_data[0]->idvariant);
                        $all_colors=explode(",",$scheme_data[0]->all_colors);

                        $l=0;
                    foreach ($vids as $idvariant){  
                        if($all_colors[0]==1){
                            $var_data = $this->General_model->get_active_variants_id($idvariant);
                            if($var_data->idproductcategory==1){            
                                $idcategory=$var_data->idcategory;
                                if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33 || $idcategory==39 || $idcategory==38 || $idcategory==36){
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,1);
                                }else{
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,$var_data->idmodel,0);
                                }   
                                foreach ($variantss as $v){
                                    $variants[]=$v->id_variant;
                                } 
                            }else{
                                $variants[]=$idvariant;
                            }
                        }else{
                            $variants[]=$idvariant;
                        }   
                    }
                        $stock_data = $this->Scheme_model->get_sale_activation_data($variants, $date_from, $date_to, 0, 0);                        
                        $count = count($stock_data); 
                        $basic = 0;$mop=0;
                        if($count){
                            foreach ($stock_data as $stock){
                                $data[] = array(
                                    'idvariant' => $stock->idvariant,
                                    'idscheme' => $idscheme,
                                    'idscheme_type' => $idscheme_type,
                                    'imei_no' => $stock->imei_no,
                                    'date' => $stock->date,                                    
                                    'idlink' => $stock->idsale,
                                    'sale_price_mop' => $stock->mop,
                                    'purchase_basic' => $stock->basic,
                                    'purchase_price' => $stock->total_amount,
                                );
                                $basic += $stock->basic;
                                $mop += $stock->mop;
                            }
                            $overall_count += $count; 
                        }  
                         
                }
            }elseif($settlement_type==2){
                if($has_slabs==0){
                    
                }else{
                     //OVAL_PER_SLAB
                    $variants=array();
                        $vids= explode(",",$scheme_data[0]->idvariant);
                        $all_colors=explode(",",$scheme_data[0]->all_colors);

                        $l=0;
                    foreach ($vids as $idvariant){ 
                        if($all_colors[0]==1){
                            $var_data = $this->General_model->get_active_variants_id($idvariant);
                            if($var_data->idproductcategory==1){            
                                $idcategory=$var_data->idcategory;
                                if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33 || $idcategory==39 || $idcategory==38 || $idcategory==36){
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,1);
                                }else{
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,$var_data->idmodel,0);
                                }   
                                foreach ($variantss as $v){
                                    $variants[]=$v->id_variant;
                                } 
                            }else{
                                $variants[]=$idvariant;
                            }
                        }else{
                            $variants[]=$idvariant;
                        }   
                    }
                        $stock_data = $this->Scheme_model->get_sale_activation_data($variants, $date_from, $date_to, 0, 0);                        
                        $count = count($stock_data); 
                        $basic = 0;$mop=0;
                        if($count){
                            foreach ($stock_data as $stock){
                                $data[] = array(
                                    'idvariant' => $stock->idvariant,
                                    'idscheme' => $idscheme,
                                    'idscheme_type' => $idscheme_type,
                                    'imei_no' => $stock->imei_no,
                                    'date' => $stock->date,                                    
                                    'idlink' => $stock->idsale,
                                    'sale_price_mop' => $stock->mop,
                                    'purchase_basic' => $stock->basic,
                                    'purchase_price' => $stock->total_amount,
                                );
                                $basic += $stock->basic;
                                $mop += $stock->mop;
                            }
                            $overall_count += $count; 
                        }  
                }
            }
        } 
        
        $this->db->trans_complete();
        if($overall_count){
//             die('<pre>'.print_r($data,1).'</pre>');
            $a=$this->Scheme_model->save_scheme_achievement_stock($data);
            if(count($focdata)>0){
                $this->Scheme_model->update_scheme_foc_data($focdata);                
            } 
            
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $d['result'] = 'Failed';
            }else{
                $this->db->trans_commit();
                $this->update_scheme($idscheme,$overall_count);
                $d['result'] = 'Success';
                $d['count'] = $overall_count;
            }
        }else{
            $d['result'] = 'Failed';
            $d['count'] = 0;
        }
        echo json_encode($d);
    }
    public function generate_purchase_claim(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $this->db->trans_begin();
        $idscheme = $this->input->post('idscheme');        
        
        $regen = $this->input->post('regen');
        if($regen){
            $this->delete_scheme_achievement_stock($idscheme);
        }
        $scheme = $this->Scheme_model->get_scheme_byid($idscheme);
        $scheme_data = $this->Scheme_model->get_schemedata_byid($idscheme);
        
        $idscheme_type = $scheme->idscheme_type; 
        $date_from = $scheme->date_from;
        $date_to = $scheme->date_to;        
        $settlement_type = $scheme->settlement_type;
        $claim_target = $scheme->claim_target;
        $has_slabs = $scheme->has_slabs;
        
         if($has_slabs==0){
             foreach ($scheme_data as $sche_var){
                        $variants=array();
                        if($sche_var->all_colors==1){
                            $var_data = $this->General_model->get_active_variants_id($sche_var->idvariant);
                            if($var_data->idproductcategory==1){            
                                $idcategory=$var_data->idcategory;
                                if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33 || $idcategory==39 || $idcategory==38 || $idcategory==36){
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,1);
                                }else{
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,$var_data->idmodel,0);
                                }   
                                foreach ($variantss as $v){
                                    $variants[]=$v->id_variant;
                                } 
                            }else{
                                $variants[]=$sche_var->idvariant;
                            }
                        }else{
                            $variants[]=$sche_var->idvariant;
                        }                        
                        $stock_data = $this->Scheme_model->get_purchase_data($variants, $date_from, $date_to, $sche_var->min_target, $sche_var->min_target);                        
                        
                        $count = count($stock_data);
                        if($count){
                            foreach ($stock_data as $stock){
                                $data[] = array(
                                    'idvariant' => $stock->idvariant,
                                    'idscheme' => $idscheme,
                                    'idscheme_type' => $idscheme_type,
                                    'imei_no' => $stock->imei_no,
                                    'date' => $stock->date,
                                    'idscheme_data' => $sche_var->id_scheme_data,
                                    'idlink' => $stock->idinward,
                                    'sale_price_mop' => $stock->total_amount,
                                    'purchase_basic' => $stock->basic,
                                    'purchase_price' => $stock->total_amount,
                                );
                            }
                            $overall_count += $count;
                            if($settlement_type==0){
                                $foc_data = $this->Scheme_model->get_scheme_foc_data($sche_var->id_scheme_data);                        
                                foreach ($foc_data as $foc){
                                    $units=$count*($foc->foc_units);
                                    $focdata[] = array(
                                        'id_foc_data' => $foc->id_foc_data, 
                                        'foc_settlement' => $units,
                                    );
                                }
                            }
                        }            
                    }
         }else{
             
                        $variants=array();
                        $vids= explode(",",$scheme_data[0]->idvariant);
                        $all_colors=explode(",",$scheme_data[0]->all_colors);

                        $l=0;
                    foreach ($vids as $idvariant){ 
                        if($all_colors[0]==1){
                            $var_data = $this->General_model->get_active_variants_id($idvariant);
                            if($var_data->idproductcategory==1){            
                                $idcategory=$var_data->idcategory;
                                if($idcategory==2 || $idcategory==28 || $idcategory==31 || $idcategory==33 || $idcategory==39 || $idcategory==38 || $idcategory==36){
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,1);
                                }else{
                                    $variantss = $this->General_model->get_same_variants_for_allocation($var_data->id_variant,$var_data->idmodel,0);
                                }   
                                foreach ($variantss as $v){
                                    $variants[]=$v->id_variant;
                                } 
                            }else{
                                $variants[]=$idvariant;
                            }
                        }else{
                            $variants[]=$idvariant;
                        }   
                    }
                            $stock_data = $this->Scheme_model->get_purchase_data($variants, $date_from, $date_to, 0, 0);                        
                            $count = count($stock_data); 
                            $basic = 0;$total_amount=0;
                            if($count){
                                foreach ($stock_data as $stock){
                                    $data[] = array(
                                        'idvariant' => $stock->idvariant,
                                        'idscheme' => $idscheme,
                                        'idscheme_type' => $idscheme_type,
                                        'imei_no' => $stock->imei_no,
                                        'date' => $stock->date,                                    
                                        'idlink' => $stock->idinward,
                                        'sale_price_mop' => $stock->total_amount,
                                        'purchase_basic' => $stock->basic,
                                        'purchase_price' => $stock->total_amount,
                                        );
                                    $basic += $stock->basic; 
                                    $total_amount += $stock->total_amount; 
                                }
                                $overall_count += $count; 
                        }  
                        if($settlement_type==0){
                            if($claim_target==1){
                                $sale_count=$basic; 
                                foreach ($scheme_data as $sche_var){
                                 if($sale_count>0){
                                    $foc_cnt=0;
                                     if($sale_count > $sche_var->min_target){
                                        $foc_cnt= 1;       
                                     }
                                     if($foc_cnt>0){
                                        $foc_data = $this->Scheme_model->get_scheme_foc_data($sche_var->id_scheme_data);  
                                        foreach ($foc_data as $foc){
                                               $units=$foc_cnt*($foc->foc_units);
                                               $focdata[] = array(
                                                   'id_foc_data' => $foc->id_foc_data, 
                                                   'foc_settlement' => $units,
                                               );
                                           }                                        
                                     }
                                   
                                 }
                            }
                            }else{
                                $sale_count=$count;  
                                foreach ($scheme_data as $sche_var){
                                 if($sale_count>0){
                                    $foc_cnt=0;
                                    $foc_cnt= intval(($sale_count/$sche_var->max_target));  
                                     if($foc_cnt>0){
                                        $foc_data = $this->Scheme_model->get_scheme_foc_data($sche_var->id_scheme_data);  
                                        foreach ($foc_data as $foc){
                                               $units=$foc_cnt*($foc->foc_units);
                                               $focdata[] = array(
                                                   'id_foc_data' => $foc->id_foc_data, 
                                                   'foc_settlement' => $units,
                                               );
                                           }
                                        $sale_count=($sale_count-($foc_cnt*$sche_var->max_target));
                                     }
                                 }
                                   
                            }
                            }
                            
                                
                        }  
         }
         $this->db->trans_complete();
        if($overall_count){
            $as=$this->Scheme_model->save_scheme_achievement_stock($data);
            if(count($focdata)>0){
                $this->Scheme_model->update_scheme_foc_data($focdata);                
            } 
            if ($this->db->trans_status() === FALSE){
                $this->db->trans_rollback();
                $d['result'] = 'Failed';
            }else{
                $this->db->trans_commit();
                $this->update_scheme($idscheme,$overall_count);
                $d['result'] = 'Success';
                $d['count'] = $overall_count;
            }
        }else{
            $d['result'] = 'Failed';
            $d['count'] = 0;
        }
        echo json_encode($d);
    }
    
    public function view_prebooking_claim(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
//        $idtype = $this->input->post('idtype');
        $idscheme = $this->input->post('idscheme');
        $scheme_type = $this->input->post('scheme_type');
        $claim_data = $this->Scheme_model->get_claim_product_data_byidscheme($idscheme);
        if(count($claim_data)){ ?>
            <table class="table table-bordered" id="<?php echo $scheme_type ?>">
                <thead>
                    <th></th>
                    <th><?php echo $scheme_type ?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <thead>
                    <th>Sr</th>
                    <th>Date</th>
                    <th>Model</th>
                    <th>IMEI</th>
                    <th>Per unit incentive</th>
                </thead>
                <tbody>
                    <?php $i=1;$np=0; foreach ($claim_data as $claim){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td>'<?php echo $claim->date ?></td>
                        <td><?php echo $claim->full_name ?></td>
                        <td><?php echo $claim->imei_no ?></td>
                        <td><?php echo $claim->new_price ?></td>
                    </tr>
                    <?php $i++; $np += $claim->new_price; } ?>
                </tbody>
                <tfoot>
                    <th></th>
                    <th></th>
                    <th>Total</th>
                    <th>Qty = <?php echo $i-1; ?></th>
                    <th><?php echo $np; ?></th>
                </tfoot>
            </table>
        <?php }else{
            echo '0';
        }
    }
    public function view_price_drop_claim(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
//        $idtype = $this->input->post('idtype');
        $idscheme = $this->input->post('idscheme');
        $scheme_type = $this->input->post('scheme_type');
        $claim_data = $this->Scheme_model->get_claim_product_data_byidscheme($idscheme);
        if(count($claim_data)){ ?>
            <table class="table table-bordered" id="<?php echo $scheme_type ?>">
                <thead>
                    <th></th>
                    <th><?php echo $scheme_type ?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <thead>
                    <th>Sr</th>
                    <th>Date</th>
                    <th>Model</th>
                    <th>IMEI</th>
                    <th>Last purchase price</th>
                    <th>Price Change</th>
                    <th>New Price</th>
                </thead>
                <tbody>
                    <?php $i=1;$lpp=0;$epc=0;$np=0; foreach ($claim_data as $claim){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td>'<?php echo $claim->date ?></td>
                        <td><?php echo $claim->full_name ?></td>
                        <td><?php echo $claim->imei_no ?></td>
                        <td><?php echo $claim->last_purchase_price ?></td>
                        <td><?php echo $claim->effective_price_change ?></td>
                        <td><?php echo $claim->new_price ?></td>
                    </tr>
                    <?php $i++; $lpp += $claim->last_purchase_price; 
                        $epc += $claim->effective_price_change;
                        $np += $claim->new_price;
                    } ?>
                </tbody>
                <tfoot>
                    <th></th>
                    <th></th>
                    <th>Total</th>
                    <th>Qty = <?php echo $i-1; ?></th>
                    <th><?php echo $lpp; ?></th>
                    <th><?php echo $epc; ?></th>
                    <th><?php echo $np; ?></th>
                </tfoot>
            </table>
        <?php }else{
            echo '0';
        }
    }
    public function view_model_discontinue_claim(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $idscheme = $this->input->post('idscheme');
        $scheme_type = $this->input->post('scheme_type');
        $claim_data = $this->Scheme_model->get_claim_product_data_byidscheme($idscheme);
        if(count($claim_data)){ ?>
            <table class="table table-bordered" id="<?php echo $scheme_type ?>">
                <thead>
                    <th></th>
                    <th><?php echo $scheme_type ?></th>
                    <th></th>
                </thead>
                <thead>
                    <th>Sr</th>
                    <th>Model</th>
                    <th>IMEI</th>
                </thead>
                <tbody>
                    <?php $i=1; foreach ($claim_data as $claim){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td><?php echo $claim->full_name ?></td>
                        <td><?php echo $claim->imei_no ?></td>
                    </tr>
                    <?php $i++; } ?>
                </tbody>
            </table>
        <?php }else{
            echo '0';
        }
    }
    public function view_sell_out_claim(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $idscheme = $this->input->post('idscheme');
        $has_slabs = $this->input->post('has_slabs');
        $scheme_type = $this->input->post('scheme_type');
        $settlement_type = $this->input->post('settlement_type');
        $claim_data = $this->Scheme_model->get_claim_product_data_byidscheme($idscheme);
        $scheme_data = $this->Scheme_model->get_schemedata_byid($idscheme);
        $scheme = $this->Scheme_model->get_scheme_byid($idscheme);
        
        
//        die('<pre>'.print_r($claim_data,1).'</pre>');
        if(count($claim_data)){ ?>
            <table class="table table-bordered" id="<?php echo $scheme_type ?>">
                <?php if($settlement_type == 2){ ?>
                <thead>
                    <th></th>
                    <th><?php echo $scheme_type ?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th colspan="2"><center>Payout</center></th>
                    <th></th>
                </thead>
                <thead>
                    <th>Sr</th>
                    <th>Sold Date</th>
                    <th>Model</th>
                    <th>IMEI</th>
                    <th>Price</th>
                    <th>Percentage</th>
                    <th>Amount</th>
                    <th>Invoice No</th>
                </thead>
                <tbody>
                    <?php $payout_per=0;
                    if($has_slabs==1){
                        if($scheme->claim_target==1){
                            $scheme_ach = $this->Scheme_model->get_sums_scheme_achievement_by_idscheme($idscheme);                             
                            $sale_count= $scheme_ach[0]->mop;
                        }else{
                            $sale_count= count($claim_data);    
                        }
                        if($sale_count>0){  
                        foreach ($scheme_data as $sd){ 
                               if($sd->min_target==$sd->max_target && $sale_count >= $sd->max_target){
                                  $payout_per= $sd->payout_per;
                               }else if($sale_count>=$sd->min_target && $sale_count <= $sd->max_target){
                                   $payout_per= $sd->payout_per;
                               } 
                            } 
                        }
                    }                    
                    $i=1;$tclaim_sum=0; foreach ($claim_data as $claim){
                        $price=0;
                        if($payout_per==0){
                            $claim_sum = ($claim->purchase_basic * $claim->payout_per)/100;  
                            $price= $claim->purchase_basic;
                        }else{
                            if($scheme->claim_target==1){
                                $claim_sum = ($claim->sale_price_mop * $payout_per)/100;
                                $price= $claim->sale_price_mop;
                            }else{
                                $claim_sum = ($claim->purchase_basic * $payout_per)/100;
                                $price= $claim->purchase_basic;
                            }
                        }
                        $tclaim_sum += $claim_sum; ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td>'<?php echo $claim->date ?></td>
                        <td><?php echo $claim->full_name ?></td>
                        <td><?php echo $claim->imei_no ?></td>
                        <td><?php echo $price; ?></td>
                        <td><?php if($payout_per==0){ echo $claim->payout_per; }else{ echo $payout_per; }?>%</td>
                        <td><?php echo $claim_sum ?></td>
                        <!--<td><a style="color: #1b6caa"><?php echo $claim->inv_no ?></a></td>-->
                        <td><a target="_blank" href="<?php echo base_url('Sale/sale_details/'.$claim->idlink) ?>" class="btn btn-sm waves-effect" style="background-color: #cdfbee"><?php echo $claim->inv_no ?></a></td>
                        <!--<td><a target="_blank" href="<?php // echo base_url('Sale/invoice_print/'.$sale->idsale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>-->
                    </tr>
                    <?php $i++; } ?>
                </tbody>
                    <tfoot>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total</th>
                        <th><?php echo $tclaim_sum ?></th>
                        <th></th>
                    </tfoot>
                <?php }else{ ?>
                <thead>
                    <th></th>
                    <th><?php echo $scheme_type ?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <thead>
                    <th>Sr</th>
                    <th>Sold Date</th>
                    <th>Model</th>
                    <th>IMEI</th>
                    <th>Invoice No</th>
                </thead>
                <tbody>
                    <?php $i=1; foreach ($claim_data as $claim){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td>'<?php echo $claim->date ?></td>
                        <td><?php echo $claim->full_name ?></td>
                        <td><?php echo $claim->imei_no ?></td>
                        <!--<td><a style="color: #1b6caa"><?php echo $claim->inv_no ?></a></td>-->
                        <td><a target="_blank" href="<?php echo base_url('Sale/sale_details/'.$claim->idlink) ?>" class="btn btn-sm waves-effect" style="background-color: #cdfbee"><?php echo $claim->inv_no ?></a></td>
                        <!--<td><a target="_blank" href="<?php // echo base_url('Sale/invoice_print/'.$sale->idsale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>-->
                    </tr>
                    <?php $i++; } ?>
                <?php } ?>
            </table>
        <?php }else{
            echo '0';
        }
    }
    public function view_purchase_claim(){
//        die('<pre>'.print_r($_POST,1).'</pre>');
        $idscheme = $this->input->post('idscheme');
        $has_slabs = $this->input->post('has_slabs');
        $scheme_type = $this->input->post('scheme_type');
        $settlement_type = $this->input->post('settlement_type');
        $claim_data = $this->Scheme_model->get_purchase_claim_product_data_byidscheme($idscheme);
        $scheme_data = $this->Scheme_model->get_schemedata_byid($idscheme);
        $scheme = $this->Scheme_model->get_scheme_byid($idscheme);
        
        if(count($claim_data)){ ?>
            <table class="table table-bordered" id="<?php echo $scheme_type ?>">
                <?php if($settlement_type == 2){ ?>
                <thead>
                    <th></th>
                    <th><?php echo $scheme_type ?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th colspan="2"><center>Payout</center></th>
                    <th></th>
                </thead>
                <thead>
                    <th>Sr</th>
                    <th>Purchase Date</th>
                    <th>Model</th>
                    <th>IMEI</th>
                    <th>Bacis Price</th>
                    <th>Percentage</th>
                    <th>Amount</th>
                    <th>Purchase Invoice</th>
                </thead>
                <tbody><?php $payout_per=0;$sale_count=0;
                    if($has_slabs==1){                               
                        if($scheme->claim_target==1){
                            $scheme_ach = $this->Scheme_model->get_sums_scheme_achievement_by_idscheme($idscheme);                             
                            $sale_count= $scheme_ach[0]->basic;
                        }else{
                            $sale_count= count($claim_data);    
                        }
                        if($sale_count>0){  
                        foreach ($scheme_data as $sd){ 
                               if($sd->min_target==$sd->max_target && $sale_count >= $sd->max_target){
                                  $payout_per= $sd->payout_per;
                               }else if($sale_count>=$sd->min_target && $sale_count <= $sd->max_target){
                                   $payout_per= $sd->payout_per;
                               } 
                            } 
                        }
                    }  
                   
                    $i=1;$tclaim_sum=0; foreach ($claim_data as $claim){
                        $price=0;
                        if($payout_per==0){
                            $claim_sum = ($claim->purchase_basic * $claim->payout_per)/100;  
                            $price= $claim->purchase_basic;
                        }else{                            
                                $claim_sum = ($claim->purchase_basic * $payout_per)/100;
                                $price= $claim->purchase_basic;
                        }
                        $tclaim_sum += $claim_sum; ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td>'<?php echo $claim->date ?></td>
                        <td><?php echo $claim->full_name ?></td>
                        <td><?php echo $claim->imei_no ?></td>
                        <td><?php echo $claim->purchase_basic ?></td>
                        <td><?php echo $payout_per ?>%</td>
                        <td><?php echo $claim_sum ?></td>
                        <!--<td><a style="color: #1b6caa"><?php echo $claim->inv_no ?></a></td>-->
                        <td><a target="_blank" href="<?php echo base_url('Purchase/inward_details/'.$claim->idlink) ?>" class="btn btn-sm waves-effect" style="background-color: #cdfbee"><?php echo $claim->financial_year.'/'.$claim->idlink ?></a></td>
                        <!--<td><a target="_blank" href="<?php // echo base_url('Sale/invoice_print/'.$sale->idsale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>-->
                    </tr>
                    <?php $i++; } ?>
                </tbody>
                    <tfoot>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Total</th>
                        <th><?php echo $tclaim_sum ?></th>
                        <th></th>
                    </tfoot>
                <?php }else{ ?>
                <thead>
                    <th></th>
                    <th><?php echo $scheme_type ?></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </thead>
                <thead>
                    <th>Sr</th>
                    <th>Sold Date</th>
                    <th>Model</th>
                    <th>IMEI</th>
                    <th>Purchase Invoice</th>
                </thead>
                <tbody>
                    <?php $i=1; foreach ($claim_data as $claim){ ?>
                    <tr>
                        <td><?php echo $i ?></td>
                        <td>'<?php echo $claim->date ?></td>
                        <td><?php echo $claim->full_name ?></td>
                        <td><?php echo $claim->imei_no ?></td>
                        <!--<td><a style="color: #1b6caa"><?php echo $claim->inv_no ?></a></td>-->
                        <td><a target="_blank" href="<?php echo base_url('Purchase/inward_details/'.$claim->idlink) ?>" class="btn btn-sm waves-effect" style="background-color: #cdfbee"><?php echo $claim->financial_year.'/'.$claim->idlink ?></a></td>
                        <!--<td><a target="_blank" href="<?php // echo base_url('Sale/invoice_print/'.$sale->idsale) ?>" class="btn btn-default btn-floating waves-effect"><i class="fa fa-print"></i></a></td>-->
                    </tr>
                    <?php $i++; } ?>
                <?php } ?>
            </table>
        <?php }else{
            echo '0';
        }
    }
    
}