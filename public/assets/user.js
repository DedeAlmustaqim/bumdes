$(document).ready(function () {
    // ===================== DATATABLE DESA =====================
    $('#tableUserKecamatan').DataTable({
        processing: true,
        serverSide: true,
        destroy: true,
        bPaginate: true,
        bLengthChange: true,
        bFilter: true,
        bInfo: true,
        bAutoWidth: true,
        order: [[0, 'asc']],
        language: {
            lengthMenu: "Tampilkan _MENU_ item per halaman",
            zeroRecords: "Tidak ada data yang ditampilkan",
            info: "Menampilkan Halaman _PAGE_ dari _PAGES_",
            infoEmpty: "Tidak ada data yang ditampilkan",
            infoFiltered: "(filtered from _MAX_ total records)",
            search: "Cari",
            paginate: {
                first: "Awal",
                last: "Akhir",
                next: ">",
                previous: "<"
            },
        },
        displayLength: 25,
        ajax: {
            url: BASE_URL + "/admin/get-user-kecamatan",
        },
        columns: [
            {
                orderable: false,
                data: function (data) {
                    return '<div class="text-left">' + data.id + '</div>';
                }
            },
            {
                orderable: false,
                data: function (data) {
                    return '<div class="text-left">' + data.name + '</div>';
                }
            },
            {
                orderable: false,
                data: function (data) {
                    return '<div class="text-left">' + data.username + '</div>';
                }
            },
            {
                orderable: false,
                data: function (data) {
                    return `
                    <div class="text-center">
                        <button type="button" class="btn btn-sm btn-primary" onclick="formUserKecamatan(this)" data-id="${data.id}" data-type="edit">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteUserKecamatan(this)" data-id="${data.id}">Hapus</button>
                    </div>`;
                }
            },
        ],
        rowCallback: function (row, data, iDisplayIndex) {
            var info = this.fnPagingInfo();
            var page = info.iPage;
            var length = info.iLength;
            var index = page * length + (iDisplayIndex + 1);
            $('td:eq(0)', row).html(index);
        }
    });
});

// ===================== FORM ADD / EDIT DESA =====================
function formUserKecamatan(el = null) {
    $('#formUserKecamatan')[0].reset();
    $('#user_id_kecamatan').val('');

    if (el && $(el).data('type') === 'edit') {
        const id = $(el).data('id');
        $('#modalUserKecamatanTitle').text('Edit Desa');

        $.ajax({
            url: BASE_URL + '/admin/get-user-kecamatan-by-id/' + id,
            type: 'GET',
            dataType: 'JSON',
            success: function (res) {
                if (res.success) {
                    $('#user_id_kecamatan').val(res.data.id);
                    $('#name_user_kecamatan').val(res.data.name);
                    $('#username_user_kecamatan').val(res.data.username);
                    $('#kecamatan_id_user_kecamatan').val(res.data.kecamatan_id);
                    $('#modalUserKecamatan').modal('show');
                } else {
                    Swal.fire('Error', 'Data User tidak ditemukan', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Gagal mengambil data desa', 'error');
            }
        });

    } else {
        $('#modalUserKecamatanTitle').text('Tambah User Kecamatan');
        $('#modalUserKecamatan').modal('show');
    }
}

// ===================== SUBMIT FORM DESA =====================
$('#formUserKecamatan').on('submit', function (e) {
    e.preventDefault();

    // 💡 Langkah 1: Bersihkan semua pesan error dan kelas 'is-invalid' sebelum submit baru
    $('.text-danger').html('');
    $('.form-control, .form-select').removeClass('is-invalid');

    const formData = new FormData(this);
    // ... (append token, dll. jika diperlukan) ...

    $.ajax({
        type: "POST",
        url: BASE_URL + "/admin/user-kecamatan-store", // URL TETAP SAMA
        processData: false,
        contentType: false,
        data: formData, // Termasuk user_id_kecamatan (untuk update) atau kosong (untuk insert)
        dataType: "JSON",

        success: function (res) {
            // Menangani respons 200 (Update) atau 201 (Insert)
            Swal.fire('Berhasil', res.message, 'success');
            $('#modalUserKecamatan').modal('hide');
            $('#tableUserKecamatan').DataTable().ajax.reload(null, false);
        },

        error: function (jqXHR) {
            const statusCode = jqXHR.status;
            const res = jqXHR.responseJSON;

            // 💡 Penanganan Error Validasi (Kode 422) tetap sama untuk Insert/Update
            if (statusCode === 422 && res && res.error) {
                $.each(res.error, function (key, messages) {
                    const errorElementId = '#error-' + key;
                    $(errorElementId).html(messages.join('<br>'));
                    $(`[name="${key}"]`).addClass('is-invalid');
                });

            } else {
                // 💡 Penanganan Error Server (Kode 500, dll)
                let errorMessage = 'Terjadi kesalahan server.';
                let errorTitle = 'Error ' + statusCode;

                if (res && res.message) {
                    errorMessage = res.message;
                } else {
                    console.error("Detail Error:", jqXHR.responseText);
                    errorMessage += ' (Status: ' + statusCode + ')';
                }

                Swal.fire(errorTitle, errorMessage, 'error');
            }
        }
    });
});

function deleteUserKecamatan(el) {
    const id = $(el).data('id');

    Swal.fire({
        title: 'Yakin ingin menghapus?',
        text: 'Data tidak bisa dikembalikan jika dihapus.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: BASE_URL + '/admin/del-user-kecamatan/' + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.success) {
                        Swal.fire('Berhasil', res.message, 'success');
                        $('#tableUserKecamatan').DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire('Gagal', res.message, 'error');
                    }
                },
                error: function () {
                    Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                }
            });
        }
    });
}

