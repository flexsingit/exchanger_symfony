<?php
/**
 * Created by PhpStorm.
 * User: himanshur
 * Date: 6/6/19
 * Time: 11:40 AM
 */

namespace App\Command;

use App\Entity\Rates;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityManager;
use App\Service\ExchangeRates;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FetchExchangeRatesCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:fetch-exchange-rates';

    public $exchangeRates;
    public $connection;

    public function __construct($name = null, ExchangeRates $exchangeRates, EntityManagerInterface $entityManager)
    {
        $this->exchangeRates = $exchangeRates;
        $this->connection = $entityManager;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Fetch all the exchange rates')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command will delete the existing exchange rates and then will again inserts the new exchange rates to the database.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            '***************************',
            ' Exchange Rates Fetcher ',
            '***************************',
            '',
        ]);
        $rates = $this->exchangeRates->fetchLatestExchangeRates();
        if(!empty($rates) && isset($rates['rates'])) {

            $output->writeln([
                "Base Currency is {$rates['base']}",
                '',
            ]);

            $current_rates = $this->connection->getRepository(Rates::class)->findAll();

            foreach ($current_rates as $rate){
                $this->connection->remove($rate);
            }

            foreach ($rates['rates'] as $currency=>$rate){
                $newRate = new Rates();
                $newRate->setBaseCurrency($rates['base']);
                $newRate->setCurrency($currency);
                $newRate->setRate($rate);
                $newRate->setCreatedAt(new \DateTime('now'));
                $newRate->setUpdatedAt(new \DateTime('now'));
                $this->connection->persist($newRate);
                $this->connection->flush();
                $output->writeln([
                    "Saved rate for {$currency} , rate is = {$rate}",
                    '',
                ]);
            }

        } else {
            $output->writeln([
                'Something went wrong with the exchange api.',
                '',
            ]);
        }

    }

}