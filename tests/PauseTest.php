<?php

namespace AmineBenHariz\Attendance\Tests;

use AmineBenHariz\Attendance\Pause;

/**
 * Class PauseTest
 * @package AmineBenHariz\Attendance\Tests
 */
class PauseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider invalidPauseCreationProvider
     * @param \DateTime $start
     * @param \DateTime $end
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidPauseCreation(\DateTime $start, \DateTime $end)
    {
        new Pause($start, $end);
    }

    /**
     * @return array
     */
    public function invalidPauseCreationProvider()
    {
        return [
            // Pause ends before even starting! (may be possible in quantum mechanics)
            [new \DateTime('2015-12-12 10:00'), new \DateTime('2015-12-12 09:30')],

            // Pause ends the next day. Are you that lazy?
            [new \DateTime('2015-12-12 10:00'), new \DateTime('2015-12-13 10:30')],
        ];
    }
}
