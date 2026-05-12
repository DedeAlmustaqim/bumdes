@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <h5 class="card-header">{{ $title }}</h5>
                <div class="card-body">

                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="formPetugas()"
                        data-type="add">+ Petugas</button>
                    <hr>
                    <div class="table-responsive">
                        <table class="table mb-0" id="tablePetugas">

                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th>Hak Akses</th>
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
    <div class="modal fade bs-example-modal-center" id="modalPetugas" tabindex="-1" role="dialog"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPetugasTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formPetugas" method="POST">
                        @csrf

                        <input type="hidden" name="petugas_id" id="petugas_id">

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Nama</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" name="name" id="name"
                                    placeholder="Masukkan Nama">
                                <div class="text-danger" id="error-name"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Username</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" name="username" id="username"
                                    placeholder="Masukkan username">
                                <div class="text-danger" id="error-username"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Password</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="password" name="password" id="password"
                                    placeholder="Masukkan Password">
                                <div class="text-danger" id="error-password"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Ulangi Password</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="password" name="password_confirmation"
                                    id="password_confirmation" placeholder="Masukan Ulangi Password">
                                <div class="text-danger" id="error-password_confirmation"></div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Hak Akses</label>
                            <div class="col-sm-9">
                                <select class="form-select" name="role" id="role">
                                    <option value="">Pilih</option>
                                    <option value="verifikator">Verifikator</option>
                                    <option value="approver">Pejabat Disposisi</option>
                                </select>
                                <div class="text-danger" id="error-role"></div>
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
            // ===================== DATATABLE DESA =====================
            $('#tablePetugas').DataTable({
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
                    url: BASE_URL + "/admin/user/get-datatables-petugas",
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
                            return '<div class="text-left">' + data.name + '</div>';
                        }
                    },
                    {
                        orderable: false,
                        data: function(data) {
                            return '<div class="text-left">' + data.username + '</div>';
                        }
                    },
                    {
                        orderable: false,
                        data: function(data) {
                            return '<div class="text-left">' + data.role + '</div>';
                        }
                    },
                    {
                        orderable: false,
                        data: function(data) {
                            return `
                    <div class="text-center">
                        <button type="button" class="btn btn-sm btn-primary" onclick="formPetugas(this)" data-id="${data.id}" data-type="edit">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deletePetugas(this)" data-id="${data.id}">Hapus</button>
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

        // ===================== FORM ADD / EDIT DESA =====================
        function formPetugas(el = null) {
            $('#formPetugas')[0].reset();
            $('#petugas_id').val('');
            $('.text-danger').html('');
            $('.form-control, .form-select').removeClass('is-invalid');

            if (el && $(el).data('type') === 'edit') {
                const id = $(el).data('id');
                $('#modalPetugasTitle').text('Edit Petugas');

                $.ajax({
                    url: BASE_URL + '/admin/user/get-petugas-by-id/' + id,
                    type: 'GET',
                    dataType: 'JSON',
                    success: function(res) {
                        if (res.success) {
                            $('#petugas_id').val(res.data.id);
                            $('#name').val(res.data.name);
                            $('#username').val(res.data.username);
                            $('#role').val(res.data.role);
                            $('#modalPetugas').modal('show');
                        } else {
                            Swal.fire('Error', 'Data User tidak ditemukan', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal mengambil data petugas', 'error');
                    }
                });

            } else {
                $('#modalPetugasTitle').text('Tambah Petugas');
                $('#modalPetugas').modal('show');
            }
        }

        // ===================== SUBMIT FORM DESA =====================
        $('#formPetugas').on('submit', function(e) {
            e.preventDefault();

            $('.text-danger').html('');
            $('.form-control, .form-select').removeClass('is-invalid');

            const formData = new FormData(this);

            $.ajax({
                type: "POST",
                url: BASE_URL + "/admin/user/petugas",
                processData: false,
                contentType: false,
                data: formData,
                dataType: "JSON",
                success: function(res) {
                    Swal.fire('Berhasil', res.message, 'success');
                    $('#modalPetugas').modal('hide');
                    $('#tablePetugas').DataTable().ajax.reload(null, false);
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
                            errorMessage += ' (Status: ' + statusCode + ')';
                        }

                        Swal.fire(errorTitle, errorMessage, 'error');
                    }
                }
            });
        });

        function deletePetugas(el) {
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
                        url: BASE_URL + '/admin/del-user-petugas/' + id,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('Berhasil', res.message, 'success');
                                $('#tablePetugas').DataTable().ajax.reload(null, false);
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
