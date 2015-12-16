<?php

namespace AmineBenHariz\Attendance\Tests;

use AmineBenHariz\Attendance\DayAttendance;
use AmineBenHariz\Attendance\Pause;

/**
 * Class DayAttendanceTest
 * @package AmineBenHariz\Attendance\Tests
 */
class DayAttendanceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider invalidDayAttendanceCreationProvider
     * @param \DateTime $arrival
     * @param \DateTime $departure
     * @param Pause[] $pauseList
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDayAttendanceCreation(\DateTime $arrival, \DateTime $departure, array $pauseList)
    {
        new DayAttendance($arrival, $departure, $pauseList);
    }

    /**
     * @return array
     */
    public function invalidDayAttendanceCreationProvider()
    {
        return [
            [
                // arrival and departure not in the same day
                new \DateTime('2015-12-12 08:30'),
                new \DateTime('2015-12-13 17:30'),
                []
            ],
            [
                // arrival after departure
                new \DateTime('2015-12-12 17:30'),
                new \DateTime('2015-12-12 08:30'),
                []
            ],
            [
                // pause starts before arrival
                new \DateTime('2015-12-12 08:30'),
                new \DateTime('2015-12-12 17:30'),
                [
                    new Pause(new \DateTime('2015-12-12 07:00'), new \DateTime('2015-12-12 07:30')),
                    new Pause(new \DateTime('2015-12-12 10:00'), new \DateTime('2015-12-12 10:30')),
                ]
            ],
            [
                // pause ends after departure
                new \DateTime('2015-12-12 08:30'),
                new \DateTime('2015-12-12 17:30'),
                [
                    new Pause(new \DateTime('2015-12-12 10:00'), new \DateTime('2015-12-12 10:30')),
                    new Pause(new \DateTime('2015-12-12 17:15'), new \DateTime('2015-12-12 17:45')),
                ]
            ],
            [
                // overlapping pauses
                new \DateTime('2015-12-12 08:30'),
                new \DateTime('2015-12-12 17:30'),
                [
                    new Pause(new \DateTime('2015-12-12 10:00'), new \DateTime('2015-12-12 10:30')),
                    new Pause(new \DateTime('2015-12-12 10:15'), new \DateTime('2015-12-12 10:45')),
                ]
            ],
        ];
    }

    /**
     * @return DayAttendance
     */
    public function testValidDayAttendanceCreation()
    {
        $arrival = new \DateTime('2015-12-12 08:30');
        $departure = new \DateTime('2015-12-12 17:30');

        $pause1 = new Pause(new \DateTime('2015-12-12 10:00'), new \DateTime('2015-12-12 10:15'));
        $pause2 = new Pause(new \DateTime('2015-12-12 12:30'), new \DateTime('2015-12-12 13:30'));
        $pause3 = new Pause(new \DateTime('2015-12-12 16:00'), new \DateTime('2015-12-12 16:15'));
        $pauseList = [$pause1, $pause2, $pause3];

        $dayAttendance = new DayAttendance($arrival, $departure, $pauseList);

        $this->assertSame($arrival, $dayAttendance->getArrival());
        $this->assertSame($departure, $dayAttendance->getDeparture());
        $this->assertSame($pauseList, $dayAttendance->getPauseList());

        return $dayAttendance;
    }

    /**
     * @depends testValidDayAttendanceCreation
     * @param DayAttendance $dayAttendance
     */
    public function testGetDuration(DayAttendance $dayAttendance)
    {
        $this->assertSame('07:30:00', $dayAttendance->getDuration()->format('%H:%I:%S'));
    }

    /**
     * @dataProvider isValidDayAttendanceLineProvider
     * @param string $dayAttendanceLine
     * @param bool $valid
     */
    public function testIsValidDayAttendanceLine($dayAttendanceLine, $valid)
    {
        $this->assertSame($valid, DayAttendance::isValidDayAttendaceLine($dayAttendanceLine));
    }

    /**
     * @return array
     */
    public function isValidDayAttendanceLineProvider()
    {
        return [
            ['', false], // empty
            ['lorem', false], // random
            ['08:30 (10:00-10:30) (16:00-16:30) 17:30', false], // no date
            ['2015-12-12|', false], // no timeLine
            ['2015-12-12|(10:00-10:30) (16:00-16:30) 17:30', false], // no arrival
            ['2015-12-12|08:30 (10:00-10:30) (16:00-16:30)', false], // no departure
            ['2015-12-1x|08:30 (10:00-10:30) (16:00-16:30) 17:30', false], // invalid date

            ['2015-12-12|08:30 (10:00-10:30) (16:00-16:30) 17:30', true], // correct
            ['2015-12-12|08:30 17:30', true], // correct, no pauses
        ];
    }

    public function testParseDayAttendanceLine()
    {
        $dayAttendanceLine = '2015-12-12|08:30 (10:00-10:30) (16:00-16:30) 17:30';
        $dayAttendance = DayAttendance::parseDayAttendanceLine($dayAttendanceLine);

        $this->assertInstanceOf('\AmineBenHariz\Attendance\DayAttendance', $dayAttendance);

        $this->assertSame('2015-12-12 08:30', $dayAttendance->getArrival()->format('Y-m-d H:i'));
        $this->assertSame('2015-12-12 17:30', $dayAttendance->getDeparture()->format('Y-m-d H:i'));

        $pauseList = $dayAttendance->getPauseList();
        $this->assertCount(2, $pauseList);

        $this->assertSame('2015-12-12 10:00', $pauseList[0]->getStart()->format('Y-m-d H:i'));
        $this->assertSame('2015-12-12 10:30', $pauseList[0]->getEnd()->format('Y-m-d H:i'));

        $this->assertSame('2015-12-12 16:00', $pauseList[1]->getStart()->format('Y-m-d H:i'));
        $this->assertSame('2015-12-12 16:30', $pauseList[1]->getEnd()->format('Y-m-d H:i'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidParseDayAttendanceLine()
    {
        $dayAttendanceLine = 'lorem';
        DayAttendance::parseDayAttendanceLine($dayAttendanceLine);
    }
}
