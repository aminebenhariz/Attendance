<?php

namespace AmineBenHariz\Attendance;

/**
 * Class Attendance
 * @package AmineBenHariz\Attendance
 */
class Attendance
{
    /**
     * @var DayAttendance[]
     */
    private $dayAttendanceList = [];

    /**
     * Attendance constructor.
     * @param DayAttendance[] $dayAttendanceList
     */
    public function __construct(array $dayAttendanceList = [])
    {
        foreach ($dayAttendanceList as $dayAttendance) {
            $this->addDayAttendance($dayAttendance);
        }
    }

    /**
     * @return DayAttendance[]
     */
    public function getDayAttendanceList()
    {
        return $this->dayAttendanceList;
    }

    /**
     * @param DayAttendance $dayAttendance
     */
    private function addDayAttendance(DayAttendance $dayAttendance)
    {
        $date = $dayAttendance->getArrival()->format('Y-m-d');

        if (isset($this->dayAttendanceList[$date])) {
            throw new \InvalidArgumentException;
        }

        $this->dayAttendanceList[$date] = $dayAttendance;
    }

    /**
     * @return int
     */
    public function getTotalMinutes()
    {
        $totalMinutes = 0;

        foreach ($this->getDayAttendanceList() as $dayAttendance) {
            $totalMinutes += $dayAttendance->getTotalMinutes();
        }

        return $totalMinutes;
    }

    /**
     * @return \DateInterval
     */
    public function getAverage()
    {
        if (empty($this->getDayAttendanceList())) {
            return new \DateInterval('PT0S');
        }

        $totalMinutes = $this->getTotalMinutes();

        $averageMinutes = $totalMinutes / count($this->getDayAttendanceList());

        return new \DateInterval('PT' . floor($averageMinutes / 60) . 'H' . $averageMinutes % 60 . 'M');
    }
}
