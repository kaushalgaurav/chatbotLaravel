@extends('layouts.inner-layouts')

@section('content')

<style>
  body[data-layout="horizontal"] .page-content {
  margin-top: 10px !important;
  padding: 30px 0px 30px !important;
}
   </style>


 <div class="analyze-section">
      <nav>
         <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#nav-conversations" type="button" role="tab" aria-selected="true">conversations' data</button>
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-metrics" type="button" role="tab" aria-selected="false">metrics</button>
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nav-analytics" type="button" role="tab" aria-selected="false">flow analytics</button>
          </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-conversations" role="tabpanel" tabindex="0">
          <div class="card-section-table">
           <div class="card-body">
            {{-- <h4 class="card-title">Datatable</h4> --}}
            <table id="datatable" class="table table-bordered dt-responsive nowrap w-100">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Position</th>
                        <th>Office</th>
                        <th>Age</th>
                        <th>Start date</th>
                        <th>Salary</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Tiger Nixon</td>
                        <td>System Architect</td>
                        <td>Edinburgh</td>
                        <td>61</td>
                        <td>2011/04/25</td>
                        <td>$320,800</td>
                    </tr>
                    <tr>
                        <td>Garrett Winters</td>
                        <td>Accountant</td>
                        <td>Tokyo</td>
                        <td>63</td>
                        <td>2011/07/25</td>
                        <td>$170,750</td>
                    </tr>
                    <tr>
                        <td>Ashton Cox</td>
                        <td>Junior Technical Author</td>
                        <td>San Francisco</td>
                        <td>66</td>
                        <td>2009/01/12</td>
                        <td>$86,000</td>
                    </tr>
                    <tr>
                        <td>Cedric Kelly</td>
                        <td>Senior Javascript Developer</td>
                        <td>Edinburgh</td>
                        <td>22</td>
                        <td>2012/03/29</td>
                        <td>$433,060</td>
                    </tr>
                    <tr>
                        <td>Airi Satou</td>
                        <td>Accountant</td>
                        <td>Tokyo</td>
                        <td>33</td>
                        <td>2008/11/28</td>
                        <td>$162,700</td>
                    </tr>
                    <tr>
                        <td>Brielle Williamson</td>
                        <td>Integration Specialist</td>
                        <td>New York</td>
                        <td>61</td>
                        <td>2012/12/02</td>
                        <td>$372,000</td>
                    </tr>
                    <tr>
                        <td>Herrod Chandler</td>
                        <td>Sales Assistant</td>
                        <td>San Francisco</td>
                        <td>59</td>
                        <td>2012/08/06</td>
                        <td>$137,500</td>
                    </tr>
                    <tr>
                        <td>Rhona Davidson</td>
                        <td>Integration Specialist</td>
                        <td>Tokyo</td>
                        <td>55</td>
                        <td>2010/10/14</td>
                        <td>$327,900</td>
                    </tr>
                </tbody>
            </table>
           </div>
        </div>
        </div>
        <div class="tab-pane fade" id="nav-metrics" role="tabpanel" tabindex="0">
            <div class="card-section-table">
               <div class="card-body">
                <p>Metrics content goes here...</p>
               </div>
            </div>
        </div>
        <div class="tab-pane fade" id="nav-analytics" role="tabpanel" tabindex="0">
             <div class="card-section-table">
               <div class="card-body">
                <p>Analytics content goes here...</p>
               </div>
            </div>
        </div>
        </div>
 </div>


@endsection

{{-- <div class="container">
    <h2>Bot Conversations Table</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                @foreach ($tableData as $botMessage => $userResponses)
                    <th>{{ $botMessage }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $maxRows = max(array_map('count', $tableData));
            @endphp
            @for ($i = 0; $i < $maxRows; $i++)
                <tr>
                    @foreach ($tableData as $userResponses)
                        <td>{{ $userResponses[$i] ?? '' }}</td>
                    @endforeach
                </tr>
            @endfor
        </tbody>
    </table>

    <h3>Generic Analytics</h3>
    <ul>
        <li>Total Conversations: {{ $analytics['total_conversations'] }}</li>
        <li>Total Messages: {{ $analytics['total_messages'] }}</li>
        <li>Bot Messages: {{ $analytics['bot_messages'] }}</li>
        <li>User Messages: {{ $analytics['user_messages'] }}</li>
        <li>Average User Messages per Conversation: {{ $analytics['average_user_messages_per_conversation'] }}</li>
    </ul>
</div> --}}
