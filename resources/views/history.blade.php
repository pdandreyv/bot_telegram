@extends('layouts.app')

@section('content')
<div id="history" class="col-sm-5 col-sm-offset-2">
	<select class="form-control" onchange="window.location.href=this.options[this.selectedIndex].value">	
		<option ></option>
		@foreach ($clients as $client)
			<option
			@if (collect(request()->segments())->last() == $client->id)
				{{'selected'}}
			@endif 
			value="{{url('history/'.$client->id)}}">{{$client->first_name}} {{$client->last_name}}
			</option>
		@endforeach
	</select> </br>
	<div class="alert alert-warning" role="alert">
	<h4>История</h4>
	</div>
	
	@foreach ($histories as $history)
            @if ($history->text)
		<div class="alert alert-warning" role="alert">
                    <p>{{$history->text}}<div class="small-date"><small>{{date("d.m.Y H:i",strtotime($history->created_at))}}</small></div></p>
		</div>
            @else
                <div class="alert alert-success text-right" role="alert">
                    <p>{{$history->bot_text}}<div class="small-date"><small>{{date("d.m.Y H:i",strtotime($history->created_at))}}</small></div></p>
		</div>
            @endif
	@endforeach
	
	
</div>
@endsection