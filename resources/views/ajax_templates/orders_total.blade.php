@if ($orders_total)
    <div class="row product-head-row">
        <div class="product-course-block col-xs-2 col-xs-offset-1">
            <span class="product-course-cost">56.80</span><i class="product-arrow fa fa-long-arrow-down" aria-hidden="true"></i> </br>
            <span class="product-course-name">текущий курс</span>
        </div>
        <div class="product-head-blocks col-xs-2 col-xs-offset-1">
            <span class="product-head-name">СУММА</span></br>
            <span class="product-head-quantity">@php echo number_format($orders_total->total,0,".",".") @endphp руб.</span>
        </div>
        <div class="product-head-blocks col-xs-3">
            <span class="product-head-name">ТОВАРНЫХ ПОЗИЦИЙ В ПРОДАЖЕ</span></br>
            <span class="product-head-quantity">{{$orders_total->product_id}}</span>
        </div>
        <div class="product-head-blocks col-xs-3">
            <span class="product-head-name">ТОВАРНЫХ ПОЗИЦИЙ ВСЕГО</span></br>
            <span class="product-head-quantity">{{$orders_total->quantity}}</span>
        </div>
    </div>
@else
    <div class="row product-head-row">
        <div class="product-course-block col-xs-2 col-xs-offset-1">
            <span class="product-course-cost">56.80</span><i class="product-arrow fa fa-long-arrow-down" aria-hidden="true"></i> </br>
            <span class="product-course-name">текущий курс</span>
        </div>
        <div class="product-head-blocks col-xs-2 col-xs-offset-1">
            <span class="product-head-name">СУММА</span></br>
            <span class="product-head-quantity">0 руб.</span>
        </div>
        <div class="product-head-blocks col-xs-3">
            <span class="product-head-name">ТОВАРНЫХ ПОЗИЦИЙ В ПРОДАЖЕ</span></br>
            <span class="product-head-quantity">0</span>
        </div>
        <div class="product-head-blocks col-xs-3">
            <span class="product-head-name">ТОВАРНЫХ ПОЗИЦИЙ ВСЕГО</span></br>
            <span class="product-head-quantity">0</span>
        </div>
    </div>
@endif
