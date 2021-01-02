# systemd-calendar-check

Validation of the systemd "Calendar" format used for timers.

It uses a giant 75 line regex to check that the pattern makes sense, but it does not verify that the components other than the timezone are valid (ie, month 94 is accepted)
