
<div class="container">
    <h2>Bot Conversations Table</h2>
    
    <table class="table table-bordered">
        <thead>
            <tr>
                @foreach($tableData as $botMessage => $userResponses)
                    <th>{{ $botMessage }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @php
                $maxRows = max(array_map('count', $tableData));
            @endphp
            @for($i = 0; $i < $maxRows; $i++)
                <tr>
                    @foreach($tableData as $userResponses)
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
</div>

