<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
       
        function digclock()
        {
                var d = new Date()
                var t = d.toLocaleTimeString()

                document.getElementById("clock").innerHTML = t
        }
        setInterval(function(){digclock()},1000)

        //scan imei
         var barcodes = [];
        $(document).on('keydown', 'input[id=scan_imei]', function(e) {
            var keyCode = e.keyCode || e.which ; 
            if (keyCode === 13 ) {
                var scan_barcode = $('#scan_imei').val();
                var branch_name = $('#branch_name').val();
                var model_name = $('#model_name').val();
                var datetime = $('#datetime').val();
                var idbranch = $('#idbranch').val();
                var idgodown = $('#idgodown').val();
              
                if(scan_barcode != ''){
                    if(barcodes.includes(scan_barcode) === false){
                        barcodes.push(scan_barcode);
                        $.ajax({
                            url:"<?php echo base_url() ?>Stock/ajax_scan_opening_imei",
                            method:"POST",
                            data:{scan_barcode: scan_barcode, branch_name: branch_name, model_name: model_name, datetime: datetime, idbranch: idbranch, idgodown: idgodown},
                            success:function(data)
                            {
                                //alert(data);
                                $("#barcode_data").append(data);
                                $('#scan_imei').val('');
                               
                            }
                        });
                    }else{
                        alert("Barcode Alreday Entered!.. ");
                        $('#scan_barcode').val('');
                        return false;
                    }
                }else{
                    alert("😡 Please Enter Barcode");
                    return false;
                }
            } 
        });
        
        $('#btnsubmit_opening').click(function (){
            var branch_name = $('#branch_name').val();
            var model_name = $('#model_name').val();
            
            if(!confirm('Do You Want To Submit Opening Stock of ' + model_name +" for " + branch_name + " Branch" )){
                return false;
            }
        });
        
        
    });
</script>
<style>
.fix {
    position: fixed;
    bottom: 80px;
    right: 20px; 
}
</style>
<div class="col-md-9"><center><h3><span class="mdi mdi-checkbox-multiple-marked-circle"></span>Opening Stock</h3></center></div>
<div class="col-md-2" style="border: 1px solid #cccccc;padding: 10px;color: white; background-image: linear-gradient(to right top, #d16ba5, #c46cb3, #b370c1, #9d74cc, #8179d6, #7075dc, #5971e2, #336ee8, #2a5fed, #2e4ef0, #3e38f1, #520eee);" >
    <center><div id="clock" style="font-family: cursive;font-size: 18px;"></div></center>
</div>
<div class="clearfix"></div><hr>
    <div class="" style="padding: 0; margin: 0; min-height: 650px;">
        <div  style="color: #3366ff"><h4><center><?php echo $branch_data->branch_name .' - '.$model_data->full_name; ?></center></h4></div>
        <div class="clearfix"></div>
        <div class="col-md-2 col-md-offset-2"><b>Scan Imei for Opening</b></div>
        <div class="col-md-4">
            <input class="form-control" type="text" id="scan_imei">
            <input type="hidden"  id="branch_name" value="<?php echo $branch_data->branch_name ?>">
            <input type="hidden" id="model_name" value="<?php echo $model_data->full_name ?>">
           
        </div>
        <div class="clearfix"></div><br>
       
            <table class="table table-bordered table-condensed">
               <thead style="background-color: #99ccff">
                   <th><b>Barcode</b></th>
                   <th><b>Product</b></th>
                   <th><b>Branch</b></th>
               </thead>
               <tbody id="barcode_data"> 
                   <?php if($opening_stock_test_data){
                       foreach($opening_stock_test_data as $opening){
                             array_push($_SESSION['scan_barcodes'], $opening->imei); ?>
                       <tr>
                           <td><?php echo $opening->imei; ?></td>
                           <td><?php echo $model_data->full_name; ?></td>
                           <td><?php echo $branch_data->branch_name; ?></td>
                       </tr>
                   <?php } } ?>

               </tbody>
            </table>
         <form>
            <input type="hidden" name="idgodown" id="idgodown" value="<?php echo $idgodown ?>">
            <input type="hidden" name="idbranch" id="idbranch" value="<?php echo $idbranch ?>">
            <input type="hidden" name="idpcat" id="idpcat" value="<?php echo $idpcat ?>">
            <input type="hidden" name="idbrand" id="idbrand" value="<?php echo $idbrand ?>">
            <input type="hidden" name="idmodel"  id="idmodel" value="<?php echo $idmodel ?>">
            <input type="hidden" name="datetime" id="datetime" value="<?php echo $datetime ?>">
            <button type="submit" name="btnsubmit_opening" class="btn btn-info fix" id="btnsubmit_opening" formmethod="POST" formaction="<?php echo base_url()?>Stock/save_scanned_opening_stock">Submit</button>
        </form>
    </div>
<?php include __DIR__.'../../footer.php'; ?>