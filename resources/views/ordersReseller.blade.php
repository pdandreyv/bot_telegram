@extends('layouts.app')

@section('head')
    <script src="{{ asset('js/products.js') }}"></script>
@endsection

@section('content')
        <div class="col-sm-12 products_main_block">
            <div class="orders-list"></div>
        </div>
@endsection

<script>
    window.onload = function() {
        getOrdersListReseller(null, null, <?php echo $resellerId  ?>);
        //setInterval('Timer()', 1000 * 60);
    };
</script>

