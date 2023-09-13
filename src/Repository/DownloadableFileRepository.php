<?php

namespace App\Repository;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\MenuItem;
use App\Entity\DownloadableFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DownloadableFile>
 *
 * @method DownloadableFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method DownloadableFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method DownloadableFile[]    findAll()
 * @method DownloadableFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DownloadableFileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DownloadableFile::class);
    }

    public function createAndSaveFile(UploadedFile $my_file):DownloadableFile {

        $count = $this->count([]);

        $directory = "uploaded_files/"; //vanno in public/uploaded_files/ e mi sta bene così ...

        $current_index = $count + 1;

        $extension = $my_file->guessExtension();
        if (!$extension) {
            // extension cannot be guessed
            $extension = 'bin';
        }

        $my_file->move($directory, $current_index.'.'.$extension);

        $result = new DownloadableFile();
        $result->setPath($directory.$current_index.'.'.$extension);
        $result->setSize($my_file->getSize());
        $result->setMimeType($my_file->getClientMimeType());
        $result->setFilename($my_file->getClientOriginalName());

        $this->getEntityManager()->persist($result);

        $this->getEntityManager()->flush();

        return $result;
    }

    public function link(MenuItem $menuItem,DownloadableFile $downloadableFile): void {

        $menuItem->setDownloadableFile($downloadableFile);
        $downloadableFile->setMenuItem($menuItem);

        $this->getEntityManager()->flush();

    }    

    public function unlink(MenuItem $menuItem): void {

        $downloadableFile = $menuItem->getDownloadableFile();

        $menuItem->setDownloadableFile(null);
        $downloadableFile->setMenuItem(null);

        $this->getEntityManager()->flush();

    }

}
