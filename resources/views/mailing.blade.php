@extends('layouts.app')

@section('content')
    <div class="clients-wrapper">
        <div class="row product-table-row">
            <div class="products-block col-xs-12">
                <div class="products-block-up">
                    <span class="product-block-bread">РАССЫЛКА</span>
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
                       <form action="{{ url('/mailing/generate') }}" method="GET">
                            <button type="submit" class="gen-button btn btn-primary">Генерация рассылки</button>
                        </form>
                    </div>
                </div>
    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
        @foreach ($categories as $one)
            @if ($one->name != 'Уцененный товар ?')
                @php($price = App\Bot_setting::where('type', $one->name)->first())
                @php($priceMiddleOpt = App\Bot_setting::where('type', $one->name . ' (средний опт)')->first())
                @php($priceOpt = App\Bot_setting::where('type', $one->name . ' (крупный опт)')->first())
                @if($price)
                    <div class="panel panel-default">
                        <div class="panel-heading mailing-panel" role="tab" id="heading{{$price->id}}">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$price->id}}" aria-expanded="true" aria-controls="collapse{{$price->id}}">
                                    <i class="fa {{$price->shortcodes}}" aria-hidden="true"></i> {{$price->type}}
                                </a>
                            </h4>
                        </div>
                        <div id="collapse{{$price->id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{$price->id}}">
                            <div class="panel-body">
                                <textarea maxlength="4040" id="area-{{$price->id}}" class="textarea_mailing">{{$price->text}}</textarea>
                                <hr>
                                <button onclick="mailing_save({{$price->id}})" id="button-{{$price->id}}" type="button" class="btn btn-primary btn-xs">Сохранить</button>
                            </div>
                        </div>
                    </div>
                @endif
                @if($priceMiddleOpt)
                    <div class="panel panel-default">
                        <div class="panel-heading mailing-panel" role="tab" id="heading{{$priceMiddleOpt->id}}">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$priceMiddleOpt->id}}" aria-expanded="true" aria-controls="collapse{{$priceMiddleOpt->id}}">
                                    <i class="fa {{$priceMiddleOpt->shortcodes}}" aria-hidden="true"></i> {{$priceMiddleOpt->type}}
                                </a>
                            </h4>
                        </div>
                        <div id="collapse{{$priceMiddleOpt->id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{$priceMiddleOpt->id}}">
                            <div class="panel-body">
                                <textarea maxlength="4040" id="area-{{$priceMiddleOpt->id}}" class="textarea_mailing">{{$priceMiddleOpt->text}}</textarea>
                                <hr>
                                <button onclick="mailing_save({{$priceMiddleOpt->id}})" id="button-{{$priceMiddleOpt->id}}" type="button" class="btn btn-primary btn-xs">Сохранить</button>
                            </div>
                        </div>
                    </div>
                @endif
                @if($priceOpt)
                    <div class="panel panel-default">
                        <div class="panel-heading mailing-panel" role="tab" id="heading{{$priceOpt->id}}">
                            <h4 class="panel-title">
                                <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse{{$priceOpt->id}}" aria-expanded="true" aria-controls="collapse{{$priceOpt->id}}">
                                    <i class="fa {{$priceOpt->shortcodes}}" aria-hidden="true"></i> {{$priceOpt->type}}
                                </a>
                            </h4>
                        </div>
                        <div id="collapse{{$priceOpt->id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{$priceOpt->id}}">
                            <div class="panel-body">
                                <textarea maxlength="4040" id="area-{{$priceOpt->id}}" class="textarea_mailing">{{$priceOpt->text}}</textarea>
                                <hr>
                                <button onclick="mailing_save({{$priceOpt->id}})" id="button-{{$priceOpt->id}}" type="button" class="btn btn-primary btn-xs">Сохранить</button>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        @endforeach
    </div>
            </div>
            </div>
@endsection