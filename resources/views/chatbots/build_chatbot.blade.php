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

    <div id="app">
        <build-chatbot :chatbot='@json($chatbot)'></build-chatbot>
    </div>
@endsection

@section('script')

@endsection
