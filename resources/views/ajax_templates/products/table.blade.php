<td colspan="7">
@if($products->count() > 0)
<table class="table products_table">
    <thead>
        <tr class="product-table-thead">
            <th>ПОЗИЦИЯ</th>
            <th>НАИМЕНОВАНИЕ</th>
            <!--<th>КОЛ-ВО НА СКЛАДЕ</th>
            <th>КРУПНЫЙ ОПТ</th>
            <th>МЕЛКИЙ ОПТ</th>-->
            <th></th>
        </tr>
    </thead>
    <tbody>
    <input type="hidden" id="cat_id" value="{{ $cat_id }}">
        @foreach ($products as $product)
        <tr>
            <td class="td_center_text product_row"><span class="product-table-bright">
                <input type="hidden" value="position-{{$product->id}}">
                <a class="view_input a-position-{{$product->id}}" onclick="('position-{{$product->id}}', 'products')">{{$product->position}}</a>
                <input class="edit_info" type="text" value="{{$product->position}}"></span>
            </td>
            <td class="td_center_text product_row">
                <input type="hidden" value="name-{{$product->id}}">
                <a class="view_input"><span class="product-table-bright">@php echo $product->name?$product->name:'...' @endphp</span></a>
                <input class="edit_info" type="text" value="{{$product->name}}"><br>
                <!--<span class="product-table-dark">{{$product->category->name}}</span></br>-->
            </td>
            <!--<td><span class="product-table-dark">{{$product->quantity}}</span></td>
            <td><span class="product-table-dark">{{$product->price_opt}}</span></td>
            <td><span class="product-table-dark">{{$product->price}}</span></td>-->
            <td class="text-right product_row">
                <i class="fa fa-cog product-table-cog" data-toggle="modal" data-target="#edit-product" onclick="getDataItem({{$product->id}})" id="settings_{{$product->id}}"></i>
                <i class="fa fa-trash product-table-trash" data-toggle="modal" data-target="#remove-product" onclick="getDataItem({{$product->id}})"></i>
                <!--span class="client-table-dark"><a href="#" onclick="getClientData({{$product->id}})" data-toggle="modal" data-target=".bs-example-modal-lg3"><img class="remove" src="{{ asset('/img/remove.png') }}"></a></span-->
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@else
        <span class="client-table-dark-dark">Продуктов не найдено.</span>
@endif
</td>


<script>
    $('.pagination li a').on('click', function() {
        getProductsTable($(this).attr('href'));
        return false;
    });

    $('a.view_input').on('click', function(){
        var input = $(this).next();
        input.show();
        $(this).hide();
        input.focus();
        input.keypress(function(event) {
            // Enter
            if(event.keyCode == 13){
                input.blur();
            }
        });

        var id = $(this).prev().val();
        var arr = id.split('-');
        var cell = arr[0];

        // Focus out
        input.focusout(function(){
            input.hide();
            var csrftoken = $('meta[name="csrf-token"]').attr('content');
            var new_value = input.val();

            $.ajax({
                url: '/products/update',
                type: 'post',
                data: {'new_value':new_value,"_token": csrftoken, 'id_value': id},
                success: function(data){
                    if(!data) data='...';
                    $(this).text(data);
                    $(this).show();
                    getProductsTable();
                },
                error: function(xhr, textStatus) {
                    //console.log(xhr);
                    //alert( [ xhr.status, textStatus ] );
                }
            });
            $(this).unbind();
            return false;
        });
    });
</script>