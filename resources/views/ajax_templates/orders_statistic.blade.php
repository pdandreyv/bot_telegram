@isset($areas)
    <div class="row product-head-row">
        <div class="product-head-blocks sum-block col-xs-4">
            <span class="product-head-name">СУММА</span></br>
            @if(Auth::user()->access !== 5)
                @php($total = number_format($areas->total_usd,2,"."," ") . ' $')
            @else
                @php($total = number_format($areas->total,0,".",".") . ' руб.')
            @endif
            <span class="product-head-quantity">{{ $total }}</span>
        </div>
        <div class="product-head-blocks col-xs-4">
            <span class="product-head-name">ПРОДАНО ПОЗИЦИЙ</span></br>
            <span class="product-head-quantity">{{$areas->product_id}}</span>
        </div>
        <div class="product-head-blocks col-xs-4">
            <span class="product-head-name">ПРОДАНО КОЛИЧЕСТВО</span></br>
            <span class="product-head-quantity">{{$areas->quantity or 0}}</span>
        </div>
    </div>
@endisset
<div class="row product-table-row">
	<div class="products-block col-xs-12">
    <div class="col-xs-3">
        <br>
        <div class="form-group">
            <input type="text" class="form-control" id="search" name="search" value="{{ $search }}" placeholder="ПОИСК ПО ИМЕНИ ИЛИ ЛОГИНУ">
        </div>
        @if ($clients->count())
            <div id="clients">
                <table class="table product-table">
                    <thead>
                    <th><span class="clients_all first_name"><strong>ВСЕ</strong></span></th>
                    </thead>
                    <tbody>
                    @foreach($clients as $client)
                    <tr>
                        @if ($client->id == $client_id)
                            <td><input type="hidden" value="{{ $client->id }}"><span class="client active_user"><span class="active_user">{{ $client->first_name }}</span><br><span class="username">{{ $client->username }}</span></span>
                        </td>
                        @else
                            <td>
                                <input type="hidden" value="{{ $client->id }}"><span class="client"><span class="first_name">{{ $client->first_name }}</span><br><span class="username">{{ $client->username }}</span></span>
                           </td>
                        @endif
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <span>Клиентов не найдено.</span>
        @endif
        <input type="hidden" class="current_user" value="{{ $client_id }}">
    </div>
    <div class="col-xs-9">
        <div class="products-block-up">
            <div class="row">
                <div class="col-xs-7">
                    <span class="product-block-bread">СТАТИСТИКА</span>
                    <div class="wrapper">
                        <div class="item">
                            <img src="{{ asset('img/ajax-loader.gif') }}" alt="Loading...">
                        </div>
                    </div>
                </div>
                <div class="col-xs-5">
                    <button class="btn btn-default btn-sm get_xls">СФОРМИРОВАТЬ И СКАЧАТЬ ОТЧЕТ (.xls)</button><br>
                </div>
            </div>
            <br>
        <div class="row">
            <div class="col-xs-8">
                </div>
                <!--div class="col-xs-4">
                    <a href="{{ asset('/exports/Statistics.xls')}}" download><button class="btn btn-success btn-sm download_xls" disabled="true">СКАЧАТЬ ОТЧЕТ</button></a>
                </div-->
            </div>
			<hr>
            <div class="row">
                <div class="col-md-6">
                    <strong>ЗА ПЕРИОД</strong>
                    <div class="input-group input-daterange">
                        <input type="text" class="form-control datepicker" value="{{ $date }}">
                        <div class="input-group-addon">до</div>
                        <input type="text" class="form-control datepicker2" value="{{ $date2 }}">
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-12">
                    <div>
                        <strong>ВЫБРАТЬ КАТЕГОРИЮ И ПРОИЗВОДИТЕЛЯ</strong>
                        <img class="show_details" src="/img/plus.png">
                        <input id="categoriesContainer" type="hidden" value="{{ $categoriesContainer }}">
                    </div>
                    <div class="categories_container">
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <div id="categories">
                                @foreach ($categories as $category)
                                    @if(in_array($category->id, $categoriesChecked))
                                        <input type="checkbox" checked class="categories" name="categories[]" value="{{$category->id}}">
                                        <lable>{{$category->name}}</lable><br>
                                    @else
                                        <input type="checkbox" class="categories" name="categories[]" value="{{$category->id}}">
                                        <lable>{{$category->name}}</lable><br>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6">
                            <div id="providers">
                                @foreach ($providers as $provider)
                                    @if(in_array($provider, $providerChecked))
                                        <input type="checkbox" checked class="providers" name="providers[]" value="{{$provider}}">
                                        <lable>{{$provider}}</lable><br>
                                    @else
                                        <input type="checkbox" class="providers" name="providers[]" value="{{$provider}}">
                                        <lable>{{$provider}}</lable><br>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
            </div>
        </div>
        <div id="stats_table">
    @if ($stats->count())
        <table class="table product-table">
            <thead>
                <tr class="product-table-thead">
                    <th><strong>НАИМЕНОВАНИЕ</strong></th>
                    <th><strong>СТРАНА</strong></th>
                    <th><strong>КОЛИЧЕСТВО</strong></th>
                @if(Auth::user()->access !== 5)
                    <th><strong>ЦЕНА($)</strong></th>
                    <th><strong>СУММА($)</strong></th>
                @else
                    <th><strong>ЦЕНА (руб.)</strong></th>
                    <th><strong>СУММА (руб.)</strong></th>
                @endif
                </tr>
            </thead>
            <tbody>
                @foreach($stats as $stat)
                    <tr class="client_info">
                        <td class="product-table-dark">{{$stat->name}}</td>
                        <td class="product-table-dark">{{$stat->country}}</td>
                        <td class="td_center_text product-table-dark">@php echo number_format($stat->sum,0,".",".") @endphp</td>
                    @if(Auth::user()->access !== 5)
                        <td class="product-table-dark">{{$stat->price_usd}}</td>
                        <td class="td_center_text product-table-dark">@php echo number_format($stat->total_usd,2,"."," ") @endphp</td>
                    @else
                        <td class="td_center_text">@php echo number_format($stat->price,0,".",".") @endphp</td>
                        <td class="td_center_text">@php echo number_format($stat->total,0,".",".") @endphp</td>
                    @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
	</div>
	@else
		<h2>Заказов нет.</h2>
	@endif
    </div>
