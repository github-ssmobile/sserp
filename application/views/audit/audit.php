<?php include __DIR__.'../../header.php'; ?>
<?= link_tag('assets/css/bootstrap-select.min.css')?>
<script>
    $(document).ready(function (){
        $('#proceed').click(function (){
           var idbranch = $('#idbranch').val();
           var idcat = $('#idcat').val();
           var idbrand = $('#idbrand').val();
           var idgodown = $('#idgodown').val();
           if(idbranch == '0' || idcat == '0' || idbrand == '0' || idgodown == '0'){
               alert("Select Data");
               return false;
           }
        });
        $('#idgodown').change(function (){
           var idgodown = $('#idgodown').val();
           if(idgodown != '0' || idgodown != ''){
                $.ajax({
                    url:"<?php echo base_url() ?>Audit/ajax_check_godown_allow_for_allbrand_audit",
                    method:"POST",
                    data:{idgodown: idgodown},
                    success:function(data)
                    {
                        
                        $('#showbrand').html(data);
                    }
                });
            }
        });
    });
</script>
<center><h3><span class="mdi mdi-barcode-scan fa-lg"></span> Stock Audit </h3></center><hr>
<div class="thumbnail" style="padding: 0; margin: 0; min-height: 650px;">
    <div  style="padding: 20px 10px; margin: 0">
        <form>
           <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
                <div class="col-md-2">
                    <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
                </div>
            <?php }else { ?>
                <div class="col-md-2">
                    <b>Branch</b>
                    <select class="form-control chosen-select" name="idbranch" id="idbranch">
                        <option value="0">Select Branch</option>
                        <?php foreach($branch_data as $branch){ ?>
                            <option value="<?php echo $branch->id_branch; ?>"><?php echo $branch->branch_name; ?></option>
                        <?php } ?>
                    </select>
                </div>
             <?php  }  ?>
             <div class="col-md-2">
                <b>Godown</b>
                <select class="form-control chosen-select" name="idgodown" id="idgodown">
                    <!--<option value="all">All</option>-->
                    <option value="0">Select Godown</option>
                    <?php foreach($godown_data as $godown){ if($godown->id_godown != 5){?>
                    <option value="<?php echo $godown->id_godown ?>"><?php echo $godown->godown_name ?></option>
                    <?php } } ?>
                </select>
            </div>
            <div class="col-md-2">
                <b>Category</b>
                <select class="form-control chosen-select" name="idcat" id="idcat">
                    <option value="0">Select Category</option>
                    <?php foreach($category_data as $cat){ ?>
                    <option value="<?php echo $cat->id_product_category ?>"><?php echo $cat->product_category_name ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2">
                <b>Brand</b>
                <div id="showbrand">
                    <select class="form-control chosen-select " name="idbrand" id="idbrand">
                        <!--<option value="all">All</option>-->
                        <option value="0">Select Brand</option>
                        <?php foreach($brand_data as $brand){ ?>
                        <option value="<?php echo $brand->id_brand ?>"><?php echo $brand->brand_name ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="col-md-1">
                <input type="hidden" name="audit_start" id="audit_start" value="<?php echo date('Y-m-d H:i:s'); ?>">
                <button class="btn btn-primary" id="proceed" formmethod="GET" formaction="<?php echo base_url()?>Audit/get_brand_for_audit" >Proceed</button></div>
        </form>
    </div>
</div>
<?php include __DIR__.'../../footer.php'; ?>