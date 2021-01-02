<?php

namespace SystemdCalendarCheck\Tests;

use PHPUnit\Framework\TestCase;
use SystemdCalendarCheck\CalendarValidator;

final class CalendarValidatorTests extends TestCase
{
    private $calendarValidator;

    public function setUp(): void
    {
        $this->calendarValidator = new CalendarValidator();
    }

    public function test_it_does_not_accept_an_empty_string()
    {
        self::assertFalse($this->calendarValidator->isValid(""));
        self::assertFalse($this->calendarValidator->isValid(" "));
    }

    public function test_it_accepts_shorthands()
    {
        self::assertTrue($this->calendarValidator->isValid("minutely"));
        self::assertTrue($this->calendarValidator->isValid("hourly"));
        self::assertTrue($this->calendarValidator->isValid("daily"));
        self::assertTrue($this->calendarValidator->isValid("monthly"));
        self::assertTrue($this->calendarValidator->isValid("weekly"));
        self::assertTrue($this->calendarValidator->isValid("yearly"));
        self::assertTrue($this->calendarValidator->isValid("quarterly"));
        self::assertTrue($this->calendarValidator->isValid("semiannually"));
    }

    public function test_it_accepts_the_normalized_form_of_specific_date_and_time()
    {
        self::assertTrue($this->calendarValidator->isValid("2030-30-03 12:30"));
    }

    public function test_it_does_not_accept_date_and_time_without_space()
    {
        self::assertFalse($this->calendarValidator->isValid("2030-30-0312:30"));
    }

    public function test_it_does_not_accept_weekdays_and_date_without_space()
    {
        self::assertFalse($this->calendarValidator->isValid("Mon2030-30-03"));
    }


    public function test_it_accepts_the_normalized_form_of_specific_date_and_time_with_seconds()
    {
        self::assertTrue($this->calendarValidator->isValid("2030-30-03 12:30:00"));
    }

    public function test_it_accepts_the_normalized_form_of_specific_date_and_time_with_time_fractions()
    {
        self::assertTrue($this->calendarValidator->isValid("2030-30-03 12:30:00.123456"));
    }

    public function test_it_accepts_less_digits_than_the_normalized_form()
    {
        self::assertTrue($this->calendarValidator->isValid("3-3-3 2:0:0.1"));
    }

    public function test_it_accepts_dates_without_time()
    {
        self::assertTrue($this->calendarValidator->isValid("2030-02-01"));
    }

    public function test_it_accepts_times_without_date()
    {
        self::assertTrue($this->calendarValidator->isValid("05:26"));
    }

    public function test_it_accepts_dates_without_year()
    {
        self::assertTrue($this->calendarValidator->isValid("02-01"));
    }

    public function test_it_accepts_dates_with_sets()
    {
        self::assertTrue($this->calendarValidator->isValid("2030-02,03,04-01"));
        self::assertTrue($this->calendarValidator->isValid("2030-02,03,04-01,2,3"));
        self::assertTrue($this->calendarValidator->isValid("2030,2031-02-01"));
    }

    public function test_it_accepts_times_with_sets()
    {
        self::assertTrue($this->calendarValidator->isValid("2030-02-01 1,3,4:01"));
        self::assertTrue($this->calendarValidator->isValid("2030-02-01 04:02,03,21:11,21"));
        self::assertTrue($this->calendarValidator->isValid("2030-02-01 04:02:30,03.123,21.2"));
    }

    public function test_it_does_not_accept_times_with_sets_ending_with_comma()
    {
        self::assertFalse($this->calendarValidator->isValid("2030-02-01 04:02,22,"));
        self::assertFalse($this->calendarValidator->isValid("2030-02-01 04,:02"));
        self::assertFalse($this->calendarValidator->isValid("2030-02-01 04:02:22,44,"));
    }

    public function test_it_does_not_accept_dates_with_sets_ending_with_comma()
    {
        self::assertFalse($this->calendarValidator->isValid("2030,-02-01 04:00"));
        self::assertFalse($this->calendarValidator->isValid("2030-02,-01 04:00"));
        self::assertFalse($this->calendarValidator->isValid("2030-02-01, 04:00"));
    }

    public function test_it_accepts_patterns_with_more_than_one_space_between_date_and_time_parts()
    {
        self::assertTrue($this->calendarValidator->isValid("2030-02-01  04:01"));
        self::assertTrue($this->calendarValidator->isValid("2030-02-01   04:01"));
    }

    public function test_it_does_not_accept_dates_with_too_many_digits()
    {
        self::assertTrue($this->calendarValidator->isValid("2030-02-1234"));
        self::assertTrue($this->calendarValidator->isValid("2030124-123402-12451212"));
    }

    public function test_it_accepts_patterns_with_wildcards_on_dates()
    {
        self::assertTrue($this->calendarValidator->isValid("*-*-* 04:01"));
        self::assertTrue($this->calendarValidator->isValid("*-*-*"));
        self::assertTrue($this->calendarValidator->isValid("*-*"));
        self::assertTrue($this->calendarValidator->isValid("2030-*-01 04:01"));
        self::assertTrue($this->calendarValidator->isValid("2030-02-* 04:01"));
    }

    public function test_it_accepts_patterns_with_wildcards_on_hours_and_minutes()
    {
        self::assertTrue($this->calendarValidator->isValid("2030-02-01 *:*"));
        self::assertTrue($this->calendarValidator->isValid("2:10"));
        self::assertTrue($this->calendarValidator->isValid("*:*:10"));
    }

