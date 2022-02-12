<?php include __DIR__.'../../print_header.php'; if(!$this->session->userdata('userid')){ return redirect(base_url()); } else {     ?>

<?php 
 $data=$transfer_data[0];
?>
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
                            <br>
                            Dc No.: &nbsp;<b><?php echo $data->id_transfer ?></b>                        
                        </div>
                    <?php }else{ ?>
                        <center><h4  style="color: #000;font-family: K2D">INTER STATE BRANCH SALES INVOICE</h4></center>
                        <div class="col-md-6 col-xs-6">
                            Reference No.: &nbsp;<b><?php echo $data->sales_invoice ?></b><br>
                            Dc No.: &nbsp;<b><?php echo $data->id_transfer ?></b>                        
                        </div>
                    <?php } ?>
                    
                    
                    <div class="col-md-6 col-xs-6" style="text-align: end;padding-right: 15px;">
                        Date: &nbsp;<?php echo date('d-M-Y', strtotime($data->scanned_time)) ?><br>
                        Shipment Date: &nbsp;<?php echo date('d-M-Y', strtotime($data->dispatch_date)) ?>
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
            <?php if($data->status < 3){ ?>
            <thead class="bg-info">
                <th>SrNo</th>
                <th class="col-md-7">Product</th>
                <th class="col-md-1">Godown</th>
                <th>Qty</th>
            </thead>
            <tbody>
                <?php 
                $i=1;
                foreach ($transfer_product as $product) { ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $product->full_name; ?></td>
                    <td><?php echo $product->godown_name; ?></td>
                    <td><?php echo $product->qty ?></td>
                </tr>
                <?php $i++; } ?>
            </tbody>
            <?php }elseif($data->status >= 3){
                        if($data->gst_type==0){ ?>                    
                            <thead class="bg-info">
                                <th>Sr</th>
                                <th class="col-md-5">Product</th>
                                <th class="col-md-1">Godown</th>
                                <th>Qty</th>
                                <th class="col-md-5">IMEI/SRNO</th>
                            </thead>
                            <tbody>
                                <?php $i = 1;
                                foreach ($transfer_product as $product) { ?>                                    
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $product->full_name; ?></td>
                                    <td><?php echo $product->godown_name; ?></td>
                                    <td><?php echo $product->qty ?></td>
                                    <td><?php echo $product->imei_no ?></td>
                                </tr>
                                <?php } ?>
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
                        foreach ($transfer_product as $product) {
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
                            
                            $gst_amt = (($rate*$tax_rate) / 100);
                            $basic_rate = ($amount) - $gst_amt;
                            
                            $taxable =  round((($rate)*($product->qty)),2);
                            $gst_amt = round((($taxable*$tax_rate) / 100),2);
                            $t_amt=round($taxable+$gst_amt,2);
                            
                            
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
                                <td><?php echo $product->imei ?></td>
                            </tr>
            <?php } ?>
                    </tbody>
            
            <?php } } ?>
        </table>
    </div>
    </div>
    <?php if($data->status >= 4){       
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
        <div style="position: fixed; right: 30px; bottom: 70px;"><a class="btn btn-floating btn-large waves-effect waves-light gradient2 print-a"><i class="pe pe-7s-print" style="font-size: 30px"></i></a></div>        
<script>
    
      window.print();     
       
</script>
        
<?php } ?>        
        
<?php include __DIR__.'../../footer.php'; ?>