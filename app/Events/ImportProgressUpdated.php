<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\UploadJob;

class ImportProgressUpdated implements ShouldBroadcast {
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public UploadJob $job;

    public function __construct(UploadJob $job) {
        $this->job = $job;
    }

    public function broadcastOn() {
        return new PrivateChannel('import-progress.' . $this->job->upload_uuid);
    }

    public function broadcastWith() {
        return [
            'processed_rows' => $this->job->processed_rows,
            'total_rows' => $this->job->total_rows,
            'inserted' => $this->job->inserted,
            'status' => $this->job->status,
        ];
    }
}
