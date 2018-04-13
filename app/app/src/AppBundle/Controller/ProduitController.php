<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Marque;
use AppBundle\Entity\Modele;
use AppBundle\Entity\RefModele;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrap3View;

use AppBundle\Entity\Produit;

/**
 * Produit controller.
 *
 */
class ProduitController extends Controller
{
    /**
     * Lists all Produit entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Produit')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($produits, $pagerHtml) = $this->paginator($queryBuilder, $request);

        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('produit/index.html.twig', array(
            'produits' => $produits,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
            'totalOfRecordsString' => $totalOfRecordsString,

        ));
    }


    public function catalogueAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Produit')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($produits, $pagerHtml) = $this->paginator($queryBuilder, $request);

        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('produit/catalogue.html.twig', array(
            'produits' => $produits,
            'pagerHtml' => $pagerHtml,
            'filterForm' => $filterForm->createView(),
            'totalOfRecordsString' => $totalOfRecordsString,

        ));
    }
    public function catalogue2Action(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $queryBuilder = $em->getRepository('AppBundle:Produit')->createQueryBuilder('e');

        list($filterForm, $queryBuilder) = $this->filter($queryBuilder, $request);
        list($produits, $pagerHtml) = $this->paginator($queryBuilder, $request);

        $totalOfRecordsString = $this->getTotalOfRecordsString($queryBuilder, $request);

        return $this->render('produit/catalogue2.html.twig', array(
            'produits' => $produits,
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
        $filterForm = $this->createForm('AppBundle\Form\ProduitFilterType');

        // Reset filter
        if ($request->get('filter_action') == 'reset') {
            $session->remove('ProduitControllerFilter');
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
                $session->set('ProduitControllerFilter', $filterData);
            }
        } else {
            // Get filter from session
            if ($session->has('ProduitControllerFilter')) {
                $filterData = $session->get('ProduitControllerFilter');

                foreach ($filterData as $key => $filter) { //fix for entityFilterType that is loaded from session
                    if (is_object($filter)) {
                        $filterData[$key] = $queryBuilder->getEntityManager()->merge($filter);
                    }
                }

                $filterForm = $this->createForm('AppBundle\Form\ProduitFilterType', $filterData);
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
        $sortCol = $queryBuilder->getRootAlias() . '.' . $request->get('pcg_sort_col', 'id');
        $queryBuilder->orderBy($sortCol, $request->get('pcg_sort_order', 'desc'));
        // Paginator
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($request->get('pcg_show', 10));

        try {
            $pagerfanta->setCurrentPage($request->get('pcg_page', 1));
        } catch (\Pagerfanta\Exception\OutOfRangeCurrentPageException $ex) {
            $pagerfanta->setCurrentPage(1);
        }

        $entities = $pagerfanta->getCurrentPageResults();

        // Paginator - route generator
        $me = $this;
        $routeGenerator = function ($page) use ($me, $request) {
            $requestParams = $request->query->all();
            $requestParams['pcg_page'] = $page;
            return $me->generateUrl('produit', $requestParams);
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
    protected function getTotalOfRecordsString($queryBuilder, $request)
    {
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
     * Displays a form to create a new Produit entity.
     *
     */
    public function newAction(Request $request)
    {

        $produit = new Produit();
        $form = $this->createForm('AppBundle\Form\ProduitType', $produit);
        $form->handleRequest($request);

       
       
        //1 Ajout une  Marque aux interface produit new
        $marque = new Marque();
        $formMarque = $this->createForm('AppBundle\Form\MarqueType', $marque);
        $formMarque->handleRequest($request);

        if ($formMarque->isSubmitted() && $formMarque->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($marque);
            $em->flush();

            $editLink = $this->generateUrl('marque_edit', array('id' => $marque->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New marque was created successfully.</a>");
          // produit_new Routour page
            $nextAction = $request->get('submit') == 'save' ? 'produit_new' : 'marque_new';
            return $this->redirectToRoute($nextAction);
        }
        //////////////////////////////////////////////
        //Ajouter Modele aux interface prodModeleuit new
        /////////////////////////////////////////////
        $modele = new Modele();
        $formModele   = $this->createForm('AppBundle\Form\ModeleType', $modele);
        $formModele->handleRequest($request);

        if ($formModele->isSubmitted() && $formModele->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($modele);
            $em->flush();
            
            $editLink = $this->generateUrl('modele_edit', array('id' => $modele->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New modele was created successfully.</a>" );
            
            $nextAction=  $request->get('submit') == 'save' ? 'produit_new' : 'modele_new';
            return $this->redirectToRoute($nextAction);
        }
          //1 Ajout une  refmodele aux interface produit new
          $refmodele = new RefModele();
          $formRefModele = $this->createForm('AppBundle\Form\RefModeleType', $refmodele);
          $formRefModele->handleRequest($request);
  
          if ($formRefModele->isSubmitted() && $formRefModele->isValid()) {
              $em = $this->getDoctrine()->getManager();
              $em->persist($refmodele);
              $em->flush();
  
              $editLink = $this->generateUrl('refmodele_edit', array('id' => $refmodele->getId()));
              $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New marque was created successfully.</a>");
            // produit_new Routour page
              $nextAction = $request->get('submit') == 'save' ? 'produit_new' : 'refmodele_new';
              return $this->redirectToRoute($nextAction);
          }






        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($produit);
            $em->flush();

            $editLink = $this->generateUrl('produit_edit', array('id' => $produit->getId()));
            $this->get('session')->getFlashBag()->add('success', "<a href='$editLink'>New produit was created successfully.</a>");

            $nextAction = $request->get('submit') == 'save' ? 'produit' : 'produit_new';
            return $this->redirectToRoute($nextAction);
        }
        return $this->render('produit/new.html.twig', array(
            'produit' => $produit,
            'form' => $form->createView(),
            //2 Appelle Marque interface creatview
            'marque' => $marque,
            'form_marque' => $formMarque->createView(),
            //Appelle modele interface creatview
            'modele' => $modele,
            'form_modele' => $formModele->createView(),

             //Appelle refmodele interface creatview
             'refmodele' => $refmodele,
             'form_refmodele' => $formRefModele->createView(),
        ));
    }


    /**
     * Finds and displays a Produit entity.
     *
     */
    public function showAction(Produit $produit)
    {
        $deleteForm = $this->createDeleteForm($produit);
        return $this->render('produit/show.html.twig', array(
            'produit' => $produit,
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Displays a form to edit an existing Produit entity.
     *
     */
    public function editAction(Request $request, Produit $produit)
    {
        $deleteForm = $this->createDeleteForm($produit);
        $editForm = $this->createForm('AppBundle\Form\ProduitType', $produit);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($produit);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Edited Successfully!');
            return $this->redirectToRoute('produit_edit', array('id' => $produit->getId()));
        }
        return $this->render('produit/edit.html.twig', array(
            'produit' => $produit,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }


    /**
     * Deletes a Produit entity.
     *
     */
    public function deleteAction(Request $request, Produit $produit)
    {

        $form = $this->createDeleteForm($produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($produit);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The Produit was deleted successfully');
        } else {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the Produit');
        }

        return $this->redirectToRoute('produit');
    }

    /**
     * Creates a form to delete a Produit entity.
     *
     * @param Produit $produit The Produit entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Produit $produit)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('produit_delete', array('id' => $produit->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }

    /**
     * Delete Produit by id
     *
     */
    public function deleteByIdAction(Produit $produit)
    {
        $em = $this->getDoctrine()->getManager();

        try {
            $em->remove($produit);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'The Produit was deleted successfully');
        } catch (Exception $ex) {
            $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the Produit');
        }

        return $this->redirect($this->generateUrl('produit'));

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
                $repository = $em->getRepository('AppBundle:Produit');

                foreach ($ids as $id) {
                    $produit = $repository->find($id);
                    $em->remove($produit);
                    $em->flush();
                }

                $this->get('session')->getFlashBag()->add('success', 'produits was deleted successfully!');

            } catch (Exception $ex) {
                $this->get('session')->getFlashBag()->add('error', 'Problem with deletion of the produits ');
            }
        }

        return $this->redirect($this->generateUrl('produit'));
    }


}
