@extends('layouts.master')

@section('title')
    @lang('translation.chatbot')
@endsection

@section('css')

@endsection

@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Backend
        @endslot
        @slot('title')
            Chatbot
        @endslot
    @endcomponent



@endsection

@section('script')

@endsection
