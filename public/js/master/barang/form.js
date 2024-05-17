select2Standard({
    parent: "#extraLargeModal",
    selector: "select[name='kategori_id']",
});
select2Standard({
    parent: "#extraLargeModal",
    selector: "select[name='satuan_id']",
});
select2Standard({
    parent: "#extraLargeModal",
    selector: "select[name='snornonsn_barang']",
});
select2Standard({
    parent: "#extraLargeModal",
    selector: "select[name='status_barang']",
});

var hargaJualBarang = new AutoNumeric("input[name='hargajual_barang']", {
    digitGroupSeparator: ",",
    decimalPlaces: 0,
    unformatOnSubmit: true,
});
var stokBarang = new AutoNumeric("input[name='stok_barang']", {
    digitGroupSeparator: ",",
    decimalPlaces: 0,
    unformatOnSubmit: true,
});
var formSubmit = document.getElementById("form-submit");

formSubmit.addEventListener("submit", function (event) {
    event.preventDefault();
    submitData();
});

function submitData() {
    const formData = {};
    formData.barcode_barang = $('input[name="barcode_barang"]').val();
    formData.nama_barang = $('input[name="nama_barang"]').val();
    formData.deskripsi_barang = $('textarea[name="deskripsi_barang"]').val();
    formData.hargajual_barang = hargaJualBarang.getNumber();
    formData.stok_barang = stokBarang.getNumber();
    formData.kategori_id = $(
        'select[name="kategori_id"] option:selected'
    ).val();

    formData.satuan_id = $('select[name="satuan_id"] option:selected').val();
    formData.snornonsn_barang = $(
        'select[name="snornonsn_barang"] option:selected'
    ).val();
    formData.lokasi_barang = $('input[name="lokasi_barang"]').val();
    formData.status_barang = $(
        'select[name="status_barang"] option:selected'
    ).val();

    $.ajax({
        type: "post",
        url: $("#form-submit").attr("action"),
        data: formData,
        dataType: "json",
        beforeSend: function () {
            clearError422();
            $("#btn-submit").attr("disabled", true);
            $("#btn-submit").html(disableButton);
        },
        success: function (data) {
            myModal.hide();
            runToast({
                title: "Successfully",
                description: data,
                type: "bg-success",
            });
            datatable.ajax.reload();
        },
        error: function (jqXHR, exception) {
            $("#btn-submit").attr("disabled", false);
            $("#btn-submit").html(enableButton);
            if (jqXHR.status === 422) {
                showErrors422(jqXHR);
            }
        },
        complete: function () {
            $("#btn-submit").attr("disabled", false);
            $("#btn-submit").html(enableButton);
        },
    });
}

var nextPage = 1;
var listItem = [];
var baseurl = $('.baseurl').data('value');
var search = '';
var isScroll = false;
var isMaxPage = false;


$('body').off('input', '.autocomplete');
$('body').on('input', '.autocomplete', debounce(function (e) {
    const value = $(this).val();
    search = value;
    let isSearch = false;
    if (value != '') {
        nextPage = 1;
        listItem = [];
        isSearch = true;
        isScroll = false;
    }

    $.ajax({
        url: `${baseurl}/autocomplete/barang`,
        type: 'get',
        dataType: 'json',
        data: {
            search: value,
            page: nextPage,
        },
        success: function (data) {
            const getDataAjax = (data) => {
                const results = data.results;
                const next_page_url = results.next_page_url !== null ? results.next_page_url.split('?page=')[1] : nextPage;
                const newData = results.data;
                isMaxPage = results.next_page_url !== null ? false : true;

                if (newData.length > 0) {
                    if (next_page_url !== nextPage && !isSearch) {
                        listItem = listItem.concat(newData);
                    } else if (isScroll) {
                        listItem = listItem.concat(newData);
                    } else {
                        listItem = newData;
                    }
                }

                nextPage = next_page_url;

                let listItemString = '';
                let buttonCloseElement = '';
                if (listItem.length > 0) {
                    newData.forEach(value => {
                        listItemString += `
                        <li class="list-group-item d-flex justify-content-between align-items-start click-list-item" data-barcode_barang="${value.barcode_barang}" style="cursor: pointer;">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">${value.nama_barang}</div>
                                    ${value.barcode_barang}
                                </div>
                        </li>
                    `;
                    });

                    buttonCloseElement = `
                <button type="button" class="btn-close-autocomplete btn btn-md" style="
                position: absolute;
                z-index: 99;
                padding: 0;
                right: 23px;
                bottom: 7px;
            ">
                    <i class="fa-regular fa-circle-xmark fa-1x" style="
                    font-size: 20px;"></i>
                </button>
                `;
                }

                const inputElement = $('.autocomplete');
                const listGroup = $('.list-group');
                const btnCloseAutoComplete = $('.btn-close-autocomplete');

                if (listGroup.length > 0) {
                    if (isScroll) {
                        listGroup.append(listItemString);
                    } else {
                        listGroup.html(listItemString);
                    }
                    btnCloseAutoComplete.replaceWith(buttonCloseElement);
                } else if (listItem.length > 0) {
                    $(buttonCloseElement).insertAfter(inputElement);
                    $(`
                        <ol class="list-group list-group-numbered list-group-item-click" style="
                            position: absolute;
                            background: white;
                            width: 94%;
                            box-sizing: border-box;
                            height: 250px;
                            overflow: scroll;
                        ">${listItemString}</ol>
                    `).insertAfter(inputElement);
                } else {
                    btnCloseAutoComplete.remove();
                    listGroup.remove();
                }
            }
            getDataAjax(data);

            // jika scroll nya maksimum
            function handleScroll() {
                function isScrollAtBottom(element) {
                    return (element.scrollTop() + element.innerHeight() >= element[0].scrollHeight);
                }
                if (isScrollAtBottom($(this)) && !isMaxPage) {
                    isScroll = true;
                    $.ajax({
                        url: `${baseurl}/autocomplete/barang`,
                        type: 'get',
                        dataType: 'json',
                        data: {
                            search: value,
                            page: nextPage,
                        },
                        success: function (data) {
                            getDataAjax(data);
                        },
                    })
                }
            }

            var olElement = document.querySelector('ol.list-group.list-group-numbered.list-group-item-click');
            olElement.addEventListener('scroll', debounce(handleScroll, 500));
        }
    })

}, 500));


const resetAutocomplete = () => {
    nextPage = 1;
    listItem = [];
    search = '';

    $('.btn-close-autocomplete').remove();
    $('.list-group-item-click').remove();
}

$('body').off('click', '.click-list-item');
$('body').on('click', '.click-list-item', function (e) {
    const barcode_barang = $(this).data('barcode_barang');
    const indexData = listItem.findIndex(item => item.barcode_barang === barcode_barang);
    if (indexData !== -1) {
        const getData = listItem[indexData];

        const setInputValue = () => {
            $('input[name="barcode_barang"]').val(getData.barcode_barang);
            $('input[name="nama_barang"]').val(getData.nama_barang);
            $('textarea[name="deskripsi_barang"]').val(getData.deskripsi_barang);
        }
        setInputValue();
    }

    resetAutocomplete();
});

$('body').off('click', '.btn-close-autocomplete');
$('body').on('click', '.btn-close-autocomplete', function (e) {
    resetAutocomplete();
});