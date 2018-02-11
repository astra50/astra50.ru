<?php

declare(strict_types=1);

namespace App\Form\Type;

use App\Form\Model\AreaModel;
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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('size', Type\IntegerType::class, [
                'label' => 'Размер м2',
            ])
            ->add('street', Type\ChoiceType::class, [
                'label' => 'Улица',
                'placeholder' => 'Выберите улицу',
                'required' => false,
                'choices' => $options['streets'],
                'choice_label' => 'name',
                'choice_value' => 'id',
                'multiple' => false,
            ])
            ->add('users', Type\ChoiceType::class, [
                'label' => 'Владельцы',
                'choices' => $options['users'],
                'choice_label' => 'realname',
                'choice_value' => 'id',
                'multiple' => true,
                'expanded' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
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
