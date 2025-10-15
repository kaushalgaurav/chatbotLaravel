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

<style>
    .text-right {
    text-align: right !important;
}
</style>

    <div id="app">
        {{-- <build-chatbot :chatbot='@json($chatbot)'></build-chatbot> --}}
        <div class="row">
     <div class="col-xl-12">
        <div class="card">
        <div class="card-body">
            <div class="card-header d-flex align-items-center justify-content-between p-0 bg-transparent mb-3">
              <h4 class="card-title">Chatbot</h4>
            </div>
            <form method="post" action="{{ route('chatbots.update', Crypt::encryptString($chatbot->id)) }}">
                @csrf
                @method('PUT')
                <div class="row g-3 align-items-center mb-4">
                    <div class="col-lg-2">
                        <label for="" class="col-form-label">Name</label>
                    </div>
                    <div class="col-lg-6">
                        <input type="text" id="inpurName" class="form-control" placeholder="Name" name="chatbot_name" value="{{ $chatbot->name }}" readonly>
                    </div>
                </div>
                <div class="row g-3 align-items-center mb-4">
                    <div class="col-lg-2">
                        <label for="" class="col-form-label">Purpose</label>
                    </div>
                    <div class="col-lg-6">
                        <input type="text" id="Purpose" class="form-control" placeholder="Purpose" name="purpose">
                    </div>
                </div>
                <div class="row g-3 align-items-center mb-4">
                    <div class="col-lg-2">
                        <label for="" class="col-form-label">Chatbot Type</label>
                    </div>
                    <div class="col-lg-6">
                        <input type="text" id="ChatbotType" class="form-control" placeholder="Chatbot Type" value="{{ $chatbot->platform }}" disabled>
                        {{-- <select class="form-control select-form" disabled>
                            <option>select chatbot</option>
                        </select> --}}
                    </div>
                </div> 
                <div class="row g-3  mb-4">
                    <div class="col-lg-2">
                        <label for="" class="col-form-label">Description</label>
                    </div>
                    <div class="col-lg-6">
                        <textarea class="form-control" rows="5" placeholder="Description" name="description"></textarea>
                    </div>
                </div>
                <div class="row g-3  mb-4">
                    <div class="col-lg-2">
                        <label for="" class="col-form-label">Upload File</label>
                    </div>
                    <div class="col-lg-6">
                        <input type="file" class="form-control" name="upload_file">
                    </div>
                </div>  
                
                <div class="row g-3 align-items-center">
                    <div class="col-lg-8">
                        <div class="text-right">
                            <a href="javascript: void(0);" class="btn btn-secondary">Reset<span class="mdi mdi-alert-circle-outline ms-1"></span></a>
                            <button type="submit" class="btn btn-primary ms-3">Submit<span class="mdi mdi-rocket-launch-outline ms-1"></span></button>
                        </div>
                    </div>
                </div>
           </form>
        </div>
      </div>
    </div>
  </div>
    </div>
@endsection

@section('script')

@endsection
