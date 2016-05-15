[![Build Status](https://travis-ci.org/Cein-Markey/php-payroll.svg?branch=master)](https://travis-ci.org/Cein-Markey/php-payroll)
[![Coverage Status](https://coveralls.io/repos/Cein-Markey/php-payroll/badge.svg?branch=master&service=github)](https://coveralls.io/github/Cein-Markey/php-payroll?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/5625661a36d0ab0021000d61/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5625661a36d0ab0021000d61)
[![Latest Stable Version](https://poser.pugx.org/ceinmarkey/process_payroll/v/stable)](https://packagist.org/packages/ceinmarkey/process_payroll)

# php-payroll 

A simple command line tool using the Symfony Console Component

## Run Build

    $ cd /path/to/process_payroll
    $ composer install
    
## Run Tests

    $ phpunit -c app
    
## Run Application

    $ php application.php start:payroll
    
## View Results
    
    $ cd /path/to/process_payroll
    $ cat ./files/payroll_files/payroll_(TIMESTAMP).csv

##Example

<img src="https://github.com/Cein-Markey/php-payroll/blob/master/public/Screen%20Shot%202015-10-14%20at%2009.32.10.png"/>
