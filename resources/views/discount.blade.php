@extends('layouts.app')

@section('head')
    <script src="{{ asset('js/discount.js') }}"></script>
@endsection

@section('content')
    <div class="col-sm-12 products_main_block">
        <div class="clients-wrapper">
        <div class="row product-table-row">
            <div class="products-block col-xs-12">
                <div class="products-block-up">
                    <div class="row">
                        <div class="col-md-9">
                            <span class="product-block-bread">УЦЕНЕННЫЙ ТОВАР</span>
                            <div class="wrapper">
                                <div class="item">
                                    <img src="{{ asset('img/ajax-loader.gif') }}" alt="Loading...">
                                </div>
                            </div>
                        </div>
                    @if(Auth::user()->access !== 6)
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-product">ДОБАВИТЬ ТОВАР</button>
                        </div>
                    @endif
                    </div>
                </div>
                <hr>
                @if(Session::has('message'))
                    <div class="alert alert-{{ Session::get('status') }} status-box">
                        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                        {{ Session::get('message') }}
                    </div>
                @endif
                @if($products->count() > 0)
                    <table class="table product-table">
                        <thead>
                        <tr class="product-table-thead">
                            <th>ПОЗИЦИЯ</th>
                            <th>НАИМЕНОВАНИЕ</th>
                            <th>ПОДКАТЕГОРИЯ</th>
                            <th>КРУПНЫЙ ОПТ</th>
                            <th>СРЕДНИЙ ОПТ</th>
                            <th>МЕЛКИЙ ОПТ</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td class="td_center_text product_row"><span class="product-table-dark">
                                @if(Auth::user()->access !== 6)
                                    <a id="a-position-{{$product->id}}" onclick="view_input('position-{{$product->id}}', 'products')">{{$product->position}}</a>
                                    <input class="edit_info" type="text" id="position-{{$product->id}}" value="{{$product->position}}"></span>
                                @else
                                    <span>{{ $product->position }}</span>
                                @endif
                                </td>
                                <td class="product_row">
                                    <span data-id="{{$product->id}}" class="product-table-bright">@php echo $product->name?$product->name:'...' @endphp</span>
                                </td>
                                <td>
                                    @if($product->category->id != config('discount.discount_category_id'))
                                        {{ $product->category->name }}
                                    @else
                                        Отсутствует
                                    @endif
                                </td>
                                <td class="product_row"><span class="product-table-dark">
                                @if(Auth::user()->access !== 6)
                                            <a id="a-price_opt-{{$product->id}}" onclick="view_input('price_opt-{{$product->id}}', 'products')">{{$product->price_opt}}</a>
                                            <input class="edit_info" type="text" id="price_opt-{{$product->id}}" value="{{$product->price_opt}}"></span>
                                    @else
                                        <span>{{ $product->price_opt }}</span>
                                    @endif
                                </td>
                                <td class="product_row"><span class="product-table-dark">
                                @if(Auth::user()->access !== 6)
                                    <a id="a-price_middle-{{$product->id}}" onclick="view_input('price_middle-{{$product->id}}', 'products')">@php echo $product->price_middle ? $product->price_middle : '...' @endphp</a>
                                    <input class="edit_info" type="text" id="price_middle-{{$product->id}}" value="{{$product->price_middle}}"></span>
                                @else
                                    <span>@php echo $product->price_middle ? $product->price_middle : '...' @endphp</span>
                                @endif
                                </td>
                                <td class="product_row"><span class="product-table-dark">
                                @if(Auth::user()->access !== 6)
                                    <a id="a-price-{{$product->id}}" onclick="view_input('price-{{$product->id}}', 'products')">{{$product->price}}</a>
                                    <input class="edit_info" type="text" id="price-{{$product->id}}" value="{{$product->price}}"></span>
                                @else
                                        <span>{{ $product->price }}</span>
                                @endif
                                </td>
                                <td class="text-right product_row">
                                    @if(Auth::user()->access !== 6)
                                        <i class="fa fa-cog product-table-cog" data-toggle="modal" data-target="#edit-product" onclick="getDataItem({{$product->id}})"></i>
                                        <i class="fa fa-trash product-table-trash" data-toggle="modal" data-target="#remove-product" onclick="getDataItem({{$product->id}})"></i>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @else
                    <h3>Продуктов не найдено.</h3>
                @endif
            </div>
        </div>
    </div>
    </div>

    <div id="edit-product" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content add_product_modal" id="product_info">

            </div>
        </div>
    </div>

    <div id="add-product" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content add_product_modal">
                <h4>Добавление товара</h4>
                <i onclick="$('#add-product').modal('toggle')" aria-hidden="true" class="fa fa-times"></i>
                <hr>
                <div class="padd">
                    <div class="form-group">
                        <label for="category_id">Подкатегория</label>
                        <select name="category_id" id="category_id" class="form-control">
                            @foreach($childrenCategories as $one)
                                <option value="{{$one->id}}">{{$one->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Название товара</label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="Название товара">
                    </div>
                    <div class="form-group">
                        <label for="country">Страна</label>
                        <select name="country" id="country" class="form-control">
                            @foreach($countries as $val)
                                <option value="{{$val->value}}">{{$val->value}}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" id="parent_id" value="{{ config('discount.discount_category_id') }}">
                    <div class="form-group">
                        <label for="position">Порядок сотировки</label>
                        <input type="text" id="position" name="position" class="form-control" placeholder="Порядок сотировки">
                    </div>
                    <div class="form-group">
                        <label for="price_opt">Крупный опт</label>
                        <input id="price_opt" type="text" name="price_opt" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="price_opt">Средний опт</label>
                        <input id="price_middle" type="text" name="price_middle" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="price">Мелкий опт</label>
                        <input id="price" type="text" name="price" class="form-control">
                    </div>
                </div>
                <hr>
                <div class="padd">
                    <button type="button" class="btn btn-primary btn-block" onclick="addProduct()">Добавить Товар</button>
                </div>
            </div>
        </div>
    </div>

    <div id="remove-product" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content add_product_modal">
                <input class="product_remove" type="hidden" value="">
                <h4>Удаление товара</h4><i class="fa fa-times" aria-hidden="true" onclick="$('#remove-product').modal('toggle')"></i>
                <hr>
                <div class="alert alert-info"><strong>Данное действие приведет к удалению записи о товаре!</strong>
                    Вы уверены в этом? Восстановить данную запись после этого будет невозможно.</div><hr>
                <button onclick="deleteItem('products', $('.product_remove').val())" class="btn btn-danger">Удалить</button>
                <button onclick="$('#remove-product').modal('toggle')" class="btn btn-default">Отменить</button>
            </div>
        </div>
    </div>
@endsection

