<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" type="image/x-icon"
        href="{{ asset('backend/sneat-bootstrap-html-admin-template-free') }}/assets/img/favicon/favicon.ico" />
    <title>Print Servis Kendaraan</title>

    <style>
        * {
            font-family: Arial, Helvetica, sans-serif;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        #tableKendaraanServis {
            border-collapse: collapse;
            width: 100%;
        }

        #tableKendaraanServis th,
        #tableKendaraanServis td {
            border: 1px solid black;
            padding: 10px
        }

        #tableJasaServis {
            border-collapse: collapse;
            width: 100%;
        }

        #tableJasaServis th,
        #tableJasaServis td {
            border: 1px solid black;
            padding: 10px
        }

        #tableSparepart {
            border-collapse: collapse;
            width: 100%;
        }

        #tableSparepart th,
        #tableSparepart td {
            border: 1px solid black;
            padding: 10px
        }

        .w-100 {
            width: 100%;
        }
    </style>
</head>

<body>
    <div style="width: 100%; margin: 0 auto;">
        @include('service::print.partials.header')
        <div style="height: 10px;"></div>
        @include('service::print.partials.kendaraan')
        <div style="height: 10px;"></div>
        @include('service::print.partials.jasaServis')
        <div style="height: 10px;"></div>
        @include('service::print.partials.sparepart')
        <div style="height: 10px;"></div>
        @include('service::print.partials.invoiceLittle')

        <div style="height: 25px;"></div>
        <div class="text-center">
            <strong style="font-size: 12px;">Powered By: {{ UtilsHelp::createdApp() }}</strong>
        </div>
    </div>
</body>

</html>
