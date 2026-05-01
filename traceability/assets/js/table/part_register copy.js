"use strict";
// Class definition

var KTDatatableLocalSortDemo = function () {


    // basic demo
    var demo = function () {
        var datatable = $('#kt_datatable').KTDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: HOST_URL + 'api/part_register/index.php',
                    },
                },
                pageSize: 10,
                serverPaging: false,
                serverFiltering: true,
                serverSorting: false,
                saveState: {
                    cookie: false,
                    webstorage: false,
                },
            },

            // layout definition
            layout: {
                scroll: true,   // biar tabel scrollable (horizontal/vertical)
                footer: false,
            },

            // column sorting
            sortable: true,

            pagination: true,

            search: {
                input: $('#kt_datatable_search_query'),
                key: 'generalSearch'
            },
            // columns definition
            columns: [
                {
                    field: 'ref_number',
                    title: 'Ref No',
                    template: function (row) {
                        return `<span style="font-size:0.875rem">${row.ref_number}</span>`;
                    }
                }, {
                    field: 'part_code',
                    title: 'Part',
                    template: function (row) {
                        return `<span style="font-size:0.875rem">${row.part_code}</span>`;
                    }
                }, {
                    field: 'qty',
                    title: 'Quantity',
                    template: function (row) {
                        return `<span style="font-size:0.875rem">${row.qty}</span>`;
                    }
                }, {
                    field: 'supplier',
                    title: 'Supplier',
                    template: function (row) {
                        return `<span style="font-size:0.875rem">${row.supplier}</span>`;
                    }
                }, {
                    field: 'status',
                    title: 'Status',
                    template: function (row) {
                        return `<span style="font-size:0.875rem">${row.status}</span>`;
                    }
                },
                {
                    field: 'part_code',
                    title: 'part',
                }, {
                    field: 'name',
                    title: 'Name',
                },
                {
                    field: 'paket_internet',
                    title: 'Paket',
                    template: function (row) {

                        return `<span>${row.paket_internet} mbps</span>`;
                    },
                }, {
                    field: 'is_verified',
                    title: 'Status',
                    autoHide: false,
                    // callback function support for column rendering
                    template: function (row) {
                        var status = {
                            'Verified': {
                                'title': 'Verified',
                                'state': 'success'
                            },
                            'Unverified': {
                                'title': 'Unverified',
                                'state': 'danger'
                            },

                        };
                        return '<span class="label label-' + status[row.is_verified].state + ' label-dot mr-2"></span><span class="font-weight-bold text-' + status[row.is_verified].state + '">' +
                            status[row.is_verified].title + '</span>';
                    },
                }, {
                    field: 'Actions',
                    title: 'Actions',
                    sortable: false,
                    width: 125,
                    overflow: 'visible',
                    autoHide: false,
                    template: function (row) {
                        var status = {
                            'Pending': {
                                'title': 'Pending',
                                'state': 'info'
                            },
                            'Rescheduled': {
                                'title': 'Rescheduled',
                                'state': 'warning'
                            },
                            'Cancelled': {
                                'title': 'Cancelled',
                                'state': 'danger'
                            },
                            'Done': {
                                'title': 'Done',
                                'state': 'success'
                            }
                        };
                        return `\
                        <div class="dropdown dropdown-inline">\
                            <a href="javascript:;" class="btn btn-sm btn-light btn-text-primary btn-icon mr-2" data-toggle="dropdown">\
                                <span class="svg-icon svg-icon-md">\
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                                            <rect x="0" y="0" width="24" height="24"/>\
                                            <path d="M5,8.6862915 L5,5 L8.6862915,5 L11.5857864,2.10050506 L14.4852814,5 L19,5 L19,9.51471863 L21.4852814,12 L19,14.4852814 L19,19 L14.4852814,19 L11.5857864,21.8994949 L8.6862915,19 L5,19 L5,15.3137085 L1.6862915,12 L5,8.6862915 Z M12,15 C13.6568542,15 15,13.6568542 15,12 C15,10.3431458 13.6568542,9 12,9 C10.3431458,9 9,10.3431458 9,12 C9,13.6568542 10.3431458,15 12,15 Z" fill="#000000"/>\
                                        </g>\
                                    </svg>\
                                </span>\
                            </a>\
                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">\
                                <ul class="navi flex-column navi-hover py-2">\
                                    <li class="navi-header font-weight-bolder text-uppercase font-size-xs text-primary pb-2">\
                                        Choose an action:\
                                    </li>\
                                   ${row.is_verified === "Unverified"
                                ? `<li class="navi-item cursor-pointer">
                                            <a href="${HOST_URL + 'pages/request/ikr/create.php?id=' + row.registrasi_key}" class="navi-link">
                                                <span class="navi-icon"><i class="flaticon2-check-mark text-success"></i></span>
                                                <span class="navi-text">Verified</span>
                                            </a>
                                        </li>
                                        <li class="navi-item cursor-pointer">
                                            <a href='${HOST_URL + 'pages/registrasi/update.php?id=' + row.registrasi_key}' class="navi-link">
                                                <span class="navi-icon "><i class="la la-pencil-alt text-warning"></i></span>
                                                <span class="navi-text">Edit</span>
                                            </a>
                                        </li >`
                                : ""}
<li class="navi-item cursor-pointer">\
    <a onclick="confirmDeleteTemplate('${row.registrasi_key}', 'controllers/registrasi/delete.php')" class="navi-link">\
        <span class="navi-icon "><i class="la la-trash text-danger"></i></span>\
        <span class="navi-text">Hapus</span>\
    </a>\
</li>\
<li class="navi-item cursor-pointer">\
    <a class="navi-link btn-detail-registrasi" data-id="${row.registrasi_id}" data-name="${row.name}" data-location="${row.location}" data-phone="${row.phone}" data-paket="${row.paket_internet}" data-verified="${row.is_verified}" data-date="${row.date}" data-time="${row.time}">\
        <span class="navi-icon"><i class="flaticon-eye text-info"></i></span>\
        <span class="navi-text"> Detail</span>\
    </a>\
</li>\
                                </ul >\
                            </div >\
                        </div >\
`;
                    },
                }],
        });

        $('#kt_datatable_search_status').on('change', function () {
            datatable.search($(this).val().toLowerCase(), 'status');
        });

        $('#kt_datepicker_3').datepicker({
            format: 'mm/dd/yyyy',
            autoclose: true,
        }).on('changeDate', function (e) {
            let val = $(this).val(); // contoh: 09/18/2025
            if (val) {
                let parts = val.split('/');
                let formatted = parts[2] + '-' + parts[0] + '-' + parts[1]; // 2025-09-18
                datatable.search($(this).val(), 'date');
            }
        });

        $('#kt_datatable_search_status, #kt_datatable_search_type, #kt_datatable_search_tech').selectpicker();
    };

    return {
        // public functions
        init: function () {
            demo();
        },
    };
}();



jQuery(document).ready(function () {
    KTDatatableLocalSortDemo.init();
});
