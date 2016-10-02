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
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Выберите платёжную цель',
            ])
            ->add('area', Type\ChoiceType::class, [
                'label' => 'Участки',
                'choices' => $options['areas'],
                'multiple' => false,
                'expanded' => false,
                'placeholder' => 'Выберите участок',
            ])
            ->add($builder->create('amount', Type\MoneyType::class, [
                'label' => 'Сумма',
                'currency' => 'RUB',
                'divisor' => 100,
            ])
                ->addModelTransformer(new MoneyTransformer())
            );
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
