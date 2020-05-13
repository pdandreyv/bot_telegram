<form method="post" action="{{ route('products.update', ['id' => $product->id]) }}" enctype="multipart/form-data">
    {{ csrf_field() }}
    <h4>Редактирование товара</h4>
        <i onclick="$('#edit-product').modal('toggle')" aria-hidden="true" class="fa fa-times"></i>
        <hr>
        <div class="padd">
        <input type="hidden" id="product" name="product_id" value="{{$product->id}}">
        <input type="hidden" id="product_subcat" name="product_subcat" value="{{$product->category_id}}">
        <div class="form-group">
            <label for="name_new">Наименование</label>
            <input id="name_new" type="text" name="name_new" class="form-control" value="{{ $product->name}}">
        </div>
        <div class="form-group">
            <label for="country_new">Страна</label>
            <select id="country_new" name="country_new" class="form-control">
                @foreach($countries as $val)
                    <option
                        @if($product->country != null && $product->attribute_value()->first())
                            @if($product->attribute_value()->first()->value == $val->value)
                                 {{ 'selected' }}
                            @endif
                        @endif
                            value="{{$val->value}}">{{$val->value}}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="memory_new">Память</label>
            <select id="memory_new" name="memory_new" class="form-control">
                @foreach($memories as $val)
                    <option
                            @if($product->memory != null && $product->memory_value()->first())
                                @if($product->memory_value()->first()->value == $val->value)
                                {{ 'selected' }}
                                @endif
                            @endif
                            value="{{$val->value}}">{{$val->value}} Gb</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="parent_id">Производитель</label>
            <select id="parent_id" name="parent_id" class="form-control" onchange="select_category($(this).val())">
                @foreach($categories as $val)
                    <option
                        @if($product->category->parent_id == 0)
                            @if ($val->name == $product->category->name)
                            {{'selected'}}
                            @endif
                            value="{{$val->id}}">{{$val->name}}</option>
                        @else
                            @if ($val->name == App\Category::find($product->category->parent_id)->name)
                            {{'selected'}}
                            @endif
                            value="{{$val->id}}">{{$val->name}}</option>
                        @endif
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
            <label for="addition_price">Увеличение цены</label>
            <input id="addition_price_new" type="text" name="addition_price_new" class="form-control" value="{{ $product->addition_price }}">
        </div>
        <div class="form-group">
            <label for="addition_count">Увеличение количества</label>
            <input id="addition_count_new" type="text" name="addition_count_new" class="form-control" value="{{ $product->addition_count }}">
        </div>
        <div class="form-group">
            <label for="one_hand">Количество в одни руки</label>
            <input id="one_hand_new" type="text" name="one_hand_new" class="form-control" value="{{ $product->one_hand }}">
        </div>
        <div class="form-group">
            <label for="description">Описание</label>
            <textarea id="description_new" type="text" name="description_new" class="form-control">{{ $product->description }}</textarea>
        </div>
        <div class="form-group">
            <label for="image">Картинка</label>
            <div id="image_new"><img src = "images/1.jpg" onerror = "this.style.display = 'none'"></div>
            <input type="file" name="images" class="form-control">
        </div>
        <div class="form-group prod-codes">
            <label for="order">СЕРИЙНЫЙ НОМЕР</label>
            <div id="prod-codes">
                @foreach($codes as $code)
                    <span>{{$code->code}}</span><i class="fa fa-remove" onclick="deleteSerialNumber({{$code->id}})"></i><br>
                @endforeach
            </div>
            <a data-toggle="modal" data-target="#add-serial-number">ДОБАВИТЬ СЕРИЙНЫЙ НОМЕР</a>
        </div>
    <hr>
    <div class="padd">
        <button type="submit" class="btn btn-primary btn-block"> <!--onclick="updateProduct()-->Сохранить</button>
    </div>
    </div>
</form>