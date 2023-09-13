<?php

namespace App\Controller;

use App\Entity\MenuItem;
use App\Entity\DownloadableFile;
use App\FormData\DownloadableFileForm;
use App\Form\DownloadableFileType;
use App\Form\SimpleFileType;
use App\Repository\DownloadableFileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @Route("/downloadable_file")
 */
class DownloadableFileController extends AbstractController
{
    /**
     * @Route("/index", name="app_downloadable_file_index", methods={"GET"})
     */
    public function index(DownloadableFileRepository $downloadableFileRepository): Response
    {
        return $this->render('downloadable_file/index.html.twig', [
            'downloadable_files' => $downloadableFileRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/new", name="app_downloadable_file_new", methods={"GET","POST"})
     */
    public function new(Request $request, DownloadableFileRepository $downloadableFileRepository, MenuItem $menuItem): Response
    {
        $downloadableFileForm = new DownloadableFileForm();
        $form = $this->createForm(DownloadableFileType::class, $downloadableFileForm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($downloadableFileForm->getAttachment() != null) {

                $downloadableFile = $downloadableFileRepository->createAndSaveFile($downloadableFileForm->getAttachment());

                $downloadableFileRepository->link($menuItem,$downloadableFile);

                return $this->back_to_menu_item_index($menuItem);

            }
            if ($downloadableFileForm->getChosen()!=null)
            {

                $downloadableFileRepository->link($menuItem,$downloadableFileForm->getChosen());

                return $this->back_to_menu_item_index($menuItem);
            }
            
        }

        return $this->renderForm('downloadable_file/new.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/new_unlinked", name="app_downloadable_file_new_unlinked", methods={"GET","POST"})
     */
    public function new_unlinked(Request $request, DownloadableFileRepository $downloadableFileRepository): Response {
        $downloadableFileForm = new DownloadableFileForm();
        $form = $this->createForm(SimpleFileType::class, $downloadableFileForm);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $downloadableFile = $downloadableFileRepository->createAndSaveFile($downloadableFileForm->getAttachment());

            return $this->redirectToRoute('app_downloadable_file_index');
 
        }

        return $this->renderForm('downloadable_file/new_unlinked.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}/unlink", name="app_downloadable_file_unlink", methods={"GET"})
     */
    public function unlink(Request $request, MenuItem $menuItem, DownloadableFileRepository $downloadableFileRepository): Response
    {
        $downloadableFileRepository->unlink($menuItem);
        
        return $this->back_to_menu_item_index($menuItem);
    }

    /**
     * @Route("/get/{id}", name="app_downloadable_file_download", methods={"GET"})
     */
    
    public function download(Request $request, DownloadableFile $downloadableFile):Response {
        // load the file from the filesystem
        $file = new File($downloadableFile->getPath());

        // rename the downloaded file
        return $this->file($file, $downloadableFile->getFilename());
    }

    private function back_to_menu_item_index(MenuItem $menuItem):Response {
        if ($menuItem->getParent() == null)
            return $this->redirectToRoute('app_menu_item_index', [], Response::HTTP_SEE_OTHER);
        else
            return $this->redirectToRoute('app_menu_item_index_level2',['id' => $menuItem->getParent()->getId()], Response::HTTP_SEE_OTHER);
    }

}
