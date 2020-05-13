<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{$title}}</title>

    <!-- Styles -->
    <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <link href="{{ asset('css/datepicker3.css') }}" rel="stylesheet">

    <!-- javascript -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/javascript.js') }}"></script>
    <script>
        var inactivityTime = function () {
            var t;
            //window.onload = resetTimer;

            document.onmousemove = resetTimer;
            document.onkeypress = resetTimer;

            function logout() {
                location.href = '/logout';
            }

            function resetTimer() {
                clearTimeout(t);
                t = setTimeout(logout, 1000 * 60 * 60);
                // 1000 milisec = 1 sec
            }
        };

        inactivityTime();
    </script>
    
    @yield('head')
    
</head>
<body>
<div id="app">
    <nav id="head-nav" class="navbar navbar-inverse navbar-fixed-top">
        <ul id="head-nav-ul" class="nav navbar-nav">
            @if (Auth::user()->access == 3)
                <li><a href="{{ url('discount') }}"><i class="fa fa-line-chart" aria-hidden="true"></i><span class="head-name"> УЦЕНЕННЫЙ ТОВАР</span></a></li>
                <li> <a href="{{ url('/nomenclature') }}"><i class="fa fa-mobile" aria-hidden="true"></i><span class="head-name"> СКЛАД</span></a></li>
                <li><a href="{{ url('statistic') }}"><i class="fa fa-line-chart" aria-hidden="true"></i><span class="head-name"> СТАТИСТИКА</span></a></li>
                <li><a href="{{ url('clients') }}"><i class="fa fa-address-card" aria-hidden="true"></i><span class="head-name"> КЛИЕНТЫ</span></a></li>
            @elseif (Auth::user()->access == 1 || Auth::user()->access == 4 || Auth::user()->access == 2)
                <li><a href="{{ url('/') }}"><i class="fa fa-file-text-o" aria-hidden="true"></i><span class="head-name"> ЗАКАЗЫ </span></a></li>
                <!--<li><a href="{{ url('statistic') }}"><i class="fa fa-line-chart" aria-hidden="true"></i><span class="head-name"> СТАТИСТИКА</span></a></li>-->
                <li><a href="{{ url('clients') }}"><i class="fa fa-address-card" aria-hidden="true"></i><span class="head-name"> КЛИЕНТЫ</span></a></li>
            @elseif (Auth::user()->access == 6)
                <li><a href="{{ url('/') }}"><i class="fa fa-file-text-o" aria-hidden="true"></i><span class="head-name"> ЗАКАЗЫ </span></a></li>
                <li><a href="{{ url('statistic') }}"><i class="fa fa-line-chart" aria-hidden="true"></i><span class="head-name"> СТАТИСТИКА</span></a></li>
                <li><a href="{{ url('clients') }}"><i class="fa fa-address-card" aria-hidden="true"></i><span class="head-name"> КЛИЕНТЫ</span></a></li>
                <li><a href="{{ url('discount') }}"><i class="fa fa-line-chart" aria-hidden="true"></i><span class="head-name"> УЦЕНЕННЫЙ ТОВАР</span></a></li>
                <li> <a href="{{ url('/nomenclature') }}"><i class="fa fa-mobile" aria-hidden="true"></i><span class="head-name"> СКЛАД</span></a></li>
            @elseif (Auth::user()->access == 5)
                <li><a href="{{ url('/') }}"><i class="fa fa-file-text-o" aria-hidden="true"></i><span class="head-name"> ЗАКАЗЫ </span></a></li>
                <li><a href="{{ url('statistic') }}"><i class="fa fa-line-chart" aria-hidden="true"></i><span class="head-name"> СТАТИСТИКА</span></a></li>
                <li> <a href="{{ url('/nomenclature') }}"><i class="fa fa-mobile" aria-hidden="true"></i><span class="head-name"> СКЛАД</span></a></li>
                <li><a href="{{ url('percent') }}"><i class="fa fa-address-card" aria-hidden="true"></i><span class="head-name"> ПРОЦЕНТ</span></a></li>
                <li><a href="{{ url('clients') }}"><i class="fa fa-address-card" aria-hidden="true"></i><span class="head-name"> КЛИЕНТЫ</span></a></li>
                <li><a href="{{ url('mailing') }}"><i class="fa fa-mobile" aria-hidden="true"></i><span class="head-name"> РАССЫЛКА</span></a></li>
            @else
                <li><a href="{{ url('/') }}"><i class="fa fa-file-text-o" aria-hidden="true"></i><span class="head-name"> ЗАКАЗЫ </span></a></li>
                <li> <a href="{{ url('/nomenclature') }}"><i class="fa fa-mobile" aria-hidden="true"></i><span class="head-name"> СКЛАД</span></a></li>
                <li><a href="{{ url('statistic') }}"><i class="fa fa-line-chart" aria-hidden="true"></i><span class="head-name"> СТАТИСТИКА</span></a></li>
                <li><a href="{{ url('discount') }}"><i class="fa fa-line-chart" aria-hidden="true"></i><span class="head-name"> УЦЕНЕННЫЙ ТОВАР</span></a></li>
                <li><a href="{{ url('clients') }}"><i class="fa fa-address-card" aria-hidden="true"></i><span class="head-name"> КЛИЕНТЫ</span></a></li>
                <li><a href="{{ url('receipts') }}"><i class="fa fa-mobile" aria-hidden="true"></i><span class="head-name"> ПОСТУПЛЕНИЯ</span></a></li>
                <li><a href="{{ url('mailing') }}"><i class="fa fa-mobile" aria-hidden="true"></i><span class="head-name"> РАССЫЛКА</span></a></li>
                <li>
        <a id="dLabel" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cog" aria-hidden="true"></i><span class="head-name">НАСТРОЙКИ</span>
        <span class="caret"></span>
        </a>
          <ul id="head-drop-menu" class="dropdown-menu" aria-labelledby="dLabel">
             <li><a href="{{ url('attributes') }}">ОБЩИЕ</a></li>
             <li><a href="{{ url('bot') }}">БОТ</a></li>
             <!--<li><a href="{{ url('mailing') }}">ПОСТУПЛЕНИЯ</a></li>-->
             <li><a href="{{ url('users') }}">ПОЛЬЗОВАТЕЛИ</a></li>
              <li><a href="{{ url('/products') }}">НОМЕНКЛАТУРА</a></li>
          </ul>
    </li>

    <!--
    <li><a href="{{ url('history') }}">ИСТОРИЯ</a></li>
    -->
