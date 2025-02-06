<?php
namespace MeetHourApp\Types;
class DeleteContact {
    public int $contact_id;

    public function __construct(int $contact_id) {
        $this->contact_id = $contact_id;
    }
    public function prepare() {
        $deleteContactProperties = [
            "contact_id" => $this->contact_id
        ];
        foreach ($deleteContactProperties as $key => $value) {
            if ($value === null) {
              unset($deleteContactProperties[$key]);
            }
          }
        return $deleteContactProperties;
    }
}