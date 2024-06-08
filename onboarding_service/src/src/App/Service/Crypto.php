<?php

namespace METRIC\App\Service;

class Crypto {

    public static function hash(string $data) {
        return hash('sha256', $data);
    }

    public static function verify($data, $hash) {
        return ($hash == self::hash($data));
    }

    public static function checkOrigin($eventTime, $eventType, $webhookToken, $eventHash) {
        $hash = hash_hmac("sha256", $eventTime . $eventType, $webhookToken); 
        return ($hash === $eventHash) ? true : false;
    }

}