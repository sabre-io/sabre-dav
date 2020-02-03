<?php

declare(strict_types=1);

namespace Sabre\CalDAV;

use Sabre\DAV;
use Sabre\DAV\MkCol;

class CalendarHomeTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Sabre\CalDAV\CalendarHome
     */
    protected $usercalendars;

    /**
     * @var Backend\BackendInterface
     */
    protected $backend;

    public function setup()
    {
        $this->backend = TestUtil::getBackend();
        $this->usercalendars = new CalendarHome($this->backend, [
            'uri' => 'principals/user1',
        ]);
    }

    public function testSimple()
    {
        $this->assertEquals('user1', $this->usercalendars->getName());
    }

    /**
     * @depends testSimple
     */
    public function testGetChildNotFound()
    {
        $this->expectException('Sabre\DAV\Exception\NotFound');
        $this->usercalendars->getChild('randomname');
    }

    public function testChildExists()
    {
        $this->assertFalse($this->usercalendars->childExists('foo'));
        $this->assertTrue($this->usercalendars->childExists('UUID-123467'));
    }

    public function testGetOwner()
    {
        $this->assertEquals('principals/user1', $this->usercalendars->getOwner());
    }

    public function testGetGroup()
    {
        $this->assertNull($this->usercalendars->getGroup());
    }

    public function testGetACL()
    {
        $expected = [
            [
                'privilege' => '{DAV:}read',
                'principal' => 'principals/user1',
                'protected' => true,
            ],
            [
                'privilege' => '{DAV:}write',
                'principal' => 'principals/user1',
                'protected' => true,
            ],
            [
                'privilege' => '{DAV:}read',
                'principal' => 'principals/user1/calendar-proxy-write',
                'protected' => true,
            ],
            [
                'privilege' => '{DAV:}write',
                'principal' => 'principals/user1/calendar-proxy-write',
                'protected' => true,
            ],
            [
                'privilege' => '{DAV:}read',
                'principal' => 'principals/user1/calendar-proxy-read',
                'protected' => true,
            ],
        ];
        $this->assertEquals($expected, $this->usercalendars->getACL());
    }

    public function testSetACL()
    {
        $this->expectException('Sabre\DAV\Exception\Forbidden');
        $this->usercalendars->setACL([]);
    }

    /**
     * @depends testSimple
     */
    public function testSetName()
    {
        $this->expectException('Sabre\DAV\Exception\Forbidden');
        $this->usercalendars->setName('bla');
    }

    /**
     * @depends testSimple
     */
    public function testDelete()
    {
        $this->expectException('Sabre\DAV\Exception\Forbidden');
        $this->usercalendars->delete();
    }

    /**
     * @depends testSimple
     */
    public function testGetLastModified()
    {
        $this->assertNull($this->usercalendars->getLastModified());
    }

    /**
     * @depends testSimple
     */
    public function testCreateFile()
    {
        $this->expectException('Sabre\DAV\Exception\MethodNotAllowed');
        $this->usercalendars->createFile('bla');
    }

    /**
     * @depends testSimple
     */
    public function testCreateDirectory()
    {
        $this->expectException('Sabre\DAV\Exception\MethodNotAllowed');
        $this->usercalendars->createDirectory('bla');
    }

    /**
     * @depends testSimple
     */
    public function testCreateExtendedCollection()
    {
        $mkCol = new MkCol(
            ['{DAV:}collection', '{urn:ietf:params:xml:ns:caldav}calendar'],
            []
        );
        $result = $this->usercalendars->createExtendedCollection('newcalendar', $mkCol);
        $this->assertNull($result);
        $cals = $this->backend->getCalendarsForUser('principals/user1');
        $this->assertEquals(3, count($cals));
    }

    /**
     * @depends testSimple
     */
    public function testCreateExtendedCollectionBadResourceType()
    {
        $this->expectException('Sabre\DAV\Exception\InvalidResourceType');
        $mkCol = new MkCol(
            ['{DAV:}collection', '{DAV:}blabla'],
            []
        );
        $this->usercalendars->createExtendedCollection('newcalendar', $mkCol);
    }

    /**
     * @depends testSimple
     */
    public function testCreateExtendedCollectionNotACalendar()
    {
        $this->expectException('Sabre\DAV\Exception\InvalidResourceType');
        $mkCol = new MkCol(
            ['{DAV:}collection'],
            []
        );
        $this->usercalendars->createExtendedCollection('newcalendar', $mkCol);
    }

    public function testGetSupportedPrivilegesSet()
    {
        $this->assertNull($this->usercalendars->getSupportedPrivilegeSet());
    }

    public function testShareReplyFail()
    {
        $this->expectException('Sabre\DAV\Exception\NotImplemented');
        $this->usercalendars->shareReply('uri', DAV\Sharing\Plugin::INVITE_DECLINED, 'curi', '1');
    }
}
