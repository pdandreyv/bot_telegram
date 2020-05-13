<form accept-charset="UTF-8" action="{{ route('users.update', ['id' => $user->id]) }}" method="GET">

    <h4>Редактировать пользователя</h4><i class="fa fa-times" aria-hidden="true" onclick="$('#edit-user').modal('toggle')"></i><hr>
    <div class="form-group">
        <label for="name">ИМЯ</label>
        <input type="text" name="name" class="form-control" value="{{ $user->name }}" >
    </div>
    <div class="form-group">
        <label for="email">ЛОГИН (E-MAIL)</label>
        <input type="text" name="email" class="form-control" value="{{ $user->email }}">
    </div>
    <!--<div class="form-group">
        <label for="password">ПАРОЛЬ</label>
        <input type="password" name="password" class="form-control" value="" placeholder="Не менее 6 символов">
    </div>-->
    <div class="form-group">
        <label for="password">UID</label>
        <input type="text" name="admin_uid" class="form-control" value="{{ $user->admin_uid }}">
    </div>
    <div class="form-group">
        <label for="access">НАСТРОЙКИ ДОСТУПА</label>
        <select class="form-control" id="access_new" name="access" onchange="getRegions($(this).val())">
            <option
                    @if ($user->access == 0)
                    {{'selected'}}
                    @endif
                    value="0">АДМИН</option>
            <!--<option
                    @if ($user->access == 1)
                    {{'selected'}}
                    @endif
                    value="1">ЗАКАЗЫ МОСКВА</option>
            <option
                    @if ($user->access == 4)
                    {{'selected'}}
                    @endif
                    value="4">ЗАКАЗЫ РЕГИОНЫ</option>-->
            <option
                    @if ($user->access == 3)
                    {{'selected'}}
                    @endif
                    value="3">УЦЕНЕНКА</option>
            <option
                    @if ($user->access == 2)
                    {{'selected'}}
                    @endif
                    value="2">МЕНЕДЖЕР РЕГИОНЫ</option>
            <option
                    @if ($user->access == 5)
                    {{'selected'}}
                    @endif
                    value="5">ПЕРЕКУП</option>
            <option
                    @if ($user->access == 6)
                    {{'selected'}}
                    @endif
                    value="6">МЕНЕДЖЕР ЗАКАЗОВ</option>
        </select>
    </div>
    <div class="form-group" id="regions_list_new">
        <label for="regions">РЕГИОНЫ</label>
        @php($counter = 0)
        @foreach($regions as $region)
            @if($region && $region != 'Москва')
                @php($selected = in_array($region, $userRegions))
                @if($selected)
                    <div class="checkbox">
                        <label><input type="checkbox" checked name="regions[]" value="{{ $region }}">{{ $region }}</label>
                    </div>
                @else
                    <div class="checkbox">
                        <label><input type="checkbox" name="regions[]" value="{{ $region }}">{{ $region }}</label>
                    </div>
                @endif
            @endif
        @endforeach
    </div>
    <div class="form-group" id="keyword_new">
        <label for="keyword">КЛЮЧЕВОЕ СЛОВО</label>
        <input type="text" name="keyword" class="form-control" value="{{ $user->keyword }}">
    </div>
    <hr>
    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>

<script>
    $(document).ready(function(){
        if($('#access_new').val() == 2) {
            $('#regions_list_new').removeClass('hidden');
        }
        else {
            $('#regions_list_new').addClass('hidden');
        }

        if($('#access_new').val() == 5) {
            $('#keyword_new').removeClass('hidden');
        }
        else {
            $('#keyword_new').addClass('hidden');
        }
    });
</script>
