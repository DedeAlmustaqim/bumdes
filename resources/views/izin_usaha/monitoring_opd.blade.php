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
                            Halaman ini menampilkan semua pengajuan izin usaha yang masuk ke dalam sistem (Monitoring), hanya untuk izin usaha yang diajukan ke OPD Anda.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="tabelMonitoringOpd" style="width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Data Usaha</th>
                                        <th width="15%">Deskripsi</th>
                                        <th>Status</th>
                                        <th>Dokumen</th>
                                        <th>BUMDes</th>
                                        <th>OPD Tujuan</th>
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
            $('#tabelMonitoringOpd').DataTable({
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
                    url: BASE_URL + "/opd/get-datatables-monitoring",
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
                                .toLocaleDateString('id-ID') + '</small><br><small>Catatan Verifikasi : ' + (data.catatan_verifikasi || '-') 
                                    + '<br>Tgl Verifikasi: ' + (data.tanggal_verifikasi || '-') + '</small><br><small>Catatan Disetujui : ' + (data.catatan_approval || '-') + 
                                        '<br> Tgl Disetujui: ' + (data.tanggal_approval || '-') + '</small></div>';
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

                            return `<div class="text-left">Dok Pengajuan :<br>
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
                            if (data.opd_id == 0) {
                                return '<span class="badge bg-warning">Belum ditetapkan</span>';
                            } else {
                                return '<span class="badge bg-info">' + data.nm_opd + '</span>';
                            }
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



       
    </script>
@endpush
