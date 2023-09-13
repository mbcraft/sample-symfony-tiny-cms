<?php

namespace App\Repository;

use App\Entity\Page;
use App\Entity\DownloadableFile;
use App\Entity\MenuItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\NoResultException;
use App\RepositoryTraits\OrderingManagerTrait;
/**
 * @extends ServiceEntityRepository<MenuItem>
 *
 * @method MenuItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method MenuItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method MenuItem[]    findAll()
 * @method MenuItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MenuItemRepository extends ServiceEntityRepository
{

    //dichiaro il trait per la gestione degli ordinamenti
    use OrderingManagerTrait;

    //dichiaro le costanti necessarie al funzionamento
    const MY_ORDER_FIELD = "order_val";
    const MY_ORDER_GETTER = "getOrderVal";
    const MY_ORDER_SETTER = "setOrderVal";
    const MY_ORDER_GROUP_GETTERS = ['getParent'];
    const MY_ORDER_GROUP_FIELDS = ['parent'];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MenuItem::class);
    }

    public function add(MenuItem $entity): void
    {
        $this->setOrderingForEntity($entity);

        $this->getEntityManager()->persist($entity);

        $this->getEntityManager()->flush();
        
    }

    public function remove(MenuItem $menuItem): void
    {
        $this->recursiveRemove($menuItem);

        $this->getEntityManager()->flush();
        
    }

    private function recursiveRemove(MenuItem $menuItem) : void
    {
        $pageRepository = $this->getEntityManager()->getRepository(Page::class);

        $downloadableFileRepository = $this->getEntityManager()->getRepository(DownloadableFile::class);

        if ($menuItem->getPage() != null)
        {
            $pageRepository->unlink($menuItem,$menuItem->getPage());
        }

        if ($menuItem->getDownloadableFile() != null) {
            $downloadableFileRepository->unlink($menuItem,$menuItem->getDownloadableFile());
        }

        $child_elements = $this->findBy(['parent' => $menuItem]);

        foreach ($child_elements as $elem) {
            $this->recursiveRemove($elem);
        }

        $this->getEntityManager()->remove($menuItem);
    }

    public function update(MenuItem $entity) {

        $this->getEntityManager()->flush();

    }

}
