<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;

/**
 * Class DefaultController
 *
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function indexAction(Request $request)
    {
        return $this->json(array('hello' => 'world!'));
    }

    /**
     * @Route("/neo/hazardous", name="neo_hazardous")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function hazardousAction(Request $request)
    {
        // Retrieves NEO
        $neos = $this->getNeoRepository()->findNeo();

        return $this->json(array_values($neos->toArray()));
    }

    /**
     * @Route("/neo/fastest", name="neo_fastest")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
     public function fastestAction(Request $request)
     {
        // Gets hazardous
        $hazardous = $this->getHazardous($request);

        // Retrieves the fastest NEO
        $neos = $this->getNeoRepository()->findFastest($hazardous);

        return $this->json(array_values($neos->toArray()));
     }
 
    /**
     * @Route("/neo/best-year", name="neo_best_year")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
     public function bestYearAction(Request $request)
     {
        // Gets hazardous
        $hazardous = $this->getHazardous($request);

        // Retrieves NEOS
        $neos = $this->getNeoRepository()->getBestYear($hazardous);

        // Returns an empty json array if $neos is NULL
        if(empty($neos)) {
            return $this->json([]);
        }

        return $this->json(array_values($neos->toArray()));
     }
 
    /**
     * @Route("/neo/best-month", name="neo_best_month")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
     public function bestMonthAction(Request $request)
     {
        // Get hazardous
        $hazardous = $this->getHazardous($request);

        // Retrieve NEOS
        $neos = $this->getNeoRepository()->getBestMonth($hazardous);

        // Return an empty json array if $neos is NULL
        if(empty($neos)) {
            return $this->json([]);
        }

        return $this->json(array_values($neos->toArray()));
     }

    /**
     * Check if request has a GET parameter hazardous with a value at 'true'.
     *
     * @param Request $request
     *
     * @return bool
     */
     private function getHazardous(Request $request)
     {
        return ($request->get('hazardous') === 'true') ? true : false;
     }

    /**
     * @return mixed
     */
     private function getNeoRepository()
     {
         return $this->get('doctrine_mongodb')->getManager()->getRepository('AppBundle:NeoDocument');
     }
}
