<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\lineCmd;

/**
 * lineCmd controller.
 *
 */
class lineCmdController extends Controller
{
    /**
     * Lists all lineCmd entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:lineCmd')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($lineCmds, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('linecmd/index.html.twig', array(
            'lineCmds' => $lineCmds,
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
        $filterForm = $this->createForm('AppBundle\Form\lineCmdFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('lineCmdControllerFilter');
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
                $session->set('lineCmdControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('lineCmdControllerFilter')) {
                $filterData = $session->get('lineCmdControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\lineCmdFilterType', $filterData);
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
            return $me->generateUrl('linecmd', $requestParams);
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
     * Displays a form to create a new lineCmd entity.
     *
     */
    public function newAction(Request $request)
    {
    
        $lineCmd = new lineCmd();
        $form   = $this->createForm('AppBundle\Form\lineCmdType', $lineCmd);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lineCmd);
            $em->flush();
            
            $editLink = $this->generateUrl('linecmd_edit', array('id' => $lineCmd->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New lineCmd was created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'linecmd' : 'linecmd_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('linecmd/new.html.twig', array(
            'lineCmd' => $lineCmd,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a lineCmd entity.
     *
     */
    public function showAction(lineCmd $lineCmd)
    {
        $deleteForm = $this->createDeleteForm($lineCmd);
        return $this->render('linecmd/show.html.twig', array(
            'lineCmd' => $lineCmd,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing lineCmd entity.
     *
     */
    public function editAction(Request $request, lineCmd $lineCmd)
    {
        $deleteForm = $this->createDeleteForm($lineCmd);
        $editForm = $this->createForm('AppBundle\Form\lineCmdType', $lineCmd);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($lineCmd);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('linecmd_edit', array('id' => $lineCmd->getId()));
        }
        return $this->render('linecmd/edit.html.twig', array(
            'lineCmd' => $lineCmd,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a lineCmd entity.
     *
     */
    public function deleteAction(Request $request, lineCmd $lineCmd)
    {
    
        $form = $this->createDeleteForm($lineCmd);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($lineCmd);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The lineCmd was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the lineCmd');
        }
        
        return $this->redirectToRoute('linecmd');
    }
    
    /**
     * Creates a form to delete a lineCmd entity.
     *
     * @param lineCmd $lineCmd The lineCmd entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(lineCmd $lineCmd)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('linecmd_delete', array('id' => $lineCmd->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete lineCmd by id
     *
     */
    public function deleteByIdAction(lineCmd $lineCmd){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($lineCmd);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The lineCmd was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the lineCmd');
        }

        return $this->redirect($this->generateUrl('linecmd'));

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
                $repository = $em->getRepository('AppBundle:lineCmd');

                foreach ($ids as $id) {
                    $lineCmd = $repository->find($id);
                    $em->remove($lineCmd);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'lineCmds was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the lineCmds ');
            }
        }

        return $this->redirect($this->generateUrl('linecmd'));
    }
    

}
