<div class="col-xs-12 end-box " style="">
    Software Developed by <span class="fa fa-copyright"></span> Vinayak Gonjari |
    <span class="mdi mdi-email-outline fa-lg"></span> vg.gonjari@gmail.com
    | <span class="mdi mdi-phone fa-lg"></span> 7387973395
</div>
<script type="text/javascript">
    $(document).ready(function () {
        Tipped.create('.simple-tooltip');
    });
</script>
<!-- /.col -->
<!--Footer end -->
<!--bootstrap JavaScript file  -->
<script src="<?php echo site_url(); ?>assets_ecom/js/bootstrap.min.js" type="text/javascript"></script>
<!--Slider JavaScript file  -->
<script src="<?php echo site_url(); ?>assets_ecom/ItemSlider/js/modernizr.custom.63321.js" type="text/javascript"></script>
<script src="<?php echo site_url(); ?>assets_ecom/ItemSlider/js/jquery.catslider.js" type="text/javascript"></script>
<script src="<?php echo site_url(); ?>assets_ecom/waves/waves.js" type="text/javascript"></script>
<script src="<?php echo site_url(); ?>assets_ecom/js/tipped.js" type="text/javascript"></script>
<script src="<?php echo site_url(); ?>assets/js/jquery.watermark.js" type="text/javascript"></script>

<script>
$(function () {
    //add text water mark;	
 $('div.watermark_text').watermark({
  text: 'Constrution Software',
  textWidth: 500,
  textColor: 'white',
  textSize:50,
 });
 //add image water mark
 $('img.watermark_img').watermark({
  path: 'logo.png'
 });	
    });
<?php for ($i = 0; $i < 10; $i++) { ?>
        $('#item_category<?php echo $i ?>').change(function () {
            var state_id = $(this).val();
            $("#items<?php echo $i ?> > option").remove();
            $.ajax({
                type: "POST",
                url: "<?php echo site_url('MY_Controller/populate_item'); ?>",
                data: {id: state_id},
                dataType: 'json',
                success: function (data) {
                    $.each(data, function (k, v) {
                        var opt = $('<option />');
                        opt.val(k);
                        opt.text(v);
                        $('#items<?php echo $i ?>').append(opt);
                    });
                }
            });
        });
<?php } ?>
    $('.datepick').datetimepicker({
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        format: "yyyy-mm-dd"
    });
    $('.datetimepick').datetimepicker({
        //language:  'fr',
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1
    });
    $('.timepick').datetimepicker({
        language: 'fr',
        weekStart: 1,
        todayBtn: 1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 1,
        minView: 0,
        maxView: 1,
        forceParse: 0
    });
</script>
</script>
</body>
</html>



