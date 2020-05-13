<h3>ДЕТАЛИ ЗАКАЗА</h3>
<input class="currentClient" type="hidden" value="{{ $clientId }}">
@if(count($orderTechnotel) || count($orderBooking))
    @if(count($orderTechnotel) > 0 && $provider != 'Booking')
        <strong>Technotel</strong>
        <table class="table" id="client_table_{{$clientId}}">
            <tr>
                <th class="td_center_text"><span>Номер</span></th>
                <th class="td_center_text"><span>Наименование</span></th>
                <th class="td_center_text"><span>Страна</span></th>
                <th class="td_center_text"><span>Кол-во</span></th>
                <th class="td_center_text"><span>Цена</span></th>
                <th class="td_center_text"><span>Сумма</span></th>
                <th></th>
            </tr>
            @php $i = 1; $sum_count = 0; @endphp
            @foreach ($orderTechnotel as $list)
                @if(Auth::user()->access === 5)
                    @php($price = $list->price)
                    @php($total = $list->total)
                @else
                    @php($price = $list->price_without_extra_charge)
                    @php($total = $list->total_without_extra_charge)
                @endif
                    <tr id="order_{{$clientId}}_{{$list->id}}" class="my_client_{{$clientId}}">
                        <td class="td_center_text"><span class="order_position">{{$i}}</span></td>
                        <td class="td_left_text">{{$list->name}}</td>
                        <td class="td_left_text">{{$list->country}}</td>
                    @if($list->deleted_at)
                        <td class="td_center_text"><span>{{$list->quantity}}</span></td>
                    @else
                        <td class="td_center_text">
                            <span class="client-table-dark"><a id="a-quantity-{{$list->id}}" onclick="view_input('quantity-{{$list->id}}', 'orders', {{$clientId}})">@php echo $list->quantity @endphp</a></span>
                            <input class="edit_info" type="text" id="quantity-{{$list->id}}" value="{{$list->quantity}} ">
                        </td>
                    @endif
                        <td class="td_center_text"><span>@php echo number_format($price,0,".",".") @endphp</span></td>
                        <td class="td_center_text"><span id="total-{{$list->id}}">@php echo number_format($total,0,".",".") @endphp</span></td>
                        <td>
                            @if($list->deleted_at == null)
                                <img class="remove_order" src="{{ asset('/img/remove.png') }}" onclick="removeOrder({{ $list->id }},{{ $clientId }})">
                            @endif
                        </td>
                    </tr>
                @php $i++; $sum_count+=$list->quantity; @endphp
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td class="td_center_text"><strong>Итого:</strong></td>
                <td class="td_center_text"><strong class="total_quantity">@php echo $sum_count; @endphp</strong></td>
                <td></td>
                <td class="td_center_text"><strong class="total_sum">@php echo number_format($sumTechno,0,".","."); @endphp</strong></td>
                <td></td>
            </tr>
        </table>
        <input type="hidden" value="Technotel" class="clientOrderProvider">
        <!--<button class="btn btn-success add-order" onclick="getProductsList('modal')" data-toggle="modal" data-target="#add_order">Добавить товар</button>-->
        <button id="send_techn_{{$clientId}}" class="btn btn-default disabled" onclick="sendOrder($(this).parent().find('.currentClient').val())">Отправить накладную</button>
        <hr>
    @endif
    @if($orderBooking->count() > 0 && $provider != 'Technotel')
        <strong>Booking</strong>
        <table class="table">
            <tr>
                <th class="td_center_text"><span>Номер</span></th>
                <th class="td_center_text"><span>Наименование</span></th>
                <th class="td_center_text"><span>Страна</span></th>
                <th class="td_center_text"><span>Кол-во</span></th>
                <th class="td_center_text"><span>Цена</span></th>
                <th class="td_center_text"><span>Сумма</span></th>
                <th></th>
            </tr>
            @php $j = 1; $sum_count = 0; @endphp
            @foreach ($orderBooking as $list)
                @if(Auth::user()->access === 5)
                    @php($price = $list->price)
                    @php($total = $list->total)
                @else
                    @php($price = $list->price_without_extra_charge)
                    @php($total = $list->total_without_extra_charge)
                @endif
                    <tr id="order_{{$clientId}}_{{$list->id}}" class="my_client_{{$clientId}}">
                        <td class="td_center_text"><span class="order_position">{{$j}}</span></td>
                        <td class="td_left_text">{{$list->name}}</td>
                        <td class="td_left_text">{{$list->country}}</td>
                    @if($list->deleted_at)
                        <td class="td_center_text"><span>{{$list->quantity}}</span></td>
                    @else
                        <td class="td_center_text">
                            <span class="client-table-dark"><a id="a-quantity-{{$list->id}}" onclick="view_input('quantity-{{$list->id}}', 'orders', {{$clientId}})">@php echo $list->quantity @endphp</a></span>
                            <input class="edit_info" type="text" id="quantity-{{$list->id}}" value="{{$list->quantity}} ">
                        </td>
                    @endif
                        <td class="td_center_text"><span>@php echo number_format($price,0,".",".") @endphp</span></td>
                        <td class="td_center_text"><span id="total-{{$list->id}}">@php echo number_format($total,0,".",".") @endphp</span></td>
                        <td>
                        @if($list->deleted_at == null)
                            <img class="remove_order" src="{{ asset('/img/remove.png') }}" onclick="removeOrder({{ $list->id }},{{ $clientId }})">
                        @endif
                        </td>
                    </tr>
                @php $j++; $sum_count+=$list->quantity; @endphp
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td class="td_center_text"><strong>Итого:</strong></td>
                <td class="td_center_text"><strong class="total_quantity">@php echo $sum_count; @endphp</strong></td>
                <td></td>
                <td class="td_center_text"><strong class="total_sum">@php echo number_format($sumBook,0,".","."); @endphp</strong></td>
                <td></td>
            </tr>
        </table>
        <input type="hidden" value="Booking" class="clientOrderProvider">
        <!--<button class="btn btn-success add-order" onclick="getProductsList('modal')" data-toggle="modal" data-target="#add_order">Добавить товар</button>-->
        <button id="send_book_{{$clientId}}" class="btn btn-default disabled send_order" onclick="sendOrder($(this).parent().find('.currentClient').val())">Отправить накладную</button>
        <hr>
    @endif
    @if(Auth::user()->access !== 5)
        <a href="{{ route('orders.reseller', ['id' => $clientId]) }}"><button class="btn btn-primary">Подробно о заказах</button></a>
    @endif
@else
    <strong>Заказов не найдено.</strong>
@endif

<div id="add_order" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <input type="hidden" value="" id="clientOrderId">
        <input type="hidden" value="" id="clientOrderProvider">
        <div class="modal-content add_product_modal" id="products_modal">

        </div>
    </div>
</div>

<script>
    $('button.add-order').on('click', function(){
        $('#clientOrderId').val($(this).parent().find('.currentClient').val());
        $('#clientOrderProvider').val($(this).prev().val());
    });
</script>

