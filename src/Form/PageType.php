<?php

namespace App\Form;

use App\Entity\Page;
use App\FormData\PageForm;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class PageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['required' => false, 'label' => 'Inserisci un titolo per una nuova pagina'])
            ->add('chosen', EntityType::class, ['class' => Page::class
                ,'choice_label' => 'title','query_builder' => function(EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('p');
                    $qb->where($qb->expr()->isNull('p.menuItem'));

                    return $qb;
                },'required' => false, 'label' => 'Oppure seleziona una pagina non associata già creata in precedenza'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PageForm::class,
        ]);
    }
}
