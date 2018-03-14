<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\ModePay;

/**
 * ModePay controller.
 *
 */
class ModePayController extends Controller
{
    /**
     * Lists all ModePay entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:ModePay')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($modePays, $pagerHtml) = $this->paginator($queryBuilder, $request);
        
        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('modepay/index.html.twig', array(
            'modePays' => $modePays,
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
        $filterForm = $this->createForm('AppBundle\Form\ModePayFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('ModePayControllerFilter');
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
                $session->set('ModePayControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('ModePayControllerFilter')) {
                $filterData = $session->get('ModePayControllerFilter');
                
                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }
                
                $filterForm = $this->createForm('AppBundle\Form\ModePayFilterType', $filterData);
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
            return $me->generateUrl('modepay', $requestParams);
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
     * Displays a form to create a new ModePay entity.
     *
     */
    public function newAction(Request $request)
    {
    
        $modePay = new ModePay();
        $form   = $this->createForm('AppBundle\Form\ModePayType', $modePay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($modePay);
            $em->flush();
            
            $editLink = $this->generateUrl('modepay_edit', array('id' => $modePay->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New modePay was created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'modepay' : 'modepay_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('modepay/new.html.twig', array(
            'modePay' => $modePay,
            'form'   => $form->createView(),
        ));
    }
    

    /**
     * Finds and displays a ModePay entity.
     *
     */
    public function showAction(ModePay $modePay)
    {
        $deleteForm = $this->createDeleteForm($modePay);
        return $this->render('modepay/show.html.twig', array(
            'modePay' => $modePay,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Displays a form to edit an existing ModePay entity.
     *
     */
    public function editAction(Request $request, ModePay $modePay)
    {
        $deleteForm = $this->createDeleteForm($modePay);
        $editForm = $this->createForm('AppBundle\Form\ModePayType', $modePay);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($modePay);
            $em->flush();
            
            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('modepay_edit', array('id' => $modePay->getId()));
        }
        return $this->render('modepay/edit.html.twig', array(
            'modePay' => $modePay,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    
    

    /**
     * Deletes a ModePay entity.
     *
     */
    public function deleteAction(Request $request, ModePay $modePay)
    {
    
        $form = $this->createDeleteForm($modePay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($modePay);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The ModePay was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the ModePay');
        }
        
        return $this->redirectToRoute('modepay');
    }
    
    /**
     * Creates a form to delete a ModePay entity.
     *
     * @param ModePay $modePay The ModePay entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(ModePay $modePay)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('modepay_delete', array('id' => $modePay->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    
    /**
     * Delete ModePay by id
     *
     */
    public function deleteByIdAction(ModePay $modePay){
        $em = $this->getDoctrine()->getManager();
        
        try {
            $em->remove($modePay);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The ModePay was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the ModePay');
        }

        return $this->redirect($this->generateUrl('modepay'));

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
                $repository = $em->getRepository('AppBundle:ModePay');

                foreach ($ids as $id) {
                    $modePay = $repository->find($id);
                    $em->remove($modePay);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'modePays was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the modePays ');
            }
        }

        return $this->redirect($this->generateUrl('modepay'));
    }
    

}
