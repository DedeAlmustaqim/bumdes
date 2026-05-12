@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <h5 class="card-header">{{ $title }}</h5>
                <div class="card-body">

                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="formOpd()"
                        data-type="add">+ OPD</button>
                    <hr>
                    <div class="table-responsive">
                        <table class="table mb-0" id="tabelOpd">

                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>OPD</th>

                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Desa -->
    <div class="modal fade bs-example-modal-center" id="modalOpd" tabindex="-1" role="dialog"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalOpdTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formOpd" method="POST">
                        @csrf

                        <input type="hidden" name="id_opd" id="id_opd">

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">OPD</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" name="nm_opd"
                                    id="nm_opd" placeholder="Masukkan Nama OPD">
                                <div class="text-danger" id="error-nm_opd"></div>
                            </div>
                        </div>


                        <div class="row justify-content-end">
                            <div class="col-sm-9">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // ===================== DATATABLE OPD =====================
            $('#tabelOpd').DataTable({
                processing: true,
                serverSide: true,
                destroy: true,
                bPaginate: true,
                bLengthChange: true,
                bFilter: true,
                bInfo: true,
                bAutoWidth: true,
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
                    url: BASE_URL + "/admin/master/get-opd",
                },
                columns: [{
                        orderable: false,
                        data: function(data) {
                            return '<div class="text-left">' + data.id + '</div>';
                        }
                    },
                    {
                        orderable: false,
                        data: function(data) {
                            return '<div class="text-left">' + data.nm_opd + '</div>';
                        }
                    },
              
                    {
                        orderable: false,
                        data: function(data) {
                            return `
                    <div class="text-center">
                        <button type="button" class="btn btn-sm btn-primary" onclick="formOpd(this)" data-id="${data.id}" data-type="edit">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteOpd(this)" data-id="${data.id}">Hapus</button>
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

        // ===================== FORM ADD / EDIT OPD =====================
        function formOpd(el = null) {
            $('#formOpd')[0].reset();
            $('#id_opd').val('');

            if (el && $(el).data('type') === 'edit') {
                const id = $(el).data('id');
                $('#modalOpdTitle').text('Edit OPD');

                $.ajax({
                    url: BASE_URL + '/admin/master/opd-by-id/' + id,
                    type: 'GET',
                    dataType: 'JSON',
                    success: function(res) {
                        if (res.success) {
                            $('#id_opd').val(res.data.id);
                            $('#nm_opd').val(res.data.nm_opd);
                            $('#modalOpd').modal('show');
                        } else {
                            Swal.fire('Error', 'Data OPD tidak ditemukan', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal mengambil data OPD', 'error');
                    }
                });

            } else {
                $('#modalOpdTitle').text('Tambah OPD');
                $('#modalOpd').modal('show');
            }
        }

        // ===================== SUBMIT FORM OPD =====================
        $('#formOpd').on('submit', function(e) {
            e.preventDefault();

            // 💡 Langkah 1: Bersihkan semua pesan error dan kelas 'is-invalid' sebelum submit baru
            $('.text-danger').html('');
            $('.form-control, .form-select').removeClass('is-invalid');

            const formData = new FormData(this);
            // ... (append token, dll. jika diperlukan) ...

            $.ajax({
                type: "POST",
                url: BASE_URL + "/admin/master/opd", // URL TETAP SAMA
                processData: false,
                contentType: false,
                data: formData, // Termasuk opd_id (untuk update) atau kosong (untuk insert)
                dataType: "JSON",

                success: function(res) {
                    // Menangani respons 200 (Update) atau 201 (Insert)
                    Swal.fire('Berhasil', res.message, 'success');
                    $('#modalOpd').modal('hide');
                    $('#tabelOpd').DataTable().ajax.reload(null, false);
                },

                error: function(jqXHR) {
                    const statusCode = jqXHR.status;
                    const res = jqXHR.responseJSON;

                    // 💡 Penanganan Error Validasi (Kode 422) tetap sama untuk Insert/Update
                    if (statusCode === 422 && res && res.error) {
                        $.each(res.error, function(key, messages) {
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

        function deleteOpd(el) {
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
                        url: BASE_URL + '/admin/master/opd/' + id,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('Berhasil', res.message, 'success');
                                $('#tabelOpd').DataTable().ajax.reload(null, false);
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
