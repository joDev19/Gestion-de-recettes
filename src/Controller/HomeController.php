<?php

namespace App\Controller;

use App\ContactDTO;
use App\Form\ContactType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    public function __construct(private MailerInterface $mailer) {}
    #[Route(path: "/", name: "home")]
    function index(Request $request): Response
    {
        return $this->render('home/index.html.twig');
    }
    #[Route("/contact", name: "contact")]
    public function contact(Request $request): Response
    {
        $contactDTO = new ContactDTO();
        $form = $this->createForm(ContactType::class, $contactDTO);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //dd($contactDTO);
            $this->sendMail(
               $contactDTO
            );
            $this->addFlash("success", "Email envoyé avec succes");
            return $this->redirectToRoute('home');
        }
        return $this->render('home/contact.html.twig', ['form' => $form]);
    }
    public function sendMail(
        ContactDTO $data,
    ) {
        $email = new TemplatedEmail()
            ->from($data->email)
            ->to(new Address($data->service.'@entreprise.com'))
            ->subject("Nouvel email de contact depuis votre site")
            ->htmlTemplate('email/contact_email_template.html.twig',)
            ->context([
                'nom' => $data->nom,
                'message' => $data->message,
            ]);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            dd("error", $e);
        }
    }
}
