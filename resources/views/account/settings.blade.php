@extends('layouts.app')

@section('title', 'Pengaturan Akun')
@section('page-title', 'Pengaturan Akun')

@section('content')
<div class="row">
    <div class="col-lg-8 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">Profil Pengguna</h4>

                <form action="{{ route('account.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="form-group mb-4">
                        <label class="d-block">Foto Profil</label>
                        <div id="profileDropzone" class="profile-dropzone">
                            <img id="profilePreview" src="{{ $user->profile_photo_url }}" alt="Foto Profil" class="profile-preview">
                            <div class="profile-dropzone-text">
                                <strong>Drag & drop</strong> foto ke area ini, atau klik untuk pilih file
                                <small class="d-block mt-1">Format JPG/PNG, maksimal 2MB.</small>
                            </div>
                            <input id="profilePhotoInput" type="file" name="profile_photo" class="d-none @error('profile_photo') is-invalid @enderror" accept="image/*">
                        </div>
                        @error('profile_photo')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @if($user->profile_photo_path)
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" value="1" id="remove_profile_photo" name="remove_profile_photo">
                                <label class="form-check-label" for="remove_profile_photo">
                                    Hapus foto profil
                                </label>
                            </div>
                        @endif
                    </div>

                    <div class="form-group mb-3">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="phone">Nomor Telepon</label>
                        <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                            value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="job_title">Jabatan</label>
                        <input type="text" id="job_title" name="job_title" class="form-control @error('job_title') is-invalid @enderror"
                            value="{{ old('job_title', $user->job_title) }}" placeholder="Contoh: Kepala UKS">
                        @error('job_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="address">Alamat</label>
                        <textarea id="address" name="address" rows="2" class="form-control @error('address') is-invalid @enderror" placeholder="Alamat domisili">{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="bio">Bio Singkat</label>
                        <textarea id="bio" name="bio" rows="2" class="form-control @error('bio') is-invalid @enderror" placeholder="Profil singkat pengguna">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4 border-secondary">
                    <h5 class="mb-3">Ubah Password (Opsional)</h5>

                    <div class="form-group mb-3">
                        <label for="current_password">Password Lama</label>
                        <input type="password" id="current_password" name="current_password"
                            class="form-control @error('current_password') is-invalid @enderror"
                            autocomplete="current-password">
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="new_password">Password Baru</label>
                        <input type="password" id="new_password" name="new_password"
                            class="form-control @error('new_password') is-invalid @enderror"
                            autocomplete="new-password">
                        @error('new_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-4">
                        <label for="new_password_confirmation">Konfirmasi Password Baru</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                            class="form-control" autocomplete="new-password">
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h5 class="mb-3">Informasi Akun</h5>
                <p class="mb-2"><strong>Role:</strong> {{ $user->role_label }}</p>
                <p class="mb-2"><strong>Status:</strong> {{ $user->status_label }}</p>
                <p class="mb-2"><strong>Jabatan:</strong> {{ $user->job_title ?: '-' }}</p>
                <p class="mb-2"><strong>Telepon:</strong> {{ $user->phone ?: '-' }}</p>
                <p class="mb-0"><strong>Terdaftar:</strong> {{ $user->created_at->translatedFormat('d F Y H:i') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .profile-dropzone {
        border: 1.5px dashed #94a3b8;
        border-radius: 14px;
        background: #eef2f7;
        padding: 14px;
        display: flex;
        align-items: center;
        gap: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .profile-dropzone:hover,
    .profile-dropzone.is-dragover {
        border-color: #5b8def;
        background: #e4ecfb;
        box-shadow: 0 0 0 3px rgba(91, 141, 239, 0.15);
    }
    .profile-preview {
        width: 72px;
        height: 72px;
        border-radius: 9999px;
        object-fit: cover;
        border: 2px solid #cbd5e1;
        flex-shrink: 0;
    }
    .profile-dropzone-text {
        color: #334155;
        font-size: 0.9rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dropzone = document.getElementById('profileDropzone');
        const input = document.getElementById('profilePhotoInput');
        const preview = document.getElementById('profilePreview');

        if (!dropzone || !input || !preview) return;

        const setPreview = (file) => {
            if (!file || !file.type.startsWith('image/')) return;
            const objectUrl = URL.createObjectURL(file);
            preview.src = objectUrl;
        };

        dropzone.addEventListener('click', () => input.click());

        input.addEventListener('change', (e) => {
            const file = e.target.files && e.target.files[0];
            setPreview(file);
        });

        ['dragenter', 'dragover'].forEach((eventName) => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.add('is-dragover');
            });
        });

        ['dragleave', 'drop'].forEach((eventName) => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropzone.classList.remove('is-dragover');
            });
        });

        dropzone.addEventListener('drop', (e) => {
            const files = e.dataTransfer && e.dataTransfer.files;
            if (!files || !files.length) return;
            input.files = files;
            setPreview(files[0]);
        });
    });
</script>
@endpush
