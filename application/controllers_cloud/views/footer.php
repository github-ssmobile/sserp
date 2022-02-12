<div class="clearfix"></div>
<div style="overflow: hidden; height: 0px; padding: 0; margin: 0">
    <table>
        <tbody>
            <!--<td>11111111111111111111111111111111111111111111111111111111111111111110000000000000000000000000000000000000000000000000000000000000000</td>-->
            <!--<td>1111111111111111111111111111110111111111110111111111111111111111111111000000000000000000000000000000000000000000000000000000000000000</td>-->
        </tbody>
    </table>
</div>
</div>
</div>
</div>
</div>
<script src="<?php echo site_url();?>assets/js/newbarjquery.canvasjs.min.js"></script>
<div class="col-xs-12 end-box" style="">
<footer>Powered by <a href="http://sscommunication.co.in/">SS Communication & Services Pvt. Ltd.</a> / IT </footer>
</div>
<script>
      $(document).ready(function(){
        $("#myInput").on("keyup", function() {
          var value = $(this).val().toLowerCase();
          $("#myTable tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
          });
        });
      });
</script>
<script type="text/javascript">
    $(".chosen").chosen();
    $(".chosen-select").chosen({ search_contains: true });
    $(function () {
        $('.example').popover({
          container: 'body'
        });
    });
    $('.datepicker').datepicker(
        format: 'yyyy/mm/dd',
    );
    $('.monthpicker').datepicker(
        format: 'yyyy-mm',
    );
    $(document).ready(function () {
        Tipped.create('.simple-tooltip');
        $('#sidebarCollapse').on('click', function () {
            $('#sidebar').toggleClass('active');
        });
    });
</script>
<!-- /.col -->
<!--bootstrap JavaScript file  -->
<script src="<?php echo site_url('assets/js/sweet-alert.min.js') ?>"></script>
<script src="<?php echo site_url(); ?>assets/js/bootstrap.min.js" type="text/javascript"></script>
<!--Slider JavaScript file  -->
<script>
function googleTranslateElementInit() {
//  new google.translate.TranslateElement({pageLanguage: 'en', includedLanguages: 'en,hi,ml,mr,ta,te,ur', layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, 'google_translate_element');
}
</script>
<!--<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>-->
<script src="<?php echo site_url(); ?>assets/waves/waves.js" type="text/javascript"></script>
<script src="<?php echo site_url(); ?>assets/js/tipped.js" type="text/javascript"></script>
<!-- select box js -->
<!--<script src="<?php // echo site_url();?>assets/js/bootstrap-select.js"></script>-->
<script>
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
    $('.monthpick').datetimepicker({
        todayBtn: 1,
        autoclose: 1,
//        todayHighlight: 1,
        startView: 3,
        minView: 3,
        format: "yyyy-mm"
    });
</script>
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



