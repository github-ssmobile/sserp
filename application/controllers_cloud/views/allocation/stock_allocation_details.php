<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>ERP</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <link rel="shortcut icon" href="<?php echo base_url()?>favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?php echo base_url()?>favicon.ico" type="image/x-icon">
    <?php date_default_timezone_set('Asia/Kolkata'); ?>
    <?= link_tag("assets/css/bootstrap.css") ?>
    <?= link_tag("assets/css/signinstyle.css") ?>
    <?= link_tag("assets/css/font-awesome.min.css") ?>
    <?= link_tag("assets/material_font/css/materialdesignicons.css") ?>
    <?= link_tag("assets/css/kurale-font.css") ?>
    <?= link_tag("assets/css/style.css") ?>
    <?=  link_tag("assets/waves/btnwave.css") ?>
    <?=  link_tag("assets/creative_font/css/pe-icon-7-stroke.css") ?>
    <?=  link_tag("assets/creative_font/css/helper.css") ?>
    <?= link_tag("assets/css/tipped.css") ?>    
    <?= link_tag("assets/css/k2d.css") ?>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans&display=swap" rel="stylesheet"> 
    <?= link_tag("assets/css/sidebar.css") ?>
    <?= link_tag('assets/css/gsdk-bootstrap-wizard.css')?>
    <?= link_tag("assets/css/new-datetimepicker.css") ?>   
    <?= link_tag("assets/css/choosen.css") ?>
    <script src="<?php echo site_url() ?>assets/js/jquery-3.1.1.min.js"></script>
    <script src="<?php echo site_url() ?>assets/js/jquery.bootstrap.js" type="text/javascript"></script>    
    <style>.btn{ margin: 0; }</style>
</head>
<body style="
      font-family: 'Nunito Sans', sans-serif;
      /*font-family: 'Roboto', sans-serif;*/
    /*font-size: 17px;*/
    font-weight: 400;">
    <!--<nav class="navbar hovereffect1 navbar-default navbar-expand-l" role="navigation" style="margin: 0; padding-bottom: 5px; height: auto; border: none; border-radius: 0;background-image: url(<?php echo base_url()?>assets/images/header.jpg)">-->
    <div style="position: absolute; z-index: 999; right: 0"><div id="google_translate_element"></div></div>
