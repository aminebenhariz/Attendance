<?php

namespace AmineBenHariz\Attendance;

/**
 * Class DayAttendance
 * @package AmineBenHariz\Attendance
 */
class DayAttendance
{
    /**
     * @var \DateTime
     */
    private $arrival;

    /**
     * @var \DateTime
     */
    private $departure;

    /**
     * @var Pause[]
     */
    private $pauseList;

    /**
     * DayAttendance constructor.
     * @param \DateTime $arrival
     * @param \DateTime $departure
     * @param Pause[] $pauseList
     */
    public function __construct(\DateTime $arrival, \DateTime $departure, array $pauseList)
    {
        if ($arrival > $departure) {
            throw new \InvalidArgumentException;
        }

        if ($arrival->format('Y-m-d') !== $departure->format('Y-m-d')) {
            throw new \InvalidArgumentException;
        }

        $this->arrival = $arrival;
        $this->departure = $departure;
        $this->pauseList = $pauseList;
    }

    /**
     * @return \DateTime
     */
    public function getArrival()
    {
        return $this->arrival;
    }

    /**
     * @return \DateTime
     */
    public function getDeparture()
    {
        return $this->departure;
    }

    /**
     * @return Pause[]
     */
    public function getPauseList()
    {
        return $this->pauseList;
    }
}
