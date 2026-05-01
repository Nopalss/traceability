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
                    field: 'pp_code',
                    title: '#',
                    textAlign: 'center',
                    width: 30,
                    template: function (row, index) {
                        return `<span style="font-size:0.975rem">${index + 1}</span>`;
                    },
                },
                {
                    field: 'production_date',
                    title: 'Date',
                    template: row => `<span style="font-size:0.875rem">${row.production_date}</span>`
                },
                {
                    field: 'total_shift',
                    title: 'Total Shift',
                    template: row => `<span style="font-size:0.875rem">${row.total_shift}</span>`
                },
                {
                    field: 'total_line',
                    title: 'Total Line',
                    template: row => `<span style="font-size:0.875rem">${row.total_line}</span>`
                },
                {
                    field: 'total_part_assy',
                    title: 'Total Part Assy',
                    template: row => `<span style="font-size:0.875rem">${row.total_part_assy}</span>`
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
                            <a onclick="confirmDeleteTemplate('${row.pp_code}', 'controllers/production_planning/delete.php')"
                               class="btn btn-sm btn-danger btn-icon">
                                <i class="flaticon-delete"></i>
                            </a>
                        `;
                    }
                }
            ]
        });

        // ================================
        // 🔍 SEARCH BUTTON
        // ================================
        $('.btn-outline-success').on('click', function (e) {
            e.preventDefault();

            let query = {};

            let date = $('#datepicker').val();


            if (date !== '') {
                query.production_date = date;
            }

            datatable.setDataSourceParam('query', query);
            datatable.setDataSourceParam('pagination.page', 1);
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