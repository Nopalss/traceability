<?php
require_once __DIR__ . '/../../includes/config.php';

$_SESSION['menu'] = 'ng_type';
$_SESSION['table'] = 'ng_type';
$_SESSION['halaman'] = 'NG Type';
$_SESSION['subHalaman'] = '';

require __DIR__ . '/../../includes/header.php';
require __DIR__ . '/../../includes/clear_temp_session.php';
require __DIR__ . '/../../includes/aside.php';
require __DIR__ . '/../../includes/navbar.php';
?>

<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container">

            <div class="card card-custom">
                <div class="card-header flex-wrap border-0 pt-6 pb-0">

                    <div class="card-title">
                        <h3 class="card-label">NG Type</h3>
                    </div>

                    <div class="card-toolbar">
                        <button class="btn btn-primary font-weight-bolder" id="addNgBtn">

                            <span class="svg-icon svg-icon-md">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24">
                                    <circle fill="#000" opacity="0.3" cx="12" cy="12" r="10" />
                                    <path d="M11 7h2v10h-2zM7 11h10v2H7z" fill="#000" />
                                </svg>
                            </span>

                            Add NG Type

                        </button>
                    </div>

                </div>

                <div class="card-body">

                    <table class="table table-bordered table-hover" id="ngTable">

                        <thead class="thead-light">

                            <tr>
                                <th width="60">ID</th>
                                <th width="200">NG TITLE</th>
                                <th>NG DESCRIPTION</th>
                                <th width="120">STATUS</th>
                                <th width="150">ACTION</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php

                            $q = $pdo->query("SELECT * FROM tbl_ng_type");

                            foreach ($q as $r) {

                                $statusColor = $r['status'] == 'ACTIVE' ? 'success' : 'secondary';

                                echo "

<tr>

<td>{$r['id']}</td>

<td>
<strong>{$r['ng_code']}</strong>
</td>

<td style='white-space:pre-line'>
{$r['ng_name']}
</td>

<td>
<span class='badge badge-$statusColor'>
{$r['status']}
</span>
</td>

<td>

<button class='btn btn-sm btn-warning editNg'
data-id='{$r['id']}'
data-code='" . htmlspecialchars($r['ng_code'], ENT_QUOTES) . "'
data-name='" . htmlspecialchars($r['ng_name'], ENT_QUOTES) . "'
data-status='{$r['status']}'
>

Edit

</button>

<button class='btn btn-sm btn-danger deleteNg'
data-id='{$r['id']}'>
Delete
</button>

</td>

</tr>

";
                            }

                            ?>

                        </tbody>

                    </table>

                </div>
            </div>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>

<script>
    /* =========================
ADD
========================= */

    $("#addNgBtn").click(function() {

        Swal.fire({

            title: 'Add NG Type',

            html: `

<input id="ng_code"
class="swal2-input"
placeholder="NG Title">

<textarea id="ng_name"
class="swal2-textarea"
placeholder="NG Description"></textarea>

<select id="ng_status"
class="swal2-input">

<option value="ACTIVE">ACTIVE</option>
<option value="INACTIVE">INACTIVE</option>

</select>

`,

            confirmButtonText: 'SAVE',

            preConfirm: () => {

                return {

                    code: $('#ng_code').val(),
                    name: $('#ng_name').val(),
                    status: $('#ng_status').val()

                }

            }

        }).then(res => {

            if (!res.isConfirmed) return

            $.post('ajax_ng_type.php', {

                action: 'insert',
                code: res.value.code,
                name: res.value.name,
                status: res.value.status

            }, function() {

                location.reload()

            })

        })

    })


    /* =========================
    EDIT
    ========================= */

    $('.editNg').click(function() {

        let id = $(this).data('id')
        let code = $(this).data('code')
        let name = $(this).data('name')
        let status = $(this).data('status')

        Swal.fire({

            title: 'Edit NG Type',

            html: `

<input id="ng_code"
class="swal2-input"
value="${code}">

<textarea id="ng_name"
class="swal2-textarea">${name}</textarea>

<select id="ng_status"
class="swal2-input">

<option value="ACTIVE">ACTIVE</option>
<option value="INACTIVE">INACTIVE</option>

</select>

`,

            didOpen: () => {
                $('#ng_status').val(status)
            },

            confirmButtonText: 'UPDATE',

            preConfirm: () => {

                return {

                    code: $('#ng_code').val(),
                    name: $('#ng_name').val(),
                    status: $('#ng_status').val()

                }

            }

        }).then(res => {

            if (!res.isConfirmed) return

            $.post('ajax_ng_type.php', {

                action: 'update',
                id: id,
                code: res.value.code,
                name: res.value.name,
                status: res.value.status

            }, function() {

                location.reload()

            })

        })

    })


    /* =========================
    DELETE
    ========================= */

    $('.deleteNg').click(function() {

        let id = $(this).data('id')

        Swal.fire({

            title: 'Delete NG Type?',
            icon: 'warning',
            showCancelButton: true

        }).then(res => {

            if (!res.isConfirmed) return

            $.post('ajax_ng_type.php', {

                action: 'delete',
                id: id

            }, function() {

                location.reload()

            })

        })

    })
</script>