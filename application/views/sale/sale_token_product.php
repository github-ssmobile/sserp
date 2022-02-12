<tbody id="product_data">
<?php
//echo '<pre>'.print_r($sale_token_product,1).'</pre>';
$stgross_total = 0;
$stfinal_discount = 0;
$stfinal_total = 0;

foreach ($sale_token_product as $token_product){
$imei = $token_product->imei_no;
$idbranch = $token_product->idbranch; 
if($token_product->idskutype == 4){ $skuvariant=4;}else{ $skuvariant=0; }; 
$idgodown = $token_product->idgodown; 
$is_dcprint = 0;// = $token_product->is_dcprint; 
$sale_type = $token_product->sale_type; 
$token_product->dcprint = $sale_token->dcprint;
$token_product->id_stock = $token_product->idstock;

// Quantity
    if($skuvariant == 4){
        if($sale_type != 2){
            $ageing_data = $this->Stock_model->get_ageing_stock_data($token_product->idproductcategory, $token_product->idbrand, $token_product->idmodel, $token_product->idvariant, $token_product->idbranch);
            if($ageing_data){
                $ageing = 1;
            }else{
                $ageing =0;
            }

            $focus_data = $this->Stock_model->get_focus_stock_data($token_product->idproductcategory, $token_product->idbrand, $token_product->idmodel, $token_product->idvariant, $token_product->idbranch);
            if($focus_data){
                $focus_status = 1;
                $focus_amount = $focus_data->incentive_amount;
            }else{
                $focus_status = 0;
                $focus_amount = 0;
            }
        }
        if($sale_type == 2){ 
            $token_product->dcprint = 0;
            $token_product->mop = 1;
            $token_product->landing = 1;
            $token_product->is_gst = 1;
            $token_product->id_stock = 0;
            $token_product->idvendor = 1;
            $ageing =0;
            $focus_amount = 0;
            $focus_status = 0;
        }
        $amount_diff = $token_product->mop - $token_product->landing; ?>
        <tr id="m<?php echo $token_product->id_stock ?>" class="skuqty_row">
            <td>
                <?php echo $token_product->product_name; ?>
                <input type="hidden" id="idtype" class="form-control idtype" name="idtype[]" value="<?php echo $token_product->idproductcategory ?>" />
                <input type="hidden" id="idcategory" class="form-control idcategory" name="idcategory[]" value="<?php echo $token_product->idcategory ?>" />
                <input type="hidden" id="idbrand" class="form-control" name="idbrand[]" value="<?php echo $token_product->idbrand ?>" />
                <input type="hidden" id="idmodel" class="form-control idmodel" name="idmodel[]" value="<?php echo $token_product->idmodel ?>" />
                <input type="hidden" id="idvariant" class="form-control idvariant" name="idvariant[]" value="<?php echo $token_product->idvariant ?>" />
                <input type="hidden" id="idgodown" class="form-control idgodown" name="idgodown[]" value="<?php echo $idgodown ?>" />
                <input type="hidden" id="skutype" class="form-control skutype" name="skutype[]" value="<?php echo $token_product->idskutype ?>" />
                <input type="hidden" id="product_name" class="form-control product_name" name="product_name[]" value="<?php echo $token_product->product_name; ?>" />
                <input type="hidden" id="is_mop" class="form-control is_mop" name="is_mop[]" value="<?php echo $token_product->is_mop; ?>" />
                <input type="hidden" id="hsn" class="form-control hsn" name="hsn[]" value="<?php echo $token_product->hsn; ?>" />
                <input type="hidden" id="is_gst" class="form-control is_gst" name="is_gst[]" value="<?php echo $token_product->is_gst; ?>" />
                <small class="pull-right" style="color:#1b6caa;font-family: Kurale;">New Godown</small>
                <input type="hidden" id="dcprint" class="dcprint" name="dcprint[]" value="<?php echo $token_product->dcprint; ?>" />
                <?php if($sale_type == 0){ ?>
                <input type="hidden" id="activation_code" name="activation_code[]" class="activation_code" value="<?php echo $token_product->activation_code; ?>" />
                <?php }else{ ?>
                <input type="text" id="activation_code" name="activation_code[]" class="activation_code form-control input-sm" required="" placeholder="Activation/Reference Code" value="<?php echo $token_product->activation_code; ?>" />
                <?php } ?>
            </td>
            <td>
                <input type="hidden" id="imei" name="imei[]" class="imei" value="<?php echo NULL ?>" />
                <?php if($sale_type == 0){ ?>
                <input type="hidden" id="insurance_imei" name="insurance_imei[]" class="insurance_imei" value="<?php echo $token_product->insurance_imei_no; ?>" />
                <?php }else{ ?>
                <input type="text" id="insurance_imei" name="insurance_imei[]" class="insurance_imei form-control input-sm" required="" placeholder="Insurance IMEI/SRNO" pattern="[a-zA-Z0-9\-]+" value="<?php echo $token_product->insurance_imei_no; ?>" />
                <?php } ?>
            </td>
            <td><?php echo $token_product->qty; ?></td>
            <td><?php echo $token_product->mrp; ?></td>
            <td><?php echo $token_product->mop; ?></td>
            <td>
                <input type="hidden" id="read_skuprice<?php echo $skuvariant ?>" value="<?php echo $token_product->mop ?>" />
                <input type="hidden" id="readprice<?php echo $token_product->id_stock ?>" value="<?php echo $token_product->mop ?>" />
                <input type="hidden" id="landing" name="landing[]" class="landing" value="<?php echo $token_product->landing ?>" />
                <input type="hidden" id="mop" name="mop[]" class="mop" value="<?php echo $token_product->mop ?>" />
                <input type="hidden" id="nlc_price" name="nlc_price[]" class="nlc_price" value="<?php echo $token_product->nlc_price ?>" />
                <input type="hidden" id="mrp" name="mrp[]" class="mrp" value="<?php echo $token_product->mrp ?>" />
                <input type="hidden" id="ageing" name="ageing[]" class="ageing" value="<?php echo $ageing ?>" />
                <input type="hidden" id="focus_st" name="focus_st[]" class="focus_st" value="<?php echo $focus_status ?>" />
                <input type="hidden" id="focus_incentive" name="focus_incentive[]" class="focus_incentive" value="<?php echo $focus_amount ?>" />
                <input type="hidden" id="salesman_price" name="salesman_price[]" class="salesman_price" value="<?php echo $token_product->salesman_price ?>" />
                <input type="hidden" id="sale_type" name="sale_type[]" class="sale_type" value="<?php echo $token_product->sale_type ?>" />
                <!--<input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $token_product->mop ?>" min="<?php echo $token_product->landing ?>" step="0.001" style="width: 90px" max="<?php echo $token_product->mrp ?>" />-->
                <?php if($sale_type == 2){ ?>
                <input type="hidden" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $token_product->mop ?>" step="0.001" style="width: 90px" />
                1
                <?php }else{ ?>
                <input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $token_product->price ?>" step="0.001" style="width: 90px" />
                <?php } ?>
            </td>
            <td>
                <?php if($sale_type == 2){ ?>
                <input type="number" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" value="<?php echo $token_product->qty ?>" min="1" style="width: 90px"/>
                <?php }else{ ?>
                <input type="number" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" value="<?php echo $token_product->qty ?>" min="1" style="width: 90px" max="<?php echo $token_product->qty; ?>"/>
                <?php } ?>
            </td>
            <td>
                <input type="hidden" id="basic" name="basic[]" class="basic" placeholder="Basic" readonly="" value="<?php echo $token_product->basic ?>"/>
                <span class="spbasic" id="spbasic" name="spbasic[]"><?php echo $token_product->basic ?></span>
                <input type="hidden" class="price_diff" id="price_diff" value="<?php echo $amount_diff ?>" />
            </td>
            <td><input type="number" id="discount_amt" name="discount_amt[]" class="form-control discount_amt input-sm" placeholder="Amount" value="<?php echo $token_product->discount_amt ?>" required="" min="0" step="0.001" style="width: 90px" <?php if($token_product->is_mop == 0){ ?> readonly="" <?php } ?> /></td>
            <td>
                <input type="hidden" id="isgst" name="isgst[]" class="isgst"  />
                <input type="hidden" id="idvendor" name="idvendor[]" class="idvendor" value="<?php echo $token_product->idvendor ?>"/>
                <input type="hidden" id="cgst" name="cgst[]" class="form-control input-sm cgst" value="<?php echo $token_product->cgst_per; ?>" readonly=""/>
                <input type="hidden" id="sgst" name="sgst[]" class="form-control input-sm sgst" value="<?php echo $token_product->sgst_per; ?>" readonly=""/>
                <input type="hidden" id="igst" name="igst[]" class="form-control input-sm igst" value="<?php echo $token_product->igst_per; ?>" readonly=""/>
                <?php echo $token_product->igst_per ?>%
            </td>
            <td>
                <input type="hidden" id="total_amt" name="total_amt[]" class="form-control total_amt input-sm" placeholder="Amount" value="<?php echo $token_product->total_amount ?>" required="" />
                <span id="sptotal_amt" class="sptotal_amt" name="sptotal_amt[]"><?php echo $token_product->total_amount ?></span>
            </td>
            <td>
                <a class="btn btn-sm gradient1 btn-warning remove" name="remove" id="remove"><i class="fa fa-trash-o fa-lg"></i></a>
                <input type="hidden" name="rowid[]" id="rowid" class="rowid" value="<?php echo $token_product->id_stock ?>" />
            </td>
        </tr>
    <?php  }else{ // IMEI/ SRNO
        $ageing_data = $this->Stock_model->get_ageing_stock_data($token_product->idproductcategory, $token_product->idbrand, $token_product->idmodel, $token_product->idvariant, $token_product->idbranch);
        if($ageing_data){
            $ageing = 1;
        }else{
            $ageing =0;
        }

        $focus_data = $this->Stock_model->get_focus_stock_data($token_product->idproductcategory, $token_product->idbrand, $token_product->idmodel, $token_product->idvariant, $token_product->idbranch);
        if($focus_data){
            $focus_status = 1;
            $focus_amount = $focus_data->incentive_amount;
        }else{
            $focus_status = 0;
            $focus_amount = 0;
        }
        $amount_diff = $token_product->mop - $token_product->landing; ?>
        <tr id="m<?php echo $token_product->id_stock ?>" class="skuimei_row">
            <td>
                <?php echo $token_product->product_name; ?>
                <input type="hidden" id="idtype" class="form-control idtype" name="idtype[]" value="<?php echo $token_product->idproductcategory ?>" />
                <input type="hidden" id="idcategory" class="form-control idcategory" name="idcategory[]" value="<?php echo $token_product->idcategory ?>" />
                <input type="hidden" id="idbrand" class="form-control" name="idbrand[]" value="<?php echo $token_product->idbrand ?>" />
                <input type="hidden" id="idvariant" class="form-control idvariant" name="idvariant[]" value="<?php echo $token_product->idvariant ?>" />
                <input type="hidden" id="idmodel" class="form-control idmodel" name="idmodel[]" value="<?php echo $token_product->idmodel ?>" />
                <input type="hidden" id="idgodown" class="form-control idgodown" name="idgodown[]" value="<?php echo $token_product->idgodown ?>" />
                <input type="hidden" id="skutype" class="form-control skutype" name="skutype[]" value="<?php echo $token_product->idskutype ?>" />
                <input type="hidden" id="product_name" class="form-control product_name" name="product_name[]" value="<?php echo $token_product->product_name; ?>" />
                <input type="hidden" id="is_mop" class="form-control is_mop" name="is_mop[]" value="<?php echo $token_product->is_mop; ?>" />
                <input type="hidden" id="hsn" class="form-control hsn" name="hsn[]" value="<?php echo $token_product->hsn; ?>" />
                <input type="hidden" id="is_gst" class="form-control is_gst" name="is_gst[]" value="<?php echo $token_product->is_gst; ?>" />
                <small class="pull-right" style="color:#1b6caa;font-family: Kurale;">New Godown</small>
                <input type="hidden" id="dcprint" class="dcprint" name="dcprint[]" value="<?php echo $token_product->dcprint; ?>" />
            </td>
            <td>
                <input type="hidden" id="imei" name="imei[]" class="imei" value="<?php echo $imei ?>" />
                <input type="hidden" id="insurance_imei" name="insurance_imei[]" class="insurance_imei" required="" />
                <input type="hidden" id="activation_code" name="activation_code[]" class="activation_code" />
                <?php echo $imei; ?>
            </td>
            <td>1</td>
            <td><?php echo $token_product->mrp; ?></td>
            <td><?php echo $token_product->mop; ?></td>
            <td>
                <input type="hidden" id="read_skuprice<?php echo $skuvariant ?>" value="<?php echo $token_product->mop ?>" />
                <input type="hidden" id="readprice<?php echo $token_product->id_stock ?>" value="<?php echo $token_product->mop ?>" />
                <input type="hidden" id="landing" name="landing[]" class="landing" value="<?php echo $token_product->landing ?>" />
                <input type="hidden" id="mop" name="mop[]" class="mop" value="<?php echo $token_product->mop ?>" />
                 <input type="hidden" id="nlc_price" name="nlc_price[]" class="nlc_price" value="<?php echo $token_product->nlc_price ?>" />
                <input type="hidden" id="mrp" name="mrp[]" class="mrp" value="<?php echo $token_product->mrp ?>" />
                <input type="hidden" id="ageing" name="ageing[]" class="ageing" value="<?php echo $ageing ?>" />
                <input type="hidden" id="focus_st" name="focus_st[]" class="focus_st" value="<?php echo $focus_status ?>" />
                <input type="hidden" id="focus_incentive" name="focus_incentive[]" class="focus_incentive" value="<?php echo $focus_amount ?>" />
                <input type="hidden" id="salesman_price" name="salesman_price[]" class="salesman_price" value="<?php echo $token_product->salesman_price ?>" />
                <!--<input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $token_product->mop ?>" min="<?php echo $token_product->landing ?>" step="0.001" style="width: 90px" max="<?php echo $token_product->mrp ?>" />-->
                <input type="number" id="price" class="form-control input-sm price" name="price[]" value="<?php echo $token_product->mop ?>" step="0.001" style="width: 90px" />
                <input type="hidden" id="sale_type" name="sale_type[]" class="sale_type" value="<?php echo $token_product->sale_type ?>" />
            </td>
            <td>
                <input type="hidden" id="qty" name="qty[]" class="form-control input-sm qty" placeholder="Qty" required="" value="1" style="width: 70px"/>
                <span id="spqty" class="spqty">1</span>
            </td>
            <td>
                <input type="hidden" id="basic" name="basic[]" class="basic" placeholder="Basic" readonly="" value="<?php echo $token_product->basic ?>"/>
                <span class="spbasic" id="spbasic" name="spbasic[]"><?php echo $token_product->basic ?></span>
                <input type="hidden" class="price_diff" id="price_diff" value="<?php echo $amount_diff ?>" />
            </td>
            <td><input type="number" id="discount_amt" name="discount_amt[]" class="form-control discount_amt input-sm" placeholder="Amount" value="<?php echo $token_product->discount_amt ?>" required="" min="0" max="<?php echo $amount_diff ?>" step="0.001" style="width: 90px" <?php if($token_product->is_mop == 0){ ?> readonly="" <?php } ?>/></td>
            <td>
                <input type="hidden" id="isgst" name="isgst[]" class="isgst"/>
                <input type="hidden" id="idvendor" name="idvendor[]" class="idvendor" value="<?php echo $token_product->idvendor ?>"/>
                <input type="hidden" id="cgst" name="cgst[]" class="form-control input-sm cgst" value="<?php echo $token_product->cgst_per; ?>" readonly=""/>
                <input type="hidden" id="sgst" name="sgst[]" class="form-control input-sm sgst" value="<?php echo $token_product->sgst_per; ?>" readonly=""/>
                <input type="hidden" id="igst" name="igst[]" class="form-control input-sm igst" value="<?php echo $token_product->igst_per; ?>" readonly=""/>
                <?php echo $token_product->igst_per ?>%
            </td>
            <td>
                <input type="hidden" id="total_amt" name="total_amt[]" class="form-control total_amt input-sm" placeholder="Amount" value="<?php echo $token_product->total_amount ?>" required="" />
                <span id="sptotal_amt" class="sptotal_amt" name="sptotal_amt[]"><?php echo $token_product->total_amount ?></span>
            </td>
            <td>
                <a class="btn btn-sm gradient1 btn-warning remove" name="remove" id="remove"><i class="fa fa-trash-o fa-lg"></i></a>
                <input type="hidden" name="rowid[]" id="rowid" class="rowid" value="<?php echo $token_product->id_stock ?>" />
            </td>
        </tr>
    <?php }
    $stgross_total += $token_product->basic;
    $stfinal_discount += $token_product->discount_amt;
    $stfinal_total += $token_product->total_amount;
} ?>
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
            <input type="hidden" name="gross_total" id="gross_total" value="<?php echo $stgross_total; ?>" />
            <span id="spgross_total"><?php echo $stgross_total; ?></span>
        </td>
        <td>
            <input type="hidden" name="final_discount" id="final_discount" class="form-control input-sm final_discount" placeholder="Total Discount" value="<?php echo $stfinal_discount; ?>" readonly=""/>
            <span id="spfinal_discount"><?php echo $stfinal_discount; ?></span>
        </td>
        <td></td>
        <td colspan="2">
            <input type="hidden" name="final_total" id="final_total" value="<?php echo $stfinal_total; ?>"/>
            <span id="spfinal_total"><?php echo $stfinal_total; ?></span>
        </td>
    </tr>
</thead>