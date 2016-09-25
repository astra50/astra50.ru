<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\PaymentType;
use AppBundle\Form\Model\PaymentTypeModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class PaymentTypeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', Type\TextType::class, [
                'label' => 'Наименование',
            ])
            ->add($builder->create('sum', Type\MoneyType::class, [
                'label' => 'Сумма',
                'currency' => 'RUB',
                'divisor' => 100,
            ])
                ->addModelTransformer(new CallbackTransformer(function ($value) {
                    return $value;
                }, function ($value) {
                    return (int) $value;
                }))
            )
            ->add('schedule', Type\ChoiceType::class, [
                'label' => 'Расписание',
                'choices' => [
                    'Разово' => PaymentType::SCHEDULE_ONCE,
                    'Ежемесячно' => PaymentType::SCHEDULE_MONTHLY,
                ],
                'data' => PaymentType::SCHEDULE_ONCE,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('calculation', Type\ChoiceType::class, [
                'label' => 'Начисление',
                'choices' => [
                    'На участок' => PaymentType::CALCULATION_EACH,
                    'На Сотку' => PaymentType::CALCULATION_SIZE,
                    'Разделить между участками' => PaymentType::CALCULATION_SHARE,
                ],
                'data' => PaymentType::CALCULATION_EACH,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('areas', Type\ChoiceType::class, [
                'label' => 'Участки',
                'choices' => $options['areas'],
                'multiple' => true,
                'expanded' => true,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => PaymentTypeModel::class,
            ])
            ->setRequired([
                'areas',
            ]);
    }
}
