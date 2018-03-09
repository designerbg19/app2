<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\EtaCmd;

/**
 * EtaCmd controller.
 *
 */
class EtaCmdController extends Controller
{
    /**
     * Lists all EtaCmd entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:EtaCmd')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($etaCmds, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('etacmd/index.html.twig', array(
            'etaCmds' => $etaCmds,
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
        $filterForm = $this->createForm('AppBundle\Form\EtaCmdFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('EtaCmdControllerFilter');
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
                $session->set('EtaCmdControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('EtaCmdControllerFilter')) {
                $filterData = $session->get('EtaCmdControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\EtaCmdFilterType', $filterData);
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
            return $me->generateUrl('etacmd', $requestParams);
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
     * Displays a form to create a new EtaCmd entity.
     *
     */
    public function newAction(Request $request)
    {
    
        $etaCmd = new EtaCmd();
        $form   = $this->createForm('AppBundle\Form\EtaCmdType', $etaCmd);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($etaCmd);
            $em->flush();
            
            $editLink = $this->generateUrl('etacmd_edit', array('id' => $etaCmd->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New etaCmd was created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'etacmd' : 'etacmd_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('etacmd/new.html.twig', array(
            'etaCmd' => $etaCmd,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a EtaCmd entity.
     *
     */
    public function showAction(EtaCmd $etaCmd)
    {
        $deleteForm = $this->createDeleteForm($etaCmd);
        return $this->render('etacmd/show.html.twig', array(
            'etaCmd' => $etaCmd,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing EtaCmd entity.
     *
     */
    public function editAction(Request $request, EtaCmd $etaCmd)
    {
        $deleteForm = $this->createDeleteForm($etaCmd);
        $editForm = $this->createForm('AppBundle\Form\EtaCmdType', $etaCmd);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($etaCmd);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('etacmd_edit', array('id' => $etaCmd->getId()));
        }
        return $this->render('etacmd/edit.html.twig', array(
            'etaCmd' => $etaCmd,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a EtaCmd entity.
     *
     */
    public function deleteAction(Request $request, EtaCmd $etaCmd)
    {
    
        $form = $this->createDeleteForm($etaCmd);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($etaCmd);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The EtaCmd was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the EtaCmd');
        }
        
        return $this->redirectToRoute('etacmd');
    }
    
    /**
     * Creates a form to delete a EtaCmd entity.
     *
     * @param EtaCmd $etaCmd The EtaCmd entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(EtaCmd $etaCmd)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('etacmd_delete', array('id' => $etaCmd->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete EtaCmd by id
     *
     */
    public function deleteByIdAction(EtaCmd $etaCmd){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($etaCmd);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The EtaCmd was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the EtaCmd');
        }

        return $this->redirect($this->generateUrl('etacmd'));

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
                $repository = $em->getRepository('AppBundle:EtaCmd');

                foreach ($ids as $id) {
                    $etaCmd = $repository->find($id);
                    $em->remove($etaCmd);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'etaCmds was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the etaCmds ');
            }
        }

        return $this->redirect($this->generateUrl('etacmd'));
    }
    

}
