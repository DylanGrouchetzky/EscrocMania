<?php

namespace App\Frontend\Controller;

use App\Entity\User;
use App\Form\ContactType;
use App\Form\SignInType;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CustomerController extends AbstractController
{
    private $hash;
    private $em;
    private $userRepository;

    public function __construct(UserPasswordHasherInterface $hash,EntityManagerInterface $em, UserRepository $userRepository){
        $this->hash = $hash;
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    #[Route('/profil', name: 'profil')]
    public function profil(): Response
    {
        $user = $this->getUser();
        return $this->render('frontend/pages/profil.html.twig',[
            'user' => $user,
        ]);
    }

    #[Route('/profil/{idUser}/{field}/{type}', name: 'profil_edit')]
    public function editProfil($idUser,$field,$type): Response
    {
        return $this->render('frontend/_parts/edit_profil.html.twig',[
            'idUser' => $idUser,
            'field' => $field,
            'typeInput' => $type,
        ]);
    }

    #[Route('/profil/{idUser}/{field}', name: 'save_new_data_profil')]
    public function saveNewDataProfil(Request $request,$idUser,$field): Response
    {
        $user = $this->userRepository->findOneBy(['id' => $idUser]);
        if (!$user) {
            $this->addFlash('error', 'Une erreur est parvenue');
            return $this->render('frontend/pages/profil.html.twig',[
                'user' => $user,
            ]);
        }
        if ($field == 'pseudo') {
            $user->setPseudo($request->request->get('newValue'));
        }elseif ($field == 'firstName') {
            $user->setFirstname($request->request->get('newValue'));
        }elseif ($field == 'lastName') {
            $user->setLastname($request->request->get('newValue'));
        }elseif ($field == 'email') {
            $user->setEmail($request->request->get('newValue'));
        }
        $this->em->persist($user);
        $this->em->flush();
        $this->addFlash('success', 'La donner a bien était enregistrer');
        return $this->render('frontend/pages/profil.html.twig',[
            'user' => $user,
        ]);
    }

    #[Route('/inscription', name: 'sign_in')]
    public function signIn(Request $request): Response
    {
        $signInForm = $this->createForm(SignInType::class);
        $signInForm->handleRequest($request);
        if ($signInForm->isSubmitted() && $signInForm->isValid()) {
            $data = $signInForm->getData();
            $createdUser = new DateTimeImmutable();
            $birthUser = $data['birthday'];
            $age = $birthUser->diff(new DateTimeImmutable())->format('%y');
            $user = new User();
            $user->setEmail($data['email'])
            ->setRoles(['ROLE_USER'])
            ->setPassword($this->hash->hashPassword($user, $data['password']))
            ->setFirstname($data['firstName'])
            ->setLastname($data['lastName'])
            ->setPseudo($data['speudo'])
            ->setBirthday($data['birthday'])
            ->setAge($age)
            ->setCreatedAt($createdUser)
            ->setModifiedAt($createdUser);
            $this->em->persist($user);
            $this->em->flush();
            $this->addFlash('success', 'Votre compte a bien était crée');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('frontend/pages/sign_in.html.twig',[
            'signInForm' => $signInForm->createView(),
        ]);
    }

    #[Route('/contact', name: 'contact')]
    public function contactPage(Request $request, MailerInterface $mailer): Response
    {
        $contactForm = $this->createForm(ContactType::class);
        $contactForm->handleRequest($request);
        if ($contactForm->isSubmitted() && $contactForm->isValid()){
            $data = $contactForm->getData();
            $email = (new Email())
            ->from($data['email'])
            ->to('escromania3993@outlook.fr')
            ->subject('Demande de contact')
            ->html('<p>De : '.$data['firstName'].' '.$data['lastName'].'</p><p>Email: '.$data['email'].'</p><p>Message : <br><br> '.$data['message'].'</p><p>Ce message provient du site Escromania</p>');

            $mailer->send($email);
            $this->addFlash('success', 'Votre message à bien était envoyé');
        }
        return $this->render('frontend/pages/contact.html.twig',[
            'formContact' => $contactForm->createView(),
        ]);
    }
}
