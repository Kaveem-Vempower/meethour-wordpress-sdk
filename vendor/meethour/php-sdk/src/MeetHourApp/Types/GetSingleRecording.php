<?php
namespace MeetHourApp\Types;
class GetSingleRecording {
    public int $recording_id;

    public function __construct(int $recording_id) {
        $this->recording_id = $recording_id;
    }
    public function prepare() {
        $getSingleRecordingProperties = [
            "recording_id" => $this->recording_id
        ];
        foreach ($getSingleRecordingProperties as $key => $value) {
            if ($value === null) {
              unset($getSingleRecordingProperties[$key]);
            }
          }
        return $getSingleRecordingProperties;
    }
}