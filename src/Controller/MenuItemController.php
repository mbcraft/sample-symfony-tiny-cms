<?php

namespace App\Controller;

use App\Entity\MenuItem;
use App\Form\MenuItemType;
use App\Repository\MenuItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/menu")
 */
class MenuItemController extends AbstractController
{
    /**
     * @Route("/index", name="app_menu_item_index", methods={"GET"})
     */
    public function index(MenuItemRepository $menuItemRepository): Response
    {
        return $this->render('menu_item/index.html.twig', [
            'menu_items' => $menuItemRepository->getOrderedFullList(['el.parent' => null]),
            'current_menu_item' => null
        ]);
    }

    /**
     * @Route("/{id}/index", name="app_menu_item_index_level2", methods={"GET"})
     */
    public function index_level2(MenuItemRepository $menuItemRepository,MenuItem $menuItem):Response {
        return $this->render('menu_item/index.html.twig', [
            'menu_items' => $menuItemRepository->getOrderedFullList(['el.parent' => $menuItem->getId()]),
            'current_menu_item' => $menuItem
        ]);
    }

    /**
     * @Route("/new", name="app_menu_item_new", methods={"GET", "POST"})
     */
    public function new(Request $request, MenuItemRepository $menuItemRepository): Response
    {
        $menuItem = new MenuItem();
        $form = $this->createForm(MenuItemType::class, $menuItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $menuItemRepository->add($menuItem);

            return $this->back_to_index($menuItem);
        }

        return $this->renderForm('menu_item/new.html.twig', [
            'menu_item' => $menuItem,
            'form' => $form,
        ]);
    }

    private function back_to_index(MenuItem $menuItem):Response {
        if ($menuItem->getParent() == null)
            return $this->redirectToRoute('app_menu_item_index', [], Response::HTTP_SEE_OTHER);
        else
            return $this->redirectToRoute('app_menu_item_index_level2',['id' => $menuItem->getParent()->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/{id}/new", name="app_menu_item_new_level2", methods={"GET", "POST"})
     */
    public function new_level2(Request $request, MenuItemRepository $menuItemRepository, MenuItem $parentItem): Response
    {
        $menuItem = new MenuItem();
        $menuItem->setParent($parentItem);

        $form = $this->createForm(MenuItemType::class, $menuItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $menuItemRepository->add($menuItem);

            return $this->back_to_index($menuItem);
        }

        return $this->renderForm('menu_item/new.html.twig', [
            'menu_item' => $menuItem,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_menu_item_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, MenuItem $menuItem, MenuItemRepository $menuItemRepository): Response
    {
        $form = $this->createForm(MenuItemType::class, $menuItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $menuItemRepository->update($menuItem);

            return $this->back_to_index($menuItem);
        }

        return $this->renderForm('menu_item/edit.html.twig', [
            'menu_item' => $menuItem,
            'form' => $form,
        ]);
    }

    //TODO sarebbe meglio usare la post con la relativa form ...
    /**
     * @Route("/delete/{id}", name="app_menu_item_delete", methods={"GET"})
     */
    public function delete(Request $request, MenuItem $menuItem, MenuItemRepository $menuItemRepository): Response
    {
        //if ($this->isCsrfTokenValid('delete'.$menuItem->getId(), $request->request->get('_token'))) {
            $menuItemRepository->remove($menuItem, true);
        //}

        return $this->back_to_index($menuItem);
    }

    /**
     * @Route("/move_to_first/{id}", name="app_menu_item_move_to_first", methods={"GET"})
     */
    public function move_to_first(Request $request, MenuItem $menuItem, MenuItemRepository $menuItemRepository): Response {
        $menuItemRepository->move_to_first($menuItem);

        return $this->back_to_index($menuItem);
    }

    /**
     * @Route("/move_to_previous/{id}", name="app_menu_item_move_to_previous", methods={"GET"})
     */
    public function move_to_previous(Request $request, MenuItem $menuItem, MenuItemRepository $menuItemRepository): Response {
        $menuItemRepository->move_to_previous($menuItem);

        return $this->back_to_index($menuItem);
    }

    /**
     * @Route("/move_to_next/{id}", name="app_menu_item_move_to_next", methods={"GET"})
     */
    public function move_to_next(Request $request, MenuItem $menuItem, MenuItemRepository $menuItemRepository): Response {
        $menuItemRepository->move_to_next($menuItem);

        return $this->back_to_index($menuItem);
    }

    /**
     * @Route("/move_to_last/{id}", name="app_menu_item_move_to_last", methods={"GET"})
     */
    public function move_to_last(Request $request, MenuItem $menuItem, MenuItemRepository $menuItemRepository): Response {
        $menuItemRepository->move_to_last($menuItem);

        return $this->back_to_index($menuItem);
    }


}
