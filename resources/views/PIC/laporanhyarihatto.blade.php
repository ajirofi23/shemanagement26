@extends('layout.picsidebar')

@section('content')

{{-- UBAH p-4 MENJADI p-3 pada div content --}}
<div class="content p-3">

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        {{-- Menggunakan H3 untuk Laporan Hyari Hatto --}}
        <h3 style="color:#0f172a;" class="fw-bold">
            {{-- ICON disesuaikan dengan Laporan --}}
            <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i> Laporan Hyari Hatto (Near Miss)
        </h3>
        
        {{-- Tombol Tambah Laporan --}}
        <button type="button" class="btn btn-warning shadow-sm text-dark" data-bs-toggle="modal" data-bs-target="#addHyariHattoModal">
            <i class="bi bi-plus-circle me-1"></i> Buat Laporan Baru
        </button>
    </div>
    
    <hr class="mb-4">

    <div class="card shadow-lg border-0">
        {{-- UBAH p-4 MENJADI p-3 pada card-body --}}
        <div class="card-body p-3">
            <h5 class="card-title mb-3 fs-5 text-secondary">
                <i class="bi bi-list-columns-reverse me-1"></i> Daftar Laporan Hyari Hatto
            </h5>

            {{-- Search & Export --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                {{-- Search --}}
                {{-- ENDPOINT disesuaikan ke rute Hyari Hatto --}}
                <form action="{{ url('/pic/laporanhyarihatto') }}" method="GET" class="d-flex flex-grow-1 me-3">
                    <div class="input-group">
                        {{-- Menggunakan form-control standar --}}
                        <input type="text" id="searchInput" name="search" value="{{ request('search') }}" class="form-control border-warning" style="max-width: 300px;" placeholder="">
                        <button type="submit" class="btn btn-warning text-dark">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                </form>

                {{-- Export (ENDPOINT disesuaikan) --}}
                <a href="{{ url('/pic/hyari-hatto/export/excel') }}" class="btn btn-success shadow-sm">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
                </a>
            </div>

            {{-- Tabel Hyari Hatto --}}
            <div class="table-responsive">
                {{-- Mengubah kelas tabel untuk menampung lebih banyak kolom --}}
                <table class="table table-hover table-striped mt-3 align-middle table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col" style="width: 30px;">No</th>
                            <th scope="col" style="width: 15%;">Perilaku Tidak Aman</th>
                            <th scope="col" style="width: 15%;">Kondisi Tidak Aman</th>
                            <th scope="col" style="width: 10%;">Potensi Bahaya</th>
                            <th scope="col" style="width: 20%;">Deskripsi</th>
                            <th scope="col" style="width: 15%;">Rekomendasi P2K3</th>
                            <th scope="col" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Mengganti $ptas dengan $hyarihattos --}}
                        @foreach($hyarihattos as $laporan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                {{-- Jika relasi banyak: Tampilkan list --}}
                                @if($laporan->ptas->count() > 0)
                                    @foreach($laporan->ptas as $pta)
                                        <span class="badge bg-secondary me-1">{{ $pta->nama_pta }}</span>
                                    @endforeach
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                {{-- Jika relasi banyak: Tampilkan list --}}
                                @if($laporan->ktas->count() > 0)
                                    @foreach($laporan->ktas as $kta)
                                        <span class="badge bg-secondary me-1">{{ $kta->nama_kta }}</span>
                                    @endforeach
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>
                                {{-- Jika relasi banyak: Tampilkan list --}}
                                @if($laporan->pbs->count() > 0)
                                    @foreach($laporan->pbs as $pb)
                                        <span class="badge bg-danger me-1">{{ $pb->nama_pb }}</span>
                                    @endforeach
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ Str::limit($laporan->deskripsi, 50) }}</td>
                            <td>{{ Str::limit($laporan->rekomendasi, 50) }}</td>
                            <td>
                                {{-- Tombol Detail/Edit --}}
                                @if(isset($permission) && $permission->can_edit)
                                <button type="button" class="btn btn-sm btn-primary me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editHyariHattoModal{{ $laporan->id }}"
                                    title="Edit/Lihat Detail">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @endif

                                {{-- Tombol Download Laporan Lengkap --}}
                                <a href="{{ url('/pic/laporanhyarihatto/download/' . $laporan->id) }}" class="btn btn-sm btn-success me-1" title="Download Laporan">
                                    <i class="bi bi-download"></i>
                                </a>

                                {{-- Tombol Delete (ENDPOINT disesuaikan) --}}
                                @if(isset($permission) && $permission->can_delete)
                                <form action="{{ url('/pic/laporanhyarihatto/destroy/' . $laporan->id) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus Laporan Hyari Hatto ini?')" title="Hapus Data">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif

                                {{-- Tombol Download Bukti (Asumsi 'bukti' adalah path file) --}}
                                @if($laporan->bukti)
                                <a href="{{ asset('storage/' . $laporan->bukti) }}" target="_blank" class="btn btn-sm btn-info text-white" title="Download Bukti">
                                    <i class="bi bi-file-image"></i>
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Paging (jika menggunakan Laravel pagination) --}}
            {{-- Jika Anda menggunakan pagination:
            <div class="mt-3">
                 $hyarihattos->links() 
            </div> 
            --}}

        </div>
    </div>