</div>
</div>


<script>
    $('.providers').on('change',function(){
        getOrdersStatistic();
    });

    $('.categories').on('change',function(){
        var timerId = setTimeout(getOrdersStatistic, 2500);
    });

    $('#search').on('input',function(){
        getOrdersStatistic();
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

    $(function () {
        $('.datepicker2').datepicker();
    });

    $('.datepicker2').datepicker({
        timePicker: true,
        format: 'yyyy-mm-dd',
        todayHighlight: true,
        autoclose: true,
        language: 'ru',
        startDate: new Date("<?php echo $startDate; ?>"),
        endDate: new Date("<?php echo $endDate; ?>")
    });

    $('.datepicker').on('change', function() {
        getOrdersStatistic($(this).val());
    });

    $('.datepicker2').on('change', function() {
        getOrdersStatistic(null, $(this).val());
    });

    $('.pagination li a').on('click', function() {
        getOrdersStatistic(null, null, $(this).attr('href'));
        return false;
    });

    $('span.client').on('click', function() {
        getOrdersStatistic(null, null, null, $(this).prev().val());
    });

    $('span.clients_all').on('click', function() {
        $('.current_user').val('all');
        getOrdersStatistic();
    });

    $('select.product-page-num-sel').on('change', function() {
        getOrdersStatistic(null, null, null, null, $(this).val());
    });

    $('button.btn-product').on('click', function() {
        getOrdersStatistic(null, null, null, null, null, $(this).val());
    });
	
	$('button.get_xls').on('click', function() {
       getStatisticXls();
    });

    $('img.show_details').on('click', function() {
        var src = '';
        if ($(this).attr("src") === "/img/plus.png") {
            src = "/img/minus.png";
            $(this).next().val(1);
        }
        else {
            src = "/img/plus.png";
            $(this).next().val(0);
        }
        $(this).attr("src", src);
        $(this).parent().next().slideToggle(500);
    });

    $(document).ready(function(){
        var src = '';
        if($('#categoriesContainer').val() == 0) {
            $('div.categories_container').hide();
            src = "/img/plus.png";
            $('img.show_details').attr("src", src);
        }
        else {
            $('div.categories_container').show();
            src = "/img/minus.png";
            $('img.show_details').attr("src", src);
        }
    });

</script>