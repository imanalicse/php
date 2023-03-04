<?php
function getCampaignScheduleTimeForSpeceficTime($when_to_send, $when_to_date, $when_to_time, $when_to_duration, $when_to_unit) {

    $user = $this->getDbTable('Users')->find()->where(["sub" => $this->authUser('sub')])->first();
    if ($user && $user['timezone']){
        $timeZone = new DateTimeZone($user['timezone']);
    } else {
        $ipLocationData = Configure::read('ip_geo_location_data');
        if (!empty($ipLocationData)) {
            $timeZone = new DateTimeZone($ipLocationData['ip_location_time_zone']);
        } else {
            $timeZone = new DateTimeZone("Australia/Melbourne");
        }
    }


    switch($when_to_send){
        case 'now':
            $scheduleTime = new DateTime("now", $timeZone);
            break;

        case 'specific-time':
            $scheduleTime = date_create_from_format('d/m/Y H:i', $when_to_date.' '.$when_to_time, $timeZone);
            break;

        case 'specific-duration-from-now':
            $scheduleTime = new DateTime('+'.$when_to_duration.' '.$when_to_unit, $timeZone);
            break;
        default:
            $scheduleTime = null;
            break;
    }

    if($scheduleTime){
        $scheduleTime = $scheduleTime->getTimestamp();
    }
    return $scheduleTime;

}