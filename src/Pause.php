<?php

namespace AmineBenHariz\Attendance;

/**
 * Class Pause
 * @package AmineBenHariz\Attendance
 */
class Pause
{
    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $end;

    /**
     * Pause constructor.
     * @param \DateTime $start
     * @param \DateTime $end
     */
    public function __construct(\DateTime $start, \DateTime $end)
    {
        if ($start > $end) {
            throw new \InvalidArgumentException("Pause can't start after ending");
        }

        if ($start->format('Y-m-d') !== $end->format('Y-m-d')) {
            throw new \InvalidArgumentException("Pause must end in the same day");
        }

        $this->start = $start;
        $this->end = $end;
    }

    /**
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @return \DateInterval
     * @throws \Exception
     */
    public function getDuration()
    {
        return $this->getStart()->diff($this->getEnd());
    }

    /**
     * @param Pause $pause
     * @return bool
     */
    public function isOverlapping(Pause $pause)
    {
        if ($this->getEnd() <= $pause->getStart()) {
            return false;
        }

        if ($this->getStart() >= $pause->getEnd()) {
            return false;
        }

        return true;
    }
}