<?php $data=$stock_allocation[0];?>
<div class="container-fluid">
    <div class="row">
        <div class="wrapper" style="display:block;">    
            <div class="tab-content" id="content">
                <?php // die('<pre>'.print_r($menus,1).'</pre>'); 
                if( $save = $this->session->flashdata('save_data')): ?>
                    <div class="alert alert-dismissible alert-info" id="alert-dismiss">
                        <?= $save ?>
                    </div>
                <?php endif; ?>
                <?php if( $save = $this->session->flashdata('reject_data')): ?>
                    <div class="alert alert-dismissible alert-danger" id="alert-dismiss">
                        <?= $save ?>
                    </div>
                <?php endif; ?>

        <style>
            @page {
              size: A4;
              margin: 40px;
              padding: 30px;
            }
            @media print {
              html, body {
                width: 210mm;
                height: 297mm;
              }
            }
        </style>
             <div class="printable">
                 <div class="container-fluid" style="font-size: 16px; padding: 0px 15px; border: 1px solid #999999; border-radius: 0; background-color: #fff; line-height: 23px">
            <div class="">
                <div class="row" style="padding: 0; border-bottom: 1px solid #999999">
                            <div class="col-md-4 col-xs-4" style="padding: 0;">
                                <img class="hovereffect" height="85" src="<?php echo base_url() ?>assets/images/logo.jpg" alt="SS Mobile"/>
                            </div>
                            <div class="col-md-8 col-xs-8 text-center" style="padding: 0; font-size: 14px; padding-top: 15px; padding-left: 10px;">
                                <h3 style="color: #000; font-family: K2D; font-size: 25px; margin: 0">SS COMMUNICATION & SERVICES PVT LTD</h3>
                                RATIKAMAL COMPLEX, 399 E WARD, SHAHUPURI, KOLHAPUR - 416001
                            </div>
                        </div>
                        <div class="row justify-content-end" style="padding: 3px 10px;">
                             <?php if($data->gst_type==0){ ?>
                                <center><h4  style="color: #000;font-family: K2D">DELIVERY CHALLAN</h4></center>
                                <div class="col-md-6 col-xs-6">
                                Mandate No.: &nbsp;<b><?php echo $data->id_stock_allocation ?></b><br>
                                Dc No.: &nbsp;<b><?php echo $data->id_outward ?></b>                        
                            </div>
                            <?php }else{ ?>
                                <center><h4  style="color: #000;font-family: K2D">INTER STATE BRANCH SALES INVOICE</h4></center>
                                <div class="col-md-6 col-xs-6">
                                Reference No.: &nbsp;<b><?php echo $data->sales_invoice ?></b><br>
                                Dc No.: &nbsp;<b><?php echo $data->id_outward ?></b>  
                                Mandate No.: &nbsp;<b><?php echo $data->idstock_allocation ?></b>  
                            </div>
                            <?php } ?>
                            <div class="col-md-6 col-xs-6" style="text-align: end;padding-right: 15px;">
                                Allocation Date: &nbsp;<?php echo $data->all_date; //date('d-M-Y', strtotime($data->confirm_time)) ?><br>
                                Shipment Date: &nbsp;<?php echo $data->dispatch_date; //date('d-M-Y', strtotime($data->dispatch_date)) ?>
                            </div>
                        </div>

                        <div class="row" style="border: 1px solid #999999; font-size: 15px;">
                            <br>
                            <div class="col-md-7 col-xs-7">
                                <b>FROM, </b><br>
                                <b>Branch: &nbsp; <?php echo $branch_data[0]->branch_name ?></b><br>
                                <b>Address: </b> <?php echo $branch_data[0]->branch_address; ?><br>
                                <b>GST No:</b> <?php echo $branch_data[0]->company_gstin; ?><br>
                                <b>Contact:</b> <?php echo $branch_data[0]->branch_contact; ?><br>
                                <!--<b>Sales Promoter:</b>--> 
                                <?php // echo $sale->full_name; ?>
                            </div>
                            <div class="col-md-5 col-xs-5">
                                <b> To,</b><br>
                                <b>Branch: &nbsp; <?php echo $branch_data[1]->branch_name ?></b><br>
                                <b>Address: </b> <?php echo $branch_data[1]->branch_address; ?><br>
                                <b>GST No:</b> <?php echo $branch_data[1]->company_gstin; ?><br>
                                <b>Contact:</b> <?php echo $branch_data[1]->branch_contact; ?><br>
                                 <br>
                            </div>                   
                        </div>        

            <div class="" style="padding: 0; margin: 0">
                <br>
                <table id="model_data" class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 14px; margin: 0">
                    <?php if($data->a_status < 3){ ?>
                    <thead class="bg-info">
                        <th>SrNo</th>
                        <th class="col-md-7">Product</th>
                        <th class="col-md-1">Godown</th>
                        <th>Qty</th>
                    </thead>
                    <tbody>
                        <?php 
                        $i=1;$t_qty=0;
                        foreach ($stock_allocation as $product) { 
                             $t_qty =$t_qty + $product->qty; ?>
                        <tr>
                            <td><?php echo $product->id_stock_allocation_data ?></td>
                            <td><?php echo $product->full_name; ?></td>
                            <td><?php echo $product->godown_name; ?></td>
                            <td><?php echo $product->qty ?></td>
                        </tr>
                        <?php $i++; } ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td>Total</td>
                            <td><?php echo $t_qty; ?></td>

                        </tr>
                    </tbody>
                    <?php }elseif($data->a_status >= 3){
                                if($data->gst_type==0){ ?>                    
                                    <thead class="bg-info">
                                        <th style="width: 2%;">Sr</th>
                                        <th style="width: 25%;" class="col-md-5">Product</th>
                                        <th style="width: 10%;" class="col-md-1">Godown</th>
                                        <th style="width: 5%;">Qty</th>
                                        <th style="width: 58%;" class="col-md-5">IMEI/SRNO</th>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1; $t_qty=0;
                                        foreach ($stock_allocation as $product) { 
                                             $array = explode(',', $product->imei);      
                                             $t_qty =$t_qty + $product->qty; ?>                                    
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo $product->full_name; ?></td>
                                            <td><?php echo $product->godown_name; ?></td>
                                            <td><?php echo $product->qty ?></td>
                                              <td><?php $j=0;$im=""; foreach($array as $imei){
                                                    if($j==2){ echo $im.$imei."<br>"; $im=""; $j=-1;}else{ $im.=$imei.", "; }
                                                    $j++;                                            
                                                }
                                                echo $im;                                                
                                                //echo $product->imei ?></td>
                                        </tr>
                                        <?php } ?>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td>Total</td>
                                            <td><?php echo $t_qty; ?></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                    <?php }else{ ?>

                    <thead class="bg-info">
                                <th>Sr</th>
                                <th class="col-md-6">Product</th>                        
                                 <th><center>Rate</center></th>
                                 <th>Qty</th>
                                <th><center>Taxable</center></th>
                                <th><center>IGST %</center></th>
                                <th><center>IGST Amount</center></th>
                                <th><center>Amount</center></th>
                                <th class="col-md-5">IMEI/SRNO</th>
                            </thead>
                            <tbody>
                                <?php $i = 1;
                               $t_rate=0; $tqty=0;$tt_amt=0;$totala=0;$t_aamt=0;
                                foreach ($stock_allocation as $product) {
                                    $array = explode(',', $product->imei);      
                                    $price=0;
                                    $amt=$product->price;
                                    $tax_rate=($product->cgst_per*2);
                                    if($product->idgodown == 2){
                                        $cal = 50/100;  //// landing minus 50% for demo stock -  inter state transfer
                                    }else{
                                        $cal = 2 /100;   //// landing minus 2% for inter state transfer
                                    }
                                    $amount = $amt - ($cal*$amt);
                                    //$price = $amt - $taxble;

                                    $rate= round(($amount*100)/(100+$tax_rate),2);
                                     $t_rate=$t_rate+$rate;
                                    $gst_amt = (($rate*$tax_rate) / 100);
                                    $basic_rate = ($amount) - $gst_amt;

                                    $taxable =  round((($rate)*($product->qty)),2);
                                    $tqty=$tqty+$product->qty;
                                    $tt_amt=$tt_amt+$taxable;
                                    $t_aamt=$t_aamt+$product->price;
                                    $gst_amt = round((($taxable*$tax_rate) / 100),2);
                                    $t_amt=round($taxable+$gst_amt,2);
                                    $totala=$totala=+$t_amt;

                                    ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $product->full_name; ?></td>                                
                                        <td><?php echo $rate ?></td>
                                        <td><?php echo $product->qty ?></td>
                                        <td><?php echo $taxable ?></td>
                                        <td><?php echo $tax_rate ?></td>
                                        <td><?php echo $gst_amt ?></td>
                                        <td><?php echo $t_amt ?></td>
                                        <td><?php $j=0;$im=""; foreach($array as $imei){
                                                    if($j==2){ echo $im.$imei."<br>"; $im=""; $j=-1;}else{ $im.=$imei.", "; }
                                                    $j++;                                            
                                                }
                                                echo $im;                                                
                                                //echo $product->imei ?></td>
                                    </tr>
                    <?php } ?>
                                    <thead class="bg-info">
                                    <th></th>
                                    <th>Total</th>
                                    <th><?php echo $t_rate; ?></th>
                                    <th><?php echo $tqty; ?></th>
                                    <th><?php echo $tt_amt; ?></th>                            
                                    <th colspan="2"></th>                        
                                    <th><center><?php echo $totala; ?></center></th>

                            </thead>

                            </tbody>

                    <?php } } ?>
                </table>
            </div>
            </div>
            <?php if($data->a_status >= 4){       
                ?>        
                <div class="thumbnail">
                    <center><h4 style="margin-bottom: 0"><i class="mdi mdi-truck"></i> Shipment Details</h4></center>
                    <div class="clearfix"></div><hr>
                    <div class="col-md-2 text-muted">Dispatch Date</div>
                    <div class="col-md-2"><?php echo $data->dispatch_date ?></div>
                    <div class="col-md-2 text-muted">Dispatch Type</div>
                    <div class="col-md-1"><?php echo $data->dispatch_type ?></div>
                    <div class="col-md-2 text-muted">Courier/ Transport Name</div>
                    <div class="col-md-3"><?php echo $data->courier_name ?></div>
                    <div class="clearfix"></div><br>
                    <div class="col-md-2 text-muted">POP/LR Number</div>
                    <div class="col-md-2"><?php echo $data->po_lr_no ?></div>
                    <div class="col-md-2 text-muted">No of Boxes</div>
                    <div class="col-md-1"><?php echo $data->no_of_boxes ?></div>
                    <div class="col-md-2 text-muted">Remark</div>
                    <div class="col-md-3"><?php echo $data->shipment_remark ?></div><div class="clearfix"></div><br>
                </div>
        <?php } ?>
            </div>
        </div>
        <div class="clearfix"></div>
        </div>
        </div>
    </div>
</div>
<div class="col-xs-12 end-box" style="">
    <footer>Powered by <a href="http://sscommunication.co.in/">SS Communication & Services Pvt. Ltd.</a> / IT </footer>
</div>
<script>
    $(document).ready(function(){
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myTable tr").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>
<script src="<?php echo site_url(); ?>assets/js/bootstrap.min.js" type="text/javascript"></script>
</body>
</html>