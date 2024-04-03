<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>كشف حساب {{ $account['name'] }}</title>
    <style>
        * {
            box-sizing: border-box !important;
            padding: 0;
            margin: 0;
            user-select: none;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }

        *::before, *::after {
            box-sizing: border-box !important;
            padding: 0;
            margin: 0;
        }

        html,
        body {
            width: 100%;
            height: 100vh !important;
            direction: rtl;
        }

        @media print {
            html,
            body {
                margin: 0 !important;
                padding: 0 !important;
            }
        }

        .pdf-document {
            display: block;
            width: 100% !important;
            max-width: 100%;
            margin: 0 auto;
            background-color: #fcfcfc;
            height: 100% !important;
        }

        @media print {
            .pdf-document {
                margin: 0 auto !important;
                padding: 0 !important;
                max-width: 210mm !important;
            }
        }

        .page {
            position: relative;
            display: block;
            width: 240mm !important;
            max-width: 240mm !important;
            /* height: 297mm !important;
            min-height: 297mm !important;
            max-height: 297mm !important; */
            min-height: 297mm !important;
            height: auto !important;
            margin: 0 auto !important;
            /* overflow: hidden !important; */
            box-sizing: border-box;
            background-color: #fff;
            margin-bottom: 4px !important;
            /*-webkit-box-shadow: 0 6px 23px 0 rgba(0, 0, 0, .26);
            -moz-box-shadow: 0 6px 23px 0 rgba(0, 0, 0, .26);
            box-shadow: 0 6px 23px 0 rgba(0, 0, 0, .26)*/
            -webkit-box-shadow: 0 6px 23px 0 rgb(0 0 0 / 16%);
            -moz-box-shadow: 0 6px 23px 0 rgba(0, 0, 0, .16);
            box-shadow: 0 6px 23px 0 rgb(0 0 0 / 16%);
        }

        @media print {
            .page {
                padding: 0 !important;
                margin: 0 auto !important;
                -webkit-box-shadow: none !important;
                -moz-box-shadow: none !important;
                box-shadow: none !important;
                outline: none !important;
            }
        }

        .table {
            border-collapse: collapse !important;
            display: table;
            width: 100%;
            max-width: 100%;
            border-top: 1px solid #ccc;
            border-left: 1px solid #ccc;
            margin: 2px auto
        }

        .row {
            display: table-row;
        }

        .cell {
            display: table-cell;
            vertical-align: middle;
            font-size: 0.75em;
            border-right: 1px solid #ccc;
            border-bottom: 1px solid #ccc;
            text-align: center;
            color: #2b2929;
            background-color: transparent;
            height: 6.99583mm;
            width: fit-content;
            font-weight: bold
        }

        .header-row > .cell {
            background-color: #05b9a424 !important;
            height: 8mm !important;
            font-weight: bold !important;
        }

        .table .row:hover .cell { /*background-color:#90caf97d*/
            cursor: pointer;
        }

        .table .row .cell {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif !important;
            font-size: 13px;
        }

        .table .row .cell:first-child {
            width: fit-content !important;
            /*font-size: 14px !important;*/
        }

        .table .row .cell:nth-child(2) {
            min-width: 140px;
        }

        .table .row .cell:nth-child(3), .table .row {
            min-width: 70px;
        }

        .table .row .cell:nth-child(4) {
            min-width: 60px;
        }

        .table .row .cell:nth-child(5) {
            min-width: 100px;
        }

        .table .row .cell:nth-child(6) {
            min-width: 100px;
        }

        .table .row .cell:nth-child(7) {
            min-width: 70px;
        }

        .header-view {
            display: block;
            width: 100%;
            height: 40mm;
        }

        .page-header {
            display: flex;
            flex-direction: row;
            justify-content: space-around;
            width: 100%;
            max-width: 210mm;
            height: 32mm;
            background-color: #fff;
            overflow: hidden;
        }

        .account-info {
            width: 100%;
            height: 8mm;
            overflow: hidden;
            text-align: center;
            border-top: solid 1px #ccc;
            border-bottom: solid 1px #ccc;
            display: flex;
            justify-content: start;
            align-items: center;
            font-size: 1.05em;
            font-weight: 600;
            line-height: 1.5;
            padding-inline: 1.5em;
            padding-top: 1.1em;
            padding-bottom: 1.4em;
        }

        @media print {
            .account-info {
                color: #212121;
            }
        }

        .field {
            /*color: #26cc92 !important;*/
            color: #ff5722 !important;
            font-weight: 500;
        }


        @media print {
            .field {
                color: #212121 !important
            }
        }

        .header-block,
        .footer-block {
            text-align: center;
            width: 33.33%;
            height: 100%;
            background-color: transparent;
            /* border: 1px solid #000; */
            border-bottom: none;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .page-content {
            position: relative;
            width: 100%;
            min-height: 245mm;
            padding: 2em;
            overflow: hidden;
        }

        .page-footer {
            width: 100%;
            height: 8mm;
            overflow: hidden;
            border-top: solid 1px #555;
            display: flex;
            justify-content: space-around;
            align-items: center;
            /* background-color: antiquewhite; */
        }

        .footer-block {
            font-size: 14px !important;
        }


        /* general css height:-webkit-fill-available; */

        .border {
            position: relative;
            width: 100%;
            font-weight: 600;
            text-align: center;
            color: #000;
            padding: 8px 12px;
            text-decoration: underline;
        }

        .logo {
            width: auto;
            height: 100%;
            padding: 8px;
            overflow: hidden;
        }

        .header__info_line {
            padding: 1px 0;
            color: rgb(63, 61, 61);
        }

        .info {
            flex-direction: column !important;
            align-items: start;
            justify-content: center !important;
            padding: 0 1em;
        }

        .f-row .cell {
            font-size: small;
            font-weight: 500 !important;
            color: #03a9f4 !important;
            background-color: white !important;
            border-right: none !important;
        }

        .f-row .cell:first-child {
            border-right: 1px solid #ccc !important;
        }

        .cell {
            font-variant-numeric: slashed-zero;
        }

        .cell:first-child {
            /*display: none !important;*/
        }

        /*text-align to right in statement cell*/
        .row .cell:nth-child(2) {
            text-align: right !important;
            padding: 2px 6px;
        }

        .row .cell {
            font-weight: 500 !important;
        }

        .f-row .cell:nth-child(2) {
            /*border-right: 1px solid #ccc !important;*/
            text-align: center !important;
        }

        /*scrollbar*/
        ::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }

        ::-webkit-scrollbar-track {
            border-radius: 100vh;
            background: #edf2f7
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 100vh;
            border: 3px solid #edf2f7
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a0aec0
        }


    </style>
