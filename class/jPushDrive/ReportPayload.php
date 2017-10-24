<?php


class jPushDrive_ReportPayload {
    private static $EFFECTIVE_TIME_UNIT = array('HOUR', 'DAY', 'MONTH');

    const REPORT_URL = 'https://report.jpush.cn/v3/received';
    const MESSAGES_URL = 'https://report.jpush.cn/v3/messages';
    const USERS_URL = 'https://report.jpush.cn/v3/users';

    private $client;

    /**
     * ReportPayload constructor.
     * @param $client JPush
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    public function getReceived($msgIds) {
        $queryParams = '?msg_ids=';
        if (is_array($msgIds) && !empty($msgIds)) {
            $msgIdsStr = implode(',', $msgIds);
            $queryParams .= $msgIdsStr;
        } elseif (is_string($msgIds)) {
            $queryParams .= $msgIds;
        } else {
            throw new InvalidArgumentException("Invalid msg_ids");
        }

        $url = jPushDrive_ReportPayload::REPORT_URL . $queryParams;
        return jPushDrive_Http::get($this->client, $url);
    }

    public function getMessages($msgIds) {
        $queryParams = '?msg_ids=';
        if (is_array($msgIds) && !empty($msgIds)) {
            $msgIdsStr = implode(',', $msgIds);
            $queryParams .= $msgIdsStr;
        } elseif (is_string($msgIds)) {
            $queryParams .= $msgIds;
        } else {
            throw new InvalidArgumentException("Invalid msg_ids");
        }

        $url = jPushDrive_ReportPayload::MESSAGES_URL . $queryParams;
        return jPushDrive_Http::get($this->client, $url);
    }

    public function getUsers($time_unit, $start, $duration) {
        $time_unit = strtoupper($time_unit);
        if (!in_array($time_unit, self::$EFFECTIVE_TIME_UNIT)) {
            throw new InvalidArgumentException('Invalid time unit');
        }

        $url = jPushDrive_ReportPayload::USERS_URL . '?time_unit=' . $time_unit . '&start=' . $start . '&duration=' . $duration;
        return jPushDrive_Http::get($this->client, $url);
    }
}
