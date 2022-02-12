<?php include __DIR__ . '../../header.php'; ?>
<div class="clearfix"></div>
<div style="font-family: K2D; font-size: 15px;" class="col-md-8 col-md-offset-2">
        <div class="thumbnail" style="border-radius: 0; margin-bottom: 0"><br>
        <center><h3 style="margin-top: 0"><span class="fa fa-sign-in fa-lg"></span> Service Case Details </h3></center><br>
          <div class="clearfix"></div>
            <div class="col-md-8 col-xs-8">
                <div>CaseID  :- <b style="color: #0e10aa !important;"><?php echo $service_data[0]->id_service ?></b></div>
                <div >Date  :- <b><?php echo date('d-M-Y', strtotime($service_data[0]->entry_time)) ?></b></div><br>                                
                
            </div>
            <div class="col-md-4 col-xs-4">
                <div>Invoice Date :- <?php echo date('d-M-Y', strtotime($service_data[0]->inv_date)) ?></div>
                <div>Invoice No :- <?php echo $service_data[0]->inv_no; ?></div>
            </div>
            <div class="clearfix"></div><hr>            
            <div class="col-md-8 col-xs-8" style="padding-left: 30px;">                
                <b>Branch: &nbsp; <?php echo $service_data[0]->branch_name ?></b><br>                        
                <b>Contact:</b> <?php echo $service_data[0]->branch_contact; ?><br>
            </div>
            <div class="col-md-4 col-xs-4" style="padding-left: 30px;">
                <b>Customer , </b><br>
                <b>Name: &nbsp; <?php echo $service_data[0]->customer_name ?></b><br>                        
                <b>Contact:</b> <?php echo $service_data[0]->mob_number; ?><br>
            </div>  
            <div class="clearfix"></div><hr>
           
            <table id="model_data" class="table table-bordered table-condensed table-full-width table-responsive table-hover" style="font-size: 14px">
            
            <thead class="bg-info">
                
                <th class="col-md-1">Sr no</th>
                <th class="col-md-7">Product / Description</th>
                              
            </thead>
            <tbody>
                <tr>                    
                    <td> 1 </td>
                    <td><?php echo $service_data[0]->full_name.' - ['.$service_data[0]->imei.']'; ?></td>
                                     
                </tr>                
                <tr>            
                    <td> </td>
                    <td colspan="1">Service Issue :- <?php echo $service_data[0]->problem; ?></td>                         
                </tr>
                <tr>
                    <td> </td>                    
                    <td colspan="1"> Remark :- <?php echo $service_data[0]->remark; ?></td>                    
                </tr>
            </tbody>
            
            </table>
        </div>
    </div>
        <div style="position: fixed; right: 30px; bottom: 70px;"><a  href="<?php echo base_url('Transfer/transfer_dc/') ?>" class="btn btn-floating btn-large waves-effect waves-light gradient2 print-a"><i class="pe pe-7s-print" style="font-size: 30px"></i></a></div>
<?php   include __DIR__ . '../../footer.php'; ?>
