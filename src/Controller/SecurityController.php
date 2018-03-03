<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function loginAction(AuthenticationUtils $authUtils): Response
    {
        return $this->render('security/login.html.twig', [
            'error' => $authUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/registration", name="registration")
     */
    public function registrationAction(
        Request $request,
        EntityManagerInterface $em,
        EncoderFactoryInterface $encoderFactory
    ): Response {
        $user = new User();

        $form = $this->createFormBuilder($user, [
            'action' => $this->generateUrl('registration'),
            'data_class' => User::class,
            'label' => false,
        ])
            ->add('email', EmailType::class, [
                'label' => 'E-Mail',
                'property_path' => 'username',
            ])
            ->add('realname', TextType::class, [
                'label' => 'Фамилия Имя Отчество',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Телефон',
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Пароль',
                'mapped' => false,
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->changePassword($form->get('password')->getData(), $encoderFactory->getEncoder($user));

            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
