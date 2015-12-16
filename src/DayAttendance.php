<?php

namespace AmineBenHariz\Attendance;

/**
 * Class DayAttendance
 * @package AmineBenHariz\Attendance
 */
class DayAttendance
{
    /**
     * example: 2015-12-12|08:30 (10:00-10:30) (12:30-13:30) (16:00-16:30) 17:30
     */
    const DAY_ATTENDANCE_LINE_REGEX = '/^\d{4}-\d{2}-\d{2}\|\d{2}:\d{2}( \(\d{2}:\d{2}-\d{2}:\d{2}\))* \d{2}:\d{2}$/';

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

        if ($this->isPauseOverlapping($pause)) {
            throw new \InvalidArgumentException;
        }


        $this->pauseList[] = $pause;
    }

    /**
     * @param Pause $pause
     * @return bool
     */
    private function isPauseOverlapping(Pause $pause)
    {
        if (empty($this->getPauseList())) {
            return false;
        }

        foreach ($this->getPauseList() as $existingPause) {
            if ($pause->isOverlapping($existingPause)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return \DateInterval
     */
    public function getDuration()
    {
        $cursor = clone $this->getArrival();

        // PHP 5.4 : empty() can only handle variables
        $pauseList = $this->getPauseList();

        if (!empty($pauseList)) {
            foreach ($pauseList as $pause) {
                $cursor->add($pause->getDuration());
            }
        }

        return $cursor->diff($this->getDeparture());
    }

    /**
     * @param $dayAttendanceLine
     * @return DayAttendance
     */
    public static function parseDayAttendanceLine($dayAttendanceLine)
    {
        if (!self::isValidDayAttendaceLine($dayAttendanceLine)) {
            throw new \InvalidArgumentException;
        }

        list($date, $timeLine) = explode('|', $dayAttendanceLine);

        $times = explode(' ', $timeLine);

        $arrival = new \DateTime($date . ' ' . array_shift($times));
        $departure = new \DateTime($date . ' ' . array_pop($times));

        $pauseList = [];
        if (!empty($times)) {
            foreach ($times as $pauseBlock) {
                // Pause Block: '(10:00-10:30)'
                $pauseStart = new \DateTime($date . ' ' . substr($pauseBlock, 1, 5));
                $pauseEnd = new \DateTime($date . ' ' . substr($pauseBlock, 7, 5));
                $pauseList[] = new Pause($pauseStart, $pauseEnd);
            }
        }

        $dayAttendance = new DayAttendance($arrival, $departure, $pauseList);
        return $dayAttendance;
    }

    /**
     * @param $dayAttendanceLine
     * @return int
     */
    public static function isValidDayAttendaceLine($dayAttendanceLine)
    {
        return preg_match(self::DAY_ATTENDANCE_LINE_REGEX, $dayAttendanceLine) === 1;
    }
}
