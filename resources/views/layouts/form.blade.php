<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GKPI - Griya Permata</title>
    
    <script>
        
    </script>

    <style>
        .header-shell {
            align-items: flex-start;
            display: flex;
            gap: 1rem;
            justify-content: space-between;
        }

        .header-menu {
            align-items: flex-start;
            display: flex;
            gap: 0.5rem;
        }

        .header-links {
            line-height: 1.8;
        }

        .user-menu {
            position: relative;
        }

        .user-menu summary {
            list-style: none;
        }

        .user-menu summary::-webkit-details-marker {
            display: none;
        }

        .user-menu-button {
            background: none;
            border: 0;
            color: #1f2937;
            cursor: pointer;
            font: inherit;
            padding: 0;
        }

        .user-menu-button:hover {
            text-decoration: underline;
        }

        .user-menu-panel {
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.12);
            min-width: 140px;
            padding: 0.35rem 0;
            position: absolute;
            right: 0;
            top: calc(100% + 0.4rem);
            z-index: 20;
        }

        .user-menu:not([open]) .user-menu-panel {
            display: none;
        }

        .user-menu-panel a,
        .user-menu-panel button {
            background: none;
            border: 0;
            color: #111827;
            cursor: pointer;
            display: block;
            font: inherit;
            padding: 0.5rem 0.9rem;
            text-align: left;
            text-decoration: none;
            width: 100%;
        }

        .user-menu-panel a:hover,
        .user-menu-panel button:hover {
            background: #f3f4f6;
        }

        @media (max-width: 768px) {
            .header-shell {
                align-items: stretch;
                flex-direction: column;
            }

            .header-links {
                line-height: 1.6;
            }

            .user-menu-panel {
                left: 0;
                right: auto;
            }
        }
    </style>

</head>
<body>
    <div class="header-shell">
        <div class="header-menu">            
            <img src="{{ asset('storage/logo-gereja.png') }}" style="width:45px;"/>
            <div class="header-links">
                <b>Menu : </b>
                <a href="{{ route('homepage') }}">Homepage</a> |
                <a href="{{ route('jemaat.index')}}">Jemaat</a> |
                <a href="{{ route('family.index')}}">Family</a> |
                <a href="{{ route('ibadah.index')}}">Ibadah</a> |
                <a href="{{ route('sermon.index')}}">Kehadiran</a> |
                <a href="{{ route('asset_type.index')}}">Tipe Asset</a> |
                <a href="{{ route('asset.index')}}">Asset</a> |
                <a href="{{ route('asset_maint.index')}}">Asset Maintenance</a> |
                <a href="{{ route('assetbooking.index')}}">Asset Booking</a> |
                <a href="{{ route('finance.index')}}">Keuangan</a> |
                <a href="{{ route('finance.transfer.create')}}">Transfer Akun</a> |
                <a href="{{ route('finance_budget_item.index')}}">Master Budget</a> |
                <a href="{{ route('finance.report')}}">Laporan Keuangan</a> |
                <br>
            </div>
        </div>
        <div>
            @if(request()->user())
                <details class="user-menu">
                    <summary class="user-menu-button">
                        Welcome, {{ request()->user()->name }}
                    </summary>

                    <div class="user-menu-panel">
                        <a href="{{ route('profile.edit') }}">Profile</a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">Logout</button>
                        </form>
                    </div>
                </details>
            @endif            
        </div>
    </div>
    <hr>

    <div>
        @yield('content')
    </div>

</body>
</html>

@yield('script')