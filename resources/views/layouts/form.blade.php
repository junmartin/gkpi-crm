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
    <div>
        <div>            
            <b>Menu : </b>
            <a href="{{ route('jemaat.index')}}">Jemaat</a> |
            <a href="{{ route('family.index')}}">Family</a> |
            <a href="{{ route('ibadah.index')}}">Ibadah</a> |
            <a href="{{ route('attendance.index')}}">Kehadiran</a> |
            <br>
        </div>
    </div>
    <hr>

    <div>
        @yield('content')
    </div>

</body>
</html>

@yield('script')