</div>

{{-- Animasi Placeholder (Dibiarkan sama) --}}
<script>
const placeholders = ["Cari Perilaku Tidak Aman", "Cari Data", "Filter"];
let current = 0;
let index = 0;
let isDeleting = false;
const speed = 100;
const delay = 1500;
const input = document.getElementById("searchInput");

function typePlaceholder() {
    const currentPlaceholder = placeholders[current];
    if (isDeleting) {
        input.placeholder = currentPlaceholder.substring(0, index);
        index--;
        if (index < 0) {
            isDeleting = false;
            current = (current + 1) % placeholders.length;
        }
    } else {
        input.placeholder = currentPlaceholder.substring(0, index);
        index++;
        if (index > currentPlaceholder.length) {
            isDeleting = true;
            setTimeout(typePlaceholder, delay);
            return;
        }
    }
    setTimeout(typePlaceholder, speed);
}

if (input && !input.value) {
    typePlaceholder();
}
</script>

@endsection

{{---------------------------------------------------------------------------------}}
{{--- SECTION MODALS: Modal untuk Laporan Hyari Hatto --}}
{{---------------------------------------------------------------------------------}}
@section('modals')

{{-- Modal Tambah Hyari Hatto --}}
<div class="modal fade" id="addHyariHattoModal" tabindex="-1" aria-labelledby="addHyariHattoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> 
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="addHyariHattoModalLabel"><i class="bi bi-plus-circle-fill me-1"></i> Buat Laporan Hyari Hatto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ url('/pic/laporanhyarihatto/store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label d-block fw-bold">Perilaku Tidak Aman <span class="text-danger">*</span></label>
                            <div class="form-check-group border p-2 rounded" style="max-height: 200px; overflow-y: auto;">
                                @foreach($masterPtas as $pta)
                                    <div class="form-check">
                                        {{-- Menggunakan nama 'pta_id[]' untuk menerima multiple value (array) --}}
                                        <input class="form-check-input" type="checkbox" name="pta_id[]" value="{{ $pta->id }}" id="pta_check_{{ $pta->id }}" {{ in_array($pta->id, old('pta_id', [])) ? 'checked' : '' }}>
                                        {{-- Asumsi kolomnya adalah nama_pta --}}
                                        <label class="form-check-label" for="pta_check_{{ $pta->id }}">
                                            {{ $pta->nama_pta }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('pta_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label d-block fw-bold">Kondisi Tidak Aman <span class="text-danger">*</span></label>
                            <div class="form-check-group border p-2 rounded" style="max-height: 200px; overflow-y: auto;">
                                @foreach($masterKtas as $kta)
                                    <div class="form-check">
                                        {{-- Menggunakan nama 'kta_id[]' --}}
                                        <input class="form-check-input" type="checkbox" name="kta_id[]" value="{{ $kta->id }}" id="kta_check_{{ $kta->id }}" {{ in_array($kta->id, old('kta_id', [])) ? 'checked' : '' }}>
                                        {{-- Asumsi kolomnya adalah nama_kta --}}
                                        <label class="form-check-label" for="kta_check_{{ $kta->id }}">
                                            {{ $kta->nama_kta }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('kta_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="lokasi" class="form-label">Lokasi Kejadian <span class="text-danger">*</span></label>
                        <input name="lokasi" id="lokasi" class="form-control" value="{{ old('lokasi') }}" required/>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block fw-bold">Potensi Bahaya <span class="text-danger">*</span></label>
                        <div class="form-check-group border p-2 rounded" style="max-height: 150px; overflow-y: auto;">
                            @foreach($masterPbs as $pb)
                                <div class="form-check">
                                    {{-- Menggunakan nama 'pb_id[]' --}}
                                    <input class="form-check-input" type="checkbox" name="pb_id[]" value="{{ $pb->id }}" id="pb_check_{{ $pb->id }}" {{ in_array($pb->id, old('pb_id', [])) ? 'checked' : '' }}>
                                    {{-- Asumsi kolomnya adalah nama_pb --}}
                                    <label class="form-check-label" for="pb_check_{{ $pb->id }}">
                                        {{ $pb->nama_pb }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('pb_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    {{-- Bagian Deskripsi, Usulan, dan Bukti tetap sama --}}
                    
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi Kejadian (Detail) <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required>{{ old('deskripsi') }}</textarea>
                        <small class="form-text text-muted">Jelaskan secara rinci kejadian atau temuan PTA/KTA.</small>
                    </div>

                    
                    <div class="mb-3">
                        <label for="usulan" class="form-label">Usulan Countermeasure <span class="text-danger">*</span></label>
                        <textarea name="usulan" id="usulan" class="form-control" rows="2" required>{{ old('usulan') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="bukti" class="form-label">Bukti (Gambar/Foto) <span class="text-danger">*</span></label>
                        <input type="file" name="bukti" id="bukti" class="form-control" accept="image/*" required>
                        <small class="form-text text-muted">Lampirkan foto kejadian/temuan.</small>
                    </div>
                    
                    <div class="modal-footer mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i> Batal</button>
                        <button type="submit" class="btn btn-warning text-dark"><i class="bi bi-save me-1"></i> Simpan Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit/Detail Hyari Hatto --}}
{{-- Mengganti $ptas dengan $hyarihattos --}}
@foreach($hyarihattos as $laporan)
@if(isset($permission) && $permission->can_edit)
<div class="modal fade" id="editHyariHattoModal{{ $laporan->id }}" tabindex="-1" aria-labelledby="editHyariHattoModalLabel{{ $laporan->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl"> 
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editHyariHattoModalLabel{{ $laporan->id }}"><i class="bi bi-pencil-square me-1"></i> Detail/Edit Laporan #{{ $laporan->id }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{-- ENDPOINT disesuaikan ke rute Hyari Hatto --}}
                <form action="{{ url('/pic/laporanhyarihatto/update/' . $laporan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label d-block fw-bold">Perilaku Tidak Aman <span class="text-danger">*</span></label>
                            <div class="form-check-group border p-2 rounded" style="max-height: 150px; overflow-y: auto;">
                                @php
                                    // Ambil ID PTA yang sudah dipilih untuk laporan ini
                                    $selectedPtas = $laporan->ptas->pluck('id')->toArray();
                                @endphp
                                @foreach($masterPtas as $pta)
                                    <div class="form-check">
                                        {{-- Menggunakan nama 'pta_id[]' --}}
                                        <input class="form-check-input" type="checkbox" name="pta_id[]" value="{{ $pta->id }}" id="edit_pta_check_{{ $laporan->id }}_{{ $pta->id }}" {{ in_array($pta->id, old('pta_id', $selectedPtas)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit_pta_check_{{ $laporan->id }}_{{ $pta->id }}">
                                            {{ $pta->nama_pta }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('pta_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label d-block fw-bold">Kondisi Tidak Aman <span class="text-danger">*</span></label>
                            <div class="form-check-group border p-2 rounded" style="max-height: 150px; overflow-y: auto;">
                                @php
                                    // Ambil ID KTA yang sudah dipilih untuk laporan ini
                                    $selectedKtas = $laporan->ktas->pluck('id')->toArray();
                                @endphp
                                @foreach($masterKtas as $kta)
                                    <div class="form-check">
                                        {{-- Menggunakan nama 'kta_id[]' --}}
                                        <input class="form-check-input" type="checkbox" name="kta_id[]" value="{{ $kta->id }}" id="edit_kta_check_{{ $laporan->id }}_{{ $kta->id }}" {{ in_array($kta->id, old('kta_id', $selectedKtas)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit_kta_check_{{ $laporan->id }}_{{ $kta->id }}">
                                            {{ $kta->nama_kta }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('kta_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label d-block fw-bold">Potensi Bahaya <span class="text-danger">*</span></label>
                            <div class="form-check-group border p-2 rounded" style="max-height: 150px; overflow-y: auto;">
                                @php
                                    // Ambil ID PB yang sudah dipilih untuk laporan ini
                                    $selectedPbs = $laporan->pbs->pluck('id')->toArray();
                                @endphp
                                @foreach($masterPbs as $pb)
                                    <div class="form-check">
                                        {{-- Menggunakan nama 'pb_id[]' --}}
                                        <input class="form-check-input" type="checkbox" name="pb_id[]" value="{{ $pb->id }}" id="edit_pb_check_{{ $laporan->id }}_{{ $pb->id }}" {{ in_array($pb->id, old('pb_id', $selectedPbs)) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="edit_pb_check_{{ $laporan->id }}_{{ $pb->id }}">
                                            {{ $pb->nama_pb }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('pb_id') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="lokasi" class="form-label">Lokasi Kejadian <span class="text-danger">*</span></label>
                        <input name="lokasi" id="lokasi" class="form-control" value="{{ old('lokasi',$laporan->lokasi) }}" required/>
                    </div>

                    <div class="mb-3">
                        <label for="edit_deskripsi_{{ $laporan->id }}" class="form-label">Deskripsi Kejadian (Detail)</label>
                        <textarea name="deskripsi" id="edit_deskripsi_{{ $laporan->id }}" class="form-control" rows="3" required>{{ old('deskripsi', $laporan->deskripsi) }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="edit_usulan_{{ $laporan->id }}" class="form-label">Usulan Countermeasure</label>
                        <textarea name="usulan" id="edit_usulan_{{ $laporan->id }}" class="form-control" rows="2" required>{{ old('usulan', $laporan->usulan) }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_rekomendasi_{{ $laporan->id }}" class="form-label fw-bold text-success">Rekomendasi P2K3 (Tindak Lanjut)</label>
                        {{-- Ini adalah kolom yang sering diisi oleh tim P2K3/HSE --}}
                        <textarea name="rekomendasi" id="edit_rekomendasi_{{ $laporan->id }}" class="form-control" rows="3">{{ old('rekomendasi', $laporan->rekomendasi) }}</textarea>
                        <small class="form-text text-muted">Diisi oleh Tim K3/P2K3 sebagai rencana tindakan korektif resmi.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label d-block">Bukti Saat Ini:</label>
                        @if($laporan->bukti)
                            <a href="{{ asset('storage/' . $laporan->bukti) }}" target="_blank">
                                <img src="{{ asset('storage/' . $laporan->bukti) }}" alt="Bukti" style="max-width: 150px; height: auto;" class="img-thumbnail mb-2">
                            </a>
                        @else
                            <p class="text-muted">Tidak ada gambar bukti terlampir.</p>
                        @endif
                        <input type="file" name="bukti" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengganti bukti.</small>
                    </div>
                    
                    <div class="modal-footer mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="bi bi-x-lg me-1"></i> Batal</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Update Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endforeach

@endsection