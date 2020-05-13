@extends('layouts.app')

@section('head')
    <script src="{{ asset('js/clients.js') }}"></script>
@endsection

@section('content')
    <div class="col-sm-12 products_main_block">
        <div id="clients_list"></div>
    </div>
@endsection

<script>
    window.onload = function () {
        getClientsList();
    }
</script>