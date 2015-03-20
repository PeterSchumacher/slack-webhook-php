<?php

/**
 * Notify Slack using a webhook
 *
 * @author peter@schumacher.dk
 */
class SlackNotifier {

    static $slackUrl      = '';
    static $timeout       = 5;
    static $slackChannel  = '';
    static $slackUsername = '';
    static $slackIcon     = '';

    static function init($slackUrl = '') {
        self::$slackUrl = $slackUrl;
    }

    static function notify($text = '') {

        if (empty(self::$slackUrl)) {
            throw new RuntimeException('Missing Slack url!');
        }

        if (empty($text)) {
            throw new RuntimeException('Empty message!');
        }

        if (empty(self::$slackChannel) && empty(self::$slackUsername) && empty(self::$slackIcon)) {
            $header = 'Content-Type: application/json';
            $data   = json_encode(["text" => $text]);
        } else {
            $header = 'Content-type: application/x-www-form-urlencoded';
            $data   = [];

            $data['text'] = $text;

            if (!empty(self::$slackChannel)) {
                $data['channel'] = self::$slackChannel;
            }

            if (!empty(self::$slackUsername)) {
                $data['username'] = self::$slackUsername;
            }

            if (!empty(self::$slackIcon)) {
                $data['icon_emoji'] = self::$slackIcon;
            }

            $data = http_build_query(['payload' => json_encode($data)]);
        }

        $context = [
            'http' => [
                'method'  => 'POST',
                'header'  => $header,
                'Content-Length: ' . strlen($data) . "\r\n",
                'content' => $data,
                'timeout' => self::$timeout, 
            ]
        ];
        
        @file_get_contents(self::$slackUrl, null, stream_context_create($context));
    }
}