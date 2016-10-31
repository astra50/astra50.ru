<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Model\PaymentModel;
use AppBundle\Form\Transformer\MoneyTransformer;
use AppBundle\Form\Transformer\UuidTransformer;
use AppBundle\Utils\FormUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class PaymentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add($builder->create('purpose', Type\ChoiceType::class, [
                'label' => 'Платёжная цель',
                'choices' => FormUtils::arrayToChoices($options['purposes'], 'name'),
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Выберите платёжную цель',
            ])->addModelTransformer(new UuidTransformer()))
            ->add($builder->create('area', Type\ChoiceType::class, [
                'label' => 'Участки',
                'choices' => FormUtils::arrayToChoices($options['areas'], 'number'),
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Выберите участок',
            ])->addModelTransformer(new UuidTransformer()))
            ->add($builder->create('amount', Type\MoneyType::class, [
                'label' => 'Сумма',
                'currency' => 'RUB',
                'divisor' => 100,
            ])->addModelTransformer(new MoneyTransformer()))
            ->add('isPositive', Type\ChoiceType::class, [
                'label' => 'Тип',
                'choices' => [
                    'Начисление' => true,
                    'Списание' => false,
                ],
                'multiple' => false,
                'expanded' => true,
                'required' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => PaymentModel::class,
            ])
            ->setRequired([
                'areas',
                'purposes',
            ]);
    }
}
