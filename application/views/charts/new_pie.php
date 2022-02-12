<script src="<?php echo site_url(); ?>assets/charts/js/amcharts.js"></script>
<script src="<?php echo site_url(); ?>assets/charts/js/gauge.js"></script>
<script src="<?php echo site_url(); ?>assets/charts/js/pie.js"></script>
<script src="<?php echo site_url(); ?>assets/charts/js/animate.min.js"></script>
<script src="<?php echo site_url(); ?>assets/charts/js/serial.js"></script>
<script src="<?php echo site_url(); ?>assets/charts/js/export.min.js"></script>
<?= link_tag("assets_ecom/css/k2d.css") ?>
<?= link_tag("assets/charts/css/export.css") ?>
<script src="<?php echo site_url(); ?>assets/charts/js/none.js"></script>
<script src="<?php echo site_url(); ?>assets/charts/js/light.js"></script>
<!-- Styles -->
<style>
#chartdiv1 {
  width: 100%;
  height: 300px;
}		
.amcharts-chart-div > a {
    display: none !important;
}
</style>
<script type="text/javascript">
    AmCharts.makeChart("chartdiv1",
        {
            "type": "pie",
            "angle": 30,
            "theme": "none",
            "balloonText": "[[title]]<br><span style='font-size:14px; font-family: K2D'><b>[[value]]</b> ([[percents]]%)</span>",
            "depth3D": 25,
            "innerRadius": "30%",
            "labelRadius": 0,
            "radius": "50%",
            "titleField": "Material",
            "valueField": "Amount",
            "allLabels": [],
            "balloon": {},
            "opacity": 0.5,
            "thousandsSeparator": ",",
            "usePrefixes": true,
            "legend": {
                "enabled": true,
                "align": "center",
                "markerType": "circle",
                "position": "right",
                "valueWidth": 150,
            },
            "titles": [],
            "dataProvider": [
            <?php foreach ($summary_report as $summary) { ?>
                {
                    "Material": "<?php echo $summary->payment_mode ?>",
                    "Amount": <?php echo $summary->amount ?>
                },
            <?php } ?>
            ]
        }
    );
</script>
<div id="chartdiv1"></div>
