<!DOCTYPE html>
<html>
	<head>
		<title>chart created with amCharts | amCharts</title>
		<meta name="description" content="chart created using amCharts live editor" />
		
		<!-- amCharts javascript sources -->
		<script type="text/javascript" src="https://www.amcharts.com/lib/3/amcharts.js"></script>
		<script type="text/javascript" src="https://www.amcharts.com/lib/3/serial.js"></script>
		

		<!-- amCharts javascript code -->
		<script type="text/javascript">
			AmCharts.makeChart("chartdiv",
				{
					"type": "serial",
					"categoryField": "category",
					"dataDateFormat": "YYYY-MM-DD",
					"startDuration": 1,
					"categoryAxis": {
						"gridPosition": "start",
						"parseDates": true
					},
					"chartCursor": {
						"enabled": true
					},
					"chartScrollbar": {
						"enabled": true
					},
					"trendLines": [],
					"graphs": [
						{
							"fillAlphas": 1,
							"id": "AmGraph-1",
							"title": "graph 1",
							"type": "column",
							"valueField": "column-1"
						}
					],
					"guides": [],
					"valueAxes": [
						{
							"id": "ValueAxis-1",
							"title": "Axis title"
						}
					],
					"allLabels": [],
					"balloon": {},
					"titles": [
						{
							"id": "Title-1",
							"size": 15,
							"text": "Chart Title"
						}
					],
					"dataProvider": [
						{
							"category": "2014-03-01",
							"column-1": 8
						},
						{
							"category": "2014-03-02",
							"column-1": 16
						},
						{
							"category": "2014-03-03",
							"column-1": 2
						},
						{
							"category": "2014-03-04",
							"column-1": 7
						},
						{
							"category": "2014-03-05",
							"column-1": 5
						},
						{
							"category": "2014-03-06",
							"column-1": 9
						},
						{
							"category": "2014-03-07",
							"column-1": 4
						},
						{
							"category": "2014-03-08",
							"column-1": 15
						},
						{
							"category": "2014-03-09",
							"column-1": 12
						},
						{
							"category": "2014-03-10",
							"column-1": 17
						},
						{
							"category": "2014-03-11",
							"column-1": 18
						},
						{
							"category": "2014-03-12",
							"column-1": 21
						},
						{
							"category": "2014-03-13",
							"column-1": 24
						},
						{
							"category": "2014-03-14",
							"column-1": 23
						},
						{
							"category": "2014-03-15",
							"column-1": 24
						}
					]
				}
			);
		</script>
	</head>
	<body>
		<div id="chartdiv" style="width: 100%; height: 400px; background-color: #FFFFFF;" ></div>
	</body>
</html>