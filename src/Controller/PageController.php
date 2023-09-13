<?php

namespace App\Controller;

use App\Entity\Page;
use App\FormData\PageForm;
use App\Entity\MenuItem;
use App\Form\PageType;
use App\Form\PageTitleType;
use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/page")
 */
class PageController extends AbstractController
{
    /**
     * @Route("/index", name="app_page_index", methods={"GET"})
     */
    public function index(PageRepository $pageRepository): Response
    {
        return $this->render('page/index.html.twig', [
            'pages' => $pageRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/new", name="app_page_new", methods={"GET", "POST"})
     */
    public function new(Request $request, PageRepository $pageRepository, MenuItem $menuItem): Response
    {
        $form_data = new PageForm();
        $form = $this->createForm(PageType::class, $form_data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($form_data->getChosen() != null) {

                $page = $form_data->getChosen();

                $pageRepository->link($menuItem, $page);

            } else {
                $page = new Page();
            }

            if ($form_data->getTitle() != null) {

                $page->setTitle($form_data->getTitle());

                $pageRepository->add($menuItem, $page);
            }

            return $this->back_to_menu_item_index($menuItem);
        }

        return $this->renderForm('page/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new_unlinked", name="app_page_new_unlinked", methods={"GET", "POST"})
     */
    public function new_unlinked(Request $request, PageRepository $pageRepository):Response {
        $page = new Page();

        $form = $this->createForm(PageTitleType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $pageRepository->add(null, $page);
            
            return $this->redirectToRoute('app_page_index');
        }

        return $this->renderForm('page/new_unlinked.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/unlink", name="app_page_unlink", methods={"GET"})
     */
    public function unlink(Request $request, MenuItem $menuItem, PageRepository $pageRepository): Response
    {
        $pageRepository->unlink($menuItem);
        
        return $this->back_to_menu_item_index($menuItem);
    }

    private function back_to_menu_item_index(MenuItem $menuItem):Response {
        if ($menuItem->getParent() == null)
            return $this->redirectToRoute('app_menu_item_index', [], Response::HTTP_SEE_OTHER);
        else
            return $this->redirectToRoute('app_menu_item_index_level2',['id' => $menuItem->getParent()->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}", name="app_page_show", methods={"GET"})
     */
    public function show(Page $page): Response
    {
        return $this->render('page/show.html.twig', [
            'page' => $page,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_page_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Page $page, PageRepository $pageRepository): Response
    {
        $form = $this->createForm(PageTitleType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pageRepository->update($page);

            return $this->back_to_menu_item_index($page->getMenuItem());
        }

        return $this->renderForm('page/edit.html.twig', [
            'page' => $page,
            'form' => $form,
        ]);
    }

}
