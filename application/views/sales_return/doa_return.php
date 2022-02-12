<?php include __DIR__ . '../../header.php'; ?>
<!--// link_tag('assets/css/bootstrap-select.min.css')-->
<script>
$(document).ready(function(){
    $(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
//    var checked = 0;
    var verify = 0;
    $(document).on('keydown', 'input[id=invno]', function(e) {
        verify = 0
        var invno = $(this).val();
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && $(this).val() !== '') {
            var branch = $('#branch').val();
            var level = $('#level').val();
            $.ajax({
                url:"<?php echo base_url() ?>Sales_return/search_sales_doa_return_invoice_byinvno",
                method:"POST",
                data:{invno : invno, branch: branch, level: level},
                success:function(data)
                {
                    $("#invoice_data").html(data);
                }
            });
        }
    });
    $(document).on('change', 'input[id=chk_return]', function() {
        var ce = $(this);
        var idsaleproduct = ce.val();
        var idmodel = $(ce).closest('td').parent('tr').find("#idmodel").val();
        var idvariant = $(ce).closest('td').parent('tr').find("#idvariant").val();
        var saleproduct_name = $(ce).closest('td').parent('tr').find("#saleproduct_name").val();
        var id_saledata = $(ce).closest('td').parent('tr').find("#id_saledata").val();
        var idtype = $(ce).closest('td').parent('tr').find("#idtype").val();
        var idcategory = $(ce).closest('td').parent('tr').find("#idcategory").val();
        var idbrand = $(ce).closest('td').parent('tr').find("#idbrand").val();
        var is_gst = $(ce).closest('td').parent('tr').find("#is_gst").val();
        var idvendor = $(ce).closest('td').parent('tr').find("#idvendor").val();
        var idgodown = $(ce).closest('td').parent('tr').find("#idgodown").val();
        var skutype = $(ce).closest('td').parent('tr').find("#skutype").val();
        var imei_no = $(ce).closest('td').parent('tr').find("#imei_no").val();
        var hsn = $(ce).closest('td').parent('tr').find("#hsn").val();
        
//        var price = $(ce).closest('td').parent('tr').find("#price").val();
//        var basic = $(ce).closest('td').parent('tr').find("#basic").val();
//        var discount_amt = $(ce).closest('td').parent('tr').find("#discount_amt").val();
//        var taxable = $(ce).closest('td').parent('tr').find("#taxable").val();
//        var cgst_amt = $(ce).closest('td').parent('tr').find("#cgst_amt").val();
//        var cgst = $(ce).closest('td').parent('tr').find("#cgst").val();
//        var sgst_amt = $(ce).closest('td').parent('tr').find("#sgst_amt").val();
//        var sgst = $(ce).closest('td').parent('tr').find("#sgst").val();
//        var igst_amt = $(ce).closest('td').parent('tr').find("#igst_amt").val();
//        var igst = $(ce).closest('td').parent('tr').find("#igst").val();
//        var tax = $(ce).closest('td').parent('tr').find("#tax").val();
//        var total_amt = $(ce).closest('td').parent('tr').find("#total_amt").val();
        
        $("#idsaleproduct").val(idsaleproduct);
        $("#dsaleproduct_name").val(saleproduct_name);
        $("#didmodel").val(idmodel);
        $("#dididvariant").val(idvariant);
        $("#didsaleproduct").val();
        $("#did_saledata").val(id_saledata);
        $("#dproduct_name").val();
        $("#didtype").val(idtype);
        $("#didcategory").val(idcategory);
        $("#didbrand").val(idbrand);
        $("#dis_gst").val(is_gst);
        $("#didvendor").val(idvendor);
        $("#didgodown").val(idgodown);
        $("#dskutype").val(skutype);
        $("#dimei_no").val(imei_no);
        $("#dhsn").val(hsn);
        
        var str = '<div class="col-md-3">DOA Product</div>\n\
                <div class="col-md-9" style="color: #31944c"><b>'+saleproduct_name+'</b></div><div class="clearfix"></div><br>\n\
                <div class="col-md-3">DOA IMEI</div>\n\
                <div class="col-md-9" style="color: #31944c"><b>'+imei_no+'</b></div><div class="clearfix"></div><br>';
        $("#doa_product_block").html(str);
        
        verify = 0;
        $("#verify_btn_block").css("display", "none");
        $("#btn_imei_verify").css("display", "block");
        
//        $("#dprice").val(price);
//        $("#dbasic").val(basic);
//        $("#ddiscount_amt").val(discount_amt);
//        $("#dtaxable").val(taxable);
//        $("#dcgst").val(cgst);
//        $("#dcgst_amt").val(cgst_amt);
//        $("#dsgst").val(sgst);
//        $("#dsgst_amt").val(sgst_amt);
//        $("#digst").val(igst);
//        $("#digst_amt").val(igst_amt);
//        $("#dtax").val(tax);
//        $("#dtotal_amt").val(total_amt);
    });
    
    $(document).on("keyup", "#new_imei_no", function (event) {
        if($('#idsaleproduct').val() == ''){
            swal("Select product for DOA!", "Must select product for return", "warning");
            $(this).val('');
            return false;
        }
        verify = 0;
        $("#verify_btn_block").css("display", "none");
        $("#btn_imei_verify").css("display", "block");
    });
    $(document).on("click", "#btn_imei_verify", function (event) {
        var imei = $('#new_imei_no').val();
        var idbranch = $('#branch').val();
        var idvariant = $('#dididvariant').val();
        verify = 0;
        // Verify IMEI
        if(idvariant == ''){
            swal("Select product for DOA!", "Must select product for return", "warning");
            return false;
        }
        if(imei == ''){
            swal("Scan New IMEI/SRNO!", "New IMEI/SRNO Required", "warning");
            return false;
        }
        $.ajax({
            url: "<?php echo base_url() ?>Sales_return/ajax_verify_imei",
            method: "POST",
            dataType: 'json',
            data:{imei : imei,idbranch: idbranch,idvariant: idvariant},
            success: function (data)
            {
                if(data.result == 'Failed'){
                    swal("Product stock not found in branch!", data.msg, "warning");
                    $("#idstock").val('');
                    return false;
                }else if(data.result == 'Godown'){ // Other than New Godown not accepted
                    swal("Product not found in New Godown!", data.msg, "warning");
                    $("#idstock").val('');
                    return false;
                }else if(data.result == 'Success'){
                    verify = 1;
                    swal('Product added: '+imei, 'Name: '+data.msg, 'success');
                    $("#idstock").val(data.idstock);
                    $("#new_is_gst").val(data.is_gst);
                    $("#new_idvendor").val(data.idvendor);
                    $("#verify_btn_block").css("display", "block");
                    $('#btn_imei_verify').hide();
                }
            }
        });
    });
    $(document).on("click", "#btn_doa_return", function (event) {
        if(verify == 0){
            $('#btn_imei_verify').trigger('click');
            swal("First Verify IMEI/SRNO!", 'Click on verify IMEI for validate product check stock', "warning");
            return false;
        }else{
            var dsaleproduct_name = $('#dsaleproduct_name').val();
            var new_imei_no = $('#new_imei_no').val();
            if (!confirm('Do you want to submit? DOA Product: '+dsaleproduct_name+' IMEI/SRNO: '+new_imei_no)) {
                return false;
            }else{
                swal("Sales return done!", 'Entry submitetd successfully', "success");
            }
        }
    });
});
</script>
<center><h3 style="margin: 0"><span class="mdi mdi-keyboard-return fa-lg"></span> Sales Return - DOA Return</h3></center><div class="clearfix"></div><hr>
<form class="doa_form_submit">
    <div class="col-md-1 col-sm-2">Invoice No</div>
    <div class="col-md-4 col-sm-7">
        <input type="text" class="form-control" id="invno" name="invno" placeholder="Search Invoice Number"/>
    </div><div class="clearfix"></div>
    <input type="hidden" name="branch" id="branch" value="<?php echo $_SESSION['idbranch'] ?>"/>
    <input type="hidden" id="level" value="<?php echo $_SESSION['level'] ?>"/>
    <input type="hidden" class="form-control input-sm" name="sales_return_by" value="<?php echo $this->session->userdata('id_users') ?>"/>
    <div id="invoice_data" style="font-size: 14px; min-height: 550px; overflow: auto"></div>
</form>
<?php include __DIR__ . '../../footer.php'; ?>