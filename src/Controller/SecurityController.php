<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Validator\Constraints\EntityExist;
use Doctrine\ORM\EntityManagerInterface;
use Grachev\TokenBundle\PayloadsProviderInterface;
use Grachev\TokenBundle\TokenGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Swift_Mailer;
use Swift_Message;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class SecurityController extends Controller
{
    private const TOKEN_SESSION_KEY = 'reset_password.token';

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
            ->getForm()
            ->handleRequest($request);

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

    /**
     * @Route("/forgot", name="password_forgot")
     */
    public function forgotPasswordAction(
        Request $request,
        TokenGeneratorInterface $generator,
        Swift_Mailer $mailer
    ): Response {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'label' => 'E-Mail',
                'constraints' => [
                    new EntityExist([
                        'field' => 'username',
                        'entity' => User::class,
                        'message' => 'Пользователя с таким E-Email не существует',
                    ]),
                    new NotBlank(),
                ],
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $token = $generator->disposable(['email' => $email]);

            $resetUrl = $this->generateUrl('password_reset', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
            $body = sprintf('Восстановить пароль: <a href="%s">ссылка</a>', $resetUrl);

            $message = (new Swift_Message('Восстановление пароля', $body, 'text/html'))
                ->setTo($email)
                ->setFrom('no-reply@astra50.ru');

            $mailer->send($message);

            $this->addFlash('info', 'Ссылка для востановления пароля отправлена на почту!');

            return $this->redirectToRoute('login');
        }

        return $this->render('security/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset/{token}", name="password_reset")
     */
    public function resetPasswordAction(
        Request $request,
        string $token,
        PayloadsProviderInterface $provider,
        EntityManagerInterface $em,
        EncoderFactoryInterface $encoderFactory
    ): Response {
        $session = $request->getSession();
        $payloads = $session->get(self::TOKEN_SESSION_KEY);

        if (null === $payloads) {
            $payloads = $provider->payloads($token);
            $session->set(self::TOKEN_SESSION_KEY, $payloads);
        }

        if (null === $payloads) {
            $this->addFlash('error', 'Ссылка устарела, попробуйте восстановить пароль ещё раз!');

            return $this->redirectToRoute('password_forgot');
        }

        $form = $this->createFormBuilder()
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'constraints' => [
                    new Length(['min' => 3]),
                ],
                'first_options' => [
                    'label' => 'Новый пароль',
                ],
                'second_options' => [
                    'label' => 'Повторите пароль',
                ],
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $session->remove(self::TOKEN_SESSION_KEY);

            $user = $em->getRepository(User::class)->findOneBy(['username' => $payloads['email']]);
            $user->changePassword($form->get('password')->getData(), $encoderFactory->getEncoder($user));

            $em->flush();

            $this->addFlash('success', 'Пароль изменён!');

            return $this->redirectToRoute('login');
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
