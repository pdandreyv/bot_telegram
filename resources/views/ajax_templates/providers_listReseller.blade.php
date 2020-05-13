<h3>ДЕТАЛИ ЗАКАЗА</h3>
@if(count($orderTechnotel) > 0 && $provider != 'Booking')
    <strong>Technotel</strong>
    <table class="table">
        <tr>
            <th class="td_center_text"><span>Номер</span></th>
            <th class="td_center_text"><span>Наименование</span></th>
            <th class="td_center_text"><span>Страна</span></th>
            <th class="td_center_text"><span>Кол-во</span></th>
            <th class="td_center_text"><span>Цена</span></th>
            <th class="td_center_text"><span>Сумма</span></th>
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
            <tr>
                <td class="td_center_text"><span class="order_position">{{$i}}</span></td>
                <td class="td_left_text">{{$list->name}}</td>
                <td class="td_left_text">{{$list->country}}</td>
                <td class="td_center_text"><span>{{$list->quantity}}</span></td>
                <td class="td_center_text"><span>@php echo number_format($price,0,".",".") @endphp</span></td>
                <td class="td_center_text"><span id="total-{{$list->id}}">@php echo number_format($total,0,".",".") @endphp</span></td>
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
        </tr>
    </table>
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
            <tr>
                <td class="td_center_text"><span class="order_position">{{$j}}</span></td>
                <td class="td_left_text">{{$list->name}}</td>
                <td class="td_left_text">{{$list->country}}</td>
                <td class="td_center_text"><span>{{$list->quantity}}</span></td>
                <td class="td_center_text"><span>@php echo number_format($price,0,".",".") @endphp</span></td>
                <td class="td_center_text"><span id="total-{{$list->id}}">@php echo number_format($total,0,".",".") @endphp</span></td>
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
        </tr>
    </table>
@endif