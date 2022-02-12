<!DOCTYPE html>
<html lang="en">
    <head>
        <title>SS ERP</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/png" href="<?php echo site_url(); ?>assets/images/favicon.jpeg"/>
        <?= link_tag("assets/login/vendor/bootstrap/css/bootstrap.min.css") ?>
        <?= link_tag("assets/login/fonts/font-awesome-4.7.0/css/font-awesome.min.css") ?>
        <?= link_tag("assets/login/vendor/animate/animate.css") ?>
        <?= link_tag("assets/login/vendor/css-hamburgers/hamburgers.min.css") ?>
        <?= link_tag("assets/login/vendor/select2/select2.min.css") ?>
        <?= link_tag("assets/login/css/util.css") ?>
        <?= link_tag("assets/login/css/main.css") ?>
        <style>
            body {
                margin: 30px;
                font-family: sans-serif;
            }

            #fontSizeWrapper { font-size: 16px; }

            #fontSize {
                width: 100px;
                font-size: 1em;
            }

            /* ————————————————————–
              Tree core styles
            */
            .tree { margin: 1em; }

            .tree input {
                position: absolute;
                clip: rect(0, 0, 0, 0);
            }

            .tree input ~ ul { display: none; }

            .tree input:checked ~ ul { display: block; }

            /* ————————————————————–
              Tree rows
            */
            .tree li {
                line-height: 1.2;
                position: relative;
                padding: 0 0 1em 1em;
            }

            .tree ul li { padding: 1em 0 0 1em; }

            .tree > li:last-child { padding-bottom: 0; }

            /* ————————————————————–
              Tree labels
            */
            .tree_label {
                position: relative;
                display: inline-block;
                background: #fff;
            }

            label.tree_label { cursor: pointer; }

            label.tree_label:hover { color: #666; }

            /* ————————————————————–
              Tree expanded icon
            */
            label.tree_label:before {
                background: #777;
                color: #fff;
                position: relative;
                z-index: 1;
                float: left;
                margin: 0 1em 0 -2em;
                width: 1em;
                height: 1em;
                border-radius: 1em;
                content: '+';
                text-align: center;
                line-height: .9em;
            }

            :checked ~ label.tree_label:before { content: '–'; }

            /* ————————————————————–
              Tree branches
            */
            .tree li:before {
                position: absolute;
                top: 0;
                bottom: 0;
                left: -.5em;
                display: block;
                width: 0;
                border-left: 1px solid #777;
                content: "";
            }

            .tree_label:after {
                position: absolute;
                top: 0;
                left: -1.5em;
                display: block;
                height: 0.5em;
                width: 1em;
                border-bottom: 1px solid #777;
                border-left: 1px solid #777;
                border-radius: 0 0 0 .3em;
                content: '';
            }

            label.tree_label:after { border-bottom: 0; }

            :checked ~ label.tree_label:after {
                border-radius: 0 .3em 0 0;
                border-top: 1px solid #777;
                border-right: 1px solid #777;
                border-bottom: 0;
                border-left: 0;
                bottom: 0;
                top: 0.5em;
                height: auto;
            }
            .tree li label{
                color: #ff0066;
            }
            .tree li:last-child:before {
                height: 1em;
                bottom: auto;
            }

            .tree > li:last-child:before { display: none; }

            .tree_custom {
                display: block;
                background: #eee;
                padding: 1em;
                border-radius: 0.3em;
            }
        </style>
    </head>
    <body>
        <div id="fontSizeWrapper">
            <label for="fontSize">Font size</label>
            <input type="range" value="1" id="fontSize" step="0.5" min="0.5" max="5" />
        </div>
        <ul class="tree">
            <li>
                <input type="checkbox" checked="checked" id="c1" />
                <label class="tree_label" for="c1">Stock</label>
                <ul>
                    <li>
                        <input type="checkbox" checked="checked" id="c2" />
                        <label for="c2" class="tree_label">Check Stock</label>
                        <ul>
                            <li><span class="tree_label">Damage Stock Report</span></li>
                            <li><span class="tree_label">Stock with Barcode</span></li>
                        </ul>
                    </li>
                </ul>
                <li>
                    <span class="tree_label">Search Barcode</span>
                </li>
            </li>
            <li>
                <input type="checkbox" checked="checked" id="c1" />
                <label class="tree_label" for="c1">Reports</label>
                <ul>
                    <li>
                        <input type="checkbox" checked="checked" id="c2" />
                        <label for="c2" class="tree_label">Sale Report</label>
                        <ul>
                            <li><span class="tree_label">Ageing Sale Report</span></li>
                            <li><span class="tree_label">Cancellation Report</span></li>
                            <li><span class="tree_label">Category Report</span></li>
                            <li><span class="tree_label">Sale Report with Barcode</span></li>
                            <li><span class="tree_label">Value Connect Report</span></li>
                        </ul>
                    </li>
                    <li>
                        <span class="tree_label">Transfer Report</span>
                    </li>
                    <li>
                        <span class="tree_label">Outstanding Report</span>
                    </li>
                    <li>
                        <span class="tree_label">Outstanding Receive Report</span>
                    </li>
                </ul>
            </li>
            <li>
                <input type="checkbox" id="c5" />
                <label class="tree_label" for="c5">Audit Reports</label>
                <ul>
                    <li><span class="tree_label">Auditor Audit Reports</span></li>
                    <li><span class="tree_label">Audit Analysis Report</span></li>
                    <li><span class="tree_label">Accountant Audit Report</span></li>
                    <li><span class="tree_label">Accountant Audit Analysis Report</span></li>
                </ul>
            </li>
        </ul>
        <script src="<?php echo site_url(); ?>assets/login/vendor/jquery/jquery-3.2.1.min.js"></script>
        <script src="<?php echo site_url(); ?>assets/login/vendor/bootstrap/js/popper.js"></script>
        <script src="<?php echo site_url(); ?>assets/login/vendor/bootstrap/js/bootstrap.min.js"></script>
        <script src="<?php echo site_url(); ?>assets/login/vendor/select2/select2.min.js"></script>
        <script src="<?php echo site_url(); ?>assets/login/vendor/tilt/tilt.jquery.min.js"></script>
        <script>
            function isNumber(n) {
                return !isNaN(parseFloat(n)) && isFinite(n);
            }

            function setFontSize(el) {
                var fontSize = el.val();

                if (isNumber(fontSize) && fontSize >= 0.5) {
                    $('body').css({fontSize: fontSize + 'em'});
                } else if (fontSize) {
                    el.val('1');
                    $('body').css({fontSize: '1em'});
                }
            }

            $(function () {

                $('#fontSize')
                        .bind('change', function () {
                            setFontSize($(this));
                        })
                        .bind('keyup', function (e) {
                            if (e.keyCode == 27) {
                                $(this).val('1');
                                $('body').css({fontSize: '1em'});
                            } else {
                                setFontSize($(this));
                            }
                        });

                $(window)
                        .bind('keyup', function (e) {
                            if (e.keyCode == 27) {
                                $('#fontSize').val('1');
                                $('body').css({fontSize: '1em'});
                            }
                        });

            });
        </script>
    </body>
</html>