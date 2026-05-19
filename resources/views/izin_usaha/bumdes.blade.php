@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <!-- ============================================
                 WIZARD CARD - INFORMASI ALUR TAHAPAN (RESPONSIVE FIXED)
                 ============================================ -->

            <div class="wizard-container mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light border-bottom">
                        <h5 class="mb-0">
                             Alur Tahapan Pengajuan Ijin Usaha
                        </h5>
                    </div>
                    <div class="card-body p-4">

                        <!-- Timeline Wizard Horizontal Responsive -->
                        <div class="timeline-wizard-responsive">

                            <!-- Step 1: DRAFT -->
                            <div class="timeline-step-responsive">
                                <div class="timeline-marker-responsive">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="timeline-content-responsive">
                                    <h6 class="timeline-title-responsive">1. Draf</h6>
                                    <p class="timeline-desc-responsive">
                                        Isi data usaha, dokumen & foto
                                    </p>
                                </div>
                                <div class="timeline-connector-responsive"></div>
                            </div>

                            <!-- Step 2: SUBMITTED -->
                            <div class="timeline-step-responsive">
                                <div class="timeline-marker-responsive">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                                <div class="timeline-content-responsive">
                                    <h6 class="timeline-title-responsive">2. Submit</h6>
                                    <p class="timeline-desc-responsive">
                                        Pengajuan akan dikirim untuk diverifikasi petugas, pastikan data & dokumen sudah benar sebelum submit
                                    </p>
                                </div>
                                <div class="timeline-connector-responsive"></div>
                            </div>

                            <!-- Step 3: VERIFIKASI -->
                            <div class="timeline-step-responsive">
                                <div class="timeline-marker-responsive">
                                    <i class="fas fa-search"></i>
                                </div>
                                <div class="timeline-content-responsive">
                                    <h6 class="timeline-title-responsive">3. Verifikasi</h6>
                                    <p class="timeline-desc-responsive">
                                        Petugas akan melakukan verifikasi data & dokumen dan memberikan rekomendasi OPD tujuan
                                    </p>
                                </div>
                                <div class="timeline-connector-responsive"></div>
                            </div>

                            <!-- Step 4: APPROVAL -->
                            <div class="timeline-step-responsive">
                                <div class="timeline-marker-responsive">
                                    <i class="fas fa-check-square"></i>
                                </div>
                                <div class="timeline-content-responsive">
                                    <h6 class="timeline-title-responsive">4. Persetujuan</h6>
                                    <p class="timeline-desc-responsive">
                                        Pejabat akan melakukan Review & setujui untuk diteruskan ke OPD atau tidak
                                    </p>
                                </div>
                                <div class="timeline-connector-responsive"></div>
                            </div>

                            <!-- Step 5: OPD -->
                            <div class="timeline-step-responsive">
                                <div class="timeline-marker-responsive">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="timeline-content-responsive">
                                    <h6 class="timeline-title-responsive">5. OPD</h6>
                                    <p class="timeline-desc-responsive">
                                        Proses penerbitan ijin
                                    </p>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>

            <!-- CSS untuk Wizard Timeline Responsive Fixed -->
            <style>
                .wizard-container {
                    width: 100%;
                }

                .timeline-wizard-responsive {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                    gap: 15px;
                    width: 100%;
                }

                .timeline-step-responsive {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 12px;
                    position: relative;
                    padding-bottom: 10px;
                }

                .timeline-marker-responsive {
                    width: 70px;
                    height: 70px;
                    border-radius: 50%;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 28px;
                    color: white;
                    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
                    transition: all 0.3s ease;
                    flex-shrink: 0;
                    z-index: 2;
                }

                .timeline-marker-responsive:hover {
                    transform: scale(1.1);
                    box-shadow: 0 6px 16px rgba(102, 126, 234, 0.5);
                }

                .timeline-content-responsive {
                    text-align: center;
                    width: 100%;
                }

                .timeline-title-responsive {
                    font-weight: 700;
                    font-size: 0.95rem;
                    margin-bottom: 0.5rem;
                    color: #333;
                    line-height: 1.2;
                }

                .timeline-desc-responsive {
                    font-size: 0.8rem;
                    line-height: 1.4;
                    color: #666;
                    margin: 0;
                }

                .timeline-connector-responsive {
                    position: absolute;
                    width: 100%;
                    height: 3px;
                    background: linear-gradient(to right, #e0e0e0, #e0e0e0);
                    top: 35px;
                    left: 50%;
                    z-index: 1;
                }

                .timeline-step-responsive:last-child .timeline-connector-responsive {
                    display: none;
                }

           
            </style>
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header  d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $title }}</h5>
                        <button type="button" class="btn btn-primary btn-sm" onclick="formIzinUsaha()" data-type="add">
                            <i class="bi bi-plus-circle"></i> Tambah Pengajuan
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="tabelIzinUsahaBumdes" style="width: 100%;">
                                <thead class="table-primary">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th>Data Usaha</th>
                                        <th width="15%">Deskripsi</th>
                                        <th>Status</th>
                                        <th>Dokumen</th>
                                        <th>OPD Tujuan</th>
                                        <th class="text-center">Aksi</th>
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
            $('#tabelIzinUsahaBumdes').DataTable({
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
                    url: BASE_URL + "/bumdes/get-datatables-izin-usaha",
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
                            const isDraft = data.status === 'DRAFT' || data.status === 'DITOLAK';
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
                                <i class="bi bi-pencil text-primary"></i> Edit
                            </button>
                        </li>
                        <li>
                            <button class="dropdown-item" type="button"
                                onclick="submitIzinUsaha(this)" data-id="${data.id}"
                                ${!isDraft ? 'disabled' : ''}>
                                <i class="bi bi-send text-success"></i> Submit
                            </button>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button class="dropdown-item text-danger" type="button"
                                onclick="deleteIzinUsaha(this)" data-id="${data.id}"
                                ${!isDraft ? 'disabled' : ''}>
                                <i class="bi bi-trash"></i> Hapus
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

        // ===================== FORM ADD / EDIT IZIN USAHA =====================
        function formIzinUsaha(el = null) {
            $('#formIzinUsaha')[0].reset();
            $('#izin_usaha_id').val('');
            $('.text-danger').html('');
            $('.form-control, .form-select').removeClass('is-invalid');

            if (el && $(el).data('type') === 'edit') {
                const id = $(el).data('id');
                $('#modalIzinUsahaTitle').text('Edit Pengajuan Izin Usaha');

                $.ajax({
                    url: BASE_URL + '/bumdes/izin-usaha/' + id,
                    type: 'GET',
                    dataType: 'JSON',
                    success: function(res) {
                        if (res.success) {
                            $('#izin_usaha_id').val(res.data.id);
                            $('#nama_usaha').val(res.data.nama_usaha);
                            $('#deskripsi_usaha').val(res.data.deskripsi_usaha);
                            $('#latitude').val(res.data.latitude);
                            $('#longitude').val(res.data.longitude);
                            $('#modalIzinUsaha').modal('show');
                        } else {
                            Swal.fire('Error', 'Data pengajuan tidak ditemukan', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal mengambil data pengajuan', 'error');
                    }
                });

            } else {
                $('#modalIzinUsahaTitle').text('Tambah Pengajuan Izin Usaha');
                $('#modalIzinUsaha').modal('show');
            }
        }

        // ===================== SUBMIT FORM IZIN USAHA =====================
        $('#formIzinUsaha').on('submit', function(e) {
            e.preventDefault();

            // Bersihkan pesan error sebelumnya
            $('.text-danger').html('');
            $('.form-control, .form-select').removeClass('is-invalid');

            const formData = new FormData(this);

            $.ajax({
                type: "POST",
                url: BASE_URL + "/bumdes/izin-usaha",
                processData: false,
                contentType: false,
                data: formData,
                dataType: "JSON",

                success: function(res) {
                    Swal.fire('Berhasil', res.message, 'success');
                    $('#modalIzinUsaha').modal('hide');
                    $('#tabelIzinUsahaBumdes').DataTable().ajax.reload(null, false);
                },

                error: function(jqXHR) {
                    const statusCode = jqXHR.status;
                    const res = jqXHR.responseJSON;

                    if (statusCode === 422 && res && res.error) {
                        $.each(res.error, function(key, messages) {
                            const errorElementId = '#error-' + key;
                            $(errorElementId).html(messages.join('<br>'));
                            $(`[name="${key}"]`).addClass('is-invalid');
                        });

                    } else {
                        let errorMessage = 'Terjadi kesalahan server.';
                        let errorTitle = 'Error ' + statusCode;

                        if (res && res.message) {
                            errorMessage = res.message;
                        } else {
                            console.error("Detail Error:", jqXHR.responseText);
                        }

                        Swal.fire(errorTitle, errorMessage, 'error');
                    }
                }
            });
        });

        function deleteIzinUsaha(el) {
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
                        url: BASE_URL + '/bumdes/izin-usaha/' + id,
                        type: 'DELETE',
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
