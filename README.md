# Attendance

[![Build Status](https://img.shields.io/travis/aminebenhariz/Attendance/master.svg?style=flat)](https://travis-ci.org/aminebenhariz/Attendance)
[![Code Coverage](https://scrutinizer-ci.com/g/aminebenhariz/Attendance/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/aminebenhariz/Attendance/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/aminebenhariz/Attendance/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/aminebenhariz/Attendance/?branch=master)

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

## Usage

``` php
$dayAttendanceLine = '2015-12-12|08:30 (10:00-10:30) (16:00-16:30) 17:30';
$dayAttendance = DayAttendance::parseDayAttendanceLine($dayAttendanceLine);

echo $dayAttendance->getDuration()->format('%H:%I:%S');
// 07:30:00

echo $dayAttendance->exportLine();
// 2015-12-12|08:30 (10:00-10:15) (12:30-13:30) (16:00-16:15) 17:30
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