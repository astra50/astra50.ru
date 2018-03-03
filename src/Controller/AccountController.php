<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/account")
 *
 * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
 */
final class AccountController extends Controller
{
    /**
     * @Route(name="account")
     */
    public function indexAction(Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();

        $form = $this->createFormBuilder($user, [])
            ->add('email', EmailType::class, [
                'disabled' => true,
                'property_path' => 'username',
            ])
            ->add('realname', TextType::class, [
                'label' => 'Полное имя',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Телефон',
            ])
            ->getForm()
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Изменения сохранены.');
        }

        return $this->render('account/show.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
