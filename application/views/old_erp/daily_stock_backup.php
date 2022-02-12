<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>ERP</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport' />
    <meta name="viewport" content="width=device-width" />
    <link rel="shortcut icon" href="<?php echo base_url()?>favicon.ico" type="image/x-icon">
    <link rel="icon" href="<?php echo base_url()?>favicon.ico" type="image/x-icon">
    <?php date_default_timezone_set('Asia/Kolkata'); ?>
    <?= link_tag("assets/css/bootstrap.css") ?>
    <script src="<?php echo site_url() ?>assets/js/jquery-3.1.1.min.js"></script>
    <script src="<?php echo site_url() ?>assets/js/jquery.bootstrap.js" type="text/javascript"></script>    
</head>
<body>
    <!--<nav class="navbar hovereffect1 navbar-default navbar-expand-l" role="navigation" style="margin: 0; padding-bottom: 5px; height: auto; border: none; border-radius: 0;background-image: url(<?php echo base_url()?>assets/images/header.jpg)">-->
<div class="container-fluid">
<div class="row">
<div class="wrapper">
    <div class="tab-content" id="content">
<script>
    window.onload=function() {
        $('#stock_download_btn').trigger('click');
    };
</script>

<div class="col-md-10"><center><h3><span class="mdi mdi-upload"></span> Daily Stock Data</h3></center></div><div class="clearfix"></div><hr>
<button id="stock_download_btn" class="btn btn-primary btn-sm" onclick="javascript:xport.toCSV('stock_data_opening_<?php echo date('d-m-Y') ?>');"><span class="fa fa-file-excel-o"></span> Export</button>
<table id="stock_data_opening_<?php echo date('d-m-Y');?>">
    <thead>
        <th>Branch</th>
        <th>Godown</th>
        <th>Category</th>
        <th>Brand</th>
        <th>Product</th>
        <th>IMEI/SRNO</th>
        <th>Qty</th>
        <th>Sender Branch</th>
        <th>Status</th>
        <th>Variant id</th>
        <th>MOP</th>
        <th>Landing</th>
        <th>MRP</th>
    </thead>
    <tbody>
        <?php foreach ($daily_stock_data as $stock){ ?>
        <tr>
            <td><?php echo $stock->branch_name ?></td>
            <td><?php echo $stock->godown_name ?></td>
            <td><?php echo $stock->product_category_name ?></td>
            <td><?php echo $stock->brand_name ?></td>
            <td><?php echo $stock->product_name ?></td>
            <td>'<?php echo $stock->imei_no ?></td>
            <td><?php echo $stock->qty ?></td>
            <td></td>
            <td>In Branch</td>
            <td><?php echo $stock->idvariant ?></td>
            <td><?php echo $stock->mop ?></td>
            <td><?php echo $stock->landing ?></td>
            <td><?php echo $stock->mrp ?></td>
        </tr>
        <?php } ?>
        <?php foreach ($daily_transit_stock_data as $transit){ ?>
        <tr>
            <td><?php echo $transit->receiver ?></td>
            <td><?php echo $transit->godown_name ?></td>
            <td><?php echo $transit->product_category_name ?></td>
            <td><?php echo $transit->brand_name ?></td>
            <td><?php echo $transit->product_name ?></td>
            <td>'<?php echo $transit->imei_no ?></td>
            <td><?php echo $transit->qty ?></td>
            <td><?php echo $transit->sender ?></td>
            <td>In Transit</td>
            <td><?php echo $transit->idvariant ?></td>
            <td><?php echo $transit->mop ?></td>
            <td><?php echo $transit->landing ?></td>
            <td><?php echo $transit->mrp ?></td>
        </tr>
        <?php } ?>
         <?php if(count($daily_rudram_stock_data)>0){ foreach ($daily_rudram_stock_data as $stock){ ?>
        <tr>
            <td><?php echo $stock->branch_name ?></td>
            <td> </td>
            <td><?php echo $stock->product_category_name ?></td>
            <td><?php echo $stock->brand_name ?></td>
            <td><?php echo $stock->product_name ?></td>
            <td> </td>
            <td><?php echo $stock->qty ?></td>
            <td></td>
            <td>In Branch</td>
            <td><?php echo $stock->idvariant ?></td>
            <td><?php echo $stock->mop ?></td>
            <td><?php echo $stock->landing ?></td>
            <td><?php echo $stock->mrp ?></td>
        </tr>
        <?php } }?>
        <?php if(count($daily_transitrudram_stock_data)>0){ foreach ($daily_transitrudram_stock_data as $transit){ ?>
        <tr>
            <td><?php echo $transit->branch_name ?></td>
            <td></td>
            <td><?php echo $transit->product_category_name ?></td>
            <td><?php echo $transit->brand_name ?></td>
            <td><?php echo $transit->product_name ?></td>
            <td></td>
            <td><?php echo $transit->qty ?></td>
            <td></td>
            <td>In Transit</td>
            <td><?php echo $transit->idvariant ?></td>
            <td><?php echo $transit->mop ?></td>
            <td><?php echo $transit->landing ?></td>
            <td><?php echo $transit->mrp ?></td>
        </tr>
        <?php } }?>
    </tbody>
