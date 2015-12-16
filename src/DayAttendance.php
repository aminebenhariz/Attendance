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
    private $pauseList = [];

    /**
     * DayAttendance constructor.
     * @param \DateTime $arrival
     * @param \DateTime $departure
     * @param Pause[] $pauseList
     */
    public function __construct(\DateTime $arrival, \DateTime $departure, array $pauseList = [])
    {
        if ($arrival > $departure) {
            throw new \InvalidArgumentException;
        }

        if ($arrival->format('Y-m-d') !== $departure->format('Y-m-d')) {
            throw new \InvalidArgumentException;
        }

        $this->arrival = $arrival;
        $this->departure = $departure;

        if (!empty($pauseList)) {
            foreach ($pauseList as $pause) {
                $this->addPause($pause);
            }
        }
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

    /**
     * @param Pause $pause
     */
    private function addPause(Pause $pause)
    {
        if ($pause->getStart() < $this->getArrival()) {
            throw new \InvalidArgumentException;
        }

        if ($pause->getEnd() > $this->getDeparture()) {
            throw new \InvalidArgumentException;
        }

        foreach ($this->getPauseList() as $existingPause) {
            if ($pause->isOverlapping($existingPause)) {
                throw new \InvalidArgumentException;
            }
        }

        $this->pauseList[] = $pause;
    }

    /**
     * @return \DateInterval
     */
    public function getDuration()
    {
        $cursor = clone $this->getArrival();

        if (!empty($this->getPauseList())) {
            foreach ($this->getPauseList() as $pause) {
                $cursor->add($pause->getDuration());
            }
        }

        return $cursor->diff($this->getDeparture());
    }
}
