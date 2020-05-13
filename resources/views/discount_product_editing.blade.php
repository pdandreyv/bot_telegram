<form method="post" action="{{ route('products.update', ['id' => $product->id]) }}" enctype="multipart/form-data">
    {{ csrf_field() }}
<h4>Редактирование товара</h4>
    <i onclick="$('#edit-product').modal('toggle')" aria-hidden="true" class="fa fa-times"></i>
    <hr>
    <div class="padd">
        <input type="hidden" id="product" name="product_id" value="{{$product->id}}">
        <input type="hidden" id="parent_id" name="parent_id" value="{{ config('discount.discount_category_id') }}">
        <div class="form-group">
            <div class="form-group">
                <label for="category_id">Подкатегория</label>
                <select name="category_id" id="category_id" class="form-control">
                    @foreach($childrenCategories as $one)
                        <option @if($one->id == $product->category_id) selected @endif value="{{$one->id}}">{{$one->name}}</option>
                    @endforeach
                </select>
            </div>
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
            <label for="description">Описание</label>
            <textarea id="description_new" type="text" name="description_new" class="form-control">{{ $product->description }}</textarea>
        </div>
        <div class="form-group">
            <label for="image">Картинка</label>
            <div id="image_new"><img src = "images/1.jpg" onerror = "this.style.display = 'none'"></div>
            <input type="file" name="images" class="form-control">
        </div>
    <hr>
    <div class="padd">
        <button type="submit" class="btn btn-primary btn-block">Сохранить</button>
    </div>
    </div>
</form>