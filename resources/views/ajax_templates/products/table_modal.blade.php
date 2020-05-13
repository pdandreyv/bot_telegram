<td colspan="7">
    @if($products->count() > 0)
        <table class="table products_table">
            <thead>
            <tr class="product-table-thead">
                <th>НАИМЕНОВАНИЕ</th>
                <th>КОЛ-ВО В НАКЛАДНОЙ</th>
                <th>КОЛ-ВО НА СКЛАДЕ</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <input type="hidden" id="cat_id" value="{{ $cat_id }}">
            @foreach ($products as $product)
                <tr>
                    <input type="hidden" value="{{ $product->id }}" class="currentProductId">
                    <td class="td_center_text product_row">
                        <span class="product-table-bright">@php echo $product->name?$product->name:'...' @endphp</span>
                    </td>
                    <td>
                        <input type="number" name="quantity" class="order_quantity">
                    </td>
                    <td>
                        <span class="product-table-dark product_quantity">{{$product->quantity}}</span>
                    </td>
                    <td class="text-right product_row">
                        <button class="btn btn-success btn-xs add_order" data-toggle="modal" data-target="#add_order" onclick="addOrder({{ $product->id }}, $(this).parent().parent().find('input.order_quantity').val(), $('#clientOrderId').val(), $('#clientOrderProvider').val())">+</button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    @else
        <span class="client-table-dark-dark">На складе продуктов нет.</span>
    @endif
</td>


<script>
    $('input.order_quantity').on('input', function() {
        var max_quantity = parseInt($(this).parent().next().find('.product_quantity').text());
        if ($(this).val() > max_quantity) {
            $(this).val(max_quantity);
        }
    });
</script>