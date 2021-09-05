[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/kovenant/sms-weather-notification-test-task/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/kovenant/sms-weather-notification-test-task/?branch=master) [![codecov](https://codecov.io/gh/kovenant/sms-weather-notification-test-task/branch/master/graph/badge.svg?token=BU5G1LFAUY)](https://codecov.io/gh/kovenant/sms-weather-notification-test-task)

# Project run and installation

Just make executable

``
chmod +x run.sh
``

execute run.sh

``
./run.sh
``

and input:

1 - for project run

2 - for run tests (unit and code style)

3 - for view code coverage

then press enter

## Application settings

Please check `src/DI/variables.php` config file

`src/DI/variables.test.php` is used for unit tests overriding

## Extending the application

You can inject alternative services in `src/DI/services.php`

For example, we can create another sending service or use another weather api

PHP DI is used https://php-di.org/

`src/DI/services.test.php` config file is used for unit tests overriding

## New sending rules

You can already note that in `variables.php` there is an `sendingRules` option

Here you can pass array of rules. Each rule must implements `WeatherRuleInterface` and must contains check if this rule
is appropriate for current weather and build a message (combination of text and recipient).

We can extend our `Weather` DTO and parse another data from weather API and implement new sending conditions, for
example about humidity

### Test coverage

/src/ - 100% files, 100% lines
