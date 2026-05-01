"use strict";
// Class definition

var KTDatatableLocalSortDemo = function () {
    // Private functions

    // basic demo
    var demo = function () {
        var datatable = $('#kt_datatable').KTDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: HOST_URL + '/api/role/index.php',
                    },
                },
                pageSize: 10,
                serverPaging: false,
                serverFiltering: true,
                serverSorting: false,
                saveState: {
                    cookie: true,
                    webstorage: true,
                },
            },
            // layout definition
            layout: {
                scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
                footer: false, // display/hide footer
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
                    field: 'role_id',
                    title: '#',
                    textAlign: 'center',
                    width: 30,
                    template: function (row, index, datatable) {
                        return index + 1;
                    },
                },
                {
                    field: 'role_name',
                    title: 'Role',
                }, {
                    field: 'Actions',
                    title: 'Actions',
                    sortable: false,
                    width: 125,
                    overflow: 'visible',
                    autoHide: false,
                    template: function (row) {
                        return `\
                               <a href="${HOST_URL}pages/role/detail.php?id=${row.role_id}"
                               class="btn btn-sm btn-info btn-icon mr-2">
                                <i class="flaticon-eye"></i>
                            </a>
                            <a href="${HOST_URL}pages/role/edit.php?id=${row.role_id}"
                               class="btn btn-sm btn-warning btn-icon mr-2">
                                <i class="flaticon-edit"></i>
                            </a>
                            <a onclick="confirmDeleteTemplate('${row.role_id}', 'controllers/role/delete.php')"
                               class="btn btn-sm btn-danger btn-icon">
                                <i class="flaticon-delete"></i>
                            </a>
                    `;
                    },
                }],
        });

        $('#kt_datatable_search_status').on('change', function () {
            datatable.search($(this).val().toLowerCase(), 'Status');
        });

        $('#kt_datatable_search_type').on('change', function () {
            datatable.search($(this).val().toLowerCase(), 'Type');
        });

        $('#kt_datatable_search_status, #kt_datatable_search_type').selectpicker();
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