</table>
<div class="clearfix"></div>
</div>
</div>
</div>
</div>
<script src="<?php echo site_url(); ?>assets/js/bootstrap.min.js" type="text/javascript"></script>
<script>
    var xport = {
  _fallbacktoCSV: true,  
  toXLS: function(tableId, filename) {   
    this._filename = (typeof filename == 'undefined') ? tableId : filename;
    
    //var ieVersion = this._getMsieVersion();
    //Fallback to CSV for IE & Edge
    if ((this._getMsieVersion() || this._isFirefox()) && this._fallbacktoCSV) {
      return this.toCSV(tableId);
    } else if (this._getMsieVersion() || this._isFirefox()) {
      alert("Not supported browser");
    }

    //Other Browser can download xls
    var htmltable = document.getElementById(tableId);
    var html = htmltable.outerHTML;

    this._downloadAnchor("data:application/vnd.ms-excel" + encodeURIComponent(html), 'xls'); 
  },
  toCSV: function(tableId, filename) {
    this._filename = (typeof filename === 'undefined') ? tableId : filename;
    // Generate our CSV string from out HTML Table
    var csv = this._tableToCSV(document.getElementById(tableId));
    // Create a CSV Blob
    var blob = new Blob([csv], { type: "text/csv" });

    // Determine which approach to take for the download
    if (navigator.msSaveOrOpenBlob) {
      // Works for Internet Explorer and Microsoft Edge
      navigator.msSaveOrOpenBlob(blob, this._filename + ".csv");
    } else {      
      this._downloadAnchor(URL.createObjectURL(blob), 'csv');      
    }
  },
  _getMsieVersion: function() {
    var ua = window.navigator.userAgent;

    var msie = ua.indexOf("MSIE ");
    if (msie > 0) {
      // IE 10 or older => return version number
      return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)), 10);
    }

    var trident = ua.indexOf("Trident/");
    if (trident > 0) {
      // IE 11 => return version number
      var rv = ua.indexOf("rv:");
      return parseInt(ua.substring(rv + 3, ua.indexOf(".", rv)), 10);
    }

    var edge = ua.indexOf("Edge/");
    if (edge > 0) {
      // Edge (IE 12+) => return version number
      return parseInt(ua.substring(edge + 5, ua.indexOf(".", edge)), 10);
    }

    // other browser
    return false;
  },
  _isFirefox: function(){
    if (navigator.userAgent.indexOf("Firefox") > 0) {
      return 1;
    }
    
    return 0;
  },
  _downloadAnchor: function(content, ext) {
      var anchor = document.createElement("a");
      anchor.style = "display:none !important";
      anchor.id = "downloadanchor";
      document.body.appendChild(anchor);

      // If the [download] attribute is supported, try to use it
      
      if ("download" in anchor) {
        anchor.download = this._filename + "." + ext;
      }
      anchor.href = content;
      anchor.click();
      anchor.remove();
  },
  _tableToCSV: function(table) {
    // We'll be co-opting `slice` to create arrays
    var slice = Array.prototype.slice;

    return slice
      .call(table.rows)
      .map(function(row) {
        return slice
          .call(row.cells)
          .map(function(cell) {
            return '"t"'.replace("t", cell.textContent);
          })
          .join(",");
      })
      .join("\r\n");
  }
};
</script>
</body>
</html>