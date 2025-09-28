<!DOCTYPE html>
<html lang='ja'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>COACHTECH</title>
    <meta name='csrf-token' content='{{ csrf_token() }}'>

    <!-- Styles -->
    <link rel='stylesheet' href='{{ asset("css/sanitize.css") }}'>
    <link rel='stylesheet' href='{{ asset("css/style.css") }}'>

    <!-- Scripts -->
    {{-- <script src='{{ asset("js/app.js") }}' defer></script> --}}
</head>
<body>
    <header class='header'>
        <div class='header__inner'>
            <a href='/' class='header__logo'>
                <img src='{{ asset("images/logo.svg") }}' alt='COACHTECH' class='header__logo-image'>
            </a>

            <nav class='header__nav'>
                @if (session()->has('admin_id') && session()->has('admin_name'))
                    {{-- 管理者用ナビゲーション --}}
                    <a href='{{ route("admin.staff.list") }}' class='header__nav-link'>スタッフ一覧</a>
                    <a href='{{ route("admin.attendance.list") }}' class='header__nav-link'>勤怠一覧</a>
                    <a href='{{ route("admin.stamp_correction_request.list") }}' class='header__nav-link'>申請一覧</a>
                    <form method='POST' action='{{ route("admin.logout") }}' class='header__nav-form'>
                        @csrf
                        <button type='submit' class='header__nav-link header__nav-link--button'>ログアウト</button>
                    </form>
                @elseif (Auth::check())
                    {{-- 一般ユーザー用ナビゲーション --}}
                    <a href='{{ route("attendance.index") }}' class='header__nav-link'>勤怠</a>
                    <a href='{{ route("attendance.list") }}' class='header__nav-link'>勤怠一覧</a>
                    <a href='{{ route("stamp_correction_request.list") }}' class='header__nav-link'>申請</a>
                    <form method='POST' action='{{ route("logout") }}' class='header__nav-form'>
                        @csrf
                        <button type='submit' class='header__nav-link header__nav-link--button'>ログアウト</button>
                    </form>
                @endif
            </nav>


        </div>
    </header>

    <main class='main'>
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>