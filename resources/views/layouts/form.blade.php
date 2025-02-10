<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GKPI - Griya Permata</title>
    
    <script>
        
    </script>

</head>
<body>
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>            
            <img src="{{ asset('storage/logo-gereja.png') }}" style="width:45px;"/>
            <b>Menu : </b>
            <a href="{{ route('jemaat.index')}}">Jemaat</a> |
            <a href="{{ route('family.index')}}">Family</a> |
            <a href="{{ route('ibadah.index')}}">Ibadah</a> |
            <a href="{{ route('sermon.index')}}">Kehadiran</a> |
            <a href="{{ route('asset_type.index')}}">Tipe Asset</a> |
            <a href="{{ route('asset.index')}}">Asset</a> |
            <a href="{{ route('asset_maint.index')}}">Asset Maintenance</a> |
            <br>
        </div>
        <div>
            @if(request()->user())
                <p>Welcome, {{ request()->user()->name }}!</p>
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