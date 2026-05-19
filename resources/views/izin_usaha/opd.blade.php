@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">

            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header  d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $title }}</h5>

                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle"></i> Halaman ini menampilkan pengajuan izin usaha yang telah
                            diverifikasi dan disetujui oleh admin. Anda dapat melihat detail pengajuan, termasuk dokumen
                            yang diunggah, status terkini, dan informasi terkait lainnya. Pastikan untuk memeriksa setiap
                            pengajuan dengan seksama sebelum mengambil keputusan selanjutnya.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="tabelOpd" style="width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Data Usaha</th>
                                        <th width="15%">Deskripsi</th>
                                        <th>Status</th>
                                        <th>Dokumen</th>
                                        <th>BUMDes</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <style>
        .card {
            border-radius: 10px;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn {
            border-radius: 6px;
        }

        .modal-content {
            border-radius: 10px;
        }
    </style>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // ===================== DATATABLE IZIN USAHA =====================
            $('#tabelOpd').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                order: [
                    [0, 'asc']
                ],
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
                    url: BASE_URL + "/opd/get-datatables",
                },
                columns: [{
                        orderable: false,
                        data: function(data) {
                            return '<div class="text-center">' + data.id + '</div>';
                        }
                    },
                    {
                        orderable: false,
                        data: function(data) {
                            let date = new Date(data.created_at);
                            return '<div class="text-left"><strong>No Pengajuan: ' + data
                                .nomor_pengajuan + '</strong><br><strong>Nama Usaha: ' + data
                                .nama_usaha +
                                '</strong><br><small class="text-muted">Tanggal dibuat: ' + date
                                .toLocaleDateString('id-ID') +
                                '</small><br><small>Catatan Verifikasi : ' + (data
                                    .catatan_verifikasi || '-') +
                                '<br>Tgl Verifikasi: ' + (data.tanggal_verifikasi || '-') +
                                '</small><br><small>Catatan Disetujui : ' + (data
                                    .catatan_approval || '-') +
                                '<br> Tgl Disetujui: ' + (data.tanggal_approval || '-') +
                                '</small></div>';
                        }
                    },


                    {
                        orderable: false,
                        data: function(data) {
                            let desc = data.deskripsi_usaha || '-';
                            if (desc.length > 50) {
                                desc = desc.substring(0, 50) + '...';
                            }

                            return '<div class="text-left">' + desc + '</div>';
                        }
                    },
                    {
                        orderable: false,
                        data: function(data) {
                            let badgeClass = 'bg-warning';
                            if (data.status === 'DISETUJUI') {
                                badgeClass = 'bg-success';
                            } else if (data.status === 'VERIFIED') {
                                badgeClass = 'bg-info';
                            } else if (data.status === 'REJECTED') {
                                badgeClass = 'bg-danger';
                            } else if (data.status === 'DRAFT') {
                                badgeClass = 'bg-secondary';
                            }

                            let alertBadge = '';
                            if (data.status === 'DRAFT') {
                                alertBadge =
                                    '<span class="badge bg-info">Siap untuk disubmit</span>';
                            }
                            return 'Status:<br> <span class="badge ' + badgeClass + '">' + data
                                .status +
                                '</span><br>Tahapan saat ini:<br> <span class="badge bg-secondary">' +
                                data
                                .tahapan_saat_ini + '</span><br>' + alertBadge;
                        }
                    },

                    {
                        orderable: false,
                        data: function(data) {

                            return `<div class="text-left">Dok. Pengajuan :<br>
                                        <a href="${BASE_URL}/${data.dok_surat}" target="_blank" class="btn btn-sm btn-outline-primary mb-1">
                                            <i class="bi bi-file-earmark-text"></i> Lihat Surat
                                        </a><br>
                                        Foto Usaha :<br>
                                        <a href="${BASE_URL}/${data.foto_usaha}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-image"></i> Lihat Foto
                                        </a>
                                    </div>`;
                        }
                    },
                    {
                        orderable: false,
                        data: function(data) {
                            return '<div class="text-left">' + (data.nm_bumdes ? data.nm_bumdes :
                                '-') + '</div>';
                        }
                    },
                    {
                        orderable: false,
                        data: function(data) {
                            const isDraft = data.status === 'DISETUJUI';
                            return `
            <div class="text-center">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" 
                        data-bs-toggle="dropdown" aria-expanded="false"
                        ${!isDraft ? 'disabled' : ''}>
                        <i class="mdi mdi-chevron-double-down"></i> Aksi
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <button class="dropdown-item" type="button"
                                onclick="formIzinUsaha(this)" data-id="${data.id}" data-type="edit"
                                ${!isDraft ? 'disabled' : ''}>
                                <i class="bi bi-pencil text-primary"></i> Upload Dok. Teknis
                            </button>
                        </li>
                        
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button class="dropdown-item text-danger" type="button"
                                onclick="deleteIzinUsaha(this)" data-id="${data.id}"
                                ${!isDraft ? 'disabled' : ''}>
                                <i class="bi bi-trash"></i> Tolak Pengajuan
                            </button>
                        </li>
                    </ul>
                </div>
            </div>`;
                        }
                    },


                ],
                rowCallback: function(row, data, iDisplayIndex) {
                    var info = this.fnPagingInfo();
                    var page = info.iPage;
                    var length = info.iLength;
                    var index = page * length + (iDisplayIndex + 1);
                    $('td:eq(0)', row).html(index);
                }
            });
        });


        function submitIzinUsaha(el) {
            const id = $(el).data('id');

            Swal.fire({
                title: 'Yakin ingin submit pengajuan?',
                text: 'Pengajuan akan dikirim untuk verifikasi.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Submit!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: BASE_URL + '/bumdes/submit/' + id,
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('Berhasil', res.message, 'success');
                                $('#tabelIzinUsahaBumdes').DataTable().ajax.reload(null, false);
                            } else {
                                Swal.fire('Gagal', res.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endpush