@endif
    @if(Auth::user()->access == 0)
        @php($clientsNew = App\Client::where('payment_type_id', null)->orWhere('current_amount', 0)->get())
        <li id="bell"><a href="#"><i class="fa fa-bell fa-2x" aria-hidden="true"><div class="round"><strong>{{ $clientsNew->count() }}</strong></div></i></a></li>
    @elseif(Auth::user()->access == 5)
        @php($clientsNew = App\Client::where('user_id', Auth::user()->id))
        @php($clientsNew = $clientsNew->where(function($query) {
            $query->where('payment_type_id', 0)
                ->orWhere('current_amount', 0)->get();
                }))
        <li id="bell"><a href="#"><i class="fa fa-bell fa-2x" aria-hidden="true"><div class="round"><strong>{{ $clientsNew->count() }}</strong></div></i></a></li>
    @endif
</ul>

    <a id="logout-button" href="{{ route('logout') }}" onclick="event.preventDefault();
    document.getElementById('logout-form').submit();">
        <span>ВЫЙТИ</span>
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                {{ csrf_field() }}
    </form>
</nav>

<div class="wrapper">
<div class="item">
    <img src="{{ asset('img/ajax-loader.gif') }}" alt="fff">
</div>
</div>
<div id="content-block">
@yield('content')
</div>
</div>
<script>
    $('#bell').hover(function () {
        $('div.round').show();
    });
    $('#bell').mouseleave(function () {
        $('div.round').hide();
    });
</script>
<script src="{{ asset ("/js/datepicker/bootstrap-datepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/js/bootstrap-datepicker.js"></script>
<!-- <script src="{{ asset ("/js/datepicker/locales/bootstrap-datepicker.ru.js") }}" charset="UTF-8"></script>-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.0/locales/bootstrap-datepicker.ru.min.js"></script>

</body>
</html>
