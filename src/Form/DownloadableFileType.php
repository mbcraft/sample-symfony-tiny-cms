<?php

namespace App\Form;

use App\Entity\DownloadableFile;
use App\FormData\DownloadableFileForm;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class DownloadableFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('attachment', FileType::class, ['required' => false, 'label' => 'Allegato da scaricare'])
            ->add('chosen', EntityType::class, ['class' => DownloadableFile::class
                ,'choice_label' => 'filename','query_builder' => function(EntityRepository $repository) {
                    $qb = $repository->createQueryBuilder('d');
                    $qb->where($qb->expr()->isNull('d.menuItem'));

                    return $qb;
                },'required' => false, 'label' => 'Oppure seleziona un allegato già caricato in precedenza'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DownloadableFileForm::class,
        ]);
    }
}
