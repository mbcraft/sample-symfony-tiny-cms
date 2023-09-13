<?php

namespace App\Repository;

use App\RepositoryTraits\OrderingManagerTrait;
use App\Entity\Page;
use App\Entity\Section;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Section>
 *
 * @method Section|null find($id, $lockMode = null, $lockVersion = null)
 * @method Section|null findOneBy(array $criteria, array $orderBy = null)
 * @method Section[]    findAll()
 * @method Section[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SectionRepository extends ServiceEntityRepository
{
    use OrderingManagerTrait;

    const MY_ORDER_FIELD = 'order_val';
    const MY_ORDER_GETTER = "getOrderVal";
    const MY_ORDER_SETTER = "setOrderVal";

    const MY_ORDER_GROUP_FIELDS = ['page'];
    const MY_ORDER_GROUP_GETTERS = ['getPage'];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Section::class);
    }

    public function add(Page $page, Section $section): void
    {
        $page->getSections()->add($section);

        $section->setPage($page);

        $this->setOrderingForEntity($section);

        $this->getEntityManager()->persist($section);

        $this->getEntityManager()->flush();
        
    }

    public function remove(Page $page, Section $section): void
    {
        $page->getSections()->remove($section);

        $this->getEntityManager()->remove($section);

        $this->getEntityManager()->flush();
    }

    public function update(Section $section) {

        $this->getEntityManager()->flush();

    }

    public function findPageSections(Page $page) {
        return $this->findBy(['page' => $page]);
    }

}
