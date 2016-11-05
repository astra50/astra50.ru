<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\Model\PaymentModel;
use AppBundle\Form\Transformer\MoneyTransformer;
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
            ->add('purpose', Type\ChoiceType::class, [
                'label' => 'Платёжная цель',
                'choices' => $options['purposes'],
                'choice_label' => 'name',
                'choice_value' => 'id.toString',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Выберите платёжную цель',
                'translation_domain' => false,
            ])
            ->add('area', Type\ChoiceType::class, [
                'label' => 'Участки',
                'choices' => $options['areas'],
                'choice_label' => 'number',
                'choice_value' => 'id.toString',
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Выберите участок',
                'translation_domain' => false,
            ])
            ->add($builder->create('amount', Type\MoneyType::class, [
                'label' => 'Сумма',
                'currency' => 'RUB',
                'divisor' => 100,
                'translation_domain' => false,
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
                'translation_domain' => false,
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
