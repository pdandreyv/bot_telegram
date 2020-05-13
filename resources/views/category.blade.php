@extends('layouts.app')

@section('content')
    <div class="clients-wrapper">
        <div class="col-sm-12 products_main_block">
            <div id="categories_list"></div>
        </div>
    </div>
@endsection

<script>
    window.onload = function () {
        getCategoriesList();
    }
</script>