<?php

namespace SystemdCalendarCheck;

use Exception;

final class CalendarValidator
{
    public function isValid(string $pattern): bool
    {
        $pattern = $this->trimAndSimplifySpaces($pattern);
        if (empty($pattern)) {
            return false;
        }

        $matches = [];
        $isFullMatch = preg_match(
            "/
            ^
            (
                (annually|minutely|hourly|daily|monthly|weekly|yearly|quarterly|semiannually)
                |
                (?P<weekdays>
                    (                        
                        (monday|tuesday|wednesday|thursday|friday|saturday|sunday|mon|tue|wed|thu|fri|sat|sun)
                        (\.\.(monday|tuesday|wednesday|thursday|friday|saturday|sunday|mon|tue|wed|thu|fri|sat|sun))?
                        ,?
                    )*
                )?
                (?P<dates>
                    [ ]?                                        # space
                    (?P<year>
                        (
                            \d{1,4}                             # year digits
                            (\/\d+)?                            # repetition
                            (,(?=\d))?                          # comma if more
                        )*
                        -
                        | \*                                    # or asterisk
                        -
                    )?                                         
                    (?P<month>
                        (
                            \d{1,2}
                            (\.\.\d{1,2}(?!\d))?                # range
                            (\/\d+)?                            # repetition
                            (,(?=\d))?                          # comma if more
                        )* 
                        | \*                                    # or asterisk
                    )
                    [-~]                                        # - or ~
                    (?P<day>
                        (
                            \d{1,2}
                            (\.\.\d{1,2}(?!\d))?                # range
                            (\/\d+)?                            # repetition
                            (,(?=\d))?                          # comma if more
                        )*
                        | \*                                    # or asterisk
                    )
                )?
                (?P<times>
                    [ ]?                                        # space
                    (?P<hours>
                        (
                            \d{1,2}                             # hours
                            (\.\.\d{1,2}(?!\d))?                # range
                            (\/\d+)?                            # repetition
                            (,(?=\d))?                          # comma if more
                        )*   
                        |
                        \*                                      # or asterisk
                    )
                    :
                    (?P<minutes>
                        (
                            \d{1,2}                             # minutes
                            (\.\.\d{1,2}(?!\d))?                # range
                            (\/\d+)?                            # repetition
                            (,(?=\d))?                          # comma if more
                        )*
                        |
                        \*
                    )
                    (?P<seconds>
                        :
                        (
                            (
                                (
                                    \d{1,2}                     # seconds
                                    (.\d+)?                     # fractional
                                    (\.\.\d{1,2}(.\d+)?(?!\d))? # range
                                )
                                (\/\d+(.\d+)?)?                 # repetition
                                (,(?=\d))?                      # comma if more
                            )*   
                            |
                            \*                                  # or asterisk
                        )
                    )?
                )?
            )?
            (?P<timezone>
                [ ]
                .+
            )?
            $
            /xi",
            $pattern,
            $matches
        );

        return $isFullMatch
            && $this->isValidTimezone($matches['timezone'] ?? '');
    }

    private function trimAndSimplifySpaces(string $pattern)
    {
        return preg_replace('/ +/', ' ', trim($pattern));
    }

    private function isValidTimezone(string $timezone): bool
    {
        if (empty($timezone)) {
            return true;
        }

        try {
            return timezone_open($timezone) !== false;
        } catch (Exception $exception) {
            return false;
        }
    }
}
