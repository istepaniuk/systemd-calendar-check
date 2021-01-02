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
            (?(DEFINE)
               (?<weekday>monday|tuesday|wednesday|thursday|friday|saturday|sunday|mon|tue|wed|thu|fri|sat|sun)
               (?<integer_or_range>
                   \d+                                 # value
                   (\.\.\d+(?!\d))?                    # range
                   (\/\d+)?                            # repetition
                   (,(?=\d))?                          # comma if there is more
               )

               (?<float_or_range>
                   \d+                                 # seconds
                   (.\d+)?                             # fractional
                   (\.\.\d+(.\d+)?(?!\d))?             # range
                   (\/\d+(.\d+)?)?                     # repetition
                   (,(?=\d))?                          # comma if there is more
               )

               (?<list_of_integer_ranges_or_a_star>
                    (?&integer_or_range)* | \*
               )

               (?<list_of_float_ranges_or_a_star>
                    (?&float_or_range)* | \*
               )
            )
            ^
            (
                (annually|minutely|hourly|daily|monthly|weekly|yearly|quarterly|semiannually)
                |
                (?P<weekdays>
                    (                        
                        \b
                        (?&weekday)(\.\.(?&weekday))?
                        ,?
                    )*
                )?
                (?P<dates>
                    (?:[ ]|^)
                    (?P<years> (?&list_of_integer_ranges_or_a_star) - )?
                    (?P<months> (?&list_of_integer_ranges_or_a_star))
                    [-~]
                    (?P<days> (?&list_of_integer_ranges_or_a_star))
                )?
                (?:[ ]|$|^)
                (?P<times>
                    (?P<hours> (?&list_of_integer_ranges_or_a_star))
                    :
                    (?P<minutes> (?&list_of_integer_ranges_or_a_star))
                    (?P<seconds> : (?&list_of_float_ranges_or_a_star))?
                )?
            )?
            (?:[ ]|$)
            (?P<timezone> .+)?
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
