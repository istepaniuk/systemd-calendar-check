<?php

namespace SystemdCalendarCheck\Tests;

use PHPUnit\Framework\TestCase;
use SystemdCalendarCheck\CalendarValidator;

final class CalendarValidatorSpecTests extends TestCase
{
    private $calendarValidator;

    public function setUp(): void
    {
        $this->calendarValidator = new CalendarValidator();
    }

    /**
     * @dataProvider casesFromTheSystemdDocumentationProvider
     * @param string $pattern
     */
    public function test_it_accepts_patterns_with_wildcards_on_seconds(string $pattern)
    {
        $this->assertTrue($this->calendarValidator->isValid($pattern), "Pattern '$pattern' should be valid");
    }

    public function casesFromTheSystemdDocumentationProvider(): array
    {
        return [
            ['Sat,Thu,Mon..Wed,Sat..Sun'],
            ['Mon,Sun 12-*-* 2,1:23'],
            ['Wed *-1'],
            ['Wed..Wed,Wed *-1'],
            ['Wed, 17:48'],
            ['Wed..Sat,Tue 12-10-15 1:2:3'],
            ['*-*-7 0:0:0'],
            ['10-15'],
            ['monday *-12-* 17:00'],
            ['Mon,Fri *-*-3,1,2 *:30:45'],
            ['12,14,13,12:20,10,30'],
            ['12..14:10,20,30'],
            ['mon,fri *-1/2-1,3 *:30:45'],
            ['03-05 08:05:40'],
            ['08:05:40'],
            ['05:40'],
            ['Sat,Sun 12-05 08:05:40'],
            ['Sat,Sun 08:05:40'],
            ['2003-03-05 05:40'],
            ['05:40:23.4200004/3.1700005'],
            ['2003-02..04-05'],
            ['2003-03-05 05:40 UTC'],
            ['2003-03-05'],
            ['03-05'],
            ['hourly'],
            ['daily'],
            ['daily UTC'],
            ['monthly'],
            ['weekly'],
            ['weekly Pacific/Auckland'],
            ['yearly'],
            ['annually'],
            ['*:2/3'],
        ];
    }
}
