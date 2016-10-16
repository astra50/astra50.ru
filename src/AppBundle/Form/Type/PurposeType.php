<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Purpose;
use AppBundle\Form\Model\PurposeModel;
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
class PurposeType extends AbstractType
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
            ->add($builder->create('amount', Type\MoneyType::class, [
                'label' => 'Сумма',
                'currency' => 'RUB',
                'divisor' => 100,
            ])
                ->addModelTransformer(new MoneyTransformer())
            )
            ->add('schedule', Type\ChoiceType::class, [
                'label' => 'Расписание',
                'choices' => [
                    'Разово' => Purpose::SCHEDULE_ONCE,
                    'Ежемесячно' => Purpose::SCHEDULE_MONTHLY,
                ],
                'data' => Purpose::SCHEDULE_ONCE,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add('calculation', Type\ChoiceType::class, [
                'label' => 'Начисление',
                'choices' => [
                    'На участок' => Purpose::CALCULATION_EACH,
                    'На Сотку' => Purpose::CALCULATION_SIZE,
                    'Разделить между участками' => Purpose::CALCULATION_SHARE,
                ],
                'data' => Purpose::CALCULATION_EACH,
                'multiple' => false,
                'expanded' => true,
            ])
            ->add($builder->create('areas', Type\ChoiceType::class, [
                'label' => 'Участки',
                'choices' => FormUtils::arrayToChoices($options['areas'], 'number'),
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
                'data_class' => PurposeModel::class,
            ])
            ->setRequired([
                'areas',
            ]);
    }
}
