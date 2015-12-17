<?php

namespace AmineBenHariz\Attendance\Tests;

use AmineBenHariz\Attendance\Attendance;
use AmineBenHariz\Attendance\DayAttendance;

/**
 * Class AttendanceTest
 * @package AmineBenHariz\Attendance\Tests
 */
class AttendanceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidAttendanceCreation()
    {
        // 2 lines with same date
        $dayAttendance1 = DayAttendance::parseDayAttendanceLine('2015-12-14|08:30 (12:30-13:30) 17:30');
        $dayAttendance2 = DayAttendance::parseDayAttendanceLine('2015-12-15|08:30 (12:30-13:30) 17:30');
        $dayAttendance3 = DayAttendance::parseDayAttendanceLine('2015-12-16|08:30 (12:30-13:30) 17:30');
        $dayAttendance4 = DayAttendance::parseDayAttendanceLine('2015-12-17|08:30 (12:30-13:30) 17:30');
        $dayAttendance5 = DayAttendance::parseDayAttendanceLine('2015-12-17|08:30 (12:30-13:30) 17:30');

        $dayAttendanceList = [
            $dayAttendance1,
            $dayAttendance2,
            $dayAttendance3,
            $dayAttendance4,
            $dayAttendance5,
        ];

        new Attendance($dayAttendanceList);
    }

    /**
     * @return Attendance
     */
    public function testValidAttendanceCreation()
    {
        $dayAttendance1 = DayAttendance::parseDayAttendanceLine('2015-12-14|08:31 (12:02-13:42) 17:25');
        $dayAttendance2 = DayAttendance::parseDayAttendanceLine('2015-12-15|08:29 (12:21-13:32) (16:12-16:22) 17:24');
        $dayAttendance3 = DayAttendance::parseDayAttendanceLine('2015-12-16|08:52 (12:18-13:12) 17:31');
        $dayAttendance4 = DayAttendance::parseDayAttendanceLine('2015-12-17|08:12 (12:21-13:52) 17:24');
        $dayAttendance5 = DayAttendance::parseDayAttendanceLine('2015-12-18|08:35 (12:24-13:25) 17:42');

        $dayAttendanceList = [
            $dayAttendance1,
            $dayAttendance2,
            $dayAttendance3,
            $dayAttendance4,
            $dayAttendance5,
        ];

        $attendance = new Attendance($dayAttendanceList);

        $this->assertInstanceOf('AmineBenHariz\Attendance\Attendance', $attendance);
        $this->assertCount(5, $attendance->getDayAttendanceList());

        return $attendance;
    }

    /**
     * @depends testValidAttendanceCreation
     * @param Attendance $attendance
     */
    public function testGetTotalMinutes(Attendance $attendance)
    {
        $this->assertSame(2300, $attendance->getTotalMinutes());
    }

    /**
     * @depends testValidAttendanceCreation
     * @depends testGetTotalMinutes
     * @param Attendance $attendance
     */
    public function testGetAverage(Attendance $attendance)
    {
        $this->assertSame('07:40', $attendance->getAverage()->format('%H:%I'));

        // test empty attendance
        $emptyAttendance = new Attendance();
        $this->assertSame('00:00', $emptyAttendance->getAverage()->format('%H:%I'));
    }
}
