<?php include __DIR__.'../../header.php'; ?>
<script>
    $(document).ready(function (){
        $('.btnsearch').click(function (){
            var idbranch = $('#idbranch').val();
            var idgodown = $('#idgodown').val();
            if(idbranch != ''){
                 $.ajax({
                    url:"<?php echo base_url() ?>Stock/ajax_get_remaining_opening_stock_test_data",
                    method:"POST",
                    data:{idbranch: idbranch, idgodown: idgodown},
                    success:function(data)
                    {
                        $('#show_openingdata').html(data);
                    }
                });
            }
        });
    });
</script>
<div class="col-md-10"><center><h3><span class="mdi mdi-checkbox-multiple-marked-circle"></span>Opening Stock</h3></center></div>
    <div class="col-md-1">
        <!--<a class="arrow-down waves-effect simple-tooltip btn-block" onclick="this.classList.toggle('active')" data-toggle="collapse" data-target="#pay" title="Add Category"></a>-->
    </div><div class="clearfix"></div><hr>
    <div class="" style="padding: 0; margin: 0; min-height: 650px;">
        <?php if($this->session->userdata('level') == 2){   // Single Branch ?>     
            <div class="col-md-2">
                <input type="hidden" id="idbranch" name="idbranch" value="<?php echo $_SESSION['idbranch']?>">
            </div>
        <?php }else { ?>
            <div class="col-md-1"><b>Branch</b></div>
            <div class="col-md-3">
                <select class="form-control chosen-select" name="idbranch" id="idbranch" required="">
                    <option value="">Select Branch</option>
                    <?php foreach ($branch_data as $branch){ ?>
                        <option value="<?php echo $branch->id_branch?>"><?php echo $branch->branch_name;?></option>
                    <?php } ?>
                </select>
            </div>
        <?php  } ?>
         <div class="col-md-1"><b>Godown</b></div>
        <div class="col-md-3">
            <select class="form-control" name="idgodown" id="idgodown" >
                <option value="ALL">All Godown</option>
                <option value="NEW">NEW</option>
                <option value="DEMO">DEMO</option>
                <option value="DOA">DOA</option>
                <option value="SERVICE">SERVICE</option>
            </select>
        </div>
         <div class="col-md-1"><button type="submit" class="btn btn-info btnsearch">Search</button></div>
        <div class="clearfix"></div><br>
        <div id="show_openingdata" >
        </div>
    </div>
<?php include __DIR__.'../../footer.php'; ?>