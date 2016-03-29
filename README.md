# Attendance

[![Build Status](https://img.shields.io/travis/aminebenhariz/Attendance/master.svg?style=flat)](https://travis-ci.org/aminebenhariz/Attendance)
[![Code Coverage](https://scrutinizer-ci.com/g/aminebenhariz/Attendance/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/aminebenhariz/Attendance/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aminebenhariz/Attendance/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aminebenhariz/Attendance/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/432625e3-2487-4f67-91fa-25d6fd54db67/big.png)](https://insight.sensiolabs.com/projects/432625e3-2487-4f67-91fa-25d6fd54db67)

## Table of Contents

+ [Install](#install)
+ [Usage](#usage)
+ [DayAttendanceLine Format](#dayattendanceline-format)
+ [Requirements](#requirements)
+ [Testing](#testing)

## Install

Via Composer

``` bash
$ composer require aminebenhariz/attendance
```

## DayAttendanceLine Format

```
2015-12-12|08:30 (10:00-10:30) (16:00-16:30) 17:30

 - 2015-12-12    : date (yyyy-mm-dd)
 - 08:30         : arrival time (hh:mm)
 - (10:00-10:30) : pause from 10:00 to 10:30
 - (16:00-16:30) : pause from 16:00 to 16:30
 - 17:30         : departure time (hh:mm)
```

Description can be added like this

```
2015-12-12|08:30 (10:00-10:30) (16:00-16:30) 17:30|Some Description
```

## Usage

### Calculate attendance

``` php
$dayAttendanceLine = '2015-12-12|08:30 (10:00-10:30) (16:00-16:30) 17:30';
$dayAttendance = DayAttendance::parseDayAttendanceLine($dayAttendanceLine);

echo $dayAttendance->getDuration()->format('%H:%I:%S');
// 07:30:00

echo $dayAttendance->exportLine();
// 2015-12-12|08:30 (10:00-10:15) (12:30-13:30) (16:00-16:15) 17:30
```

### Calculate average attendance of multiple days

``` php
$day1 = DayAttendance::parseDayAttendanceLine('2015-12-14|08:31 (12:02-13:42) 17:25');
$day2 = DayAttendance::parseDayAttendanceLine('2015-12-15|08:29 (12:21-13:32) (16:12-16:22) 17:24');
$day3 = DayAttendance::parseDayAttendanceLine('2015-12-16|08:52 (12:18-13:12) 17:31');
$day4 = DayAttendance::parseDayAttendanceLine('2015-12-17|08:12 (12:21-13:52) 17:24');
$day5 = DayAttendance::parseDayAttendanceLine('2015-12-18|08:35 (12:24-13:25) 17:42');

$attendance = new Attendance([$day1, $day2, $day3, $day4, $day5]);

echo $attendance->getAverage()->format('%H:%I');
// 07:40
```

## Requirements

The following versions of PHP are supported by this version.

+ PHP 5.4
+ PHP 5.5
+ PHP 5.6
+ PHP 7.0
+ HHVM

## Testing

``` bash
$ composer test
```
