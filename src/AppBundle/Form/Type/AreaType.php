<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Model\AreaModel;
use AppBundle\Form\Transformer\UuidTransformer;
use AppBundle\Utils\FormUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class AreaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('size', Type\IntegerType::class, [
                'label' => 'Размер м2',
            ])
            ->add($builder->create('street', Type\ChoiceType::class, [
                'label' => 'Улица',
                'placeholder' => 'Выберите улицу',
                'required' => false,
                'choices' => FormUtils::arrayToChoices($options['streets'], 'name'),
                'multiple' => false,
            ])->addModelTransformer(new UuidTransformer()))
            ->add($builder->create('users', Type\ChoiceType::class, [
                'label' => 'Владельцы',
                'choices' => FormUtils::arrayToChoices($options['users'], 'realname'),
                'multiple' => true,
                'expanded' => true,
            ])->addModelTransformer(new UuidTransformer()));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => AreaModel::class,
            ])
            ->setRequired([
                'streets',
                'users',
            ]);
    }
}
