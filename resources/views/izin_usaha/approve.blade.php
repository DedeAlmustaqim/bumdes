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
                            Halaman ini menampilkan pengajuan izin usaha berstatus <strong>VERIFIED / Sudah
                                Diverifikasi</strong>.
                            Silakan lakukan Persetujuan dengan memeriksa data usaha, dokumen surat, dan foto usaha.
                            Pilih <strong>Persetujuan</strong> (Setujui atau Tolak),
                            serta isi <strong>Catatan Persetujuan</strong>, kemudian klik tombol
                            <span class="badge bg-success">Simpan</span> untuk menyimpan hasil Persetujuan.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="tabelApprove" style="width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Data Usaha</th>
                                        <th width="15%">Deskripsi</th>
                                        <th>Status</th>
                                        <th>Dokumen</th>
                                        <th>BUMdes</th>
                                        <th>OPD Tujuan</th>
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

    <!-- Modal Izin Usaha -->
    <div class="modal fade" id="modalIzinUsaha" tabindex="-1" aria-labelledby="modalIzinUsahaTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalIzinUsahaTitle"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formIzinUsaha" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="izin_usaha_id" id="izin_usaha_id">

                        <div class="mb-3">
                            <label for="nama_usaha" class="form-label fw-semibold">Nama Usaha <span
                                    class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="nama_usaha" id="nama_usaha"
                                placeholder="Masukkan nama usaha" maxlength="255">
                            <div class="text-danger small mt-1" id="error-nama_usaha"></div>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi_usaha" class="form-label fw-semibold">Deskripsi Usaha <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" name="deskripsi_usaha" id="deskripsi_usaha"
                                placeholder="Masukkan deskripsi usaha (minimal 20 karakter)" rows="4"></textarea>
                            <small class="text-muted">Minimal 20 karakter</small>
                            <div class="text-danger small mt-1" id="error-deskripsi_usaha"></div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="latitude" class="form-label fw-semibold">Latitude</label>
                                <input class="form-control" type="number" name="latitude" id="latitude"
                                    placeholder="-90 hingga 90" step="0.0001">
                                <div class="text-danger small mt-1" id="error-latitude"></div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="longitude" class="form-label fw-semibold">Longitude</label>
                                <input class="form-control" type="number" name="longitude" id="longitude"
                                    placeholder="-180 hingga 180" step="0.0001">
                                <div class="text-danger small mt-1" id="error-longitude"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="dok_surat" class="form-label fw-semibold">Dokumen Surat <span
                                    class="text-danger">*</span></label>
                            <input class="form-control" type="file" name="dok_surat" id="dok_surat"
                                accept=".pdf,.doc,.docx">
                            <small class="text-muted">Format: PDF, DOC, DOCX (Max 5MB)</small>
                            <div class="text-danger small mt-1" id="error-dok_surat"></div>
                        </div>

                        <div class="mb-3">
                            <label for="foto_usaha" class="form-label fw-semibold">Foto Usaha</label>
                            <input class="form-control" type="file" name="foto_usaha" id="foto_usaha"
                                accept="image/*">
                            <small class="text-muted">Format: JPG, PNG, JPEG (Max 2MB)</small>
                            <div class="text-danger small mt-1" id="error-foto_usaha"></div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Simpan
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Approval Izin Usaha -->
    <div class="modal fade" id="modalApproveIzinUsaha" tabindex="-1" aria-labelledby="modalApproveIzinUsahaTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalApproveIzinUsahaTitle"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formApproveIzinUsaha" method="POST">
                        @csrf
                        <input type="hidden" name="izin_usaha_id" id="approve_izin_usaha_id">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Hasil Approval <span
                                    class="text-danger">*</span></label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_approved"
                                        id="is_approved_yes" value="1">
                                    <label class="form-check-label" for="is_approved_yes">Setujui</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="is_approved"
                                        id="is_approved_no" value="0">
                                    <label class="form-check-label" for="is_approved_no">Tolak</label>
                                </div>
                            </div>
                            <div class="text-danger small mt-1" id="error-is_approved"></div>
                        </div>

                        <div class="mb-3">
                            <label for="catatan_approval" class="form-label fw-semibold">Catatan Approval <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" name="catatan_approval" id="catatan_approval" rows="4"
                                placeholder="Masukkan catatan approval minimal 10 karakter"></textarea>
                            <div class="text-danger small mt-1" id="error-catatan_approval"></div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Proses Approval
                            </button>
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle"></i> Batal
                            </button>
                        </div>
                    </form>
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
            $('#tabelApprove').DataTable({
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
                    url: BASE_URL + "/approver/get-datatables-izin-usaha",
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
                                .toLocaleDateString('id-ID') + '</small><br></div>';

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
                    {
                        orderable: false,
                        data: function(data) {
                            const canApprove = data.status === 'VERIFIED';
                            return `
            <div class="text-center">
                <div class="btn-group">
                    <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle"
                        data-bs-toggle="dropdown" aria-expanded="false"
                        ${!canApprove ? 'disabled' : ''}>
                        <i class="mdi mdi-chevron-double-down"></i> Aksi
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <button class="dropdown-item" type="button"
                                onclick="openApproveModal(this)" data-id="${data.id}"
                                ${!canApprove ? 'disabled' : ''}>
                                <i class="bi bi-check2-circle text-success"></i> Approval
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



        function openApproveModal(el) {
            $('#formApproveIzinUsaha')[0].reset();
            $('#approve_izin_usaha_id').val($(el).data('id'));
            $('.text-danger').html('');
            $('.form-control, .form-select, .form-check-input').removeClass('is-invalid');
            $('#modalApproveIzinUsahaTitle').text('Approval Pengajuan Izin Usaha');
            $('#modalApproveIzinUsaha').modal('show');
        }

        $('#formApproveIzinUsaha').on('submit', function(e) {
            e.preventDefault();

            $('.text-danger').html('');
            $('.form-control, .form-select, .form-check-input').removeClass('is-invalid');

            const id = $('#approve_izin_usaha_id').val();
            const formData = $(this).serialize();

            $.ajax({
                url: BASE_URL + '/approver/approve/' + id,
                type: 'POST',
                data: formData,
                dataType: 'JSON',
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Berhasil', res.message, 'success');
                        $('#modalApproveIzinUsaha').modal('hide');
                        $('#tabelApprove').DataTable().ajax.reload(null, false);
                    } else {
                        Swal.fire('Gagal', res.message || 'Proses approval gagal', 'error');
                    }
                },
                error: function(jqXHR) {
                    const res = jqXHR.responseJSON;
                    if (jqXHR.status === 422 && res && res.error) {
                        $.each(res.error, function(key, messages) {
                            $(`#error-${key}`).html(messages.join('<br>'));
                            $(`[name="${key}"]`).addClass('is-invalid');
                        });
                    } else {
                        Swal.fire('Error', res?.message || 'Terjadi kesalahan server', 'error');
                    }
                }
            });
        });
    </script>
@endpush
