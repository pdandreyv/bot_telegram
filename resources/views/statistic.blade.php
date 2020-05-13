@extends('layouts.app')

@section('content')
    <div class="col-sm-12 products_main_block">
        <div class="orders-statistic">

        </div>
    </div>
@endsection

<script>
    window.onload = function () {
        getOrdersStatistic();
        setInterval('Timer()', 1000 * 60);
    }
</script>