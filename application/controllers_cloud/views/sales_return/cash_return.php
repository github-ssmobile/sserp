<?php include __DIR__ . '../../header.php'; ?>
<!--// link_tag('assets/css/bootstrap-select.min.css')-->
<script>
$(document).ready(function(){
    $(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
    var products = [], checked;
    $(document).on('keydown', 'input[id=invno]', function(e) {
        var invno = $(this).val();
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && $(this).val() !== '') {
            var branch = $('#branch').val();
            var level = $('#level').val();
            $.ajax({
                url:"<?php echo base_url() ?>Sales_return/search_sales_cash_return_invoice_byinvno",
                method:"POST",
                data:{invno : invno, branch: branch, level: level},
                success:function(data)
                {
                    products = [];
                    checked = 0;
                    $("#invoice_data").html(data);
                }
            });
        }
    });
    
    var sales_return_type, return_cash=0, temp_cash=0;
    $(document).on('change', 'input[id=chk_return]', function() {
        var ce = $(this);
        var saleproduct_id = $(ce).closest('td').parent('tr').find(".saleproduct_id").val();
        var idmodel = $(ce).closest('td').parent('tr').find(".idmodel").val();
        var skutype = $(ce).closest('td').parent('tr').find(".skutype").val();
        var qty = +$(ce).closest('td').parent('tr').find(".selected_qty").val();
        var price = +$(ce).closest('td').parent('tr').find(".price").val();
        sales_return_type = $("#sales_return_type").val();
        if(qty == ''){ qty = 0; }
        if($(this).prop("checked") == true){
            checked = checked + qty;
            return_cash += price;
            if(products.includes(saleproduct_id) === false){
                products.push(saleproduct_id);
            }
            if(skutype == '4'){
                var selected_qty = $(ce).closest('td').parent('tr').find(".selected_qty");
                selected_qty.removeAttr('readonly');
                selected_qty.prop('min', 1);
                selected_qty.val(1);
            }
            else{
                temp_cash += price;
            }
            $("#sales_return_product_id").val(saleproduct_id);
            $("#sales_return_model_id").val(idmodel);
            $("#txt_selected_sale_products").val(products);
        }else if($(this).prop("checked") == false){
            checked = checked - qty;
            return_cash -= price;
            if(skutype =='4'){
                var selected_qty = $(ce).closest('td').parent('tr').find(".selected_qty");
                selected_qty.attr('readonly', true);
                selected_qty.val(0);
            }
            else{
                temp_cash -= price;
            }
            products = jQuery.grep(products, function(value) { return value !== saleproduct_id; });
            $("#txt_selected_sale_products").val(products);
        }
        $('#sales_return_cash_lb').html(return_cash+' <i class="fa fa-rupee"></i>');
        $('#temp_cash').val(temp_cash);
        $('#sales_return_cash').val(return_cash);
    });
    $(document).on('change', 'input[id=selected_qty]', function() {
        var cash = 0;
        var qty = $(this).val();
        var price = +$(this).closest('td').parent('tr').find(".price").val();
        var temp_cash = +$('#temp_cash').val();
        var row_cash_amount = price * qty;
        cash = temp_cash + row_cash_amount;
        $(this).closest('td').parent('tr').find(".row_cash_amount").val(row_cash_amount);
        $('#sales_return_cash_lb').html(cash+' <i class="fa fa-rupee"></i>');
        $('#sales_return_cash').val(cash);
    });
    $(document).on("click", "#btn_product_return", function (event) {
        var sales_return_cash = +$('#sales_return_cash').val();
        if(sales_return_cash == 0){
            swal("Select any product!", "😠 Select product for return", "warning");
            return false;
        }
        var daybook_sum_cash = +$('#daybook_sum_cash').val();
        if(sales_return_cash > daybook_sum_cash){
            swal("Alert you do not have enough cash for return!", "😠 Please check your daybook cash", "warning");
            return false;
        }
    });
});
</script>
<center><h3 style="margin: 0"><span class="mdi mdi-keyboard-return fa-lg"></span> Sales Return - Cash</h3></center><div class="clearfix"></div><hr>
<?php 
//    $var_closer = 1;
//    if(count($sale_last_entry_byidbranch)){
//        if($sale_last_entry_byidbranch[0]->sum_cash == 0){
//            $var_closer = 1;
//        }else{
//            if(count($cash_closure_last_entry) == 0){
//                $var_closer = 0;
//            }else{
//                if($last_date_entry[0]->date == $cash_closure_last_entry[0]->date){
//                    $var_closer = 1;
//                }elseif($last_date_entry[0]->date > $cash_closure_last_entry[0]->date){
//                    $var_closer = 0;
//                }
//            }
//        }
//    }
    if($var_closer){ ?>
        <form>
            <div class="col-md-1 col-sm-2">Invoice No</div>
            <div class="col-md-4 col-sm-7">
                <input type="text" class="form-control" id="invno" name="invno" placeholder="Search Invoice Number"/>
            </div><div class="clearfix"></div>
            <input type="hidden" name="branch" id="branch" value="<?php echo $_SESSION['idbranch'] ?>"/>
            <input type="hidden" id="level" value="<?php echo $_SESSION['level'] ?>"/>
            <input type="hidden" class="form-control input-sm" name="sales_return_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
            <div id="invoice_data" style="font-size: 14px; min-height: 550px; overflow: auto"></div>
        </form>
    <?php }else{ 
        echo '<center><h3>You did not submitted yesterdays cash closure</h3>'.
            '<img src="'.base_url().'assets/images/highAlertIcon.gif" />'
            .'<h3>You must have to submit cash closure first.</h3>'
            .'</center>';
    } ?>
<?php include __DIR__ . '../../footer.php'; ?>