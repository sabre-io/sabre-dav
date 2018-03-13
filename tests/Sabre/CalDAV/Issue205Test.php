<?php declare (strict_types=1);

namespace Sabre\CalDAV;

use GuzzleHttp\Psr7\ServerRequest;
use Sabre\VObject;

/**
 * This unittest is created to check if a VALARM TRIGGER of PT0S is supported
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @license http://sabre.io/license/ Modified BSD License
 */
class Issue205Test extends \Sabre\DAVServerTest {

    protected $setupCalDAV = true;

    protected $caldavCalendars = [
        [
            'id'           => 1,
            'name'         => 'Calendar',
            'principaluri' => 'principals/user1',
            'uri'          => 'calendar1',
        ]
    ];

    protected $caldavCalendarObjects = [
        1 => [
            'event.ics' => [
                'calendardata' => 'BEGIN:VCALENDAR
VERSION:2.0
BEGIN:VEVENT
UID:20120330T155305CEST-6585fBUVgV
DTSTAMP:20120330T135305Z
DTSTART;TZID=Europe/Berlin:20120326T155200
DTEND;TZID=Europe/Berlin:20120326T165200
SUMMARY:original summary
TRANSP:OPAQUE
BEGIN:VALARM
ACTION:AUDIO
ATTACH;VALUE=URI:Basso
TRIGGER:PT0S
END:VALARM
END:VEVENT
END:VCALENDAR
',
            ],
        ],
    ];

    function testIssue205() {

        $request = new ServerRequest(
             'REPORT',

            '/calendars/user1/calendar1',
            [
                'Content-Type' => 'application/xml',
            'Depth'        => '1',
        ], '<?xml version="1.0" encoding="utf-8" ?>
<C:calendar-query xmlns:D="DAV:" xmlns:C="urn:ietf:params:xml:ns:caldav">
    <D:prop>
        <C:calendar-data>
            <C:expand start="20120325T220000Z" end="20120401T215959Z"/>
        </C:calendar-data>
        <D:getetag/>
    </D:prop>
    <C:filter>
        <C:comp-filter name="VCALENDAR">
            <C:comp-filter name="VEVENT">
                <C:comp-filter name="VALARM">
                    <C:time-range start="20120325T220000Z" end="20120401T215959Z"/>
                </C:comp-filter>
            </C:comp-filter>
        </C:comp-filter>
    </C:filter>
</C:calendar-query>');

        $response = $this->request($request);

        $body = $response->getBody()->getContents();
        $this->assertFalse(strpos($body, '<s:exception>Exception</s:exception>'), 'Exception occurred: ' . $body);
        $this->assertFalse(strpos($body, 'Unknown or bad format'), 'DateTime unknown format Exception: ' . $body);

        // Everts super awesome xml parser.
        $body = substr(
            $body,
            $start = strpos($body, 'BEGIN:VCALENDAR'),
            strpos($body, 'END:VCALENDAR') - $start + 13
        );
        $body = str_replace('&#13;', '', $body);

        $vObject = VObject\Reader::read($body);

        $this->assertEquals(1, count($vObject->VEVENT));

    }
}
