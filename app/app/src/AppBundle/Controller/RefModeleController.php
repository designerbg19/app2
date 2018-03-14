<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\RefModele;

/**
 * RefModele controller.
 *
 */
class RefModeleController extends Controller
{
    /**
     * Lists all RefModele entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:RefModele')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($refModeles, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('refmodele/index.html.twig', array(
            'refModeles' => $refModeles,
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
        $filterForm = $this->createForm('AppBundle\Form\RefModeleFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('RefModeleControllerFilter');
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
                $session->set('RefModeleControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('RefModeleControllerFilter')) {
                $filterData = $session->get('RefModeleControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\RefModeleFilterType', $filterData);
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
            return $me->generateUrl('refmodele', $requestParams);
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
     * Displays a form to create a new RefModele entity.
     *
     */
    public function newAction(Request $request)
    {
    
        $refModele = new RefModele();
        $form   = $this->createForm('AppBundle\Form\RefModeleType', $refModele);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($refModele);
            $em->flush();
            
            $editLink = $this->generateUrl('refmodele_edit', array('id' => $refModele->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New refModele was created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'refmodele' : 'refmodele_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('refmodele/new.html.twig', array(
            'refModele' => $refModele,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a RefModele entity.
     *
     */
    public function showAction(RefModele $refModele)
    {
        $deleteForm = $this->createDeleteForm($refModele);
        return $this->render('refmodele/show.html.twig', array(
            'refModele' => $refModele,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing RefModele entity.
     *
     */
    public function editAction(Request $request, RefModele $refModele)
    {
        $deleteForm = $this->createDeleteForm($refModele);
        $editForm = $this->createForm('AppBundle\Form\RefModeleType', $refModele);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($refModele);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('refmodele_edit', array('id' => $refModele->getId()));
        }
        return $this->render('refmodele/edit.html.twig', array(
            'refModele' => $refModele,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a RefModele entity.
     *
     */
    public function deleteAction(Request $request, RefModele $refModele)
    {
    
        $form = $this->createDeleteForm($refModele);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($refModele);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The RefModele was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the RefModele');
        }
        
        return $this->redirectToRoute('refmodele');
    }
    
    /**
     * Creates a form to delete a RefModele entity.
     *
     * @param RefModele $refModele The RefModele entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(RefModele $refModele)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('refmodele_delete', array('id' => $refModele->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete RefModele by id
     *
     */
    public function deleteByIdAction(RefModele $refModele){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($refModele);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The RefModele was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the RefModele');
        }

        return $this->redirect($this->generateUrl('refmodele'));

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
                $repository = $em->getRepository('AppBundle:RefModele');

                foreach ($ids as $id) {
                    $refModele = $repository->find($id);
                    $em->remove($refModele);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'refModeles was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the refModeles ');
            }
        }

        return $this->redirect($this->generateUrl('refmodele'));
    }
    

}
