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
}
