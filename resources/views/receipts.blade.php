@extends('layouts.app')

@section('content')
    <div class="clients-wrapper">
        <div class="row product-table-row">
            <div class="products-block col-xs-12">
                <div class="products-block-up">
                    <span class="product-block-bread">ПОСТУПЛЕНИЯ</span>
                </div>
                <hr>
                <div class="alert alert-info" role="alert">
                    <div class="mailing_time">
                        <p>Время рассылки:</p>
                        <p>
                            <input id="area-{{$send_rassilka->id}}" type="hidden" value="{{$send_rassilka->id}}">
                            <input id="send_rassilka" type="checkbox" onclick="rassilka_save({{$send_rassilka->id}})" {{$send_rassilka->text ? 'checked' : ''}}>
                            <lable for="send_rassilka">Рассылка посылается при нажатии на Start</lable>
                        </p>
                        @foreach ($mailings as $mail)
                            @if ($mail->type == 'from')
                                С: <input onclick="new_cron('from-{{$mail->id}}')" type='time' id="cron_from" name='calendar' value='{{$mail->text}}'>
                            @endif
                            @if ($mail->type == 'to')
                                ПО: <input onclick="new_cron('to-{{$mail->id}}')" type='time' id="cron_to" name='calendar' value='{{$mail->text}}'>
                        @endif
                    @endforeach
                    </div>
                </div>
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    @php
                        $num = 'in';
                    @endphp
                    <div class="panel panel-default">
                        <div class="panel-heading mailing-panel" role="tab" id="heading{{$mailing->id}}">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$mailing->id}}" aria-expanded="true" aria-controls="collapse{{$mailing->id}}">
                                    <i class="fa {{$mailing->shortcodes}}" aria-hidden="true"></i> {{$mailing->type}}
                                </a>
                            </h4>
                        </div>
                        <div id="collapse{{$mailing->id}}" class="panel-collapse collapse {{$num}}" role="tabpanel" aria-labelledby="heading{{$mailing->id}}">
                            <div class="panel-body">
                                <textarea maxlength="4040" id="area-{{$mailing->id}}" class="textarea_mailing">{{$mailing->text}}</textarea>
                                <hr>
                                <button onclick="mailing_save({{$mailing->id}})" id="button-{{$mailing->id}}" type="button" class="btn btn-primary btn-xs">Сохранить</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection