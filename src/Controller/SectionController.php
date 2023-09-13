<?php

namespace App\Controller;

use App\Entity\Page;
use App\Entity\Section;
use App\Form\SectionType;
use App\Repository\SectionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/section")
 */
class SectionController extends AbstractController
{
    /**
     * @Route("/{id}/index", name="app_section_index", methods={"GET"})
     */
    public function index(SectionRepository $sectionRepository, Page $page): Response
    {
        return $this->render('section/index.html.twig', [
            'page' => $page,
            'sections' => $sectionRepository->getOrderedFullList(['el.page' => $page->getId()])
        ]);
    }

    /**
     * @Route("/{id}/new", name="app_section_new", methods={"GET", "POST"})
     */
    public function new(Request $request, SectionRepository $sectionRepository, Page $page): Response
    {
        $section = new Section();
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sectionRepository->add($page,$section);

            return $this->redirectToRoute('app_section_index', ['id' => $page->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('section/new.html.twig', [
            'page' => $page,
            'section' => $section,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/show/{id}", name="app_section_show", methods={"GET"})
     */
    public function show(Section $section): Response
    {
        return $this->render('section/show.html.twig', [
            'section' => $section,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="app_section_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Section $section, SectionRepository $sectionRepository): Response
    {
        $form = $this->createForm(SectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sectionRepository->update($section);

            return $this->redirectToRoute('app_section_index', ['id' => $section->getPage()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('section/edit.html.twig', [
            'section' => $section,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/delete/{id}", name="app_section_delete", methods={"POST"})
     */
    public function delete(Request $request, Section $section, SectionRepository $sectionRepository): Response
    {
        $page = $section->getPage();

        $sectionRepository->remove($page, $section);
        
        return $this->redirectToRoute('app_section_index', ['id' => $page->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/move_to_first/{id}", name="app_section_move_to_first", methods={"GET"})
     */
    public function move_to_first(Request $request, Section $section, SectionRepository $sectionRepository): Response {
        $sectionRepository->move_to_first($section);

        return $this->redirectToRoute('app_section_index', ['id' => $section->getPage()->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/move_to_previous/{id}", name="app_section_move_to_previous", methods={"GET"})
     */
    public function move_to_previous(Request $request, Section $section, SectionRepository $sectionRepository): Response {
        $sectionRepository->move_to_previous($section);

        return $this->redirectToRoute('app_section_index', ['id' => $section->getPage()->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/move_to_next/{id}", name="app_section_move_to_next", methods={"GET"})
     */
    public function move_to_next(Request $request, Section $section, SectionRepository $sectionRepository): Response {
        $sectionRepository->move_to_next($section);

        return $this->redirectToRoute('app_section_index', ['id' => $section->getPage()->getId()], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/move_to_last/{id}", name="app_section_move_to_last", methods={"GET"})
     */
    public function move_to_last(Request $request, Section $section, SectionRepository $sectionRepository): Response {
        $sectionRepository->move_to_last($section);

        return $this->redirectToRoute('app_section_index', ['id' => $section->getPage()->getId()], Response::HTTP_SEE_OTHER);
    }
}
