<?php

namespace API;

class Logger
{
    const LOGS = __DIR__.'/logs';

    const LEVEL_FAILED  = 'FAILED';
    const LEVEL_SUCCESS = 'SUCCESS';

    /**
     * @param string $level
     * @param string $company
     * @param string $site
     * @param string $lot
     * @param string $message
     * @return void
     */
    public static function log(
        string $level,
        string $company,
        string $site,
        string $lot,
        string $message
    ) {
        $date = gmdate("D, d M Y H:i:s T");
        $message = "[{$date}][{$company}][{$site}] {$level}, LOT {$lot}: {$message}\r\n";
        @file_put_contents(static::LOGS.'/'.$company.'/'.$site.'.log', $message, \FILE_APPEND | \LOCK_EX);
    }
}
