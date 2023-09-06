<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Tag;
use App\Entity\Tva;
use App\Entity\User;
use App\Entity\Order;
use DateTimeImmutable;
use App\Entity\Address;
use App\Entity\Comment;
use App\Entity\Payment;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\RowOrder;
use App\Entity\PaymentType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hash;
    public function __construct(UserPasswordHasherInterface $hash){
        $this->hash = $hash;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // LES NOUVELLES FIXTURES
        for ($i=0; $i < 5; $i++) { 
            $user = new User();
            $createdUser = new DateTimeImmutable($faker->date());
            $birthUser = $faker->dateTime();
            $age = $birthUser->diff(new DateTimeImmutable())->format('%y');

            $user
            ->setFirstName("Firstname ".$i)
            ->setLastName("Lastname ".$i)
            ->setEmail($faker->email())
            ->setBirthday($birthUser)
            ->setPseudo("Pseudo ".$i)
            ->setAvatar("test")
            ->setRoles(["ROLE_USER"])
            ->setAge($age)
            ->setPassword($this->hash->hashPassword($user, "hello"))
            ->setCreatedAt($createdUser)
            ->setModifiedAt($createdUser);

            $manager->persist($user);
        }

        // CATEGORY
        $category1 = new Category();
        $category1->setName("Jeux")
        ->setDescription($faker->sentence(20))
        ->setSlug("jeux");
        $manager->persist($category1);

        $category2 = new Category();
        $category2->setName("Goodies")
        ->setDescription($faker->sentence(20))
        ->setSlug("goodies");
        $manager->persist($category2);

        $category3 = new Category();
        $category3->setName("Posters")
        ->setDescription($faker->sentence(20))
        ->setSlug("posters");
        $manager->persist($category3);

        // TAG
        
        $tag1 = new Tag();
        $tag1->setName('Rpg');
        $manager->persist($tag1);
        
        $tag2 = new Tag();
        $tag2->setName('Plateform');
        $manager->persist($tag2);
        
        $tag3 = new Tag();
        $tag3->setName('MMORPG');
        $manager->persist($tag3);
        
        $tag4 = new Tag();
        $tag4->setName('MOBA');
        $manager->persist($tag4);
        
        $tag5 = new Tag();
        $tag5->setName('Horreur');
        $manager->persist($tag5);
        
        $tag6 = new Tag();
        $tag6->setName("Hack'n Slash");
        $manager->persist($tag6);
        
        $tag7 = new Tag();
        $tag7->setName('RTS');
        $manager->persist($tag7);
        
        $tag8 = new Tag();
        $tag8->setName('FPS');
        $manager->persist($tag8);

        for ($i=0; $i < 100; $i++) {
            $product = new Product();
            $product->setName("Jeu ".$i)
            ->setPrice($faker->numberBetween(2000, 10000))
            ->setQuantity($faker->numberBetween(1, 40))
            ->setCreatedAt(new DateTimeImmutable($faker->date()))
            ->setPublishedAt(new DateTimeImmutable($faker->date()))
            ->setDescription($faker->sentence(20))
            ->setSlug("jeu-".$i)
            ->setDiscount($faker->numberBetween(0, 75))
            ->setCategory($category1);
            $manager->persist($product);
        }

        for ($i=0; $i < 100; $i++) {
            $product = new Product();
            $product->setName("Goodies ".$i)
            ->setPrice($faker->numberBetween(2000, 10000))
            ->setQuantity($faker->numberBetween(1, 40))
            ->setCreatedAt(new DateTimeImmutable($faker->date()))
            ->setPublishedAt(new DateTimeImmutable($faker->date()))
            ->setDescription($faker->sentence(20))
            ->setSlug("goodies-".$i)
            ->setDiscount($faker->numberBetween(0, 75))
            ->setCategory($category2);
            $manager->persist($product);
        }

        for ($i=0; $i < 100; $i++) {
            $product = new Product();
            $product->setName("Poster ".$i)
            ->setPrice($faker->numberBetween(2000, 10000))
            ->setQuantity($faker->numberBetween(1, 40))
            ->setCreatedAt(new DateTimeImmutable($faker->date()))
            ->setPublishedAt(new DateTimeImmutable($faker->date()))
            ->setDescription($faker->sentence(20))
            ->setSlug("poster-".$i)
            ->setDiscount($faker->numberBetween(0, 75))
            ->setCategory($category3);
            $manager->persist($product);
        }

        // // USERS
        // for($i = 0; $i < 5 ; $i++){
        //     $user = new User();
        //     $createdUser = new DateTimeImmutable($faker->date());
        //     $birthUser = $faker->dateTime();
        //     $age = $birthUser->diff(new DateTimeImmutable())->format('%y');
        //     $user
        //     ->setFirstName("Firstname ".$i)
        //     ->setLastName("Lastname ".$i)
        //     ->setEmail($faker->email())
        //     ->setBirthday($birthUser)
        //     ->setPseudo("Pseudo ".$i)
        //     ->setAvatar("test")
        //     ->setRoles(["ROLE_USER"])
        //     ->setPassword($faker->password())
        //     ->setAge($age)
        //     ->setCreatedAt($createdUser)
        //     ->setModifiedAt($createdUser);

        //     // ADDRESS
        //     $address = new Address();
        //     $address->setLine($faker->sentence(20))
        //     ->setCity($faker->word())
        //     ->setZipcode($faker->randomNumber(5, true))
        //     ->setCountry($faker->word())
        //     ->setState($faker->word())
        //     ->addUser($user);

        //     // PRODUCT
        //     for($i = 0; $i < 2 ; $i++){
        //         $product = new Product();
        //         $product->setName($faker->sentence(2))
        //         ->setPrice($faker->randomFloat(2, 20, 80))
        //         ->setQuantity($faker->numberBetween(1, 40))
        //         ->setCreatedAt(new DateTimeImmutable($faker->date()))
        //         ->setPublishedAt(new DateTimeImmutable($faker->date()))
        //         ->setDescription($faker->sentence(20))
        //         ->setSlug($faker->slug())
        //         ->setDiscount($faker->numberBetween(0, 75));

        //         // CATEGORY
        //         $category = new Category();
        //         $category->setName($faker->sentence(2))
        //         ->setDescription($faker->sentence(20))
        //         ->setSlug($faker->slug())
        //         ->addProduct($product);
                
        //         // TAG
        //         $tag = new Tag();
        //         $tag->setName($faker->word())
        //         ->addProduct($product);

        //         // COMMENT
        //         $comment = new Comment();
        //         $comment->setText($faker->sentence(10))
        //         ->setNote($faker->numberBetween(0, 5))
        //         ->setCreatedAt(new DateTimeImmutable($faker->date()))
        //         ->setUser($user)
        //         ->setProduct($product);
    
        //         // TVA
        //         $tva = new Tva();
        //         $tva->setName($faker->word())
        //         ->setRate($faker->numberBetween(1, 20))
        //         ->addProduct($product);

        //         // ORDER
        //         $order = new Order();
        //         $order->setCreatedAt(new DateTimeImmutable($faker->date()))
        //         ->setStep($faker->numberBetween(0, 2))
        //         ->setUser($user);

        //         // ROWORDER
        //         $row = new RowOrder();
        //         $row->setNameProduct($product->getName())
        //         ->setDescriptionProduct($product->getDescription())
        //         ->setQuantity($faker->numberBetween(1, 3))
        //         ->setUnitPrice($product->getPrice())
        //         ->setProduct($product)
        //         ->setBasketOrder($order);

        //         // PAYMENT
        //         $payment = new Payment();
        //         $payment->setReference($faker->ean13())
        //         ->setStatut($faker->word())
        //         ->setCreatedAt(new DateTimeImmutable($faker->date()))
        //         ->setPaymentOrder($order);

        //         // PAYMENTTYPE
        //         $paymentType = new PaymentType();
        //         $paymentType->setName($faker->sentence(2))
        //         ->setDescription($faker->sentence(20))
        //         ->setPicture($faker->image(null, 640, 480))
        //         ->setPayment($payment);

        //         $manager->persist($paymentType);
        //         $manager->persist($payment);
        //         $manager->persist($row);
        //         $manager->persist($order);
        //         $manager->persist($tva);
        //         $manager->persist($comment);
        //         $manager->persist($product);
        //         $manager->persist($tag);
        //         $manager->persist($category);
        //     }
        //     $manager->persist($user);
        //     $manager->persist($address);
        // }

        $manager->flush();
    }
}
