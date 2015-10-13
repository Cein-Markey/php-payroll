<?php

namespace AppBundle\Tests\Util;

use Acme\Console\Command\PayrollCommand;

class PayrollCommandTest extends \PHPUnit_Framework_TestCase
{

    public function testGetMonthRangeReturnsDatePeriodObject()
    {
    	$payroll = new PayrollCommand();
        $this->assertInstanceOf('DatePeriod', $payroll->getMonthRange());
    }

    public function testGetBasicSalaryDateReturnsDateTimeObject()
    {
    	$payroll = new PayrollCommand();
        $this->assertInstanceOf('DateTime', $payroll->getBasicSalaryDate(new \DateTime));
    }

    public function testGetBonusDateReturnsDateTimeObject()
    {
    	$payroll = new PayrollCommand();
        $this->assertInstanceOf('DateTime', $payroll->getBonusDate(new \DateTime));
    }

    public function testProcessBonusReturnsDateTimeObject()
    {
    	$payroll = new PayrollCommand();
        $this->assertInstanceOf('DateTime', $payroll->processBonus(new \DateTime));
    }

}