<?php

namespace App\Form;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the custom form field type used to add a hidden entity
 *
 * See https://symfony.com/doc/current/form/create_custom_field_type.html
 */
class EntityHiddenType extends HiddenType implements DataTransformerInterface
{

    /** @var ManagerRegistry $dm */
    private $dm;

    /** @var string $entityClass */
    private $entityClass;

    /**
     *
     * @param ManagerRegistry $doctrine
     */
    public function __construct(ManagerRegistry $doctrine)
    {
        $this->dm = $doctrine;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->define('entityClass');

        $resolver->setDefaults(['compound' => false]);
    }

    /**
     *
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Set class, eg: App\Entity\RuleSet
        if (!isset($options['entityClass'])) throw new \Exception("It is mandatory to declare the entity class!");

        $this->entityClass = $options['entityClass'];
        $builder->addModelTransformer($this);
    }

    public function transform($data): string
    {
        // Modified from comments to use instanceof so that base classes or interfaces can be specified
        if (null === $data || !$data instanceof $this->entityClass) {
            return '';
        }

        $res = $data->getId();

        return $res;
    }

    public function reverseTransform($data)
    {
        if (!$data) {
            return null;
        }

        $res = null;
        try {
            $rep = $this->dm->getRepository($this->entityClass);
            $res = $rep->findOneBy(array(
                "id" => $data
            ));
        }
        catch (\Exception $e) {

            throw new TransformationFailedException($e->getMessage());
        }

        if ($res === null) {
            throw new TransformationFailedException(sprintf('A %s with id "%s" does not exist!', $this->entityClass, $data));
        }

        return $res;
    }
}