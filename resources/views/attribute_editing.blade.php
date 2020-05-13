<form accept-charset="UTF-8" action="{{ url('/attributes/value/update/' . $attribute->id) }}" method="POST">
    {{csrf_field()}}
    <h4>Редактировать значение атрибута</h4><i class="fa fa-times" aria-hidden="true" onclick="$('#edit_value').modal('toggle')"></i><hr>
    <div class="form-group">
        <label for="parent_id">НАИМЕНОВАНИЕ</label>
        <input type="hidden" name="attribute_id" id="attribute_id" value="{{ $attribute->id }}">
        <input type="text" name="value" class="form-control" value="{{ $attribute->value }}">
    </div>
    <div class="form-group">
        <label for="flag_id">ФЛАГ</label>
        <input type="text" name="flag" class="form-control" value="{{ $attribute->additional_data }}">
    </div>
    <hr>
    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>