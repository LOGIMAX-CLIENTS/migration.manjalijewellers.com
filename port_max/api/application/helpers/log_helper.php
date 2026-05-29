<?php
defined('BASEPATH') or exit('No direct script access allowed');

class LogHelper
{
    public static function activityLog($data)
    {
        $date = date('Y-m-d');
        $time = date('H:i:s');

        $destDir = FCPATH . "uploads/" . $data['client'] . "/imported/";
        $logDir = APPPATH . "logs/";

        $logFile = ($data['type'] == 'import' ? $destDir : $logDir) . "{$date}.txt";

        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        if (!is_dir($destDir)) {
            mkdir($destDir, 0777, true);
        }

        $id = (!empty($data['fileId']) ? $data['fileId'] : $data['page']);

        file_put_contents(
            $logFile,
            $time."|{$id}|".json_encode($data) . PHP_EOL,
            FILE_APPEND
        );
    }
}
