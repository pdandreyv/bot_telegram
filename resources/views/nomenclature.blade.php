@extends('layouts.app')

@section('head')
    <script src="{{ asset('js/products.js') }}"></script>
@endsection

@section('content')
    <div class="col-sm-12 products_main_block">
        <div class="row product-head-row">
            <div class="product-head-blocks col-xs-4">
                <span class="product-head-name">КОЛИЧЕСТВО ВСЕГО</span></br>
                <span class="product-head-quantity">{{$all_prod_count}}</span>
            </div>
            <div class="product-head-blocks col-xs-4">
                <span class="product-head-name">ПРОДАНО</span></br>
                <span class="product-head-quantity">{{$quantity}}</span>
            </div>
            <div class="product-head-blocks sum-block col-xs-4">
                <span class="product-head-name">ОСТАТОК</span></br>
                <span class="product-head-quantity">{{$prod_count}}</span>
            </div>
        </div>
        <div class="row product-table-row">
            <div class="products-block col-xs-12">
                <div class="products-block-up">
                    <div class="row">
                        <div class="col-md-4">
                            <span class="product-block-bread">СКЛАД</span>
                            <div class="wrapper">
                                <div class="item">
                                    <img src="{{ asset('img/ajax-loader.gif') }}" alt="Loading...">
                                </div>
                            </div>
                        </div>
                    </div>
                @if(Auth::user()->access !== 6 && Auth::user()->access !== 3)
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <form id="form-import" action="{{url('/nomenclature/import')}}" name="form-import" method="post"  enctype="multipart/form-data">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <label for="upload" class="product-btn-default">
                                    <span class="btn btn-default" aria-hidden="true">ОБНОВИТЬ БАЗУ</span>
                                    <input type="file" id="upload" name="upload" onchange="$('#form-import').submit()" style="display:none">
                                </label>
                            </form>
                            <label class="product-btn-primary">
                                @if(Auth::user()->access != 5)
                                    <span class="btn btn-success" onclick="getProductsList('nomenclature')" data-toggle="modal" data-target="#add_order" aria-hidden="true">ДОБАВИТЬ ТОВАР</span>
                                @else
                                    <a href="{{ url('percent') }}"><span class="btn btn-success" aria-hidden="true">ПРОЦЕНТ</span></a>
                                @endif
                            </label>
                        </div>
                    </div>
                @endif
                </div>
                <hr>
                @if(Session::has('message'))
                    <div class="alert alert-{{ Session::get('status') }} status-box">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        {{ Session::get('message') }}
                    </div>
                @endif
                @if(Session::has('fileError'))
                    @php ($errors = Session::get('fileError'))
                    @foreach($errors as $error)
                        <div class="alert alert-danger status-box">
                            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            {{ $error }}
                        </div>
                    @endforeach
                @endif
                @if(Session::has('productsError'))
                    @php ($errors = Session::get('productsError'))
                    @foreach($errors as $error)
                        <div class="alert alert-danger status-box">
                            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                            {{ $error }}
                        </div>
                    @endforeach
                @endif
                @if($products != null)
                    <table class="table product-table">
                        <thead>
                        <tr class="product-table-thead">
                            <th>ПОЗИЦИЯ</th>
                            <th>НАИМЕНОВАНИЕ</th>
                            <th>СТРАНА</th>
                        @if(Auth::user()->access != 5)
                            <th>КОЛ-ВО В ОДНИ РУКИ</th>
                            <th>КОЛ-ВО НА СКЛАДЕ</th>
                            <th>ЦЕНА В $</th>
                        @endif
                        @if(Auth::user()->access == 5)
                            <th>ЦЕНА ЗАКУПКИ</th>
                        @endif
                            <th>КРУПНЫЙ ОПТ</th>
                            <th>СРЕДНИЙ ОПТ</th>
                            <th>МЕЛКИЙ ОПТ</th>
                        @if(Auth::user()->access != 5)
                            <th>УВЕЛИЧЕНИЕ КОЛ-ВА</th>
                            <th>УВЕЛИЧЕНИЕ ЦЕНЫ</th>
                        @endif
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($products as $product)
                            @if(Auth::user()->access === 5)
                                @php($type = Auth::user()->client->type)
                                @if($type == 0)
                                    @php($price = $product->price_old)
                                @elseif($type == 1)
                                    @php($price = $product->price_opt_old)
                                @elseif($type == 2)
                                    @php($price = $product->price_middle_old)
                                @endif
                            @endif
                            <tr>
                                <td class="td_center_text product_row"><span class="product-table-dark">
                                @if(Auth::user()->access == 0)
                                    <a id="a-position_xls-{{$product->id}}" onclick="view_input('position_xls-{{$product->id}}', 'products')">{{$product->position_xls}}</a>
                                    <input class="edit_info" type="text" id="position_xls-{{$product->id}}" value="{{$product->position_xls}}"></span>
                                @else
                                    <span>{{ $product->position_xls }}</span>
                                @endif
                                </td>
                                <td class="product_row">
                                    <span data-id="{{$product->id}}" class="product-table-dark">@php echo $product->name?$product->name:'...' @endphp</span>
                                </td>
                                <td class="product_row">
                                    <span class="product-table-dark">{{$product->country}}</span>
                                </td>
                            @if(Auth::user()->access != 5)
                                <td class="product_row">
                                @if(Auth::user()->access !== 6 && Auth::user()->access !== 3)
                                    <span class="product-table-dark"><a id="a-one_hand-{{$product->id}}" onclick="view_input('one_hand-{{$product->id}}', 'products')">{{$product->one_hand}}</a></span>
                                    <input class="edit_info" type="text" id="one_hand-{{$product->id}}" value="{{$product->one_hand}}"></span>
                                @else
                                    <span class="product-table-dark">{{ $product->one_hand }}</span>
                                @endif
                                </td>
                                <td class="product_row">
                                @if(Auth::user()->access !== 6 && Auth::user()->access !== 3)
                                    <span class="product-table-dark"><a id="a-quantity-{{$product->id}}" onclick="view_input('quantity-{{$product->id}}', 'products')">{{$product->quantity}}</a></span>
                                    <input class="edit_info" type="text" id="quantity-{{$product->id}}" value="{{$product->quantity}}"></span>
                                @else
                                    <span class="product-table-dark">{{ $product->quantity }}</span>
                                @endif
                                </td>
                                <td class="product_row">
                                    <span class="product-table-dark">
                                        {{ $product->price_usd }}
                                    </span>
                                </td>
                            @endif
                            @if(Auth::user()->access === 5)
                                <td class="product_row"><span class="product-table-dark">
                                    {{ $price }}
                                </span></td>
                            @endif
                                <td class="product_row"><span class="product-table-dark">
                                    @if (Auth::user()->access == 5)
                                        @php($percent = Auth::user()->percents->where('category_id', $product->category_id)->first()->percentLarge)
                                        @php($price_opt = round(($price + (($price * $percent)/100))/10, 0,  PHP_ROUND_HALF_UP) * 10)
                                        {{ $price_opt }}
                                    @else
                                        {{ $product->price_opt_old }}
                                    @endif
                                </span></td>
                                <td class="product_row"><span class="product-table-dark">
                                    @if (Auth::user()->access == 5)
                                            @php($percent = Auth::user()->percents->where('category_id', $product->category_id)->first()->percentMiddle)
                                            @php($price_middle = round(($price + (($price * $percent)/100))/10, 0,  PHP_ROUND_HALF_UP) * 10)
                                            {{ $price_middle }}
                                        @else
                                            {{ $product->price_middle_old }}
                                        @endif
                                </span></td>
                                <td class="product_row">
                                    <span class="product-table-dark">
                                        @if (Auth::user()->access == 5)
                                            @php($percent = Auth::user()->percents->where('category_id', $product->category_id)->first()->percentSmall)
                                            @php($price = round(($price + (($price * $percent)/100))/10, 0,  PHP_ROUND_HALF_UP) * 10)
                                            {{ $price }}
                                        @else
                                            {{ $product->price_old }}
                                        @endif
                                    </span></td>
                            @if(Auth::user()->access != 5)
                                <td class="product_row">
                                @if(Auth::user()->access !== 6 && Auth::user()->access !== 3)
                                    <span class="product-table-dark"><a id="a-addition_count-{{$product->id}}" onclick="view_input('addition_count-{{$product->id}}', 'products')">{{$product->addition_count}}</a></span>
                                    <input class="edit_info" type="text" id="addition_count-{{$product->id}}" value="{{$product->addition_count}}"></span>
                                @else
                                    <span class="product-table-dark">{{ $product->addition_count }}</span>
                                @endif
                                </td>
                                <td class="product_row">
                                @if(Auth::user()->access !== 6 && Auth::user()->access !== 3)
                                    <span class="product-table-dark"><a id="a-addition_price-{{$product->id}}" onclick="view_input('addition_price-{{$product->id}}', 'products')">{{$product->addition_price}}</a></span>
                                    <input class="edit_info" type="text" id="addition_price-{{$product->id}}" value="{{$product->addition_price}}"></span>
                                @else
                                    <span class="product-table-dark">{{ $product->addition_price }}</span>
                                @endif
                                </td>
                            @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div id="add_order" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <input type="hidden" value="" id="clientOrderId">
            <input type="hidden" value="" id="clientOrderProvider">
            <div class="modal-content add_product_modal nomenclature_modal" id="products_modal">

            </div>
        </div>
    </div>
@endsection

