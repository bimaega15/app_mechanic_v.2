// "use strict";
var datatable;
var penjualanId = $(".penjualan_id").data("value");
var body = $("body");
var jsonRow = $(".json_row").data("value");

$(document).ready(function () {
    const renderPrintKasir = (outputData) => {
        var output = "";
        $.ajax({
            url: $(".btn-print").attr("href"),
            dataType: "json",
            type: "get",
            data: {
                penjualan_id: outputData,
            },
            dataType: "text",
            async: false,
            success: function (data) {
                output = data;
            },
        });

        return output;
    };

    const printOutput = (output) => {
        var printWindow = window.open("", "_blank");
        printWindow.document.write(output);
        printWindow.document.close();
        printWindow.print();
        printWindow.close();
    };

    body.off("click", ".btn-print");
    body.on("click", ".btn-print", function (e) {
        e.preventDefault();
        const url = $(this).attr("href");
        const output = renderPrintKasir(penjualanId);
        printOutput(output);
    });

    body.off("click", ".btn-delete");
    body.on("click", ".btn-delete", function (e) {
        e.preventDefault();
        myModal.hide();

        basicDeleteConfirmDatatable({
            urlDelete: $(this).attr("href"),
            data: {},
            text: "Apakah anda yakin ingin menghapus item ini?",
        });
    });

    body.off("click", ".btn-edit-transaksi");
    body.on("click", ".btn-edit-transaksi", function (e) {
        e.preventDefault();
        if (jsonRow.penjualan_cicilan.length > 0) {
            runToast({
                type: "bg-danger",
                title: "Transaksi Berlangsung",
                description:
                    "Transaksi ini sudah melakukan pembayaran piutang, sehingga tidak dapat edit transaksi kembali",
            });
        } else {
            const url = $(this).attr("href");
            window.location.href = url;
        }
    });
});
