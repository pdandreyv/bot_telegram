<div class="products-block-up">
    <div class="row">
        <div class="col-md-12">
            <span class="product-block-bread">НОМЕНКЛАТУРА</span>
            <i data-toggle="modal" data-target="#add_order" aria-hidden="true" class="fa fa-times close_window"></i>
            <div class="wrapper">
                <div class="item">
                    <img src="{{ asset('img/ajax-loader.gif') }}" alt="Loading...">
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
@if($categories->count() > 0)
    <input type="hidden" id="selected_cat" value="">
    <input type="hidden" id="selected_country" value="">
    <table class="table tproduct-table">
        <thead>
        <tr class="product-table-thead">
            <th>№</th>
            <th>НАЗВАНИЕ КАТЕГОРИИ</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th>КОЛИЧЕСТВО ПОЗИЦИЙ</th>
        </tr>
        </thead>
        <tbody>

        @foreach($categories as $category)
            @php $children = \App\Category::where('parent_id', $category->id)->get(); @endphp
            @if ($category->parent_id == 0)
                @if(count($children) == 0)
                    <tr class="client_info parent choose_category" onclick='$("tr.products_details").hide();$("tr.child").hide();$("#selected_cat").val({{ $category->id }});$("tr#countries-{{ $category->id }}").slideToggle(500);'>
                @else
                    <tr class="client_info parent open_category" onclick='$("tr.products_details").hide();$("tr.countries_details").hide();$("tr.cat{{ $category->id }}").slideToggle();'>
                        @endif
                        <td class="td_center_text"><strong>{{$category->id}}</strong></td>
                        @if(count($children) > 0)
                            <td><span class="first_name">{{$category->name}}</span></td>
                        @else
                            <td class="td_center_text">
                                <span class="first_name choose_category">{{$category->name}}</span>
                                <input type="hidden" class="current_id" value="{{$category->id}}">
                            </td>
                        @endif
                        <td></td>
                        <td></td>
                        <td class="td_center_text"></td>
                        <td></td>
                        <td>{{ $category->getCurrentCountZero($category->name) }}</td>
                    </tr>
                    @if(count($children) > 0)
                        @foreach($children as $child)
                            <tr class="client_info cat{{ $category->id }} child choose_category" onclick='$("tr.products_details").hide();$("#selected_cat").val({{ $child->id }});$("tr#countries-{{ $child->id }}").slideToggle();'>
                                <td></td>
                                <td class="td_center_text">
                                    <span class="client-table-dark">{{$child->name}}</span>
                                    <input type="hidden" class="current_id" value="{{$child->id}}">
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td class="td_center_text"><span class="client-table-dark">{{ $child->getCurrentCountZero($child->name) }}</span></td>
                            </tr>
                            <tr id="countries-{{$child->id}}" class="countries_details country{{ $category->id }}">
                                @include('ajax_templates.products.countries_list_nomenclature', ['category' => $child, 'modal' => $modal])
                            </tr>
                        @endforeach
                    @else
                        <tr id="countries-{{$category->id}}" class="countries_details">
                            @include('ajax_templates.products.countries_list_nomenclature', ['modal' => $modal])
                        </tr>
                    @endif
                @endif
                @endforeach
        </tbody>
    </table>
@endif
<input type="hidden" id="last_product" value="">

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
                    <label for="name">Название товара</label>
                    <input type="text" id="name" name="name" class="form-control" placeholder="Название товара">
                </div>
                <div class="form-group">
                    <label for="parent_id">Производитель</label>
                    <select id="parent_id" name="parent_id" class="form-control" onchange="select_category($(this).val())">
                        @foreach($categories as $val)
                            <option value="{{$val->id}}">{{$val->name}}</option>
                        @endforeach
                    </select>
                </div>
                <div id="div-subcat" class="form-group hidden">
                    <label for="subcat">Модель</label>
                    <select id="subcat" name="subcat" class="form-control">
                        <option value="parent">Выберите модель</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="country">Страна</label>
                    <select name="country" id="country" class="form-control">
                        @foreach($countries as $val)
                            <option value="{{$val->value}}">{{$val->value}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="position">Порядок сотировки</label>
                    <input type="text" id="position" name="position" class="form-control" placeholder="Порядок сотировки">
                </div>
            </div>
            <hr>
            <div class="padd">
                <button type="button" class="btn btn-primary btn-block" onclick="addProduct()">Добавить Товар</button>
            </div>
        </div>
    </div>
</div>

<div id="add-serial-number" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content add_category_modal">
            <h4>Добавить серийный номер</h4><i class="fa fa-times" aria-hidden="true" onclick="$('#add-serial-number').modal('toggle');"></i><hr>
            <div class="form-group">
                <label for="parent_id">СЕРИЙНЫЙ НОМЕР</label>
                <input type="text" id="serial_number" class="form-control">
            </div>
            <hr>
            <button type="submit" class="btn btn-primary" onclick="addSerialNumber()">Сохранить</button>
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

<script>
    $('tr.choose_category').on('click', function() {
        var catId = $(this).find('input.current_id').val();
        var spanes = $('.countries_details');
        $.each(spanes, function(index, value){
            if($(value).find('.id').val() !== catId){
                $(value).hide();
            }
        });
    });

    $('.fa-trash').on('click', function() {
        $('.product_remove').val($(this).next().val());
    });
</script>