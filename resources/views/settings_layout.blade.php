@extends('layouts.app')

@section('head')

@endsection

@section('content')
    <div class="col-sm-12 products_main_block">
    <ul class="tab-menu">
        <li @if($page == 'attributes') class="active" @endif ><a href="{{ url('attributes') }}">ОБЩИЕ</a></li>
        <li @if($page == 'bot_setting') class="active" @endif><a href="{{ url('bot') }}">БОТ</a></li>
        <li @if($page == 'user') class="active" @endif><a href="{{ url('users') }}">ПОЛЬЗОВАТЕЛИ</a></li>
        <li @if($page == 'product') class="active last" @else class="last" @endif><a href="{{ url('products') }}">НОМЕНКЛАТУРА</a></li>
    </ul>

    <div class="settings-wrapper">
        <div class="row">
            <div class="products-block col-xs-12">
                @if (count($errors) > 0)
                    <div class="row">
                        <div class="alert alert-danger status-box col-sm-6 alert-wrapper">
                            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
                @if(Session::has('clientNotFound'))
                    <div class="row">
                        <div class="alert alert-danger status-box col-sm-6 alert-wrapper">
                            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            {{ Session::get('clientNotFound') }}
                        </div>
                    </div>
                @endif
                @yield('settings-content')
            </div>
        </div>
    </div>
    </div>
@endsection