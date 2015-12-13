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

    /**
     * @return Pause
     */
    public function testValidPauseCreation()
    {
        $start = new \DateTime('2015-12-12 09:30');
        $end = new \DateTime('2015-12-12 10:00');

        $pause = new Pause($start, $end);

        $this->assertSame($start, $pause->getStart());
        $this->assertSame($end, $pause->getEnd());

        return $pause;
    }

    /**
     * @dataProvider pauseOverlappingProvider
     * @param Pause $pause1
     * @param Pause $pause2
     * @param $overlapping
     */
    public function testPauseOverlapping(Pause $pause1, Pause $pause2, $overlapping)
    {
        // isOverlapping is symmetric, must work both ways
        $this->assertSame($overlapping, $pause1->isOverlapping($pause2));
        $this->assertSame($overlapping, $pause2->isOverlapping($pause1));
    }

    /**
     * @return array
     */
    public function pauseOverlappingProvider()
    {
        return [
            [
                // no overlapping
                //   ======
                //             =======
                new Pause(new \DateTime('2015-12-12 09:30'), new \DateTime('2015-12-12 10:00')),
                new Pause(new \DateTime('2015-12-12 10:30'), new \DateTime('2015-12-12 11:00')),
                false,
            ],
            [
                // intersection
                //   ======
                //        =======
                new Pause(new \DateTime('2015-12-12 09:30'), new \DateTime('2015-12-12 10:00')),
                new Pause(new \DateTime('2015-12-12 09:45'), new \DateTime('2015-12-12 10:15')),
                true,
            ],
            [
                // contained
                //       ======
                //     ===========
                new Pause(new \DateTime('2015-12-12 09:30'), new \DateTime('2015-12-12 11:00')),
                new Pause(new \DateTime('2015-12-12 10:00'), new \DateTime('2015-12-12 10:30')),
                true,
            ],
            [
                // just touching, this is not considered overlapping
                //    ======
                //          =======
                new Pause(new \DateTime('2015-12-12 09:30'), new \DateTime('2015-12-12 10:00')),
                new Pause(new \DateTime('2015-12-12 10:00'), new \DateTime('2015-12-12 10:30')),
                false,
            ],
        ];
    }
}
