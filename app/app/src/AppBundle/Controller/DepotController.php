<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\Depot;

/**
 * Depot controller.
 *
 */
class DepotController extends Controller
{
    /**
     * Lists all Depot entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Depot')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($depots, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('depot/index.html.twig', array(
            'depots' => $depots,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
            'totalOfRecordsString' => $totalOfRecordsString,

        ));
    }

    /**
    * Create filter form and process filter request.
    *
    */
    protected function filter($queryBuilder, Request $request)
    {
        $session = $request->getSession();
        $filterForm = $this->createForm('AppBundle\Form\DepotFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('DepotControllerFilter');
        }

        // Filter action
        if ($request->get('filter_action') == 'filter') {
            // Bind values from the request
            $filterForm->handleRequest($request);

            if ($filterForm->isValid()) {
                // Build the query from the given form object
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
                // Save filter to session
                $filterData = $filterForm->getData();
                $session->set('DepotControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('DepotControllerFilter')) {
                $filterData = $session->get('DepotControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\DepotFilterType', $filterData);
                $this->get('lexik_form_filter.query_builder_updater')->addFilterConditions($filterForm, $queryBuilder);
            }
        }

        return array($filterForm, $queryBuilder);
    }


    /**
    * Get results from paginator and get paginator view.
    *
    */
    protected function paginator($queryBuilder, Request $request)
    {
        //sorting
        $sortCol = $queryBuilder->getRootAlias().'.'.$request->get('pcg_sort_col', 'id');
        $queryBuilder->orderBy($sortCol, $request->get('pcg_sort_order', 'desc'));
        // Paginator
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($request->get('pcg_show' , 10));

        try {
            $pagerfanta->setCurrentPage($request->get('pcg_page', 1));
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $ex) {
            $pagerfanta->setCurrentPage(1);
        }
        
        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $me = $this;
        $routeGenerator = function($page) use ($me, $request)
        {
            $requestParams = $request->query->all();
            $requestParams['pcg_page'] = $page;
            return $me->generateUrl('depot', $requestParams);
        };

        // Paginator - view
        $view = new TwitterBootstrap3View();
        $pagerHtml = $view->render($pagerfanta, $routeGenerator, array(
            'proximity' => 3,
            'prev_message' => 'previous',
            'next_message' => 'next',
        ));

        return array($entities, $pagerHtml);
    }
    
    
    
    /*
     * Calculates the total of records string
     */
    protected function getTotalOfRecordsString($queryBuilder, $request) {
        $totalOfRecords = $queryBuilder->select('COUNT(e.id)')->getQuery()->getSingleScalarResult();
        $show = $request->get('pcg_show', 10);
        $page = $request->get('pcg_page', 1);

        $startRecord = ($show * ($page - 1)) + 1;
        $endRecord = $show * $page;

        if ($endRecord > $totalOfRecords) {
            $endRecord = $totalOfRecords;
        }
        return "Showing $startRecord - $endRecord of $totalOfRecords Records.";
    }
    
    

    /**
     * Displays a form to create a new Depot entity.
     *
     */
    public function newAction(Request $request)
    {
    
        $depot = new Depot();
        $form   = $this->createForm('AppBundle\Form\DepotType', $depot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($depot);
            $em->flush();
            
            $editLink = $this->generateUrl('depot_edit', array('id' => $depot->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New depot was created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'depot' : 'depot_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('depot/new.html.twig', array(
            'depot' => $depot,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a Depot entity.
     *
     */
    public function showAction(Depot $depot)
    {
        $deleteForm = $this->createDeleteForm($depot);
        return $this->render('depot/show.html.twig', array(
            'depot' => $depot,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing Depot entity.
     *
     */
    public function editAction(Request $request, Depot $depot)
    {
        $deleteForm = $this->createDeleteForm($depot);
        $editForm = $this->createForm('AppBundle\Form\DepotType', $depot);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($depot);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('depot_edit', array('id' => $depot->getId()));
        }
        return $this->render('depot/edit.html.twig', array(
            'depot' => $depot,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a Depot entity.
     *
     */
    public function deleteAction(Request $request, Depot $depot)
    {
    
        $form = $this->createDeleteForm($depot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($depot);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The Depot was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the Depot');
        }
        
        return $this->redirectToRoute('depot');
    }
    
    /**
     * Creates a form to delete a Depot entity.
     *
     * @param Depot $depot The Depot entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Depot $depot)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('depot_delete', array('id' => $depot->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete Depot by id
     *
     */
    public function deleteByIdAction(Depot $depot){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($depot);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The Depot was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the Depot');
        }

        return $this->redirect($this->generateUrl('depot'));

    }
    

    /**
    * Bulk Action
    */
    public function bulkAction(Request $request)
    {
        $ids = $request->get("ids", array());
        $action = $request->get("bulk_action", "delete");

        if ($action == "delete") {
            try {
                $em = $this->getDoctrine()->getManager();
                $repository = $em->getRepository('AppBundle:Depot');

                foreach ($ids as $id) {
                    $depot = $repository->find($id);
                    $em->remove($depot);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'depots was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the depots ');
            }
        }

        return $this->redirect($this->generateUrl('depot'));
    }
    

}
