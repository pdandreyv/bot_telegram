@isset($areas)
    <div class="row product-head-row">
        <div class="product-head-blocks sum-block col-xs-4">
            <span class="product-head-name">СУММА</span></br>
            <span class="product-head-quantity" id="order_sum">@php echo number_format($areas->total,0,".",".") @endphp руб.</span>
        </div>
        <div class="product-head-blocks col-xs-4">
            <span class="product-head-name">ТОВАРНЫХ ПОЗИЦИЙ В ПРОДАЖЕ</span></br>
            <span class="product-head-quantity" id="order_products">{{$areas->product_id}}</span>
        </div>
        <div class="product-head-blocks col-xs-4">
            <span class="product-head-name">ТОВАРНЫХ ПОЗИЦИЙ ВСЕГО</span></br>
            <span class="product-head-quantity" id="order_quantity">{{$areas->quantity or 0}}</span>
        </div>
    </div>
@endisset

<div class="row product-table-row">
    <input type="hidden" id="resellerId" value="{{ $resellerId }}">
    <div class="products-block col-xs-12">
        <div class="products-block-up">
            <div class="row">
                <div class="col-md-8">
                    @php($reseller = App\Client::find($resellerId))
                    <span class="product-block-bread">ЗАКАЗЫ {{ mb_strtoupper($reseller->first_name . ' ' . $reseller->last_name) }}</span>
                    <div class="wrapper">
                        <div class="item">
                            <img src="{{ asset('img/ajax-loader.gif') }}" alt="Loading...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="provider">ВЫБЕРИТЕ ПОСТАВЩИКА</label>
                    <select class="form-control" id="provider">
                        <option
                                @if ($provider == 'all')
                                {{'selected'}}
                                @endif
                                value="all">ВСЕ</option>
                        <option
                                @if ($provider == 'Technotel')
                                {{'selected'}}
                                @endif
                                value="Technotel">Technotel</option>
                        <option
                                @if ($provider == 'Booking')
                                {{'selected'}}
                                @endif
                                value="Booking">Booking</option>
                    </select>
                </div>
            </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="region">РЕГИОН</label>
                        <select id="region" class="form-control">
                            @if (Auth::user()->getAccess() || $user->access == 5)
                                <option
                                        @if ($id == 'all')
                                        {{'selected'}}
                                        @endif
                                        value="all">Все</option>
                            @elseif ($user->access == 4)
                                <option
                                    @if ($id == 'allRegions')
                                    {{'selected'}}
                                    @endif
                                    value="allRegions">Все регионы</option>
                            @endif
                            @foreach($regions as $one)
                                @if($one)
                                    @if($user->access == 1 && $one != 'Москва')
                                        @continue
                                    @elseif($user->access == 4 && $one == 'Москва')
                                        @continue
                                    @endif
                                    <option
                                        @if ($one == $id)
                                        {{'selected'}}
                                        @endif
                                        value="{{ $one }}">{{ $one }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group date">
                        <label>ДАТА ЗАКАЗА</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input class="form-control pull-right datepicker" id="" type="text" name="date" value="{{ $date }}">
                        </div>
                    </div>
                </div>
        </div>
    @if (!count($orders))
        <h2>Заказов нет.</h2>
    @else
    <input type="hidden" value="" id="client_show">
    <table class="table product-table">
        <thead>
			<tr class="product-table-thead">
				<th>№</th>
				<th>КАТЕГОРИЯ</th>
				<th>ФИО ЗАКАЗЧИКА</th>
				<th>LOGIN В СИСТЕМЕ</th>
				<th>СУММА ЗАКАЗА</th>
				<th>КОЛИЧЕСТВО ПОЗИЦИЙ</th>
				<th>КОЛИЧЕСТВО ВСЕГО</th>
				<th></th>
			</tr>
        </thead>
        <tbody>
		@foreach ($orders as $order)
			<tr class="client_info" id="order_client_{{ $order->client_id }}">
				<td class="td_center_text">
                    <span class="currentOrder">{{ $order->id}}</span>
				</td>
                @if($order->type === 0)
                    <td><span class="product-table-bright"><button class="btn btn-success btn-xs">   Мелкий  </button></span></td>
                @elseif($order->type === 1)
                    <td><span class="product-table-bright"><button class="btn btn-primary btn-xs">Крупный</button></span></td>
                @elseif($order->type === 2)
                    <td><span class="product-table-bright"><button class="btn btn-warning btn-xs">Средний</button></span></td>
                @endif
				<td><span class="product-table-bright">{{$order->firstname}} {{$order->lastname}}</span></td>
				<td><span class="product-table-bright">{{$order->username}}</span></td>
				<td><span class="product-table-bright order_sum">@php echo number_format($order->total,0,".","."); @endphp</span></td>
				<td><span class="product-table-bright order_products">@php echo $order->product_id;  @endphp</span></td>
				<td><span class="product-table-bright order_quantity">@php echo $order->quantity;  @endphp</span></td>
				<td><img id="image_client_{{ $order->client_id }}" src='/img/plus.png' alt="plus" class="show_details"><input type="hidden" value="{{ $order->client_id }}"></td>
			</tr>
			<tr>
                <td colspan="8" class="details">
                    <div class="list-group list_orders" id="client_{{ $order->client_id }}">
                    <!-- Providers List -->
                    </div>
                </td>
		    </tr>
        @endforeach
        </tbody>
    </table>
    @include('parts.pagination_block')
    </div>
    @endif
</div>
</div>

<script>
    $('#region').on('change',function(){
        getOrdersListReseller();
    });
    $('#provider').on('change',function(){
        getOrdersListReseller();
    });

    $(function () {
        $('.datepicker').datepicker();
    });

    $('.datepicker').datepicker({
        timePicker: true,
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true,
        language: 'ru',
        datesDisabled: <?php echo $disableDates; ?>,
        startDate: new Date("<?php echo $startDate; ?>"),
        endDate: new Date("<?php echo $endDate; ?>")
    });


    $('.pagination li a').on('click', function() {
        var url = $(this).attr('href');
        getOrdersListReseller(url);
        return false;
    });


    $('.datepicker').on('change', function() {
        getOrdersListReseller();
    });
	
	$('img.show_details').on('click', function() {
        var div = $(this).parent().parent().next().find('div.list_orders');
	    var providersLists = $('div.list_orders');
        $.each( providersLists, function( key, value ) {
            if ($(value).attr('id') != $(div).attr('id')) {
                $(value).hide();
                var image = $(value).parent().parent().prev().find('img.show_details');
                var src = "/img/plus.png";
                image.attr("src", src);
            }
        });

        $(div).slideToggle(500);
        getProvidersListsReseller($(this).next().val(), $(this).parent().parent().find('.currentOrder').text());
		var src = ($(this).attr("src") === "/img/plus.png")
                    ? "/img/minus.png" 
                    : "/img/plus.png";
		$(this).attr("src", src);
	});
	
	$('select.product-page-num-sel').on('change', function() {
        getOrdersListReseller(null, $(this).val());
	});
</script>