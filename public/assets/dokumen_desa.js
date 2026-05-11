

$(document).ready(function () {
    // ===================== DATATABLE DESA =====================
    $('#tableUplaodDokumen').DataTable({
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
            url: BASE_URL + "/desa/get-dokumen-desa",
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
                    return '<div class="text-left">' + data.pemohon_nama + '</div>';
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
                    return '<div class="text-left">' + data.desa_name + '</div>';
                }
            },
            {
                orderable: false,
                data: function (data) {
                    return `
                    <div class="text-center">
                        <a  class="btn btn-sm btn-primary" href="${BASE_URL}/${data.path}" target="_blank">Lihat Dokumen</a>
                    </div>`;
                }
            },
            {
                orderable: false,
                data: function (data) {
                    let text = '';
                    switch (data.status) {
                        case 'diajukan':
                            text = '<span class="badge rounded-pill bg-info">Diajukan</span>';
                            break;
                        case 'diproses':
                            text = '<span class="badge rounded-pill bg-warning">Diproses</span>';
                            break;

                        case 'ditolak':
                            text = '<span class="badge rounded-pill bg-danger">Ditolak</span>';
                            break;
                        case 'selesai':
                            text = '<span class="badge rounded-pill bg-success">Selesai</span>';
                            break;
                    }
                    return text;
                }
            },
            {
                orderable: false,
                data: function (data) {
                    let html = '';
                    if (data.status == 'diajukan') {
                        html += `
                    <div class="text-center">
                        <button type="button" class="btn btn-sm btn-primary" onclick="formDesa(this)" data-id="${data.id}" data-type="edit">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteDokumen(this)" data-id="${data.id}">Hapus</button>
                    </div>`;
                    } else if (data.status == 'ditolak') {
                        html += `
                        <div class="text-center">
                            <small class="text-danger">Alasan Ditolak :<br> ${data.notes}</small>
                        </div>
                    `;
                    }
                    return html;
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

$('#formUploadDokumen').on('submit', function (e) {

    e.preventDefault();
    var postData = new FormData($("#formUploadDokumen")[0]);
    var csrfToken = $('meta[name="csrf-token"]').attr('content'); // Ambil token CSRF
    postData.append('_token', csrfToken); // Sertakan token CSRF di FormData
    $.ajax({
        type: "POST",
        url: BASE_URL + "/desa/upload-dokumen",
        processData: false,
        contentType: false,
        data: postData,
        dataType: "JSON",
        success: function (data) {
            if (data.success == false) {

                data.errors.forEach(function (error) {
                    // Karena error adalah string, kita bisa langsung menampilkannya
                    Swal.fire('Gagal', 'Data gagal Simpan : ' + error, 'error', 'warning');;
                });
            } else if (data.success == true) {
                Swal.fire('Berhasil', 'Data telah disimpan', 'success');
                $('#tableUplaodDokumen').DataTable().ajax.reload(null, false);
                $('#modalUploadDokumen').modal('hide');
            }
        },
    });
    return false;
});
function deleteDokumen(el) {
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
                url: BASE_URL + '/desa/delete-dokumen/' + id,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (res) {
                    if (res.success) {
                        Swal.fire('Berhasil', res.message, 'success');
                        $('#tableUplaodDokumen').DataTable().ajax.reload(null, false);
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