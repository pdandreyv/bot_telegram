<div class="row product-table-row">
    <div class="products-block col-xs-12">
        <div class="products-block-up">
            <div class="row">
                <div class="col-md-8">
                    <span class="product-block-bread">КАТЕГОРИИ</span>
                    <div class="wrapper">
                        <div class="item">
                            <img src="{{ asset('images/ajax-loader.gif') }}" alt="Loading...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-8">
                <button class="btn btn-default add_product_button" data-toggle="modal" data-target=".bs-example-modal-lg2"> ДОБАВИТЬ МОДЕЛЬ </button>
                <button type="button" class="btn btn-primary add_product_button" data-toggle="modal" data-target=".bs-example-modal-lg3">ДОБАВИТЬ КАТЕГОРИЮ</button>
            </div>
        </div>
        <hr>
        <table class="table tproduct-table">
            <thead>
            <tr class="product-table-thead">
                <th>№</th>
                <th>НАЗВАНИЕ КАТЕГОРИИ</th>
                <th>АКТИВНОСТЬ</th>
                <th>ВРЕМЯ СТАРТА КАТЕГОРИИ</th>
                <th>КОЛ-ВО МОДЕЛЕЙ</th>
                <th>ПОРЯДОК</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($categories as $category)
                @if ($category->parent_id == 0)
                    <tr class="client_info parent_{{$category->id}} parent">
                        <td class="td_center_text"><strong><span class="category_id">{{$category->id}}</span></strong></td>
                        <td class="td_center_text">
                            <span class="client-table-dark"><a id="a-name-{{$category->id}}" onclick="view_input('name-{{$category->id}}', 'categories')">{{$category->name}}</a></span>
                            <input class="edit_info" type="text" id="name-{{$category->id}}" value="{{$category->name}}">
                        </td>
                        @if($category->visible)
                            <td><span class="product-table-bright"><button class="btn btn-success btn-xs" onclick="changeCatVisibility({{$category->id}})">   Активно  </button></span></td>
                        @else
                            <td><span class="product-table-bright"><button class="btn btn-danger btn-xs" onclick="changeCatVisibility({{$category->id}})">Неактивно</button></span></td>
                        @endif
                        <td><input type='time' name='calendar' value='{{$category->start_date}}' oninput="updateTime({{ $category->id }} + '-start_date-' + $(this).val())"></td>
                        <td class="td_center_text">{{ $category->getSubcatsCount($category->id) }}</td>
                        <td><img class="position_up" src='/img/up.png' alt="up"><input id='id_current' type="hidden" value="{{$category->id}}"><img class="position_down" src='/img/down.png' alt="down"></td>
                        <td><img src='/img/plus.png' alt="plus" class="show_details details_{{$category->id}}"></td>
                    </tr>
                    <tr class="client_info cat_children" id="cat_{{ $category->id }}_children">

                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>

        <div class="modal fade bs-example-modal-lg3" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content add_category_modal">
                    <form accept-charset="UTF-8" action="{{ url('/category/create') }}" method="GET">
                        <h4>Добавить категорию</h4><i class="fa fa-times" aria-hidden="true" onclick="$('.bs-example-modal-lg3').modal('toggle')"></i><hr>
                        <div class="form-group">
                            <label for="parent_id">НАИМЕНОВАНИЕ ПРОИЗВОДИТЕЛЯ</label>
                            <input type="hidden" name="parent_id" value="parent">
                            <input type="text" name="name" class="form-control">
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade bs-example-modal-lg2" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content add_category_modal">
                    <form accept-charset="UTF-8" action="{{ url('/category/create') }}" method="GET">
                        <h4>Добавить модель</h4><i class="fa fa-times" aria-hidden="true" onclick="$('.bs-example-modal-lg2').modal('toggle')"></i><hr>
                        <div class="form-group">
                            <label for="parent_id">ПРОИЗВОДИТЕЛЬ</label>
                            <select name="parent_id" class="form-control">
                                @foreach($categories as $category)
                                    @if ($category->parent_id == 0)
                                        <option value="{{$category->id}}">{{$category->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name">НАЗВАНИЕ МОДЕЛИ</label>
                            <input type="text" name="name" class="form-control">
                        </div>
                        <hr>
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('img.show_details').on('click', function() {
        var id_current = $(this).parent().parent().find('span.category_id').html();
        var trs = $('tr.cat_children');
        $.each(trs, function(index, value) {
            $(value).html('');
        });

        if($(this).attr("src") == "/img/plus.png"){
            var src = '/images/minus.png';
            getChildren(id_current);
            $('tr#cat_' + id_current + '_children').show();
        }
        else{
            var src = '/img/plus.png';
            $('tr#cat_' + id_current + '_children').hide();
        }
        $(this).attr("src", src);

        var images = $('img.show_details');
        $.each(images, function(index, value) {
            if(!$(value).hasClass('details_' + id_current) && $(value).attr("src") == '/images/minus.png') {
                var src = '/img/plus.png';
                $(value).attr("src", src);
            }
        });
    });

    $('img.position_up').on('click', function(){
        var id_current = $(this).next().val();
        var id_prev = $(this).parent().parent().prev().prev().find('span.category_id').html();

        if(typeof id_prev == 'undefined') {
            return;
        }

        changePosition(id_current, id_prev, 'parent');
    });

    $('img.position_down').on('click', function(){
        var id_current = $(this).prev().val();
        var id_next = $(this).parent().parent().next().next().find('span.category_id').html();

        if(typeof id_next == 'undefined') {
            return;
        }

        changePosition(id_current, id_next, 'parent');
    });
</script>