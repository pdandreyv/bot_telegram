@extends('bot_views.layout')

@section('content')
        @if(Session::has('message'))
            <div class="alert alert-{{ Session::get('status') }} status-box">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                {{ Session::get('message') }}
            </div>
        @endif
        <div class="row">
            <div class="col-md-12">
                <form action="/send" class="form-signin" method="post">
                    {{ csrf_field() }}
                    <h2 class="form-signin-heading">Send Message as Telegram Bot</h2>
                    <label for="inputText" class="sr-only">Client UID</label>
                    <input name="client" type="text" id="client" class="form-control" value="260734958" required autofocus />
                    <br>
                    <label for="inputText" class="sr-only">Message</label>
                    <textarea name="message" type="text" id="inputText" class="form-control" placeholder="Message" required>Hello</textarea>
                    <br />
                    <button class="btn btn-lg btn-primary btn-block" type="submit">Send Message</button>
                </form>
                <br />
            </div>
        </div>


@stop