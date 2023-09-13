<?php

namespace App\Repository;

use App\Entity\Section;
use App\Entity\MenuItem;
use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Page>
 *
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function add(?MenuItem $menuItem, Page $page): void
    {
        $this->getEntityManager()->persist($page);

        if ($menuItem) {
            $menuItem->setPage($page);
            $page->setMenuItem($menuItem);
        }

        $this->getEntityManager()->flush();
    }

    public function update(Page $page): void {

        $this->getEntityManager()->flush();

    }

    public function link(MenuItem $menuItem, Page $page): void 
    {
        $menuItem->setPage($page);
        $page->setMenuItem($menuItem);

        $this->getEntityManager()->flush();
    }

    public function unlink(MenuItem $menuItem): void
    {
        $page = $menuItem->getPage();

        $menuItem->setPage(null);
        $page->setMenuItem(null);

        $this->getEntityManager()->flush();
        
    }

}