    public function test_it_accepts_patterns_with_wildcards_on_seconds()
    {
        self::assertTrue($this->calendarValidator->isValid("2030-02-01 *:*"));
        self::assertTrue($this->calendarValidator->isValid("*:*:*"));
        self::assertTrue($this->calendarValidator->isValid("10:*:*"));
        self::assertTrue($this->calendarValidator->isValid("*:10:*"));
        self::assertTrue($this->calendarValidator->isValid("*:10"));
        self::assertTrue($this->calendarValidator->isValid("*:*:10"));
    }

    public function test_it_accepts_patterns_with_weekdays()
    {
        self::assertTrue($this->calendarValidator->isValid("MondaY"));
        self::assertTrue($this->calendarValidator->isValid("Tuesday"));
        self::assertTrue($this->calendarValidator->isValid("Wednesday"));
        self::assertTrue($this->calendarValidator->isValid("ThurSday"));
        self::assertTrue($this->calendarValidator->isValid("friday"));
        self::assertTrue($this->calendarValidator->isValid("saturday"));
        self::assertTrue($this->calendarValidator->isValid("sunday"));
    }

    public function test_it_accepts_patterns_with_abbreviated_weekdays()
    {
        self::assertTrue($this->calendarValidator->isValid("Mon"));
        self::assertTrue($this->calendarValidator->isValid("Tue"));
        self::assertTrue($this->calendarValidator->isValid("Wed"));
        self::assertTrue($this->calendarValidator->isValid("Thu"));
        self::assertTrue($this->calendarValidator->isValid("fri"));
        self::assertTrue($this->calendarValidator->isValid("sat"));
        self::assertTrue($this->calendarValidator->isValid("sun"));
    }

    public function test_it_accepts_weekday_sets()
    {
        self::assertTrue($this->calendarValidator->isValid("Mon,Fri"));
        self::assertFalse($this->calendarValidator->isValid("MonFri"));
    }

    public function test_it_accepts_weekdays_without_dates_or_times()
    {
        self::assertTrue($this->calendarValidator->isValid("Mon,Fri"));
    }

    public function test_it_accepts_weekday_sets_ending_with_comma()
    {
        # This is the behaviour as of systemd version 247
        self::assertTrue($this->calendarValidator->isValid("Mon,Fri,"));
    }

    public function test_it_accepts_weekdays_with_times()
    {
        self::assertTrue($this->calendarValidator->isValid("Mon,Fri 8:15"));
    }

    public function test_it_accepts_weekdays_with_ranges()
    {
        self::assertTrue($this->calendarValidator->isValid("Mon..Friday"));
    }

    public function test_it_accepts_weekdays_with_lists_and_ranges()
    {
        self::assertTrue($this->calendarValidator->isValid("Mon..Thursday,Sat,Sun"));
    }

    public function test_it_does_not_accept_weekdays_ranges_with_more_than_two_elements()
    {
        self::assertFalse($this->calendarValidator->isValid("Mon..Thursday..Sat,Sun"));
        self::assertFalse($this->calendarValidator->isValid("Sun,Mon..Thu..Sat"));
    }

    public function test_it_accepts_dates_with_ranges()
    {
        self::assertTrue($this->calendarValidator->isValid("2023-05..72-*"));
    }

    public function test_it_accepts_times_with_ranges_in_minutes_and_hours()
    {
        self::assertTrue($this->calendarValidator->isValid("08..18:00"));
        self::assertTrue($this->calendarValidator->isValid("40:18..28"));
    }

    public function test_it_accepts_times_with_ranges_in_the_seconds()
    {
        self::assertTrue($this->calendarValidator->isValid("10:10:08..18"));
        self::assertTrue($this->calendarValidator->isValid("10:40:18..28"));
        self::assertTrue($this->calendarValidator->isValid("10:40:18.0002..28.500"));
    }

    public function test_it_does_not_accept_hour_ranges_with_more_than_two_parts()
    {
        self::assertFalse($this->calendarValidator->isValid("08..18..33:10:00"));
    }

    public function test_it_does_not_accept_minute_ranges_with_more_than_two_parts()
    {
        self::assertFalse($this->calendarValidator->isValid("08:10..23..33:00"));
    }

    public function test_it_does_not_accept_second_ranges_with_more_than_two_parts()
    {
        self::assertFalse($this->calendarValidator->isValid("08:01:10..23..33"));
    }

    public function test_it_accepts_dates_with_ranges_and_with_time()
    {
        self::assertTrue($this->calendarValidator->isValid("*-*-* 01:02"));
    }

    public function test_it_accepts_dates_with_last_day_specifier()
    {
        self::assertTrue($this->calendarValidator->isValid("2023-05~01"));
        self::assertTrue($this->calendarValidator->isValid("2023-05~01 05:00:00"));
        self::assertTrue($this->calendarValidator->isValid("2023-05~07/1"));
    }

    public function test_it_accepts_time_minute_repetition()
    {
        self::assertTrue($this->calendarValidator->isValid("*-*-* 01:0/10"));
    }

    public function test_it_accepts_time_hour_repetition()
    {
        self::assertTrue($this->calendarValidator->isValid("*-*-* 01/1:00"));
    }

    public function test_it_accepts_time_second_repetition()
    {
        self::assertTrue($this->calendarValidator->isValid("*-*-* 01:10:0/15"));
    }

    public function test_it_accepts_date_day_repetition()
    {
        self::assertTrue($this->calendarValidator->isValid("*-*-1/3"));
    }

    public function test_it_accepts_date_month_repetition()
    {
        self::assertTrue($this->calendarValidator->isValid("*-1/3-*"));
    }

    public function test_it_accepts_date_year_repetition()
    {
        self::assertTrue($this->calendarValidator->isValid("2010/3-*-*"));
    }
}
