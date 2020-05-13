@extends('settings_layout')

@section('settings-content')
<div class="products-block-up">
    <div class="row">
        <div class="col-md-8">
            <span class="product-block-bread">СПИСОК ПОЛЬЗОВАТЕЛЕЙ</span>
        </div>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-8">
        <button class="btn btn-default add_product_button" data-toggle="modal" data-target=".bs-example-modal-lg"> ДОБАВИТЬ ПОЛЬЗОВАТЕЛЯ </button>
    </div>
</div>
<hr>
<table class="table tproduct-table">
    <thead>
    <tr class="product-table-thead">
        <th>ЛОГИН</th>
        <th>ИМЯ</th>
        <th>ПРАВА ДОСТУПА</th>
        <th>UID</th>
        <th>КЛЮЧЕВОЕ СЛОВО</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr class="client_info">
                <td>{{$user->email}}</td>
                <td>{{$user->name}}</td>
                <td>
                    @if ($user->access == 0)
                        АДМИН
                    @elseif($user->access == 1)
                        ЗАКАЗЫ МОСКВА
                    @elseif ($user->access == 4)
                        ЗАКАЗЫ РЕГИОНЫ
                    @elseif ($user->access == 3)
                        УЦЕНЕНКА
                    @elseif ($user->access == 2)
                        МЕНЕДЖЕР РЕГИОНЫ
                    @elseif ($user->access == 5)
                        ПЕРЕКУП
                    @endif
                </td>
                <td>{{ $user->admin_uid }}</td>
                <td>{{ $user->keyword }}</td>

                <td class="text-right">
                    <i class="fa fa-cog product-table-cog" data-toggle="modal" data-target="#edit-user" onclick="getUser({{$user->id}})"></i>
                    <form accept-charset="UTF-8" action="{{ route('users.delete', ['id' => $user->id]) }}" method="GET"><button type="submit"><img src="/img/delete_black.png"></button></form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content add_category_modal">
            <form accept-charset="UTF-8" action="{{ route('users.create') }}" method="GET">
                <h4>Добавить пользователя</h4><i class="fa fa-times" aria-hidden="true" onclick="$('.bs-example-modal-lg').modal('toggle')"></i><hr>
                <div class="form-group">
                    <label for="name">ИМЯ</label>
                    <input type="text" name="name" class="form-control" value="" >
                </div>
                <div class="form-group">
                    <label for="email">ЛОГИН (E-MAIL)</label>
                    <input type="text" name="email" class="form-control" value="">
                </div>
                <!--<div class="form-group">
                    <label for="password">ПАРОЛЬ</label>
                    <input type="password" name="password" class="form-control" value="" placeholder="Не менее 6 символов">
                </div>-->
                <div class="form-group">
                    <label for="password">UID</label>
                    <input type="text" name="admin_uid" class="form-control">
                </div>
                <div class="form-group">
                    <label for="access">НАСТРОЙКИ ДОСТУПА</label>
                    <select class="form-control" id="access" name="access" onchange="getRegions($(this).val())">
                        <option value="0">АДМИН</option>
                        <!--<option value="1">ЗАКАЗЫ МОСКВА</option>
                        <option value="4">ЗАКАЗЫ РЕГИОНЫ</option>-->
                        <option value="3">УЦЕНЕНКА</option>
                        <option value="2">МЕНЕДЖЕР РЕГИОНЫ</option>
                        <option value="5">ПЕРЕКУП</option>
                        <option value="6">МЕНЕДЖЕР ЗАКАЗОВ</option>
                    </select>
                </div>
                <div class="form-group" id="regions_list">
                    <label for="regions">РЕГИОНЫ</label>
                        @foreach($regions as $region)
                            @if($region && $region != 'Москва')
                                <div class="checkbox">
                                    <label><input type="checkbox" name="regions[]" value="{{ $region }}">{{ $region }}</label>
                                </div>
                            @endif
                        @endforeach
                </div>
                <div class="form-group" id="keyword">
                    <label for="keyword">КЛЮЧЕВОЕ СЛОВО</label>
                    <input type="text" name="keyword" class="form-control">
                </div>
                <hr>
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </form>
        </div>
    </div>
</div>

<div id="edit-user" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content add_product_modal" id="user_info">

        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
        getRegions($('select#access').val());
    });

    $('.client_group').on('change', function(){
        check_checkbox($(this).val(), 'users');
    });
</script>
@endsection