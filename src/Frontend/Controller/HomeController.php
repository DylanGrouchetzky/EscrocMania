<?php

namespace App\Frontend\Controller;

use App\Entity\User;
use App\Form\ContactType;
use App\Form\SignInType;
use App\Repository\TagRepository;
use App\Repository\CommentRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HomeController extends AbstractController
{
    private $tagRepository;
    private $productRepository;
    private $categoryRepository;
    private $commentRepository;
    private $params;
    private $hash;
    private $em;

    public function __construct(TagRepository $tagRepository, 
    ProductRepository $productRepository, 
    CategoryRepository $categoryRepository,
    CommentRepository $commentRepository,
    ContainerBagInterface $params,
    UserPasswordHasherInterface $hash,
    EntityManagerInterface $em){
        $this->tagRepository = $tagRepository;
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->commentRepository = $commentRepository;
        $this->params = $params;
        $this->hash = $hash;
        $this->em = $em;
    }
    
    #[Route('/', name: 'home')]
    public function home(): Response
    {
        $tags = $this->tagRepository->findAll();
        $lastsProducts = $this->productRepository->findBy([], ["published_at" => "DESC"], 15);
        $discountsProducts = $this->productRepository->getRandomProduct(4);
        return $this->render('frontend/pages/home.html.twig', [
            'tags' => $tags,
            'lastsProducts' => $lastsProducts,
            'discountsProducts' => $discountsProducts
        ]);
    }

    #[Route('/mentions-legales', name: 'mention_legales')]
    public function mentionsLegale(): Response
    {
        return $this->render('frontend/pages/mentions_legales.html.twig');
    }

    #[Route('/politique-de-confidentialite', name: 'politique_de_confidentialite')]
    public function politiqueDeConfidentialite(): Response
    {
        return $this->render('frontend/pages/politique_de_confidentialite.html.twig');
    }

    #[Route('/category/{slugCategory}', name: 'view_category')]
    public function viewAllGame($slugCategory): Response
    {
        $category = $this->categoryRepository->findOneBy(["slug" => $slugCategory]);
        $products = $this->productRepository->findBy(['category' => $category],['published_at' => 'ASC'],$this->params->get('limitProductCategory'));
        if ($slugCategory == 'jeux') {
            $bg = 'img/bg-game.jpg';
        }elseif ($slugCategory == 'goodies') {
            $bg = 'img/bg-goodies.jpg';
        }elseif ($slugCategory == 'poster') {
            $bg = 'img/bg-poster.jpg';
        }else{
            $bg = null;
        }
        return $this->render('frontend/pages/category.html.twig',[
            'category' => $slugCategory,
            'bg' => $bg,
            'products' => $products,
        ]);
    }

    #[Route('/next-category/{slugCategory}/{start}/{action}', name: 'view_category_next_product')]
    public function nextProduct($slugCategory,$start,$action){
        
        $category = $this->categoryRepository->findOneBy(["slug" => $slugCategory]);

        if ($action == 'next') {
            $start = $start + $this->params->get('limitProductCategory');
            $nextProduct = $this->productRepository->findBy(['category' => $category],['published_at' => 'ASC'],1,$start + $this->params->get('limitProductCategory'));
            if (!$nextProduct) {
                $classButtonNext = "disabled";
            }else{
                $classButtonNext = null;
            }
            $classButtonPrev = null;
        }elseif ($action == 'prev') {
            $start = $start - $this->params->get('limitProductCategory');
            if ($start == 0 || $start < 0) {
                $classButtonPrev = "disabled";
            }else{
                $classButtonPrev = null;
            }
            $classButtonNext = null;
        }

        $products = $this->productRepository->findBy(['category' => $category],['published_at' => 'ASC'],$this->params->get('limitProductCategory'),$start);
        
        return $this->render('frontend/_parts/nextProduct.html.twig',[
            'category' => $slugCategory,
            'products' => $products,
            'start' => $start,
            'classButtonNext' => $classButtonNext,
            'classButtonPrev' => $classButtonPrev,
        ]);
    }

    #[Route('/produit/{idProduct}', name: 'view_product')]
    public function viewProduct(Request $request ,$idProduct): Response
    {
        $product = $this->productRepository->findOneBy(["id" => $idProduct]);
        $comments = $this->commentRepository->findBy(["product" => $product]);
        $session = $request->getSession();
        return $this->render('frontend/pages/product.html.twig', [
            "product" => $product,
            "comments" => $comments
        ]);
    }

    #[Route('/search-product', name: 'search_product')]
    public function searcProduct(): Response
    {
        return $this->render('frontend/pages/search.html.twig');
    }

    #[Route('/contact', name: 'contact')]
    public function contactPage(Request $request, MailerInterface $mailer): Response
    {
        $contactForm = $this->createForm(ContactType::class);
        $contactForm->handleRequest($request);
        if ($contactForm->isSubmitted() && $contactForm->isValid()){
            $data = $contactForm->getData();
            $email = (new Email())
            ->from('escromania3993@outlook.fr')
            ->to('escromania3993@outlook.fr')
            ->subject('test')
            ->text('Ceci est un test');

            $mailer->send($email);
        }
        return $this->render('frontend/pages/contact.html.twig',[
            'formContact' => $contactForm->createView(),
        ]);
    }

    #[Route('/profil', name: 'profil')]
    public function profil(Request $request): Response
    {
        $user = $this->getUser();
        return $this->render('frontend/pages/profil.html.twig',[
            'user' => $user,
        ]);
    }

    #[Route('/panier', name: 'panier')]
    public function panier(SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $panierWithData = [];
        foreach ($panier as $id => $quantity) {
            $panierWithData[] = [
                'product' => $this->productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = 0;
        foreach ($panierWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }
        return $this->render('frontend/pages/panier.html.twig',[
            'items' => $panierWithData,
            'total' => $total,
        ]);
    }

    #[Route('/panier/add/{id}', name: 'panier_add')]
    public function panierAdd(SessionInterface $session, $id): Response
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            $panier[$id]++;
        }else{
            $panier[$id] = 1;
        }
        $session->set('panier', $panier);
        return $this->redirectToRoute('panier');
    }

    #[Route('/panier/remove/{id}', name: 'panier_remove')]
    public function panierRemove(SessionInterface $session, $id): Response
    {
        $panier = $session->get('panier', []);
        if (!empty($panier[$id])) {
            unset($panier[$id]);
        }
        $session->set('panier', $panier);
        return $this->redirectToRoute('panier');
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
}
