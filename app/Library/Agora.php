<?php

namespace App\Library;

use TaylanUnutmaz\AgoraTokenBuilder\RtcTokenBuilder;

class Agora
{
    protected static $appId;
    protected static $appCertificate;

    public static function init()
    {
        self::$appId = env('AGORA_APP_ID');
        self::$appCertificate = env('AGORA_APP_CERTIFICATE');

        if (!self::$appId || !self::$appCertificate) {
            throw new \Exception('AGORA_APP_ID or AGORA_APP_CERTIFICATE is not set.');
        }
    }

    public static function generateToken($channelName, $uid, $role = RtcTokenBuilder::RolePublisher, $tokenExpirationInSeconds = 3600, $privilegeExpirationInSeconds = 3600)
    {
        self::init(); 

        return RtcTokenBuilder::buildTokenWithUid(
            self::$appId,
            self::$appCertificate,
            $channelName,
            $uid,
            $role,
            $tokenExpirationInSeconds,
            $privilegeExpirationInSeconds
        );
    }
}
