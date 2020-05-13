<td colspan="7">
    @if($products->count() > 0)
        <table class="table products_table">
            <thead>
            <tr class="product-table-thead">
                <th class="text-center">НАИМЕНОВАНИЕ</th>
                <th class="text-center">КОЛ-ВО В ОДНИ РУКИ</th>
                <th class="text-center">КОЛ-ВО</th>
                <th class="text-center">ЦЕНА В $</th>
                <th class="text-center">КРУПНЫЙ ОПТ</th>
                <th class="text-center">СРЕДНИЙ ОПТ</th>
                <th class="text-center">МЕЛКИЙ ОПТ</th>
                <th class="text-center">УВЕЛИЧЕНИЕ К-ВА</th>
                <th class="text-center">УВЕЛИЧЕНИЕ ЦЕНЫ</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <input type="hidden" id="cat_id" value="{{ $cat_id }}">
            @foreach ($products as $product)
                <tr id="product_{{ $product->id }}">
                    <input type="hidden" value="{{ $product->id }}" class="currentProductId">
                    <td class="td_center_text product_row">
                        <span class="product-table-bright">@php echo $product->name?$product->name:'...' @endphp</span>
                    </td>
                    <td class="text-center">
                        <input type="number" name="one_hand" class="product_input one_hand">
                        <span id="error_one_hand_{{$product->id}}" class="error_text"></span>
                    </td>
                    <td class="text-center">
                        <input type="number" name="quantity" class="product_input quantity">
                        <span id="error_quantity_{{$product->id}}" class="error_text"></span>
                    </td>
                    <td class="text-center">
                        <input type="text" name="price_usd" class="product_input price_usd">
                        <span id="error_price_usd_{{$product->id}}" class="error_text"></span>
                    </td>
                    <td class="text-center">
                        <input type="number" name="price_opt" class="product_input price_opt">
                        <span id="error_price_opt_{{$product->id}}" class="error_text"></span>
                    </td>
                    <td class="text-center">
                        <input type="number" name="price_middle" class="product_input price_middle">
                        <span id="error_price_middle_{{$product->id}}" class="error_text"></span>
                    </td>
                    <td class="text-center">
                        <input type="number" name="price" class="product_input price">
                        <span id="error_price_{{$product->id}}" class="error_text"></span>
                    </td>
                    <td class="text-center">
                        <input type="number" name="additional_count" class="product_input additional_count">
                        <span id="error_additional_count_{{$product->id}}" class="error_text"></span>
                    </td>
                    <td class="text-center">
                        <input type="number" name="additional_price" class="product_input additional_price">
                        <span id="error_additional_price_{{$product->id}}" class="error_text"></span>
                    </td>
                    <td class="text-right product_row">
                        <button type="button" class="btn btn-success btn-xs add_order" onclick="addStockProduct({{$product->id}})">+</button>
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