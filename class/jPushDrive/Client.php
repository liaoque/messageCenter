<?php


class jPushDrive_Client
{

    private $appKey;
    private $masterSecret;
    private $retryTimes;
    private $logFile;

    public function __construct($appKey, $masterSecret, $logFile = jPushDrive_Config::DEFAULT_LOG_FILE, $retryTimes = jPushDrive_Config::DEFAULT_MAX_RETRY_TIMES)
    {
        if (!is_string($appKey) || !is_string($masterSecret)) {
            throw new InvalidArgumentException("Invalid appKey or masterSecret");
        }
        $this->appKey = $appKey;
        $this->masterSecret = $masterSecret;
        if (!is_null($retryTimes)) {
            $this->retryTimes = $retryTimes;
        } else {
            $this->retryTimes = 1;
        }

        $this->logFile = $logFile;
    }

    public function push()
    {
        return new jPushDrive_PushPayload($this);
    }

    public function report()
    {
        return new jPushDrive_ReportPayload($this);
    }

    public function device()
    {
        return new jPushDrive_DevicePayload($this);
    }

    public function schedule()
    {
        return new jPushDrive_SchedulePayload($this);
    }

    public function getAuthStr()
    {
        return $this->appKey . ":" . $this->masterSecret;
    }

    public function getRetryTimes()
    {
        return $this->retryTimes;
    }

    public function getLogFile()
    {
        return $this->logFile;
    }


}