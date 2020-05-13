@extends('settings_layout')

@section('settings-content')
    <h3>Управление рассылкой</h3>
    <p>ВРЕМЯ РАССЫЛКИ</p>
    @foreach ($bots as $mail)
        @if ($mail->type == 'from')
            <lable>С: </lable>
            <input onclick="new_cron('from-{{$mail->id}}')" type='time' id="cron_from" name='calendar' value='{{$mail->text}}'>
        @endif
        @if ($mail->type == 'to')
            ПО: <input onclick="new_cron('to-{{$mail->id}}')" type='time' id="cron_to" name='calendar' value='{{$mail->text}}'>
        @endif
    @endforeach
    <hr>
    <h3>Сообщения</h3>
	@foreach ($bots as $bot)
		@if ($bot->code == 'message')
            <div class="alert alert-warning" role="alert">
                <span class="first_name">{{strtoupper($bot->type)}}</span>
                <img class="show_details" src="/img/plus.png">
            </div>
            <div class="list_orders">
                @if ($bot->shortcodes)
                    <div class="alert alert-info bot_shortcodes" role="alert">
                        {{$bot->shortcodes}}
                    </div>
                @endif
                    <!--<a id="a-text-{{$bot->id}}" onclick="view_textarea('text-{{$bot->id}}', 'bot')">{{$bot->text}}</a>-->
                  <textarea class="bot_message" type="text" id="area-text-{{$bot->id}}">{{$bot->text}}</textarea>
                    <button type="button" id="button-text-{{$bot->id}}" onclick="updateBotSetting('text-' + {{$bot->id}} + '-' + $(this).prev().val())" class="btn btn-primary btn-sm save_message">Сохранить</button>
                    <button type="button" id="close-text-{{$bot->id}}" onclick="$(this).prev().prev().val('')" class="btn btn-default btn-sm">Очистить</button>
            </div>
		@endif
	@endforeach
    <hr>
    <h3>Кнопки</h3>
	@foreach ($bots as $bot)
        @if ($bot->code == 'button')
            <div class="col-md-4">
                <input class="edit_info input_bot" type="text" id="text-{{$bot->id}}" value="{{$bot->text}}">
                <button id="a-text-{{$bot->id}}" class="btn btn-default btn-lg button_bot" type="submit" onclick="view_input('text-{{$bot->id}}', 'bot')">{{$bot->text}}</button></br></br>
            </div>
        @endif
	@endforeach

    <script>
        $('img.show_details').on('click', function() {
            var src = ($(this).attr("src") === "/img/plus.png")
                ? "/img/minus.png"
                : "/img/plus.png";
            $(this).attr("src", src);
            $(this).parent().next().first('div.list_orders').slideToggle(500);
        });

        $('button.save_message').on('click', function() {
            var image = $(this).parent().prev().find('img.show_details');
            var src = (image.attr("src") === "/img/plus.png")
                ? "/img/minus.png"
                : "/img/plus.png";
            $(image).attr("src", src);
            $(this).parent().hide(500);
        });
    </script>
@endsection