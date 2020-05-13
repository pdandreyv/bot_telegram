<div class="products-block-up">
    <div class="row">
        <h4>НОМЕНКЛАТУРА</h4>
        <i data-toggle="modal" data-target="#add_order" aria-hidden="true" class="fa fa-times"></i>
        <div class="wrapper">
            <div class="item">
                <img src="{{ asset('img/ajax-loader.gif') }}" alt="Loading...">
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
            <th>КОЛИЧЕСТВО НА СКЛАДЕ</th>
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
                        <td>{{ $category->getCount($category->name) }}</td>
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
                                <td class="td_center_text"><span class="client-table-dark">{{ $child->getCount($child->name) }}</span></td>
                            </tr>
                            <tr id="countries-{{$child->id}}" class="countries_details country{{ $category->id }}">
                                @include('ajax_templates.products.countries_list', ['category' => $child, 'modal' => $modal])
                            </tr>
                        @endforeach
                    @else
                        <tr id="countries-{{$category->id}}" class="countries_details">
                            @include('ajax_templates.products.countries_list', ['modal' => $modal])
                        </tr>
                    @endif
                @endif
                @endforeach
        </tbody>
    </table>
@endif
<input type="hidden" id="last_product" value="">