<div class="clients-wrapper">
<div class="row product-table-row">
    <div class="products-block col-xs-12">
        <div class="products-block-up">
            <div class="row">
                <div class="col-md-3">
                    <span class="product-block-bread">КЛИЕНТЫ ({{ $clientsCounter }})</span>
                    <div class="wrapper">
                        <div class="item">
                            <img src="{{ asset('img/ajax-loader.gif') }}" alt="Loading...">
                        </div>
                    </div>
                </div>
            @if(!in_array(Auth::user()->access, [5, 6, 2, 3]))
                <div class="col-md-8">
                    <button class="btn btn-default" onclick="activate_clients(0)"> ОТКЛЮЧИТЬ ВСЕХ КЛИЕНТОВ </button>
                    <button class="btn btn-primary" onclick="activate_clients(1)"> АКТИВИРОВАТЬ ВСЕХ КЛИЕНТОВ </button>
                </div>
            @endif
            </div>
        </div>
        <hr>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label for="region">РЕГИОН</label>
                <select id="region" class="form-control">
                    <option
                        @if ($region == 'all')
                        {{'selected'}}
                        @endif
                        value="all">Все</option>
                    @foreach($regions as $one)
                        @if($one)
                            <option
                                @if ($one == $region)
                                    {{'selected'}}
                                @endif
                                value="{{ $one }}">{{ $one }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="group">ГРУППА</label>
                <select id="group" class="form-control">
                    <option
                            @if ($group == 'all')
                            {{'selected'}}
                            @endif
                            value="all">ВСЕ</option>
                    <option
                            @if ($group == 'big')
                            {{'selected'}}
                            @endif
                            value="big">КРУПНЫЙ ОПТ</option>
                    <option
                            @if ($group == 'medium')
                            {{'selected'}}
                            @endif
                            value="medium">СРЕДНИЙ ОПТ</option>
                    <option
                            @if ($group == 'small')
                            {{'selected'}}
                            @endif
                            value="small">МЕЛКИЙ ОПТ</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="group">ТИП ОПЛАТЫ</label>
                <select id="paymentType" class="form-control">
                    <option
                            @if ($paymentType == 'all')
                            {{'selected'}}
                            @endif
                            value="all">ВСЕ</option>
                    <option
                            @if ($paymentType == 1)
                            {{'selected'}}
                            @endif
                            value="1">ПО ФАКТУ</option>
                    <option
                            @if ($paymentType == 2)
                            {{'selected'}}
                            @endif
                            value="2">ПО ПРЕДОПЛАТЕ</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label for="group">ПОИСК</label>
                <input type="text" class="form-control" id="search2" name="search" value="{{ $search }}" placeholder="ПОИСК ПО ИМЕНИ ИЛИ TGNAME">
            </div>
        </div>
    </div>
    @if ($clients->count())
    <table class="table tproduct-table">
        <thead>
            <tr class="product-table-thead">
                <th class="middle">ФИО<i class="fa fa-chevron-up table-fa-first sort" aria-hidden="true"></i><input type="hidden" value="first_name-ASC"><i class="fa fa-chevron-down table-fa-second sort" aria-hidden="true"></i><input type="hidden" value="first_name-DESC"></th>
            @if(Auth::user()->access == 0)
                <th class="middle">НОМЕР КЛИЕНТА</th>
            @endif
                <th class="middle">АКТИВНОСТЬ</th>
                <th class="middle">ТИП ОПЛАТЫ</th>
                <th class="middle">СУММА</th>
                <th class="middle">ГОРОД<i class="fa fa-chevron-up table-fa-first sort" aria-hidden="true"></i><input type="hidden" value="city-ASC"><i class="fa fa-chevron-down table-fa-second sort" aria-hidden="true"></i><input type="hidden" value="city-DESC"></th>
                <th class="middle"></th>
                <th></th>
            </tr>
        </thead>
        <tbody id='test_ajax'>
        @foreach ($clients as $client)
            <tr class="client_info">
                <td>
                    @if ($client->user && $client->user->access == 5)
                        <button class="btn btn-primary btn-xs resellerInfo">+</button>
                        <input id=resellerUserId type="hidden" value="{{ $client->id }}">
                    @endif
                    @if(!in_array(Auth::user()->access, [6, 2, 3]))
                        <span class="client-table-dark"><a id="a-first_name-{{$client->id}}" onclick="view_input('first_name-{{$client->id}}', 'clients')">@php echo $client->first_name?$client->first_name:'...' @endphp</a></span>
                        <input class="edit_info" type="text" id="first_name-{{$client->id}}" value="{{$client->first_name}}">
                        <span class="client-table-dark"><a id="a-last_name-{{$client->id}}" onclick="view_input('last_name-{{$client->id}}', 'clients')">@php echo $client->last_name?$client->last_name:'...' @endphp</a></span>
                        <input class="edit_info" type="text" id="last_name-{{$client->id}}" value="{{$client->last_name}}">
                    @else
                        <span>@php echo $client->first_name?$client->first_name:'...' @endphp</span>
                        <span>@php echo $client->last_name?$client->last_name:'...' @endphp</span>
                    @endif
                    @if($client->payment_type_id == 0 || $client->current_amount == 0)
                        <span class="new_client"><strong>новый</strong></span>
                    @endif
                </td>
                @if(Auth::user()->access === 0)
                <td>
                    <span class="client-table-dark"><a id="a-unique_number-{{$client->id}}" onclick="view_input('unique_number-{{$client->id}}', 'clients')">@php echo $client->unique_number?$client->unique_number:'...' @endphp</a></span>
                    <input class="edit_info" type="text" id="unique_number-{{$client->id}}" value="{{$client->unique_number}}">
                </td>
                @endif
                <td class="td_center_text">
                    @if($client->active)
                        <button class="btn btn-success btn-xs" @if(!in_array(Auth::user()->access, [6, 2, 3])) onclick="changeActivity({{$client->id}})" @endif>&nbsp;&nbsp;Активный&nbsp;&nbsp;</button>
                    @else
                        <button class="btn btn-danger btn-xs" @if(!in_array(Auth::user()->access, [6, 2, 3])) onclick="changeActivity({{$client->id}})" @endif>Неактивный</button>
                    @endif
                </td>
                <td>
                    @if(!in_array(Auth::user()->access, [6, 3]))
                        <select class="client_group" class="form-control">
                                    <option value="null_paymentType_{{$client->id}}">Не выбрано</option>
                                    @foreach($paymentTypes as $type)
                                        @if($client->paymentType)
                                            <option
                                        @if ($client->payment_type_id == $type->id)
                                        {{'selected'}}
                                        @endif
                                        value="{{ $type->id }}_paymentType_{{$client->id}}">{{ $type->name }}</option>
                                @else
                                    <option
                                        value="{{ $type->id }}_paymentType_{{$client->id}}">{{ $type->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    @else
                        @if($client->paymentType)
                            {{ $client->paymentType->name }}
                        @else
                            Не выбрано
                        @endif
                    @endif
                </td>
                <td>
                    @if(!in_array(Auth::user()->access, [6, 2, 3]))
                        <span class="client-table-dark"><a id="a-current_amount-{{$client->id}}" onclick="view_input('current_amount-{{$client->id}}', 'clients')">@php echo number_format($client->current_amount,0,".",".") @endphp</a></span>
                        <input class="edit_info" type="text" id="current_amount-{{$client->id}}" value="{{$client->current_amount}} ">
                    @else
                        <span>@php echo number_format($client->current_amount,0,".",".") @endphp</span>
                    @endif
                </td>
                <td class="td_center_text">
                    @if(!in_array(Auth::user()->access, [6, 2, 3]))
                        <span class="client-table-dark"><a id="a-city-{{$client->id}}" onclick="view_input('city-{{$client->id}}', 'clients')">@php echo $client->city?$client->city:'...' @endphp</a></span>
                        <input class="edit_info" type="text" id="city-{{$client->id}}" value="{{$client->city}} ">
                    @else
                        <span>@php echo $client->city ? $client->city : '...' @endphp</span>
                    @endif
                </td>
                <td class="text-right">
                    <a href="{{ url('/history/'.$client->id) }}" title="История"><i class="fa fa-list-alt" aria-hidden="true"></i></a>
                @if(!in_array(Auth::user()->access, [6, 2, 3]))
                        <a href="#" onclick="getClientData({{$client->id}})" title="Удалить" data-toggle="modal" data-target=".bs-example-modal-lg3"><img class="remove_order" src="{{ asset('/img/remove.png') }}"></a>
                @endif
                @if(!in_array(Auth::user()->access, [6, 3]))
                    <a href="#" onclick="getClientData({{$client->id}})" title="Редактировать" data-toggle="modal" data-target=".bs-example-modal-lg2"><i data-client="{{ $client->id }}" class="fa fa-cog product-table-cog" aria-hidden="true"></i></a>
                @endif
                </td>
            </tr>
            @if ($client->user && $client->user->access == 5)
                @php($resellerClients = App\Client::where('user_id', $client->user->id)->get())
                @if($resellerClients->count())
                    @foreach($resellerClients as $one)
                        <tr class="client_info_{{ $client->id }} resellerClients">
                            <td>
                                @if(!in_array(Auth::user()->access, [6, 2, 3]))
                                    <span class="client-table-dark"><a id="a-first_name-{{$one->id}}" onclick="view_input('first_name-{{$one->id}}', 'clients')">@php echo $one->first_name?$one->first_name:'...' @endphp</a></span>
                                    <input class="edit_info" type="text" id="first_name-{{$one->id}}" value="{{$one->first_name}}">
                                    <span class="client-table-dark"><a id="a-last_name-{{$one->id}}" onclick="view_input('last_name-{{$one->id}}', 'clients')">@php echo $one->last_name?$one->last_name:'...' @endphp</a></span>
                                    <input class="edit_info" type="text" id="last_name-{{$one->id}}" value="{{$one->last_name}}">
                                @else
                                    <span>@php echo $one->first_name?$one->first_name:'...' @endphp</span>
                                    <span>@php echo $one->last_name?$one->last_name:'...' @endphp</span>
                                @endif
                                @if($one->payment_type_id == 0 || $one->current_amount == 0)
                                    <span class="new_client"><strong>новый</strong></span>
                                @endif
                            </td>
                            @if(Auth::user()->access === 0)
                                <td>
                                    <span class="client-table-dark"><a id="a-unique_number-{{$one->id}}" onclick="view_input('unique_number-{{$one->id}}', 'clients')">@php echo $one->unique_number?$one->unique_number:'...' @endphp</a></span>
                                    <input class="edit_info" type="text" id="unique_number-{{$one->id}}" value="{{$one->unique_number}}">
                                </td>
                            @endif
                            <td class="td_center_text">
                                @if($one->active)
                                    <button class="btn btn-success btn-xs" @if(!in_array(Auth::user()->access, [6, 2, 3])) onclick="changeActivity({{$one->id}})" @endif>&nbsp;&nbsp;Активный&nbsp;&nbsp;</button>
                                @else
                                    <button class="btn btn-danger btn-xs" @if(!in_array(Auth::user()->access, [6, 2, 3])) onclick="changeActivity({{$one->id}})" @endif>Неактивный</button>
                                @endif
                            </td>
                            <td>
                                @if(!in_array(Auth::user()->access, [6, 2, 3]))
                                    <select class="client_group" class="form-control">
                                        <option value="null_paymentType_{{$one->id}}">Не выбрано</option>
                                        @foreach($paymentTypes as $type)
                                            @if($one->paymentType)
                                                <option
                                                        @if ($one->payment_type_id == $type->id)
                                                        {{'selected'}}
                                                        @endif
                                                        value="{{ $type->id }}_paymentType_{{$one->id}}">{{ $type->name }}</option>
                                            @else
                                                <option
                                                        value="{{ $type->id }}_paymentType_{{$one->id}}">{{ $type->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                @else
                                    @if($one->paymentType)
                                        {{ $one->paymentType->name }}
                                    @else
                                        Не выбрано
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if(!in_array(Auth::user()->access, [6, 2, 3]))
                                    <span class="client-table-dark"><a id="a-current_amount-{{$one->id}}" onclick="view_input('current_amount-{{$one->id}}', 'clients')">@php echo number_format($one->current_amount,0,".",".") @endphp</a></span>
                                    <input class="edit_info" type="text" id="current_amount-{{$one->id}}" value="{{$one->current_amount}} ">
                                @else
                                    <span>@php echo number_format($one->current_amount,0,".",".") @endphp</span>
                                @endif
                            </td>
                            <td class="td_center_text">
                                @if(!in_array(Auth::user()->access, [6, 2, 3]))
                                    <span class="client-table-dark"><a id="a-city-{{$one->id}}" onclick="view_input('city-{{$one->id}}', 'clients')">@php echo $one->city?$one->city:'...' @endphp</a></span>
                                    <input class="edit_info" type="text" id="city-{{$one->id}}" value="{{$one->city}} ">
                                @else
                                    <span>@php echo $one->city ? $one->city : '...' @endphp</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ url('/history/'.$one->id) }}" title="История"><i class="fa fa-list-alt" aria-hidden="true"></i></a>
                            @if(!in_array(Auth::user()->access, [6, 2, 3]))
                                <a href="#" onclick="getClientData({{$one->id}})" title="Удалить" data-toggle="modal" data-target=".bs-example-modal-lg3"><img class="remove_order" src="{{ asset('/img/remove.png') }}"></a>
                            @endif
                            @if(!in_array(Auth::user()->access, [6, 3]))
                                <a href="#" onclick="getClientData({{$one->id}})" title="Редактировать" data-toggle="modal" data-target=".bs-example-modal-lg2"><i data-client="{{ $one->id }}" class="fa fa-cog product-table-cog" aria-hidden="true"></i></a>
                            @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            @endif
        @endforeach
        </tbody>
    </table>
    @else
        <h3>Клиентов нет.</h3>
    @endif
    </div>
</div>
</div>

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div id="cats" class="modal-content add_product_modal">
            <h4>Выберите категории, которые не будет видеть клиент</h4><hr>
            <input type="hidden" id="client_id" name="client_id" value="">
            @foreach ($categories as $category)
                @if ($category->id != 7)
                    <input type="checkbox" class="categories" name="categories[]" value="{{$category->id}}">
                    <lable>{{ $category->name }}</lable><br>
                @endif
            @endforeach
            <hr>
            <input type="checkbox" id="showReceipts" name="showReceipts" onclick="changeReceipts($('.client_remove').val())">
            <lable>Поступления</lable><br>
            <br>
            <button onclick="saveClientCats($('#client_id').val(),$('.categories:checkbox:checked').map(function(){return $(this).val();}).get());$('.bs-example-modal-lg2').modal('toggle')" class="btn btn-primary">Сохранить</button>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content add_product_modal">
            <input type="hidden" class="current_activity" name="" value="">
            <h4>Настройки пользователя</h4><i class="fa fa-times" aria-hidden="true" onclick="$('.bs-example-modal-lg2').modal('toggle')"></i>
            <hr>
            <span class="title">tgName</span><br>
            <div class="tgname"> </div>
            <span class="title">UID</span><br>
            <div class="uid"></div>
        @if(!in_array(Auth::user()->access, [5, 2]))
            <span class="title">Невидимые категории</span><br>
            <span class="client-table-dark"><a href="#" onclick="showCats($('.client_remove').val());$('.bs-example-modal-lg2').modal('toggle')" data-toggle="modal" data-target=".bs-example-modal-lg">Изменить</a></span>
            <br><br>
        @endif
            <span class="title">Группа</span><br>
            @if(Auth::user()->access !== 2)
            <div>
                <select id="typeGroup" class="client_type form-control">

                </select>
            </div>
            @else
            <div>
                <strong><span class="type_group"></span></strong>
            </div>
            @endif
            <br>
        </div>
    </div>
</div>

<div class="modal fade bs-example-modal-lg3" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content add_product_modal">
            <input class="client_remove" type="hidden" value="">
            <h4>Удаление пользователя</h4><i class="fa fa-times" aria-hidden="true" onclick="$('.bs-example-modal-lg3').modal('toggle')"></i>
            <hr>
            <div class="alert alert-info"><strong>Данное действие приведет к удалению записи о клиенте!</strong>
                Вы уверены в этом? Восстановить данную запись после этого будет невозможно.</div><hr>
            <button onclick="removeClient($('.client_remove').val())" class="btn btn-danger">Удалить</button>
            <button onclick="$('.bs-example-modal-lg3').modal('toggle')" class="btn btn-default">Отменить</button>
        </div>
    </div>
</div>

<script>
    $('#search2').on('input', function(){
        getClientsList();
    });

    $('#region').on('change',function(){
        getClientsList();
    });

    $('#group').on('change',function(){
        getClientsList();
    });

    $('#paymentType').on('change',function(){
        getClientsList();
    });

    $('.client_group').on('change', function(){
        check_checkbox($(this).val());
    });

    $('.client_type').on('change', function(){
        var id = $(this).val() + $('.client_remove').val();
        check_checkbox(id);
    });

    $('.unique_number').on('input', function(){
        var id = $(this).val() + '+' + $('.client_remove').val();
        changeNumber(id);
    });

    $('.pagination li a').on('click', function() {
        var url = $(this).attr('href');
        getClientsList(url);
        return false;
    });

    $('select.product-page-num-sel').on('change', function() {
        getClientsList(null, $(this).val());
    });

    $('.sort').on('click', function() {
        $('.sort').removeClass('sorting_active');
        $(this).addClass('sorting_active');
        getClientsList(null, null, $(this).next().val());
    });

    $('.sort').each(function(){
        if ($(this).next().val() == '<?php echo $sorting; ?>') {
            $(this).addClass('sorting_active');
        }
    });

    $('button.resellerInfo').on('click', function() {
        var clientId = $(this).next().val();
        $('tr.client_info_' + clientId).each(function() {
            $(this).toggle(400);
        });
    });

</script>
