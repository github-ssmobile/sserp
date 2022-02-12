<?php include 'chart_header.php'; ?>

<script>
var chart = AmCharts.makeChart( "chartdiv1", {
  "type": "serial",
  "hideCredits":true,
  "angle": 35,
  "depth3D": 20,
  "columnWidth": 0.78,
  "startDuration": 1,
  "prefixesOfBigNumbers": [],
  "prefixesOfSmallNumbers": [],
  "legend": {
    "horizontalGap": 10,
    "useGraphSettings": true,
    "markerSize": 10, 
    "align": "center",
    "enabled": true,
  },
  "dataProvider": [
<?php foreach ($months as $mt => $mk) { ?>
    {
    "month": '<?php echo $mk ?>',
    "sitepayment": '<?php foreach($monthly_income as $income){ if($income->month == $mt && $income->year == $year){ echo $income->total_amount; }} ?>',
    "flatinstallment": '<?php foreach($monthly_installment as $install){ if($install->month == $mt && $install->year == $year){ echo $install->sumin_amount; }} ?>',
    "expurchase": '<?php foreach($supplier_payment_monthly as $spm){ if($spm->month == $mt && $spm->year == $year){ echo $spm->sum_total; }} ?>',
    "exsalary": '<?php foreach($monthly_salary_total as $salary_sum){ if($salary_sum->month == $mt && $salary_sum->year == $year){ echo $salary_sum->sum_salary; }} ?>',
  }, 
<?php } ?>
   ],
  "valueAxes": [ {
    "unit": "₹",
//    "position": "left",
//    "title": "Balance Sheet",
    "stackType": "regular",
    "axisAlpha": 0,
    "gridAlpha": 0
  } ],
  "graphs": [ {
    "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]] ₹</b></span>",
    "fillAlphas": 0.7,
    "labelText": "[[value]]",
    "lineAlpha": 0.3,
    "title": "Income - Site Payment",
    "type": "column",
    "color": "#000000",
    "valueField": "sitepayment"
  }, {
    "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]] ₹</b></span>",
    "fillAlphas": 0.7,
    "labelText": "[[value]]",
    "lineAlpha": 1,
    "title": "Income - Flat Installment",
    "type": "column",
    "color": "#000000",
    "valueField": "flatinstallment"
  }, {
    "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]] ₹</b></span>",
    "fillAlphas": 0.7,
    "labelText": "[[value]]",
    "lineAlpha": 1,
    "title": "Expense - Purchase",
    "type": "column",
    "newStack": true,
    "color": "#000000",
    "valueField": "expurchase"
  }, {
    "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]] ₹</b></span>",
    "fillAlphas": 0.7,
    "labelText": "[[value]]",
    "lineAlpha": 1,
    "title": "Expense - Salary",
    "type": "column",
    "color": "#000000",
    "valueField": "exsalary"
  }
  ],
//  "plotAreaBorderAlpha": 0,
  "chartScrollbar": {},
  "chartCursor": {
        "cursorAlpha": 0
    },
  "categoryField": "month",
  "categoryAxis": {
    "gridPosition": "start",
    "axisAlpha": 0,
    "gridAlpha": 0,
    "position": "left"
  },
  "export": {
    "enabled": false
  }

} );
</script>
<!-- HTML -->
<div id="chartdiv1"></div>



<style>
#chartdiv {
  width: 100%;
  height: 480px;
}										
</style>
<script type="text/javascript">
    AmCharts.makeChart("chartdiv",
        {
            "hideCredits":true,
            "type": "serial",
            "categoryField": "Month",
            "columnSpacing": 10,
            "columnWidth": 0.18,
            "angle": 30,
            "depth3D": 30,
            "startDuration": 1,
            "prefixesOfBigNumbers": [],
            "prefixesOfSmallNumbers": [],
            "categoryAxis": {
                "gridPosition": "start"
            },
            "trendLines": [],
            "graphs": [
                {
                    "balloonText": "[[title]] of [[Month]] <?php echo $year ?>:[[value]] ₹",
                    "fillAlphas": 1,
//                    "id": "AmGraph-1",
                    "title": "Inward",
                    "type": "column",
                    "lineAlpha": 1.2,
                    "valueField": "Inward"
                },
                {
                    "balloonText": "[[title]] of [[Month]] <?php echo $year ?>:[[value]] ₹",
                    "fillAlphas": 1,
                    "lineAlpha": 1.2,
//                    "id": "AmGraph-2",
                    "title": "Outward",
                    "type": "column",
                    "valueField": "Outward"
                },
            ],
            "guides": [],
            "valueAxes": [{
                "unit": "₹",
                "position": "left",
                "title": "Balance Sheet",
            }],
            "allLabels": [],
//            "balloon": {},
            "balloon": {
            "fixedPosition": false,
            "fillAlpha": 0,
		"fontSize": 12,
            },
            "legend": {
                "align": "center",
                "enabled": true,
                "useGraphSettings": true
            },
//            "titles": [
//                {
//                    "id": "Title-1",
//                    "size": 15,
//                    "text": "Chart Title"
//                }
//            ],
            "dataProvider": [
                <?php foreach($monthly_income as $income){ foreach ($monthly_salary_total as $salary_sum){ foreach ($purchase_amount as $puramt) {  
                if($puramt->month == $income->month && $salary_sum->month == $puramt->month && $puramt->year == $year && $income->year == $year && $year == $salary_sum->year ){ ?>
                {
                    "Month": "<?php echo date('F', mktime(0, 0, 0, $income->month, 10)) ?>",
                    "Inward": <?php echo $income->total_amount; ?>,
                    "Outward": <?php echo $puramt->sum_total + $salary_sum->sum_salary ?>,
                },  <?php }}}} ?>
            ]
        }
    );
</script>
	
<!--<div id="chartdiv"></div>-->