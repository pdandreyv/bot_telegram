@extends('layouts.app')

@section('content')
    <div class="clients-wrapper">
        <div class="col-sm-12 products_main_block">
            <div class="row product-table-row">
                <div class="products-block col-xs-12">
                    <div class="products-block-up">
                        <div class="row">
                            <div class="col-md-8">
                                <span class="product-block-bread">ПРОЦЕНТ</span>
                                <div class="wrapper">
                                    <div class="item">
                                        <img src="{{ asset('images/ajax-loader.gif') }}" alt="Loading...">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <table class="table tproduct-table">
                        <thead>
                        <tr class="product-table-thead">
                            <th>№</th>
                            <th>НАЗВАНИЕ КАТЕГОРИИ</th>
                            <th>КОЛ-ВО ПОДКАТЕГОРИЙ</th>
                            <th>КРУПНЫЙ ОПТ</th>
                            <th>СРЕДНИЙ ОПТ</th>
                            <th>МЕЛКИЙ ОПТ</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($categories as $category)
                            <tr class="client_info parent_{{$category->id}} parent">
                                <td class="td_center_text"><strong><span class="category_id">{{$category->id}}</span></strong></td>
                                <td class="td_center_text">
                                    <span class="first_name">{{ $category->name }}</span>
                                </td>
                                <td class="td_center_text">{{ $category->getSubcatsCount($category->id) }}</td>
                                <td>
                                    @php($percentCurrent = App\Percent::where('user_id', Auth::user()->id)->where('category_id', $category->id)->first())
                                    <select class="client_type form-control" onchange='check_checkbox($(this).val() + "_percentLarge_{{ $percentCurrent->id }}", "percents")'>
                                        <option @if(0.05 == $percentCurrent->percentLarge) selected @endif value="0.05">0.05</option>
                                        <option @if(0.10 == $percentCurrent->percentLarge) selected @endif value="0.10">0.10</option>
                                        <option @if(0.15 == $percentCurrent->percentLarge) selected @endif value="0.15">0.15</option>
                                        <option @if(0.20 == $percentCurrent->percentLarge) selected @endif value="0.20">0.20</option>
                                        <option @if(0.25 == $percentCurrent->percentLarge) selected @endif value="0.25">0.25</option>
                                        <option @if(0.30 == $percentCurrent->percentLarge) selected @endif value="0.30">0.30</option>
                                        <option @if(0.35 == $percentCurrent->percentLarge) selected @endif value="0.35">0.35</option>
                                        <option @if(0.40 == $percentCurrent->percentLarge) selected @endif value="0.40">0.40</option>
                                        <option @if(0.45 == $percentCurrent->percentLarge) selected @endif value="0.45">0.45</option>
                                        <option @if(0.50 == $percentCurrent->percentLarge) selected @endif value="0.50">0.50</option>
                                        <option @if(0.55 == $percentCurrent->percentLarge) selected @endif value="0.55">0.55</option>
                                        <option @if(0.60 == $percentCurrent->percentLarge) selected @endif value="0.60">0.60</option>
                                        <option @if(0.65 == $percentCurrent->percentLarge) selected @endif value="0.65">0.65</option>
                                        <option @if(0.70 == $percentCurrent->percentLarge) selected @endif value="0.70">0.70</option>
                                        <option @if(0.50 == $percentCurrent->percentLarge) selected @endif value="0.75">0.75</option>
                                        <option @if(0.80 == $percentCurrent->percentLarge) selected @endif value="0.80">0.80</option>
                                        <option @if(0.85 == $percentCurrent->percentLarge) selected @endif value="0.85">0.85</option>
                                        <option @if(0.90 == $percentCurrent->percentLarge) selected @endif value="0.90">0.90</option>
                                        <option @if(1 == $percentCurrent->percentLarge) selected @endif value="1">1</option>
                                        <option @if(1.25 == $percentCurrent->percentLarge) selected @endif value="1.25">1.25</option>
                                        <option @if(1.50 == $percentCurrent->percentLarge) selected @endif value="1.50">1.50</option>
                                        <option @if(1.75 == $percentCurrent->percentLarge) selected @endif value="1.75">1.75</option>
                                        <option @if(2 == $percentCurrent->percentLarge) selected @endif value="2">2</option>
                                        <option @if(2.25 == $percentCurrent->percentLarge) selected @endif value="2.25">2.25</option>
                                        <option @if(2.50 == $percentCurrent->percentLarge) selected @endif value="2.50">2.50</option>
                                        <option @if(2.75 == $percentCurrent->percentLarge) selected @endif value="2.75">2.75</option>
                                        <option @if(3 == $percentCurrent->percentLarge) selected @endif value="3">3</option>
                                        <option @if(3.25 == $percentCurrent->percentLarge) selected @endif value="3.25">3.25</option>
                                        <option @if(3.50 == $percentCurrent->percentLarge) selected @endif value="3.50">3.50</option>
                                        <option @if(3.75 == $percentCurrent->percentLarge) selected @endif value="3.75">3.75</option>
                                        <option @if(4 == $percentCurrent->percentLarge) selected @endif value="4">4</option>
                                        <option @if(4.25 == $percentCurrent->percentLarge) selected @endif value="4.25">4.25</option>
                                        <option @if(4.50 == $percentCurrent->percentLarge) selected @endif value="4.50">4.50</option>
                                        <option @if(4.75 == $percentCurrent->percentLarge) selected @endif value="4.75">4.75</option>
                                        <option @if(5 == $percentCurrent->percentLarge) selected @endif value="5">5</option>
                                    </select>
                                </td>
                                <td>
                                    @php($percentCurrent = App\Percent::where('user_id', Auth::user()->id)->where('category_id', $category->id)->first())
                                    <select class="client_type form-control" onchange='check_checkbox($(this).val() + "_percentMiddle_{{ $percentCurrent->id }}", "percents")'>
                                        <option @if(0.05 == $percentCurrent->percentMiddle) selected @endif value="0.05">0.05</option>
                                        <option @if(0.10 == $percentCurrent->percentMiddle) selected @endif value="0.10">0.10</option>
                                        <option @if(0.15 == $percentCurrent->percentMiddle) selected @endif value="0.15">0.15</option>
                                        <option @if(0.20 == $percentCurrent->percentMiddle) selected @endif value="0.20">0.20</option>
                                        <option @if(0.25 == $percentCurrent->percentMiddle) selected @endif value="0.25">0.25</option>
                                        <option @if(0.30 == $percentCurrent->percentMiddle) selected @endif value="0.30">0.30</option>
                                        <option @if(0.35 == $percentCurrent->percentMiddle) selected @endif value="0.35">0.35</option>
                                        <option @if(0.40 == $percentCurrent->percentMiddle) selected @endif value="0.40">0.40</option>
                                        <option @if(0.45 == $percentCurrent->percentMiddle) selected @endif value="0.45">0.45</option>
                                        <option @if(0.50 == $percentCurrent->percentMiddle) selected @endif value="0.50">0.50</option>
                                        <option @if(0.55 == $percentCurrent->percentMiddle) selected @endif value="0.55">0.55</option>
                                        <option @if(0.60 == $percentCurrent->percentMiddle) selected @endif value="0.60">0.60</option>
                                        <option @if(0.65 == $percentCurrent->percentMiddle) selected @endif value="0.65">0.65</option>
                                        <option @if(0.70 == $percentCurrent->percentMiddle) selected @endif value="0.70">0.70</option>
                                        <option @if(0.75 == $percentCurrent->percentMiddle) selected @endif value="0.75">0.75</option>
                                        <option @if(0.80 == $percentCurrent->percentMiddle) selected @endif value="0.80">0.80</option>
                                        <option @if(0.85 == $percentCurrent->percentMiddle) selected @endif value="0.85">0.85</option>
                                        <option @if(0.90 == $percentCurrent->percentMiddle) selected @endif value="0.90">0.90</option>
                                        <option @if(1 == $percentCurrent->percentMiddle) selected @endif value="1">1</option>
                                        <option @if(1.25 == $percentCurrent->percentMiddle) selected @endif value="1.25">1.25</option>
                                        <option @if(1.50 == $percentCurrent->percentMiddle) selected @endif value="1.50">1.50</option>
                                        <option @if(1.75 == $percentCurrent->percentMiddle) selected @endif value="1.75">1.75</option>
                                        <option @if(2 == $percentCurrent->percentMiddle) selected @endif value="2">2</option>
                                        <option @if(2.25 == $percentCurrent->percentMiddle) selected @endif value="2.25">2.25</option>
                                        <option @if(2.50 == $percentCurrent->percentMiddle) selected @endif value="2.50">2.50</option>
                                        <option @if(2.75 == $percentCurrent->percentMiddle) selected @endif value="2.75">2.75</option>
                                        <option @if(3 == $percentCurrent->percentMiddle) selected @endif value="3">3</option>
                                        <option @if(3.25 == $percentCurrent->percentMiddle) selected @endif value="3.25">3.25</option>
                                        <option @if(3.50 == $percentCurrent->percentMiddle) selected @endif value="3.50">3.50</option>
                                        <option @if(3.75 == $percentCurrent->percentMiddle) selected @endif value="3.75">3.75</option>
                                        <option @if(4 == $percentCurrent->percentMiddle) selected @endif value="4">4</option>
                                        <option @if(4.25 == $percentCurrent->percentMiddle) selected @endif value="4.25">4.25</option>
                                        <option @if(4.50 == $percentCurrent->percentMiddle) selected @endif value="4.50">4.50</option>
                                        <option @if(4.75 == $percentCurrent->percentMiddle) selected @endif value="4.75">4.75</option>
                                        <option @if(5 == $percentCurrent->percentMiddle) selected @endif value="5">5</option>
                                    </select>
                                </td>
                                <td>
                                    @php($percentCurrent = App\Percent::where('user_id', Auth::user()->id)->where('category_id', $category->id)->first())
                                    <select class="client_type form-control" onchange='check_checkbox($(this).val() + "_percentSmall_{{ $percentCurrent->id }}", "percents")'>
                                        <option @if(0.05 == $percentCurrent->percentSmall) selected @endif value="0.05">0.05</option>
                                        <option @if(0.10 == $percentCurrent->percentSmall) selected @endif value="0.10">0.10</option>
                                        <option @if(0.15 == $percentCurrent->percentSmall) selected @endif value="0.15">0.15</option>
                                        <option @if(0.20 == $percentCurrent->percentSmall) selected @endif value="0.20">0.20</option>
                                        <option @if(0.25 == $percentCurrent->percentSmall) selected @endif value="0.25">0.25</option>
                                        <option @if(0.30 == $percentCurrent->percentSmall) selected @endif value="0.30">0.30</option>
                                        <option @if(0.35 == $percentCurrent->percentSmall) selected @endif value="0.35">0.35</option>
                                        <option @if(0.40 == $percentCurrent->percentSmall) selected @endif value="0.40">0.40</option>
                                        <option @if(0.45 == $percentCurrent->percentSmall) selected @endif value="0.45">0.45</option>
                                        <option @if(0.50 == $percentCurrent->percentSmall) selected @endif value="0.50">0.50</option>
                                        <option @if(0.55 == $percentCurrent->percentSmall) selected @endif value="0.55">0.55</option>
                                        <option @if(0.60 == $percentCurrent->percentSmall) selected @endif value="0.60">0.60</option>
                                        <option @if(0.65 == $percentCurrent->percentSmall) selected @endif value="0.65">0.65</option>
                                        <option @if(0.70 == $percentCurrent->percentSmall) selected @endif value="0.70">0.70</option>
                                        <option @if(0.75 == $percentCurrent->percentSmall) selected @endif value="0.75">0.75</option>
                                        <option @if(0.80 == $percentCurrent->percentSmall) selected @endif value="0.80">0.80</option>
                                        <option @if(0.85 == $percentCurrent->percentSmall) selected @endif value="0.85">0.85</option>
                                        <option @if(0.90 == $percentCurrent->percentSmall) selected @endif value="0.90">0.90</option>
                                        <option @if(1 == $percentCurrent->percentSmall) selected @endif value="1">1</option>
                                        <option @if(1.25 == $percentCurrent->percentSmall) selected @endif value="1.25">1.25</option>
                                        <option @if(1.50 == $percentCurrent->percentSmall) selected @endif value="1.50">1.50</option>
                                        <option @if(1.75 == $percentCurrent->percentSmall) selected @endif value="1.75">1.75</option>
                                        <option @if(2 == $percentCurrent->percentSmall) selected @endif value="2">2</option>
                                        <option @if(2.25 == $percentCurrent->percentSmall) selected @endif value="2.25">2.25</option>
                                        <option @if(2.50 == $percentCurrent->percentSmall) selected @endif value="2.50">2.50</option>
                                        <option @if(2.75 == $percentCurrent->percentSmall) selected @endif value="2.75">2.75</option>
                                        <option @if(3 == $percentCurrent->percentSmall) selected @endif value="3">3</option>
                                        <option @if(3.25 == $percentCurrent->percentSmall) selected @endif value="3.25">3.25</option>
                                        <option @if(3.50 == $percentCurrent->percentSmall) selected @endif value="3.50">3.50</option>
                                        <option @if(3.75 == $percentCurrent->percentSmall) selected @endif value="3.75">3.75</option>
                                        <option @if(4 == $percentCurrent->percentSmall) selected @endif value="4">4</option>
                                        <option @if(4.25 == $percentCurrent->percentSmall) selected @endif value="4.25">4.25</option>
                                        <option @if(4.50 == $percentCurrent->percentSmall) selected @endif value="4.50">4.50</option>
                                        <option @if(4.75 == $percentCurrent->percentSmall) selected @endif value="4.75">4.75</option>
                                        <option @if(5 == $percentCurrent->percentSmall) selected @endif value="5">5</option>
                                    </select>
                                </td>
                                <td><img src='/img/plus.png' alt="plus" class="show_details details_{{$category->id}}"><input type="hidden" value="{{ $category->id }}"></td>
                            </tr>
                                <tr class="client_info cat_children" id="cat_{{ $category->id }}_children">
                                    <td colspan="7">
                                        <table class="table country-table">
                                            <tbody>
                                            @php($children = App\Category::where('parent_id', $category->id)->get())
                                            @if($children->count() > 0)
                                                @foreach ($children as $child)
                                                    <tr class="client_info_{{$child->id}}">
                                                        <td><input type="hidden" class="current_child_id" value="{{$child->id}}"></td>
                                                        <td class="td_center_text cat_child_name">
                                                            <span class="client-table-dark"><span class="product-table-bright">{{ $child->name }}</span></span>
                                                        </td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>
                                                            @php($percentChild = App\Percent::where('user_id', Auth::user()->id)->where('category_id', $child->id)->first())
                                                            <select class="client_type form-control" onchange='check_checkbox($(this).val() + "_percentLarge_{{ $percentChild->id }}", "percents")'>
                                                                <option @if(0.05 == $percentChild->percentLarge) selected @endif value="0.05">0.05</option>
                                                                <option @if(0.10 == $percentChild->percentLarge) selected @endif value="0.10">0.10</option>
                                                                <option @if(0.15 == $percentChild->percentLarge) selected @endif value="0.15">0.15</option>
                                                                <option @if(0.20 == $percentChild->percentLarge) selected @endif value="0.20">0.20</option>
                                                                <option @if(0.25 == $percentChild->percentLarge) selected @endif value="0.25">0.25</option>
                                                                <option @if(0.30 == $percentChild->percentLarge) selected @endif value="0.30">0.30</option>
                                                                <option @if(0.35 == $percentChild->percentLarge) selected @endif value="0.35">0.35</option>
                                                                <option @if(0.40 == $percentChild->percentLarge) selected @endif value="0.40">0.40</option>
                                                                <option @if(0.45 == $percentChild->percentLarge) selected @endif value="0.45">0.45</option>
                                                                <option @if(0.50 == $percentChild->percentLarge) selected @endif value="0.50">0.50</option>
                                                                <option @if(0.55 == $percentChild->percentLarge) selected @endif value="0.55">0.55</option>
                                                                <option @if(0.60 == $percentChild->percentLarge) selected @endif value="0.60">0.60</option>
                                                                <option @if(0.65 == $percentChild->percentLarge) selected @endif value="0.65">0.65</option>
                                                                <option @if(0.70 == $percentChild->percentLarge) selected @endif value="0.70">0.70</option>
                                                                <option @if(0.50 == $percentChild->percentLarge) selected @endif value="0.75">0.75</option>
                                                                <option @if(0.80 == $percentChild->percentLarge) selected @endif value="0.80">0.80</option>
                                                                <option @if(0.85 == $percentChild->percentLarge) selected @endif value="0.85">0.85</option>
                                                                <option @if(0.90 == $percentChild->percentLarge) selected @endif value="0.90">0.90</option>
                                                                <option @if(1 == $percentChild->percentLarge) selected @endif value="1">1</option>
                                                                <option @if(1.25 == $percentChild->percentLarge) selected @endif value="1.25">1.25</option>
                                                                <option @if(1.50 == $percentChild->percentLarge) selected @endif value="1.50">1.50</option>
                                                                <option @if(1.75 == $percentChild->percentLarge) selected @endif value="1.75">1.75</option>
                                                                <option @if(2 == $percentChild->percentLarge) selected @endif value="2">2</option>
                                                                <option @if(2.25 == $percentChild->percentLarge) selected @endif value="2.25">2.25</option>
                                                                <option @if(2.50 == $percentChild->percentLarge) selected @endif value="2.50">2.50</option>
                                                                <option @if(2.75 == $percentChild->percentLarge) selected @endif value="2.75">2.75</option>
                                                                <option @if(3 == $percentChild->percentLarge) selected @endif value="3">3</option>
                                                                <option @if(3.25 == $percentChild->percentLarge) selected @endif value="3.25">3.25</option>
                                                                <option @if(3.50 == $percentChild->percentLarge) selected @endif value="3.50">3.50</option>
                                                                <option @if(3.75 == $percentChild->percentLarge) selected @endif value="3.75">3.75</option>
                                                                <option @if(4 == $percentChild->percentLarge) selected @endif value="4">4</option>
                                                                <option @if(4.25 == $percentChild->percentLarge) selected @endif value="4.25">4.25</option>
                                                                <option @if(4.50 == $percentChild->percentLarge) selected @endif value="4.50">4.50</option>
                                                                <option @if(4.75 == $percentChild->percentLarge) selected @endif value="4.75">4.75</option>
                                                                <option @if(5 == $percentChild->percentLarge) selected @endif value="5">5</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            @php($percentChild = App\Percent::where('user_id', Auth::user()->id)->where('category_id', $child->id)->first())
                                                            <select class="client_type form-control" onchange='check_checkbox($(this).val() + "_percentMiddle_{{ $percentChild->id }}", "percents")'>
                                                                <option @if(0.05 == $percentChild->percentMiddle) selected @endif value="0.05">0.05</option>
                                                                <option @if(0.10 == $percentChild->percentMiddle) selected @endif value="0.10">0.10</option>
                                                                <option @if(0.15 == $percentChild->percentMiddle) selected @endif value="0.15">0.15</option>
                                                                <option @if(0.20 == $percentChild->percentMiddle) selected @endif value="0.20">0.20</option>
                                                                <option @if(0.25 == $percentChild->percentMiddle) selected @endif value="0.25">0.25</option>
                                                                <option @if(0.30 == $percentChild->percentMiddle) selected @endif value="0.30">0.30</option>
                                                                <option @if(0.35 == $percentChild->percentMiddle) selected @endif value="0.35">0.35</option>
                                                                <option @if(0.40 == $percentChild->percentMiddle) selected @endif value="0.40">0.40</option>
                                                                <option @if(0.45 == $percentChild->percentMiddle) selected @endif value="0.45">0.45</option>
                                                                <option @if(0.50 == $percentChild->percentMiddle) selected @endif value="0.50">0.50</option>
                                                                <option @if(0.55 == $percentChild->percentMiddle) selected @endif value="0.55">0.55</option>
                                                                <option @if(0.60 == $percentChild->percentMiddle) selected @endif value="0.60">0.60</option>
                                                                <option @if(0.65 == $percentChild->percentMiddle) selected @endif value="0.65">0.65</option>
                                                                <option @if(0.70 == $percentChild->percentMiddle) selected @endif value="0.70">0.70</option>
                                                                <option @if(0.50 == $percentChild->percentMiddle) selected @endif value="0.75">0.75</option>
                                                                <option @if(0.80 == $percentChild->percentMiddle) selected @endif value="0.80">0.80</option>
                                                                <option @if(0.85 == $percentChild->percentMiddle) selected @endif value="0.85">0.85</option>
                                                                <option @if(0.90 == $percentChild->percentMiddle) selected @endif value="0.90">0.90</option>
                                                                <option @if(1 == $percentChild->percentMiddle) selected @endif value="1">1</option>
                                                                <option @if(1.25 == $percentChild->percentMiddle) selected @endif value="1.25">1.25</option>
                                                                <option @if(1.50 == $percentChild->percentMiddle) selected @endif value="1.50">1.50</option>
                                                                <option @if(1.75 == $percentChild->percentMiddle) selected @endif value="1.75">1.75</option>
                                                                <option @if(2 == $percentChild->percentMiddle) selected @endif value="2">2</option>
                                                                <option @if(2.25 == $percentChild->percentMiddle) selected @endif value="2.25">2.25</option>
                                                                <option @if(2.50 == $percentChild->percentMiddle) selected @endif value="2.50">2.50</option>
                                                                <option @if(2.75 == $percentChild->percentMiddle) selected @endif value="2.75">2.75</option>
                                                                <option @if(3 == $percentChild->percentMiddle) selected @endif value="3">3</option>
                                                                <option @if(3.25 == $percentChild->percentMiddle) selected @endif value="3.25">3.25</option>
                                                                <option @if(3.50 == $percentChild->percentMiddle) selected @endif value="3.50">3.50</option>
                                                                <option @if(3.75 == $percentChild->percentMiddle) selected @endif value="3.75">3.75</option>
                                                                <option @if(4 == $percentChild->percentMiddle) selected @endif value="4">4</option>
                                                                <option @if(4.25 == $percentChild->percentMiddle) selected @endif value="4.25">4.25</option>
                                                                <option @if(4.50 == $percentChild->percentMiddle) selected @endif value="4.50">4.50</option>
                                                                <option @if(4.75 == $percentChild->percentMiddle) selected @endif value="4.75">4.75</option>
                                                                <option @if(5 == $percentChild->percentMiddle) selected @endif value="5">5</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            @php($percentChild = App\Percent::where('user_id', Auth::user()->id)->where('category_id', $child->id)->first())
                                                            <select class="client_type form-control" onchange='check_checkbox($(this).val() + "_percentSmall_{{ $percentChild->id }}", "percents")'>
                                                                <option @if(0.05 == $percentChild->percentSmall) selected @endif value="0.05">0.05</option>
                                                                <option @if(0.10 == $percentChild->percentSmall) selected @endif value="0.10">0.10</option>
                                                                <option @if(0.15 == $percentChild->percentSmall) selected @endif value="0.15">0.15</option>
                                                                <option @if(0.20 == $percentChild->percentSmall) selected @endif value="0.20">0.20</option>
                                                                <option @if(0.25 == $percentChild->percentSmall) selected @endif value="0.25">0.25</option>
                                                                <option @if(0.30 == $percentChild->percentSmall) selected @endif value="0.30">0.30</option>
                                                                <option @if(0.35 == $percentChild->percentSmall) selected @endif value="0.35">0.35</option>
                                                                <option @if(0.40 == $percentChild->percentSmall) selected @endif value="0.40">0.40</option>
                                                                <option @if(0.45 == $percentChild->percentSmall) selected @endif value="0.45">0.45</option>
                                                                <option @if(0.50 == $percentChild->percentSmall) selected @endif value="0.50">0.50</option>
                                                                <option @if(0.55 == $percentChild->percentSmall) selected @endif value="0.55">0.55</option>
                                                                <option @if(0.60 == $percentChild->percentSmall) selected @endif value="0.60">0.60</option>
                                                                <option @if(0.65 == $percentChild->percentSmall) selected @endif value="0.65">0.65</option>
                                                                <option @if(0.70 == $percentChild->percentSmall) selected @endif value="0.70">0.70</option>
                                                                <option @if(0.50 == $percentChild->percentSmall) selected @endif value="0.75">0.75</option>
                                                                <option @if(0.80 == $percentChild->percentSmall) selected @endif value="0.80">0.80</option>
                                                                <option @if(0.85 == $percentChild->percentSmall) selected @endif value="0.85">0.85</option>
                                                                <option @if(0.90 == $percentChild->percentSmall) selected @endif value="0.90">0.90</option>
                                                                <option @if(1 == $percentChild->percentSmall) selected @endif value="1">1</option>
                                                                <option @if(1.25 == $percentChild->percentSmall) selected @endif value="1.25">1.25</option>
                                                                <option @if(1.50 == $percentChild->percentSmall) selected @endif value="1.50">1.50</option>
                                                                <option @if(1.75 == $percentChild->percentSmall) selected @endif value="1.75">1.75</option>
                                                                <option @if(2 == $percentChild->percentSmall) selected @endif value="2">2</option>
                                                                <option @if(2.25 == $percentChild->percentSmall) selected @endif value="2.25">2.25</option>
                                                                <option @if(2.50 == $percentChild->percentSmall) selected @endif value="2.50">2.50</option>
                                                                <option @if(2.75 == $percentChild->percentSmall) selected @endif value="2.75">2.75</option>
                                                                <option @if(3 == $percentChild->percentSmall) selected @endif value="3">3</option>
                                                                <option @if(3.25 == $percentChild->percentSmall) selected @endif value="3.25">3.25</option>
                                                                <option @if(3.50 == $percentChild->percentSmall) selected @endif value="3.50">3.50</option>
                                                                <option @if(3.75 == $percentChild->percentSmall) selected @endif value="3.75">3.75</option>
                                                                <option @if(4 == $percentChild->percentSmall) selected @endif value="4">4</option>
                                                                <option @if(4.25 == $percentChild->percentSmall) selected @endif value="4.25">4.25</option>
                                                                <option @if(4.50 == $percentChild->percentSmall) selected @endif value="4.50">4.50</option>
                                                                <option @if(4.75 == $percentChild->percentSmall) selected @endif value="4.75">4.75</option>
                                                                <option @if(5 == $percentChild->percentSmall) selected @endif value="5">5</option>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @else
                                                    Подкатегорий нет.
                                                @endif
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
                $('img.show_details').on('click', function() {
                    var id_current = $(this).parent().parent().find('span.category_id').html();

                    if($(this).attr("src") == "/img/plus.png"){
                        var src = '/images/minus.png';
                        var category_parent = $(this).next().val();
                        //getChildren(id_current);
                        $('tr#cat_' + category_parent + '_children').show();
                    }
                    else{
                        var src = '/img/plus.png';
                        $('tr#cat_' + id_current + '_children').hide();
                    }
                    $(this).attr("src", src);
                });
            </script>
        </div>
    </div>
@endsection