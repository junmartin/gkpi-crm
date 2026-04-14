@extends('layouts.form')

@section('content')
<style>
    .homepage-shell {
        padding: 1rem 0 2rem;
    }

    .homepage-hero {
        background: linear-gradient(135deg, #fff8f5 0%, #fff 52%, #f6f9ff 100%);
        border: 1px solid #e9edf5;
        border-radius: 20px;
        padding: 1.5rem;
        box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);
        margin-bottom: 1.25rem;
    }

    .homepage-eyebrow {
        color: #b45309;
        font-size: 0.85rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        margin-bottom: 0.75rem;
        text-transform: uppercase;
    }

    .homepage-title {
        color: #111827;
        font-size: 2rem;
        font-weight: 700;
        line-height: 1.15;
        margin: 0;
    }

    .homepage-subtitle {
        color: #4b5563;
        font-size: 1rem;
        line-height: 1.6;
        margin: 0.9rem 0 0;
        max-width: 44rem;
    }

    .homepage-grid {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    }

    .homepage-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
        color: inherit;
        display: block;
        padding: 1.25rem;
        text-decoration: none;
        transition: transform 160ms ease, box-shadow 160ms ease, border-color 160ms ease;
    }

    .homepage-card:hover {
        border-color: #f97316;
        box-shadow: 0 20px 36px rgba(249, 115, 22, 0.14);
        transform: translateY(-2px);
    }

    .homepage-card-icon {
        align-items: center;
        background: #fff2e8;
        border-radius: 14px;
        color: #ea580c;
        display: inline-flex;
        font-size: 1.35rem;
        font-weight: 700;
        height: 48px;
        justify-content: center;
        margin-bottom: 0.95rem;
        width: 48px;
    }

    .homepage-card-title {
        color: #111827;
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0 0 0.45rem;
    }

    .homepage-card-copy {
        color: #4b5563;
        font-size: 0.95rem;
        line-height: 1.55;
        margin: 0;
    }

    .homepage-card-link {
        color: #ea580c;
        display: inline-block;
        font-size: 0.9rem;
        font-weight: 700;
        margin-top: 1rem;
    }

    @media (max-width: 640px) {
        .homepage-shell {
            padding-top: 0.75rem;
        }

        .homepage-hero {
            border-radius: 16px;
            padding: 1.1rem;
        }

        .homepage-title {
            font-size: 1.5rem;
        }

        .homepage-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="homepage-shell">
    <section class="homepage-hero">
        <div class="homepage-eyebrow">GKPI CRM</div>
        <h1 class="homepage-title">Selamat datang, {{ request()->user()->name }}.</h1>
        <p class="homepage-subtitle">
            Ini beranda utama untuk pengguna mobile. Pilih modul yang ingin dibuka, lalu lanjutkan ke pengelolaan jemaat, aset, keuangan, kehadiran, dan administrasi.
        </p>
    </section>

    <section class="homepage-grid">
        <a class="homepage-card" href="{{ route('jemaat.index') }}">
            <div class="homepage-card-icon">JM</div>
            <h2 class="homepage-card-title">Jemaat</h2>
            <p class="homepage-card-copy">Kelola data jemaat, keluarga, dan informasi anggota dari satu tempat.</p>
            <span class="homepage-card-link">Buka modul</span>
        </a>

        <a class="homepage-card" href="{{ route('asset.index') }}">
            <div class="homepage-card-icon">AS</div>
            <h2 class="homepage-card-title">Aset</h2>
            <p class="homepage-card-copy">Lihat daftar aset, pemeliharaan, status, dan booking aset gereja.</p>
            <span class="homepage-card-link">Buka modul</span>
        </a>

        <a class="homepage-card" href="{{ route('finance.index') }}">
            <div class="homepage-card-icon">FN</div>
            <h2 class="homepage-card-title">Keuangan</h2>
            <p class="homepage-card-copy">Masuk ke transaksi, master budget, transfer akun, dan laporan keuangan.</p>
            <span class="homepage-card-link">Buka modul</span>
        </a>

        <a class="homepage-card" href="{{ route('sermon.index') }}">
            <div class="homepage-card-icon">KH</div>
            <h2 class="homepage-card-title">Kehadiran</h2>
            <p class="homepage-card-copy">Catat ibadah, kehadiran jemaat, dan rekap kegiatan pelayanan.</p>
            <span class="homepage-card-link">Buka modul</span>
        </a>

        <a class="homepage-card" href="{{ route('profile.index') }}">
            <div class="homepage-card-icon">AD</div>
            <h2 class="homepage-card-title">Administrasi</h2>
            <p class="homepage-card-copy">Akses pengaturan user, role, permission, dan halaman administrasi lain.</p>
            <span class="homepage-card-link">Buka modul</span>
        </a>

        <a class="homepage-card" href="https://gkpi-griyapermata.org" target="_blank" rel="noreferrer">
            <div class="homepage-card-icon">WB</div>
            <h2 class="homepage-card-title">Website</h2>
            <p class="homepage-card-copy">Buka website publik GKPI Griya Permata untuk melihat konten eksternal.</p>
            <span class="homepage-card-link">Buka website</span>
        </a>
    </section>
</div>
@endsection