$(document).ready(function () {
    $('#tableJenisLayanan').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: BASE_URL + '/master/jenis-layanan-data',
        },
        columns: [
            { data: 'id', orderable: false },
            { data: 'name', orderable: false },
            {
                data: null,
                orderable: false,
                render: function (data) {
                    return `
                        <button class="btn btn-sm btn-primary" onclick="formJenisLayanan(this)" data-id="${data.id}" data-type="edit">Edit</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteJenisLayanan(this)" data-id="${data.id}">Hapus</button>
                    `;
                }
            }
        ]
    });
});

function formJenisLayanan(el = null) {
    $('#formJenisLayanan')[0].reset();
    $('#jenis_layanan').val('');

    if (el && $(el).data('type') === 'edit') {
        const id = $(el).data('id');
        $.get(`${BASE_URL}/master/get-jenis-layanan/` + id, function (res) {
            $('#jenis_layanan_id').val(res.data.id);
            $('#jenis_layanan').val(res.data.name);
            $('#modalJenisLayanan').modal('show');
        }).fail(function () {
            Swal.fire('Error', 'Gagal mengambil data', 'error');
        });
    } else {
        $('#modalJenisLayanan').modal('show');
    }
}

$('#formJenisLayanan').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    formData.append('_token', csrfToken);

    $.ajax({
        type: 'POST',
        url: BASE_URL + '/master/jenis-layanan/store',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'JSON',
        success: function (res) {
            if (!res.success) {
                Swal.fire('Gagal', res.message, 'error');
            } else {
                Swal.fire('Berhasil', res.message, 'success');
                $('#modalJenisLayanan').modal('hide');
                $('#tableJenisLayanan').DataTable().ajax.reload();
            }
        },
        error: function () {
            Swal.fire('Error', 'Terjadi kesalahan server', 'error');
        }
    });
});

function deleteJenisLayanan(el) {
    const id = $(el).data('id');
    Swal.fire({
        title: 'Yakin hapus?',
        text: 'Data tidak bisa dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: BASE_URL + '/master/jenis-layanan/delete/' + id,
                type: 'DELETE',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    if (res.success) {
                        Swal.fire('Berhasil', res.message, 'success');
                        $('#tableJenisLayanan').DataTable().ajax.reload();
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
