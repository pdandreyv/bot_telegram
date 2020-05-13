<td colspan="7">
    <input type="hidden" class="current_category" value="{{$category_id}}">
    <table class="table country-table">
        <tbody>
        @if($children->count() > 0)
            @foreach ($children as $child)
                <tr class="client_info_{{$child->id}}">
                    <td><input type="hidden" class="current_child_id" value="{{$child->id}}"></td>
                    <td class="td_center_text cat_child_name">
                        <span class="client-table-dark"><span class="product-table-bright" id="a-name-{{$child->id}}" onclick="view_input('name-{{$child->id}}', 'categories')">{{$child->name}}</span></span>
                        <input class="edit_info" type="text" id="name-{{$child->id}}" value="{{$child->name}}">
                    </td>
                    @if($child->visible)
                        <td class="text-left"><span class="product-table-bright"><button class="btn btn-success btn-xs" onclick="changeCatVisibility({{ $child->id }})">   Активно  </button></span></td>
                    @else
                        <td><span class="product-table-bright"><button class="btn btn-primary btn-xs" onclick="changeCatVisibility({{ $child->id }})">Неактивно</button></span></td>
                    @endif
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right">
                        <img class="position_up_child" src='/img/up.png' alt="up"><input id='id_current' type="hidden" value="{{$child->id}}"><img class="position_down_child" src='/img/down.png' alt="down">
                    </td>
                    <td class="text-right cat_child_remove"><form accept-charset="UTF-8" action="{{ route('category_delete', ['id' => $child->id]) }}" method="GET"><button type="submit"><img src='/img/delete_black.png' alt="delete_category"></button></form></td>
                </tr>
            @endforeach
        @endif
            <tr class="client_info cat{{ $category_id }} remove">
                <td colspan="10"><input class="cat_id" type="hidden" value="{{ $category_id }}"><form accept-charset="UTF-8" action="{{ route('category_delete', ['id' => $category_id]) }}" method="GET"><button type="submit" class="btn btn-primary pull-right">Удалить категорию</button></form></td>
            </tr>
        </tbody>
    </table>
</td>

<script>
    $('img.position_up_child').on('click', function(){
        var id_current = $(this).next().val();
        var id_prev = $(this).parent().parent().prev().find('input.current_child_id').val();
        var id_category = $('input.current_category').val();

        if(typeof id_prev == 'undefined') {
            return;
        }

        changePosition(id_current, id_prev, id_category);
    });

    $('img.position_down_child').on('click', function(){
        var id_current = $(this).prev().val();
        var id_next = $(this).parent().parent().next().find('input.current_child_id').val();
        var id_category = $('input.current_category').val();

        if(typeof id_next == 'undefined') {
            return;
        }

        changePosition(id_current, id_next, id_category);
    });
</script>
