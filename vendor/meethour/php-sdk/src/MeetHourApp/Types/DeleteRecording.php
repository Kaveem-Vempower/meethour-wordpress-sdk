<?php
namespace MeetHourApp\Types;
class DeleteRecording {
    public int $recording_id;

    public function __construct(int $recording_id) {
        $this->recording_id = $recording_id;
    }
    public function prepare() {
        $deleteRecordingProperties = [
            "recording_id" => $this->recording_id
        ];
        foreach ($deleteRecordingProperties as $key => $value) {
            if ($value === null) {
              unset($deleteRecordingProperties[$key]);
            }
          }
        return $deleteRecordingProperties;
    }
}