

$(document).ready(function () {
    // ===================== DATATABLE DESA =====================
    $('#tableVerifikasiDokumen').DataTable({
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
            url: BASE_URL + "/verifikasi/get-verifikasi",
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
                    let button = '';

                    if (data.status === 'diajukan') {
                        button = `
                            <div class="text-center">
                                <button type="button" class="btn btn-sm btn-success" onclick="prosesDokumen(this)" data-id="${data.id}" data-type="Proses">Proses</button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="tolakDokumen(this)" data-id="${data.id}">Tolak</button>
                            </div>
                        `;
                    } else if (data.status === 'ditolak') {
                        button = `
                            <div class="text-center">
                                <small class="text-danger">Alasan Ditolak : ${data.notes}</small>
                            </div>
                        `;
                    } else if (data.status === 'diproses') {
                        button = `
                            <div class="text-center">
                                <small class="text-success">Lanjutkan upload Dokumen Sah</small>
                            </div>
                        `;
                    }

                    return button;
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

function prosesDokumen(el) {
    const id = $(el).data('id');
    Swal.fire({
        title: 'Lanjutkan Proses Dokumen?',
        text: '',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya Lanjutkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: BASE_URL + '/verifikasi/proses-dokumen/' + id,
                type: 'PUT',
                data: { _token: $('meta[name="csrf-token"]').attr('content') },
                success: function (res) {
                    if (res.success) {
                        Swal.fire('Berhasil', res.message, 'success');
                        $('#tableVerifikasiDokumen').DataTable().ajax.reload();
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


function tolakDokumen(el) {


    const id = $(el).data('id');
    $('#id_pengajuan').val(id);
    $('#modalTolakDokumen').modal('show');
}

$('#formTolakDokumen').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    formData.append('_token', csrfToken);

    $.ajax({
        type: 'POST',
        url: BASE_URL + '/verifikasi/tolak-dokumen',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'JSON',
        success: function (res) {
            if (!res.success) {
                Swal.fire('Gagal', res.message, 'error');
            } else {
                Swal.fire('Berhasil', res.message, 'success');
                $('#modalTolakDokumen').modal('hide');
                $('#tableVerifikasiDokumen').DataTable().ajax.reload();
            }
        },
        error: function () {
            Swal.fire('Error', 'Terjadi kesalahan server', 'error');
        }
    });
});
