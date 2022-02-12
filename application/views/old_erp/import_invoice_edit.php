<?php include __DIR__ . '../../header.php'; ?>
<script>
$(document).ready(function(){
    $(window).keydown(function(event){ if(event.keyCode === 13) { event.preventDefault(); return false; } });
    $(document).on('keydown', 'input[id=import_invoice]', function(e) {
       
        var imp_invoice = $(this).val();
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && $(this).val() !== '') {
            var level = $('#level').val();
            $.ajax({
                url:"<?php echo base_url() ?>Old_erp/get_import_invoice_data",
                method:"POST",
                data:{imp_invoice : imp_invoice,level: level},
                success:function(data)
                {
                    $("#import_data").html(data);
                   // $(".chosen-select").chosen({search_contains: true});
                }
            });
        }
    });
});

   
</script>
<a href="#" class="img intro-banner-vdo-play-btn pinkBg" style="display: none">
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
    <span class="ripple pinkBg"></span>
</a>
<center><h3 style="margin: 0"><span class="mdi mdi-margin fa-lg"></span>Import Invoice Edit</h3></center><div class="clearfix"></div><hr>
<div class="col-md-2 col-sm-2">Import Invoice :</div>
<div class="col-md-4 col-sm-7">
    <input type="text" class="form-control" id="import_invoice" name="import_invoice" placeholder="Search Import Invoice"/>
</div><div class="clearfix"></div>
<input type="hidden" id="level" value="<?php echo $_SESSION['level'] ?>"/>
<input type="hidden" class="form-control input-sm" name="edited_by" value="<?php echo $this->session->userdata('id_users') ?>"/><br>
<div class="panel panel-info panel-body" style="padding: 15px; margin: 0; overflow: auto; font-size: 13px;"> 
<div id="import_data" style="font-size: 14px; min-height: 550px; overflow: auto"></div>
</div>
<style>
.btn-outline {
    background-color: transparent;
    color: inherit;
    transition: all .5s;
    border-radius: 2px;
    text-transform: capitalize;
}
.btn-primary.btn-outline {
    border: 1px solid #428bca;
    color: #428bca;
}
.btn-success.btn-outline {
    border: 1px solid #1fa337;
    color: #1fa337;
}
.btn-info.btn-outline {
    border: 1px solid #5bc0de;
    color: #5bc0de;
}
.btn-warning.btn-outline {
    border: 1px solid #ff8a1c;
    color: #ff8a1c;
}
.btn-danger.btn-outline {
    border: 1px solid #d9534f;
    color: #d9534f;
}
.btn-primary.btn-outline:hover,
.btn-success.btn-outline:hover,
.btn-info.btn-outline:hover,
.btn-warning.btn-outline:hover,
.btn-danger.btn-outline:hover {
    color: #fff;
}
</style>
<?php include __DIR__ . '../../footer.php'; ?>