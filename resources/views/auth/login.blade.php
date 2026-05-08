@extends('layouts.app')

@section('title', 'Login Admin - BusMania')

@section('content')
<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh; background: var(--background);">
    <div style="background: var(--surface); padding: 2.5rem; border-radius: 16px; box-shadow: var(--shadow-md); width: 100%; max-width: 400px; border: 1px solid var(--border);">
        
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="display: inline-flex; align-items: center; justify-content: center; width: 60px; height: 60px; background: rgba(10, 79, 59, 0.1); color: var(--primary); border-radius: 12px; margin-bottom: 1rem;">
                <i class="ph ph-shield-check" style="font-size: 2rem;"></i>
            </div>
            <h2 style="margin: 0; color: var(--text-main); font-family: 'Libre Baskerville', serif;">Admin Portal</h2>
            <p style="margin: 0.5rem 0 0; color: var(--text-muted); font-size: 0.9rem;">Masuk untuk mengelola operasional BusMania.</p>
        </div>

        @if($errors->any())
            <div style="background: #fef2f2; color: #dc2626; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.85rem; border: 1px solid #fecaca;">
                @foreach ($errors->all() as $error)
                    <div><i class="ph ph-warning-circle"></i> {{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.85rem;">Alamat Email</label>
                <div style="position: relative;">
                    <i class="ph ph-envelope" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="email" name="email" value="{{ old('email') }}" style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 0.9rem;" placeholder="admin@busmania.com" required autofocus>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label style="display: block; font-weight: 600; margin-bottom: 0.5rem; font-size: 0.85rem;">Kata Sandi</label>
                <div style="position: relative;">
                    <i class="ph ph-lock-key" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="password" name="password" style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 1px solid var(--border); border-radius: 8px; font-family: inherit; font-size: 0.9rem;" placeholder="Masukkan kata sandi" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.8rem; font-size: 0.95rem; display: flex; justify-content: center; align-items: center; gap: 0.5rem;">
                Masuk ke Dashboard <i class="ph ph-sign-in"></i>
            </button>
        </form>

        <div style="text-align: center; margin-top: 1.5rem; border-top: 1px dashed var(--border); padding-top: 1.5rem;">
            <a href="/" style="color: var(--text-muted); text-decoration: none; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.3rem;">
                <i class="ph ph-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>
@endsection
