$(document).ready(function () {
    // ===================== DATATABLE DESA =====================
    $('#tableDesa').DataTable({
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
            url: BASE_URL + "/master/get-data-desa",
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
                    return '<div class="text-left">' + data.desa_name + '</div>';
                }
            },
            {
                orderable: false,
                data: function (data) {
                    return '<div class="text-left">' + data.kecamatan_name + '</div>';
                }
            },
            {
                orderable: false,
                data: function (data) {
                    return `
                    <div class="text-center">
                        <button type="button" class="btn btn-sm btn-primary" onclick="formDesa(this)" data-id="${data.id}" data-type="edit">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteDesa(this)" data-id="${data.id}">Hapus</button>
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
function formDesa(el = null) {
    $('#formDesa')[0].reset();
    $('#desa_id').val('');

    if (el && $(el).data('type') === 'edit') {
        const id = $(el).data('id');
        $('#modalDesaTitle').text('Edit Desa');

        $.ajax({
            url: BASE_URL + '/master/get-desa/' + id,
            type: 'GET',
            dataType: 'JSON',
            success: function (res) {
                if (res.success) {
                    $('#desa_id').val(res.data.id);
                    $('#desa').val(res.data.name);
                    $('#kecamatan_id').val(res.data.kecamatan_id);
                    $('#modalAddDesa').modal('show');
                } else {
                    Swal.fire('Error', 'Data desa tidak ditemukan', 'error');
                }
            },
            error: function () {
                Swal.fire('Error', 'Gagal mengambil data desa', 'error');
            }
        });

    } else {
        $('#modalDesaTitle').text('Tambah Desa');
        $('#modalAddDesa').modal('show');
    }
}

// ===================== SUBMIT FORM DESA =====================
$('#formDesa').on('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    formData.append('_token', csrfToken);

    $.ajax({
        type: "POST",
        url: BASE_URL + "/master/desa/store",
        processData: false,
        contentType: false,
        data: formData,
        dataType: "JSON",
        success: function (res) {
            if (!res.success) {
                if (res.errors) {
                    let messages = Object.values(res.errors)
                        .map(err => err.join('<br>'))
                        .join('<br>');
                    Swal.fire('Validasi Gagal', messages, 'warning');
                } else {
                    Swal.fire('Gagal', res.message || 'Data gagal disimpan', 'error');
                }
            } else {
                Swal.fire('Berhasil', res.message, 'success');
                $('#modalAddDesa').modal('hide');
                $('#tableDesa').DataTable().ajax.reload(null, false);
            }
        },
        error: function () {
            Swal.fire('Error', 'Terjadi kesalahan server', 'error');
        }
    });
});

function deleteDesa(el) {
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
                url: BASE_URL + '/master/desa/' + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.success) {
                        Swal.fire('Berhasil', res.message, 'success');
                        $('#tableDesa').DataTable().ajax.reload(null, false);
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

