<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Document\NeoDocument;

/**
 * Class NeoCommand
 *
 * @package AppBundle\Command
 */
class NeoCommand extends  ContainerAwareCommand
{

    /**
     * configure
     */
    protected function configure()
    {
        $this
	        ->setName('app:get_neo')
            ->setDescription('Get NEOs for 3 days.')
            ->setHelp('This command allows to get the count of Near Earth Objects for 3 days from NASA api and store it into mongoDB database.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln([ 'Retrieving NEO' ]);

        // Init curl
        $ch = curl_init();
        // Checks curl init
        if($ch == FALSE)
        {
            return;
        }
        curl_setopt($ch, CURLOPT_URL, $this->getFullUrl());
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        $response = curl_exec($ch);

        if($errno = curl_errno($ch)) {
            $error_message = curl_strerror($errno);
            $output->writeln([
                "cURL error ({$errno}):\n {$error_message}"
            ]);
            return;
        }
        // Frees curl
        curl_close($ch);
        
        // Decodes response
        $data = json_decode($response);

        // Checks data
        if(empty($data))
        {
            $output->writeln([
                'getting blank response without any data',
            ]);
            return;
        }

        // Checks json keys
        if( array_key_exists( "element_count", $data) === false
            ||array_key_exists( "near_earth_objects", $data) === false
          ) {
            $output->writeln([
                'The API response is not in proper format with include this key "element_count" and  "near_earth_objects"',
            ]);

            return;
        }
 
        $dm = $this->getContainer()->get('doctrine_mongodb')->getManager();
        $output->writeln([ 'Remove all current data if exist' ]);
        $dm->getRepository('AppBundle:NeoDocument')->clearAllDocument();

        $output->writeln([ 'Start to insert records from api to DB' ]);
        $nbr = 1;
        $countRecord = 0;
        // array of dates
        foreach($data->{'near_earth_objects'} as $key => $neos)
        {
            foreach($neos as $neo)
            {
				$neoDoc = new NeoDocument();
				$neoDoc->setReference($neo->{'neo_reference_id'})
				    ->setName($neo->{'name'})
				    ->setSpeed($neo->{'close_approach_data'}[0]->{'relative_velocity'}->{'kilometers_per_hour'})
				    ->setIsHazardous($neo->{'is_potentially_hazardous_asteroid'})
				    ->setDate($key);

				
				$dm->persist($neoDoc);

				if (($nbr == 50) || ((int)$data->{'element_count'} == (int)$nbr)) {
					$dm->flush();
                    $dm->clear();
					$nbr = 0;
				}
				$nbr++;
                $countRecord++;
            }
        }
        $output->writeln(["Number of NEO added: " . $countRecord ]);
        $output->writeln([ 'Finished to insert records' ]);
    }

    /**
     * @return string
     */
    private function getFullUrl()
    {
        // Sets url and a duration of 3 days
        return $this->getContainer()->getParameter("nasa_api_url") . "?api_key=" . $this->getContainer()->getParameter("nasa_api_key") .
            "&start_date=" . date( "Y-m-d", time() - (3600 * 24 ) ) .
            "&end_date=" . date( "Y-m-d", time() - (3600 * 24 * 3) ) .
            "&detailed=false";
    }
}