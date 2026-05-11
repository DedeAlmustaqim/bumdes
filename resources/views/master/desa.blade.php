@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <h5 class="card-header">{{ $title }}</h5>
                <div class="card-body">

                    <button type="button" class="btn btn-primary waves-effect waves-light" onclick="formDesa()"
                        data-type="add">+ Desa</button>
                    <hr>
                    <div class="table-responsive">
                        <table class="table mb-0" id="tabelDesa" style="width: 100%;">

                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Nama Desa</th>
                                    <th>Nama Kecamatan</th>

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
    <div class="modal fade bs-example-modal-center" id="modalDesa" tabindex="-1" role="dialog"
        aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content modal-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalDesaTitle"></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formDesa" method="POST">
                        @csrf

                        <input type="hidden" name="id_desa" id="id_desa">

                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Nama Desa</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" name="nm_desa" id="nm_desa"
                                    placeholder="Masukkan Nama Desa">
                                <div class="text-danger" id="error-nm_desa"></div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-3 col-form-label">Kecamatan</label>
                            <div class="col-sm-9">
                                <select class="form-select" name="kecamatan_id" id="kecamatan_id">
                                    <option value="">Pilih</option>
                                    @foreach ($kecamatan as $item)
                                        <option value="{{ $item->id }}">{{ $item->nm_kecamatan }}</option>
                                    @endforeach
                                </select>
                                <div class="text-danger" id="error-kecamatan_id"></div>
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
            $('#tabelDesa').DataTable({
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
                    url: BASE_URL + "/admin/master/get-desa",
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
                            return '<div class="text-left">' + data.nm_desa + '</div>';
                        }
                    },
                    {
                        orderable: false,
                        data: function(data) {
                            return '<div class="text-left">' + data.nm_kecamatan + '</div>';
                        }
                    },

                    {
                        orderable: false,
                        data: function(data) {
                            return `
                    <div class="text-center">
                        <button type="button" class="btn btn-sm btn-primary" onclick="formDesa(this)" data-id="${data.id}" data-type="edit">Edit</button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="deleteDesa(this)" data-id="${data.id}">Hapus</button>
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
        function formDesa(el = null) {
            $('#formDesa')[0].reset();
            $('#id_desa').val('');

            if (el && $(el).data('type') === 'edit') {
                const id = $(el).data('id');
                $('#modalDesaTitle').text('Edit Desa');

                $.ajax({
                    url: BASE_URL + '/admin/master/desa-by-id/' + id,
                    type: 'GET',
                    dataType: 'JSON',
                    success: function(res) {
                        if (res.success) {
                            $('#id_desa').val(res.data.id);
                            $('#nm_desa').val(res.data.nm_desa);
                            $('#kecamatan_id').val(res.data.kecamatan_id);
                            $('#modalDesa').modal('show');
                        } else {
                            Swal.fire('Error', 'Data Desa tidak ditemukan', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal mengambil data desa', 'error');
                    }
                });

            } else {
                $('#modalDesaTitle').text('Tambah Desa');
                $('#modalDesa').modal('show');
            }
        }

        // ===================== SUBMIT FORM DESA =====================
        $('#formDesa').on('submit', function(e) {
            e.preventDefault();

            // 💡 Langkah 1: Bersihkan semua pesan error dan kelas 'is-invalid' sebelum submit baru
            $('.text-danger').html('');
            $('.form-control, .form-select').removeClass('is-invalid');

            const formData = new FormData(this);
            // ... (append token, dll. jika diperlukan) ...

            $.ajax({
                type: "POST",
                url: BASE_URL + "/admin/master/desa", // URL TETAP SAMA
                processData: false,
                contentType: false,
                data: formData, // Termasuk desa_id (untuk update) atau kosong (untuk insert)
                dataType: "JSON",

                success: function(res) {
                    // Menangani respons 200 (Update) atau 201 (Insert)
                    Swal.fire('Berhasil', res.message, 'success');
                    $('#modalDesa').modal('hide');
                    $('#tabelDesa').DataTable().ajax.reload(null, false);
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

        function deleteDesa(el) {
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
                        url: BASE_URL + '/admin/master/desa/' + id,
                        type: 'DELETE',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire('Berhasil', res.message, 'success');
                                $('#tabelDesa').DataTable().ajax.reload(null, false);
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
