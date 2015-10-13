<?php

namespace Acme\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class PayrollCommand extends Command
{
    /**
     * Header values for payroll csv
     * @var array $csv_headers
     */
    protected $csv_headers = array(
            'Month,',
            'Salary Payment Date,',
            'Bonus Payment Date'
        );

    /**
     * Payroll csv
     * @var $payroll_csv handler
     */
    protected $payroll_csv;

    /**
     * Output object
     * @var Output object used to write to user
     */
    public $payroll_output;

    /**
     * Payroll bonus date
     */
    const BONUS_DATE = '15';

    /**
     * Payroll month range
     */
    const PAYROLL_MONTH_RANGE = '12';

    /**
     * Set up payroll configuration
     */
    protected function configure()
    {
        $this->setName('start:payroll')
            ->setDescription('Create Payroll CSV');
    }

    /**
     * Execute payroll application
     * 
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //Set our command line payroll output class variable
        $this->payroll_output = $output;

        //Run payroll application
        $this->processPayroll($output);
    }

    /**
     * Process payroll executes methods needed
     * to build the payroll csv
     */
    protected function processPayroll()
    {
        //Create payroll csv with headers
        if ($this->createCsv()) {

            //Get 12 month range
            $total_months = $this->getMonthRange();

            //Check that there are months in the date range
            if ($total_months) {

                //Feedback to the user
                $this->payroll_output->writeln('<info>Writing to csv...</info>');

                //Create a progress bar to show progression for each month
                $progress = new ProgressBar($this->payroll_output, self::PAYROLL_MONTH_RANGE);

                //Start our payroll progress
                $progress->start();

                //Iterate over each month's within range
                foreach($total_months as $current_month) {

                    //Get the bonus date
                    $bonus_payment_date = $this->processBonus($current_month);

                    //Get the basic salary date
                    $basic_salary_payment_date = $this->getBasicSalaryDate($current_month);

                    //Build up data array
                    $data_arr = array(
                        $current_month->format('F'),                
                        $basic_salary_payment_date->format('Y-m-d'),
                        $bonus_payment_date->format('Y-m-d')
                    );
                    
                    //Write it to the payrole csv
                    if(fputcsv($this->payroll_csv, $data_arr)) {

                        //Increase our progress bar
                        $progress->advance();

                        //Feedback to user
                        $this->payroll_output->writeln(' Processing '.$current_month->format('F'));    
                    }
                }

                //Close our csv
                if(fclose($this->payroll_csv)) {

                    //Complete our progress bar
                    //and feedback to user
                    $progress->finish();

                    $this->payroll_output->writeln(' <info>Process payroll complete.</info>');

                }
            }
        }
    }

    /**
     * Get a month range of 12 months
     * 
     * @return DatePeriod Object
     */
    public function getMonthRange()
    {
        //Get the current date
        $start_date = new \DateTime();

        //Set a 12 month increase
        $end_date = new \DateTime();

        $end_date = $end_date->modify('+'.self::PAYROLL_MONTH_RANGE.' month');

        //Set an interval of month for iteration
        $month_interval = new \DateInterval('P1M');

        //Our 12 month range from todays date
        $total_months = new \DatePeriod($start_date, $month_interval ,$end_date);

        return $total_months;
    }

    /**
     * Creates payroll csv
     */
    protected function createCsv()
    {
        //Timestamp our payroll csv
        $payroll_csv_timestamp = new \DateTime;

        $csv_file_path = 'files/payroll_files/payroll_'.$payroll_csv_timestamp->format('Y-m-d').'.csv';

        //Create payroll csv
        if ($payroll_csv = fopen($csv_file_path, 'w')) {

            //Write payroll csv headers
            foreach ($this->csv_headers as $header) {
                fwrite($payroll_csv, $header);
            }

            //Return to a new line after writing payroll headers
            fwrite($payroll_csv, "\n");

            //Assign Payroll csv to class variable
            $this->payroll_csv = $payroll_csv;

            //Feedback to the user
            $this->payroll_output->writeln('<info>CSV created...</info>');

            return true;

        } else {

            $this->payroll_output->writeln('<error>Issue creating CSV, please try again.</error>');

            return false;

        }
    }

    /**
     * Process our bonus dates
     *
     * @param  DateTime $date
     * @return DateTime Bonus Payment Date
     */
    public function processBonus(\DateTime $date)
    {
        //Get first and last day of the month for iteration
        $first_day = $date->modify('first day of this month');

        $last_day = new \DateTime($date->format('Y-m-d'));
        $last_day->modify('last day of this month');

        $day_interval = new \DateInterval('P1D');

        $total_days = new \DatePeriod($first_day, $day_interval ,$last_day);

        //Iterate over month days
        foreach ($total_days as $day) {

            //Check if 15th is on a weekend
            if ($day->format('d') == self::BONUS_DATE) {

                //Get bonus date
                return $this->getBonusDate($day);
            }
        }
    }

    /**
     * Determines our bonus date
     * If the bonus date (15th) falls on a weekend,
     * increment the date to the following wednesday
     * 
     * @param  DateTime $date
     * @return DateTime $date
     */
    public function getBonusDate(\DateTime $date)
    {
        //Check if the current day is a weekend
        if (in_array($date->format('N'), array(6, 7))) {

            //Return new date
            return $date->modify('next wednesday');

        } else {

            //Return original date
            return $date;

        }
    }

    /**
     * Determines if the last day of the month
     * is a weekend. If it is, the payment date will be
     * the Friday before the weekend.
     * 
     * @param  DateTime $date
     * @return DateTime $date
     */
    public function getBasicSalaryDate(\DateTime $date)
    {

        switch ($date->modify('last day of this month')->format('N')) {
            case 6:
                //If date is a Saturday, decrement by 1 day
                return $date->sub(new \DateInterval('P1D'));
                break;
            case 7:
                //If the date is a Sunday, decrement by 2 days
                return $date->sub(new \DateInterval('P2D'));
                break;
            default:
                //If the date is a week day, return as normal
                return $date;
                break;
        }

    }
}