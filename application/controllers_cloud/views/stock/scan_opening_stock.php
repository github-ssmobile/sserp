<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
        $('#btnsearch').click(function (){
         
            var idgodown = $('#idgodown') .val();
            var idbranch = $('#idbranch') .val();
            var idpcat = $('#idpcat') .val();
            var idbrand = $('#idbrand') .val();
            var idmodel = $('#idmodel') .val();
            if(idgodown == '') {
              alert("Select Godown");
              return false;
            } 
            if(idbranch == '' ){
              alert("Select Branch");
              return false;  
            }
            if(idpcat == ''){  
                alert("Select Category");
                return false;
            }
            if(idbrand == ''){
                alert("Select Brand");
                return false;
            }
            if(idmodel == ''){
                alert("Select Model");
                return false;
            }
        });
        
    });
</script>
<div class="col-md-10"><center><h3><span class="mdi mdi-checkbox-multiple-marked-circle"></span>Opening Stock</h3></center></div>
    <div class="col-md-1">
        <!--<a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a>-->
    </div><div class="clearfix"></div><hr>
    <div class="" style="padding: 0; margin: 0; min-height: 650px;">
        <div class="clearfix"></div><br>
        <form>
            <div class="col-md-2">
                <select class="form-control" name="idgodown" id="idgodown" >
                    <option value="">Select Godown</option>
                    <?php foreach ($godown_data as $godown){ ?>
                    <option value="<?php echo $godown->id_godown?>"><?php echo $godown->godown_name;?></option>
                    <?php } ?>
                </select>
            </div>
            <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
                <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
            <?php } else { ?>
                <div class="col-md-2">
                    <select class="form-control chosen-select" name="idbranch" id="idbranch">
                        <option value="">Select Branch</option>
                        <?php foreach ($branch_data as $branch){ ?>
                        <option value="<?php echo $branch->id_branch?>"><?php echo $branch->branch_name;?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php } ?>
            <div class="col-md-2">
                <select class="form-control chosen-select" name="idpcat" id="idpcat">
                    <option value="">Select Category</option>
                    <?php foreach ($product_category as $pcat){ ?>
                    <option value="<?php echo $pcat->id_product_category?>"><?php echo $pcat->product_category_name;?></option>
                    <?php  } ?>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control chosen-select" name="idbrand" id="idbrand">
                    <option value="">Select Brand</option>
                    <?php foreach ($brand_data as $brand){ ?>
                    <option value="<?php echo $brand->id_brand?>"><?php echo $brand->brand_name;?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control chosen-select" name="idmodel" id="idmodel">
                    <option value="">Select Model</option>
                    <?php foreach ($model_data as $model){ ?>
                    <option value="<?php echo $model->id_variant?>"><?php echo $model->full_name;?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-md-1"><button type="submit" formmethod="GET" formaction="<?php echo base_url()?>Stock/scan_opening_stock_imei" id="btnsearch" class="btn btn-info">Submit</button></div>
        </form>
    </div>
<?php include __DIR__.'../../footer.php'; ?>