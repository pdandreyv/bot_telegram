@extends('layouts.app')

@section('content')
    <div class="clients-wrapper">
        <div class="row product-table-row">
            <div class="products-block col-xs-12">
                <div class="products-block-up">
                    <span class="product-block-bread">РАССЫЛКА</span>
                </div>
                <hr>
                <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                    @foreach ($mailings as $key=>$one)
                        <div class="panel panel-default">
                            <div class="panel-heading mailing-panel" role="tab" id="heading">
                                <h4 class="panel-title mailing_caption">
                                    <i class="fa {{ App\Bot_setting::where('type', $key)->first()->shortcodes }}" aria-hidden="true"></i>
                                    {{ $key }}
                                </h4>
                            </div>
                            <div class="mailing_section">
                                {!!  nl2br($one) !!}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        $('.mailing_caption').on('click', function() {
            $(this).parent().next().toggle(500);
        });
    </script>
@endsection