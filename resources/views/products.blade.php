@extends('settings_layout')

@section('head')
    <script src="{{ asset('js/products.js') }}"></script>
@endsection

@section('settings-content')
    <div id="products_list"></div>
@endsection

<script>
    window.onload = function () {
        getProductsList();
    }
</script>
