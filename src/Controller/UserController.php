<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Roles;
use Doctrine\ORM\EntityManagerInterface;
use ReflectionClass;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/users")
 *
 * @Security("is_granted(constant('App\\Roles::ADMIN'))")
 */
final class UserController extends Controller
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/", name="user_index")
     */
    public function indexAction(): Response
    {
        $users = $this->em->getRepository(User::class)->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/{id}", name="user_edit")
     */
    public function editAction(Request $request, User $user): Response
    {
        $form = $this->createFormBuilder($user, [
            'data_class' => User::class,
        ])
            ->add('realname', TextType::class, [
                'label' => 'Ф.И.О.',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Телефон',
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Роли',
                'choice_loader' => new CallbackChoiceLoader(function () {
                    $roles = (new ReflectionClass(Roles::class))->getConstants();

                    return array_combine($roles, $roles);
                }),
                'multiple' => true,
                'expanded' => true,
            ])
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
                if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
                    $event->getForm()->remove('roles');
                }
            })
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
