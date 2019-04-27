<?php

namespace App\Controller;

use App\Entity\Area;
use App\Entity\Purpose;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/receipt")
 */
final class ReceiptController extends Controller
{
    /**
     * @Route("/{number}/{owner}/{purpose}/{amount}", name="receipt")
     *
     * @ParamConverter("area", options={"mapping": {"number": "number"}})
     * @ParamConverter("owner", options={"mapping": {"owner": "id"}})
     * @ParamConverter("purpose", options={"mapping": {"purpose": "id"}})
     */
    public function indexAction(Area $area, User $owner, Purpose $purpose, string $amount): Response
    {
        return $this->render('pd4.html.twig', [
            'area' => $area,
            'owner' => $owner,
            'purpose' => $purpose,
            'amount' => $amount,
        ]);
    }
}
