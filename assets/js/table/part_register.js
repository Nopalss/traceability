"use strict";

var KTDatatableLocalSortDemo = function () {

    var datatable;

    var demo = function () {

        datatable = $('#kt_datatable').KTDatatable({
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

            layout: {
                scroll: true,
                footer: false,
            },

            sortable: true,
            pagination: true,

            columns: [
                {
                    field: 'part_code',
                    title: 'Part Code',
                    template: row => `<span style="font-size:0.875rem">${row.part_code}</span>`
                },
                {
                    field: 'part_name',
                    title: 'Part Name',
                    template: row => `<span style="font-size:0.875rem">${row.part_name}</span>`
                },
                {
                    field: 'supplier',
                    title: 'Supplier',
                    template: row => `<span style="font-size:0.875rem">${row.name_supplier}</span>`
                },
                {
                    field: 'Actions',
                    title: 'Actions',
                    sortable: false,
                    width: 125,
                    autoHide: false,
                    template: function (row) {
                        return `
                            <a href="${HOST_URL}pages/part_register/detail.php?id_part=${row.id_part}"
                               class="btn btn-sm btn-info btn-icon mr-2">
                                <i class="flaticon-eye"></i>
                            </a>
                            <a href="${HOST_URL}pages/part_register/edit.php?id_part=${row.id_part}"
                               class="btn btn-sm btn-warning btn-icon mr-2">
                                <i class="flaticon-edit"></i>
                            </a>
                            <a onclick="confirmDeleteTemplate('${row.part_code}', 'controllers/part_register/delete.php')"
                               class="btn btn-sm btn-danger btn-icon">
                                <i class="flaticon-delete"></i>
                            </a>
                        `;
                    }
                }
            ]
        });

        // 🔍 SEARCH BUTTON (BENAR & STABIL)
        $('.btn-outline-success').on('click', function (e) {
            e.preventDefault();

            let query = {};

            let partCode = $('#kt_datatable_search_query').val().trim();
            let supplier = $('#kt_datatable_search_status').val();

            if (partCode !== '') {
                query.part_code = partCode;
            }

            if (supplier !== '') {
                query.supplier = supplier;
            }

            // SET QUERY SEKALI
            datatable.setDataSourceParam('query', query);

            // RESET PAGE KE 1
            datatable.setDataSourceParam('pagination.page', 1);

            // LOAD SEKALI
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
