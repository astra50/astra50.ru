<?php

namespace AppBundle\Form;

use AppBundle\Model\AccountModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
class AccountType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'disabled' => true,
            ])
            ->add('realname', TextType::class, [
                'label' => 'Полное имя',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Телефон',
            ])
            ->add('placement', TextType::class, [
                'label' => 'Номер участка',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => AccountModel::class,
            ]);
    }
}
