"use strict";

var KTDatatableLocalSortDemo = function () {

    var datatable;

    var demo = function () {

        datatable = $('#kt_datatable').KTDatatable({
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: HOST_URL + 'api/production_planning/index.php',
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

            layout: {
                scroll: true,
                footer: false,
            },

            sortable: true,
            pagination: true,

            columns: [
                {
                    field: 'line_name',
                    title: 'Line',
                    template: row => `<span style="font-size:0.975rem">${row.line_name}</span>`
                },
                {
                    field: 'product_code',
                    title: 'Product Code',
                    template: row => `<span style="font-size:0.875rem">${row.product_code}</span>`
                },
                {
                    field: 'production_date',
                    title: 'Date',
                    template: row => `<span style="font-size:0.875rem">${row.production_date}</span>`
                },
                {
                    field: 'total_qty',
                    title: 'QTY',
                    template: row => `<span style="font-size:0.875rem">${row.total_qty}</span>`
                },
                {
                    field: 'status',
                    title: 'Status',
                    template: row => `<span style="font-size:0.875rem">${row.status}</span>`
                },
                {
                    field: 'Actions',
                    title: 'Actions',
                    sortable: false,
                    width: 125,
                    autoHide: false,
                    template: function (row) {
                        return `
                            <a href="${HOST_URL}pages/production_planning/detail.php?pp_code=${row.pp_code}"
                               class="btn btn-sm btn-info btn-icon mr-2">
                                <i class="flaticon-eye"></i>
                            </a>
                            <a href="${HOST_URL}pages/production_planning/edit.php?pp_code=${row.pp_code}"
                               class="btn btn-sm btn-warning btn-icon mr-2">
                                <i class="flaticon-edit"></i>
                            </a>
                            <a onclick="confirmDeleteTemplate('${row.pp_code}', 'controllers/production_planning/delete.php')"
                               class="btn btn-sm btn-danger btn-icon">
                                <i class="flaticon-delete"></i>
                            </a>
                        `;
                    }
                }
            ]
        });

        // 🔍 SEARCH BUTTON (BENAR & STABIL)
        // 🔍 SEARCH BUTTON
        $('.btn-outline-success').on('click', function (e) {
            e.preventDefault();

            let query = {};

            let keyword = $('#kt_datatable_search_query').val().trim();
            let line = $('#kt_datatable_search_line').val();
            let date = $('#datepicker').val();

            // search bebas (line_id atau product_code)
            if (keyword !== '') {
                query.keyword = keyword;
            }

            // filter line
            if (line !== '') {
                query.line_id = line;
            }

            // filter tanggal
            if (date !== '') {
                query.production_date = date;
            }

            // kirim parameter ke datatable
            datatable.setDataSourceParam('query', query);

            // reset page ke 1 biar gak nyangkut di page lama
            datatable.setDataSourceParam('pagination.page', 1);

            // reload data
            datatable.load();
        });

    };

    return {
        init: function () {
            demo();
        }
    };
}();

jQuery(document).ready(function () {
    KTDatatableLocalSortDemo.init();
});