</head>
<body>
<div class="pdf-document">
    <div class="page">
        <div class="header-view">
            <div class="page-header">
                <!-- header right -->
                <div class="header-block" style="overflow: hidden;">
                    <img id="logo" class="logo" src="https://static.vecteezy.com/system/resources/previews/007/410/289/original/online-shop-logo-design-vector.jpg" alt="Logo" style="object-fit: cover; transform: scale(1.2)"/>
                </div>
                <!-- header center -->
                <div class="header-block">
                    <h1 class="border">كشف حساب</h1>
                </div>
                <!-- header left -->
                <div class="header-block info" style="width: 38% !important;">
                    <div class="header__info_line" style="font-weight: 500;"> رقم الحساب
                         <span id="account-number" class="field" style="font-size: 14px !important;">&nbsp;&nbsp;&nbsp;{{ substr(strtoupper(str_replace('-', '', $account['id'])), 0, 10) }}</span>
                    </div>
                    <div class="header__info_line" style="font-weight: 500;"> الـعـمـلــة
                         <span id="account-coin" class="field" style="font-size: 14px !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $currency['name'] }} </span>
                    </div>
                    @if(!empty($date))
                        <div class="header__info_line" style="font-weight: 500;"> الــتاريــــخ
                            <span id="date" Class="field" style="font-size: 14px !important;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $date }}</span>
                        </div>
                    @endif
                </div>
            </div>
            <!-- header bottom -->
            <div class="account-info">
                الحساب ⇇
                <span>&nbsp; <span id="account-name" style="color:#ff5722">{{ $account['name'] }}</span> &nbsp;</span>
            </div>
            <!-- page content area -->
            <div class="page-content content-one">
                <div class="table">
                    <div class="row header-row">
                        <div class="cell">التاريخ</div>
                        <div class="cell">البيان</div>
                        <div class="cell">الدائن</div>
                        <div class="cell">المدين</div>
                        <div class="cell">الرصيد</div>
                    </div>
                    @foreach($entries as $entry)
                        <div class="row row-{{$entry['id']}}">
                            <div class="cell">{{ \Carbon\Carbon::parse($entry['date'])->toDateString() }}</div>
                            <div class="cell">{{ $entry['statement'] }}</div>
                            <div class="cell">{{ $entry['creditor'] }}</div>
                            <div class="cell">{{ $entry['debtor']  }}</div>
                            <div class="cell">{{ $entry['balance'] }}</div>
                        </div>
                    @endforeach
                    @if(!empty($entries))
                        <div class="row f-row">
                            <div class="cell"></div>
                            <div class="cell">
                                الرصيد
                                @php
                                  if ($total['creditor'] > $total['debtor'] ) echo "لكم"; else echo "عليكم";
                                @endphp
                            </div>
                            <div class="cell"> {{ $total['creditor'] }} </div>
                            <div class="cell">{{ $total['debtor'] }}</div>
                            <div class="cell">{{ $total['balance'] }}</div>
                        </div>
                    @endif
                </div>
            </div>
            <!-- page footer -->
            <div class="page-footer">
                <div class="footer-block dayDate" id="ar-date"></div>
                <div class="footer-block page-number"><strong>( 1 )</strong></div>
                <div class="footer-block"> المستخدم :&nbsp;<span id="user" style="font-weight: bold"></span></div>
            </div>

        </div>
    </div>
</div>
</body>
</html>
