<?php

namespace App\Controller;

use App\Entity\Entry;
use App\Form\EntryType;
use App\Repository\EntryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/entry")
 */
class EntryController extends Controller
{
    /**
     * @Route("/", name="entry_index", methods="GET")
     */
    public function index(EntryRepository $entryRepository): Response
    {
        return $this->render('entry/index.html.twig', ['entries' => $entryRepository->findAll()]);
    }
   
    /**
     * @Route("/new", name="entry_new", methods="GET|POST")
     */
    public function new(Request $request): Response
    {
        $entry = new Entry();
        $form = $this->createForm(EntryType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $city = $entry->getCity();
            $val = $entry->getValuePln();
            
            $ch = curl_init('https://restcountries.eu/rest/v2/capital/'.$city);
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_exec($ch);

            if (curl_errno($ch)) {
                die('Couldn\'t send request: ' . curl_error($ch));
            } else {
                $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($resultStatus == 200) {
                    $response = file_get_contents('https://restcountries.eu/rest/v2/capital/'.$city);
                    $response = json_decode($response, true);
                    $country = $response['0']['name'];
                    $curtag = $response['0']['currencies']['0']['code'];

                    $response1 = file_get_contents('http://api.nbp.pl/api/exchangerates/rates/A/'.$curtag.'/?format=json');
                    $response1 = json_decode($response1, true);
                    $exchange = $response1['rates']['0']['mid'];

                    $entry->setCountry($country);
                    $entry->setTag($curtag);
                    $entry->setVlaueAfter(number_format((float)($val/$exchange), 2, '.', ''));
                    $entry->setDate(new \DateTime('now'));
        
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($entry);
                    $em->flush();
        
                    return $this->redirectToRoute('entry_index');
                } else {
                    return $this->render('entry/error.html.twig');
                    //die('Request failed: HTTP status code: ' . $resultStatus .' '.'This is not Capital city :)');
                }
            }

            curl_close($ch);  
        }

        return $this->render('entry/new.html.twig', [
            'entry' => $entry,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="entry_show", methods="GET")
     */
    public function show(Entry $entry): Response
    {
        return $this->render('entry/show.html.twig', ['entry' => $entry]);
    }

    /**
     * @Route("/{id}/edit", name="entry_edit", methods="GET|POST")
     */
    public function edit(Request $request, Entry $entry): Response
    {
        $form = $this->createForm(EntryType::class, $entry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
            $city = $entry->getCity();
            $val = $entry->getValuePln();
            $ch = curl_init('https://restcountries.eu/rest/v2/capital/'.$city);
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_exec($ch);

            if (curl_errno($ch)) {
                die('Couldn\'t send request: ' . curl_error($ch));
            } else {
                $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($resultStatus == 200) {
                    
                    $response = file_get_contents('https://restcountries.eu/rest/v2/capital/'.$city);
                    $response = json_decode($response, true);
                    $country = $response['0']['name'];
                    $curtag = $response['0']['currencies']['0']['code'];

                    $response1 = file_get_contents('http://api.nbp.pl/api/exchangerates/rates/A/'.$curtag.'/?format=json');
                    $response1 = json_decode($response1, true);
                    $exchange = $response1['rates']['0']['mid'];

                    $entry->setCountry($country);
                    $entry->setTag($curtag);
                    $entry->setVlaueAfter(number_format((float)($val/$exchange), 2, '.', ''));
                    $entry->setDate(new \DateTime('now'));

                    $this->getDoctrine()->getManager()->flush();
                    //return $this->redirectToRoute('entry_edit', ['id' => $entry->getId()]);
                    return $this->redirectToRoute('entry_index');
                   
                } else {
                    return $this->render('entry/error.html.twig');
                    //die('Request failed: HTTP status code: ' . $resultStatus .' '.'This is not Capital city :)');
                }
            }

            curl_close($ch);
        }

        return $this->render('entry/edit.html.twig', [
            'entry' => $entry,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="entry_delete", methods="DELETE")
     */
    public function delete(Request $request, Entry $entry): Response
    {
        if ($this->isCsrfTokenValid('delete'.$entry->getId(), $request->request->get('_token'))) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($entry);
            $em->flush();
        }

        return $this->redirectToRoute('entry_index');
    }
}

