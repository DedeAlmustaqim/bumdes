

$(document).ready(function () {
    // ===================== DATATABLE DESA =====================
    $('#tableApprovalDokumen').DataTable({
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
            url: BASE_URL + "/approve-dokumen/get-dokumen",
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
                    return '<div class="text-left">' + data.desa_name + '<br>' + data.kecamatan_name + '</div>';
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




                    if (data.path_approve === null) {
                        button = `<div class="btn-group" role="group">
                                                <button id="btnGroupVerticalDrop1" type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Aksi <i class="mdi mdi-chevron-down"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="btnGroupVerticalDrop1" style="">
                                                    <a class="dropdown-item" href="javascript:void(0)" onclick="uploadDokSah(this)" data-id="${data.id}">Upload Dokumen Final</a>
                                                    <a class="dropdown-item" href="javascript:void(0)" onclick="uploadDokSah(this)" data-id="${data.id}">Detail</a>
                                                </div>
                                            </div>
                                                    `;
                    } else if (data.status === 'selesai') {
                        button = `
                            <div class="btn-group" role="group">
                                                <button id="btnGroupVerticalDrop1" type="button" class="btn btn-primary btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Aksi <i class="mdi mdi-chevron-down"></i>
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="btnGroupVerticalDrop1" style="">
                                                    <a href="${BASE_URL}/${data.path_approve}" target="_blank" class="dropdown-item">Lihat Dokumen Final</a>
                                                    <a class="dropdown-item" href="javascript:void(0)" onclick="uploadDokSah(this)" data-id="${data.id}">Upload Ulang</a>
                                                    <a class="dropdown-item" href="javascript:void(0)" onclick="uploadDokSah(this)" data-id="${data.id}">Detail</a>
                                                </div>
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


function uploadDokSah(el) {


    const id = $(el).data('id');
    $('#id_approval').val(id);
    $('#modalApprovalDokumen').modal('show');
}

$('#formApprovalDokumen').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    formData.append('_token', csrfToken);

    $.ajax({
        type: 'POST',
        url: BASE_URL + '/approve-dokumen/dokumen-final',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'JSON',
        success: function (res) {
            if (!res.success) {
                Swal.fire('Gagal', res.message, 'error');
            } else {
                Swal.fire('Berhasil', res.message, 'success');
                $('#modalApprovalDokumen').modal('hide');
                $('#tableApprovalDokumen').DataTable().ajax.reload();
            }
        },
        error: function () {
            Swal.fire('Error', 'Terjadi kesalahan server', 'error');
        }
    });
});
