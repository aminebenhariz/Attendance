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
    const DAY_ATTENDANCE_LINE_REGEX =
        '/^\d{4}-\d{2}-\d{2}\|\d{2}:\d{2}( \(\d{2}:\d{2}-\d{2}:\d{2}\))* \d{2}:\d{2}(\|[^\|]*)?$/';

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
     * @var string
     */
    private $description = "";

    /**
     * DayAttendance constructor.
     * @param \DateTime $arrival
     * @param \DateTime $departure
     * @param Pause[] $pauseList
     */
    public function __construct(\DateTime $arrival, \DateTime $departure, array $pauseList = [])
    {
        if ($arrival > $departure || $arrival->format('Y-m-d') !== $departure->format('Y-m-d')) {
            throw new \InvalidArgumentException;
        }

        $this->arrival = $arrival;
        $this->departure = $departure;

        foreach ($pauseList as $pause) {
            $this->addPause($pause);
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
        $existingPauseList = $this->getPauseList();
        if (empty($existingPauseList)) {
            return false;
        }

        foreach ($existingPauseList as $existingPause) {
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
     * @return int
     */
    public function getTotalMinutes()
    {
        $duration = $this->getDuration();
        return intval($duration->format('%H')) * 60 + intval($duration->format('%I'));
    }

    /**
     * @param $dayAttendanceLine
     * @return DayAttendance
     */
    public static function parseDayAttendanceLine($dayAttendanceLine)
    {
        if (!self::isValidDayAttendanceLine($dayAttendanceLine)) {
            throw new \InvalidArgumentException;
        }

        list($date, $timeLine, $description) = self::explodeDayAttendanceLineParts($dayAttendanceLine);

        $times = explode(' ', $timeLine);

        $arrival = new \DateTime($date . ' ' . array_shift($times));
        $departure = new \DateTime($date . ' ' . array_pop($times));

        $pauseList = self::parsePauseBlocks($date, $times);

        $dayAttendance = new DayAttendance($arrival, $departure, $pauseList);
        $dayAttendance->setDescription($description);
        return $dayAttendance;
    }

    /**
     * @param string $dayAttendanceLine
     * @return array
     */
    private static function explodeDayAttendanceLineParts($dayAttendanceLine)
    {
        $parts = explode('|', $dayAttendanceLine);

        // if empty description
        if (!isset($parts[2])) {
            $parts[2] = '';
        }

        return $parts;
    }

    /**
     * @param string $date date in Y-m-d format, ex: 2016-03-29
     * @param array $pauseBlocks
     * @return Pause[]
     */
    private static function parsePauseBlocks($date, $pauseBlocks)
    {
        if (empty($pauseBlocks)) {
            return [];
        }

        $pauseList = [];
        foreach ($pauseBlocks as $pauseBlock) {
            $pauseList[] = self::parsePauseBlock($date, $pauseBlock);
        }

        return $pauseList;
    }

    /**
     * @param string $date date in Y-m-d format, ex: 2016-03-29
     * @param string $pauseBlock ex: (10:00-10:30)
     * @return Pause
     */
    private static function parsePauseBlock($date, $pauseBlock)
    {
        $pauseStart = new \DateTime($date . ' ' . substr($pauseBlock, 1, 5));
        $pauseEnd = new \DateTime($date . ' ' . substr($pauseBlock, 7, 5));
        return new Pause($pauseStart, $pauseEnd);
    }

    /**
     * @param $dayAttendanceLine
     * @return int
     */
    public static function isValidDayAttendanceLine($dayAttendanceLine)
    {
        return preg_match(self::DAY_ATTENDANCE_LINE_REGEX, $dayAttendanceLine) === 1;
    }

    /**
     * @return string
     */
    public function exportLine()
    {
        $line = $this->getDate() . '|' . $this->getTimeLine();

        if ($this->getDescription() !== '') {
            $line .= '|' . $this->getDescription();
        }

        return $line;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->getArrival()->format('Y-m-d');
    }

    /**
     * @return string
     */
    public function getTimeLine()
    {
        $line = $this->getArrival()->format('H:i');

        foreach ($this->getPauseList() as $pause) {
            $line .= ' ' . $pause->exportBlock();
        }

        $line .= ' ' . $this->getDeparture()->format('H:i');

        return $line;
    }
}
