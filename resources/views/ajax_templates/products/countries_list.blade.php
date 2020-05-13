<td colspan="7">
    <input type="hidden" class="id" value="{{$category->id}}">
    <input type="hidden" id="modal" value="{{$modal}}">
    @if($countries->count() > 0)
        <table class="table country-table">
            <tbody>
                @php ($counter = 0)
                @foreach ($countries as $country)
                    @if(!$modal)
                        @php ($countryCounter = App\Product::countryCounter($category->name, $country->value))
                    @elseif($modal)
                        @php ($countryCounter = App\Product::countryCounterNomenclature($category->name, $country->value))
                    @endif
                    @if ($countryCounter > 0)
                        <tr class="choose_country open_category" onclick='$("#selected_country").val({{ $country->id }}); getProductsTable(); $("tr#category-{{ $category->id }}-country-{{$country->id}}").slideToggle(500);'>
                            <td class="td_center_text">
                                <span class="product-table-bright"><strong>{{ $country->value }}</strong></span>
                                <input type="hidden" class="country_id" value="{{$country->id}}">
                            </td>
                            <td class="td_left_text">
                                <span class="product-table-bright">{{ $countryCounter }}</span>
                            </td>
                            <td></td>
                        </tr>
                        <tr id="category-{{$category->id}}-country-{{$country->id}}" class="products_details">

                        </tr>
                        @php ($counter++)
                    @endif
                @endforeach
                @if(!$modal)
                    @php ($noCountryCounter = App\Product::countryCounter($category->name, null))
                @elseif($modal)
                    @php ($noCountryCounter = App\Product::countryCounterNomenclature($category->name, null))
                @endif
                    @if ($noCountryCounter > 0)
                        <tr class="choose_country open_category" onclick='$("#selected_country").val("without"); getProductsTable(); $("tr#category-{{ $category->id }}-country-without").slideToggle(500);'>
                            <td class="td_center_text">
                                <span class="product-table-bright"><strong>Без страны</strong></span>
                            </td>
                            <td class="td_left_text">
                                <span class="product-table-bright">{{ $noCountryCounter }}</span>
                            </td>
                            <td></td>
                        </tr>
                        <tr id="category-{{$category->id}}-country-without" class="products_details">

                        </tr>
                    @endif
                    @if ($noCountryCounter == 0 && $counter == 0)
                        <span class="product-table-bright"><strong>Продуктов не найдено</strong></span>
                    @endif
        @endif
        </tbody>
    </table>
</td>
