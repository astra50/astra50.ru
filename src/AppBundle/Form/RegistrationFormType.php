<?php

namespace AppBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class RegistrationFormType extends \FOS\UserBundle\Form\Type\RegistrationFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('realname', null, [
                'label' => 'ФИО',
            ])
            ->add('phone', null, [
                'label' => 'Телефон',
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Пароль',
            ]);
    }
}
