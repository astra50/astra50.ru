<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\PaymentPurpose;
use AppBundle\Form\Model\PaymentPurposeModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class PaymentPurposeType extends AbstractType
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
                    'Разово' => PaymentPurpose::SCHEDULE_ONCE,
                    'Ежемесячно' => PaymentPurpose::SCHEDULE_MONTHLY,
                ],
                'data' => PaymentPurpose::SCHEDULE_ONCE,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('calculation', Type\ChoiceType::class, [
                'label' => 'Начисление',
                'choices' => [
                    'На участок' => PaymentPurpose::CALCULATION_EACH,
                    'На Сотку' => PaymentPurpose::CALCULATION_SIZE,
                    'Разделить между участками' => PaymentPurpose::CALCULATION_SHARE,
                ],
                'data' => PaymentPurpose::CALCULATION_EACH,
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
                'data_class' => PaymentPurposeModel::class,
            ])
            ->setRequired([
                'areas',
            ]);
    }
}
