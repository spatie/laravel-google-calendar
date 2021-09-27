# Changelog

All notable changes to `laravel-google-calendar` will be documented in this file

## 3.4.0 - 2021-09-27

- Domain-wide delegation support (#230)

## 3.3.1 - 2021-04-13

- allow optional parameters on delete (#200)

## 3.3.0 - 2021-04-13

- allow user to pass in optional parameters to `updateEvent` (#199)

## 3.2.1 - 2020-12-07

- add support PHP 8

## 3.2.0 - 2020-11-19

- add support for adding attendees to events (#178)

## 3.1.0 - 2020-09-08

- add support for Laravel 8

## 3.0.0 - 2020-08-24

- add support for OAuth2 authentication, source property on events (#163)

## 2.6.2 - 2020-07-19

- allow `CarbonImmutable` date (#160)

## 2.6.1 - 2020-04-17

- revert changes of previous release

## 2.6.0 - 2020-04-17

- make factory more flexible

## 2.5.3 - 2020-04-17

- make factory more flexible

## 2.5.2 - 2020-04-14

- add quick save (#147)

## 2.5.1 - 2020-04-01

- allow usage of Carbon immutable (#141)

## 2.5.0 - 2020-03-03

- add support for Laravel 7

## 2.4.0 - 2020-02-20

- allow passing array of credentials (#139)

## 2.3.2 - 2019-12-16
- Fixed fetching more than 250 results of calendar events (#133)

## 2.3.1 - 2019-12-15
- Add getter for calendar ID per event (#131)

## 2.3.0 - 2019-09-04
- Laravel 6 compatibility; dropped support for older versions

## 2.2.2 - 2019-02-27
- allow carbon v2

## 2.2.1 - 2018-09-27
- `listEvents` now returns events sorted chronologically

## 2.2.0 - 2018-01-10
- add ability to add query params

## 2.1.1 - 2017-10-16
- improve sorting

## 2.1.0 - 2017-10-15
- add `update` method

### 2.0.0 - 2017-07-20
- use Google API v2
- rename config file

### 1.1.0 - 2017-04-26
- Added: `addAttendee` method to `Event`

### 1.0.3 - 2016-11-26
- Fixed: Bug regarding creation of events with custom ids

### 1.0.2 - 2016-08-06
- Fixed: The timezone of a passed Carbon object will be used when creating events

### 1.0.1 - 2016-07-29
- Fixed: Creating an event on an alternative calendar wont fail anymore

### 1.0.0 - 2016-05-24
- Initial release
