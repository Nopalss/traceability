"use strict";
// Class definition

var KTDatatableLocalSortDemo = function () {
    // Private functions


    // basic demo
    var demo = function () {
        function formatTanggal(dateString, format = 'indo', withTime = false) {
            if (!dateString) return '-';

            // amankan format MySQL -> ISO
            const date = new Date(dateString.replace(' ', 'T'));
            if (isNaN(date)) return '-';

            // ===============================
            // FORMAT DATABASE (YYYY-MM-DD)
            // ===============================
            if (format === 'ymd') {
                const y = date.getFullYear();
                const m = String(date.getMonth() + 1).padStart(2, '0');
                const d = String(date.getDate()).padStart(2, '0');
                return `${y}-${m}-${d}`;
            }

            // ===============================
            // FORMAT INDONESIA
            // ===============================
            const options = {
                day: '2-digit',
                month: 'long',
                year: 'numeric'
            };

            let result = date.toLocaleDateString('id-ID', options);

            if (withTime) {
                const time = date.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
                result += ` ${time}`;
            }

            return result;
        }
        var datatable = $('#kt_datatable').KTDatatable({
            // datasource definition
            data: {
                type: 'remote',
                source: {
                    read: {
                        url: HOST_URL + '/api/part/incoming/all.php',
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
                    field: 'incoming_date',
                    title: 'Incoming',
                    template: function (row) {
                        return `<span style="font-size:0.875rem">${row.incoming_date}</span>`;
                    }
                }, {
                    field: 'status',
                    title: 'Status',
                    template: function (row) {
                        return `<span style="font-size:0.875rem">${row.status}</span>`;
                    }
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

