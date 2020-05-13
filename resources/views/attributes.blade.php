@extends('settings_layout')

@section('settings-content')
    @foreach($attributes as $attribute)
        <div class="title_attr">
            <span class="attribute_name">{{ $attribute->name }}</span>
        </div>
        @foreach($attribute->attribute_values()->get() as $one)
            <button class="btn btn-info">{{  $one->value }} @if($attribute->name == 'Память') Gb @endif</button>
            <i class="fa fa-pencil" aria-hidden="true" data-toggle="modal" data-target="#edit_value" onclick="getAttributeId({{ $attribute->id }}); getAttributeData({{$one->id}})"></i>
            <img class="attr_value" src="{{ asset('img/cross.png') }}"><input type="hidden" value="{{  $one->id }}">
        @endforeach
        <br>
        <div class="title_attr">
            <span class="first_name" data-toggle="modal" data-target=".bs-example-modal-lg" onclick="getAttributeId({{ $attribute->id }})"><u>ДОБАВИТЬ ЕЩЕ</u></span>
        </div>
            <hr>
    @endforeach
    <div id="categories_list"></div>

    <div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content add_category_modal">
                <form accept-charset="UTF-8" action="{{ url('/attributes/value/create') }}" method="GET">
                    <h4>Добавить значение атрибута</h4><i class="fa fa-times" aria-hidden="true" onclick="$('.bs-example-modal-lg').modal('toggle')"></i><hr>
                    <div class="form-group">
                        <label for="parent_id">НАИМЕНОВАНИЕ</label>
                        <input type="hidden" name="attribute_id" id="attribute_id" value="">
                        <input type="text" name="value" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="flag_id">ФЛАГ</label>
                        <input type="text" name="flag" class="form-control">
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit_value" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content add_category_modal" id="value_info">

            </div>
        </div>
    </div>

    <script>
        window.onload = function () {
            getCategoriesList();
        }

        function getAttributeId(id) {
            $('input#attribute_id').val(id);
        }

        $('img.attr_value').on('click', function() {
            var id = $(this).next().val();
            $.ajax({
                url: '/attributes/value/delete',
                type: 'get',
                data: {'id': id},
                success: function(){
                    window.location.replace('/attributes');
                }
            });
        });
    </script>
@endsection