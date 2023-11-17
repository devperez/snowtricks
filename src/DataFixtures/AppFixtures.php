<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\User;
use App\Entity\Media;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Enums\TrickCategories;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Let's create some accounts
        $user1 = new User();
        $user1->setName('Pierre');
        $user1->setPassword(password_hash('123456', PASSWORD_BCRYPT));
        $user1->setEmail('pierre@email.com');
        $user1->setPhoto('/images/avatar2-65576e0293d06.png');
        $user1->setIsVerified('1');

        $manager->persist($user1);

        $user2 = new User();
        $user2->setName('David');
        $user2->setPassword(password_hash('123456', PASSWORD_BCRYPT));
        $user2->setEmail('david@email.com');
        $user2->setPhoto('/images/1000-F-223507324-jKl7xbsaEdUjGr42WzQeSazKRighVDU4-65576d587903b.jpg');
        $user2->setIsVerified('1');

        $manager->persist($user2);

        $user3 = new User();
        $user3->setName('Paul');
        $user3->setPassword(password_hash('123456', PASSWORD_BCRYPT));
        $user3->setEmail('paul@email.com');
        $user3->setPhoto('/images/logo-6557493f9c29e.webp');
        $user3->setIsVerified('0');

        $manager->persist($user3);

        $manager->flush();

        // Let's create some tricks
        $trick1 = new Trick();
        $trick1->setUser($user1);
        $trick1->setName('Mute');
        $trick1->setDescription('Saisie de la carre frontside de la planche entre les deux pieds avec la main avant.');
        $trick1->setCategory(TrickCategories::Grab);

        $manager->persist($trick1);

        $trick2 = new Trick();
        $trick2->setUser($user2);
        $trick2->setName('Sad');
        $trick2->setDescription('Saisie de la carre backside de la planche, entre les deux pieds, avec la main avant.');
        $trick2->setCategory(TrickCategories::Grab);
        
        $manager->persist($trick2);
        
        $trick3 = new Trick();
        $trick3->setUser($user3);
        $trick3->setName('Indy');
        $trick3->setDescription('Saisie de la carre frontside de la planche entre les deux pieds avec la main arrière');
        $trick3->setCategory(TrickCategories::Grab);
        
        $manager->persist($trick3);

        $trick4 = new Trick();
        $trick4->setUser($user1);
        $trick4->setName('Stalefish');
        $trick4->setDescription('saisie de la carre backside de la planche entre les deux pieds avec la main arrière');
        $trick4->setCategory(TrickCategories::Grab);

        $manager->persist($trick4);

        $manager->flush();

        // Let's add some pictures and videos
        $media1 = new Media();
        $media1->setTrick($trick1);
        $media1->setType('photo');
        $media1->setMedia('/images/Trick-Mute-Grab-620x444-655773a507622.jpg');

        $manager->persist($media1);

        $media2 = new Media();
        $media2->setTrick($trick2);
        $media2->setType('photo');
        $media2->setMedia('/images/Trick-Meloncollie-Grab-620x421-6557748dc47ea.jpg');

        $manager->persist($media2);

        $media3 = new Media();
        $media3->setTrick($trick3);
        $media3->setType('photo');
        $media3->setMedia('/images/Trick-Indy-Grab-620x447-655772c544f1f.jpg');

        $manager->persist($media3);

        $media4 = new Media();
        $media4->setTrick($trick4);
        $media4->setType('photo');
        $media4->setMedia('/images/Tricks-Stalefish-Grab-620x393-65577066edd07.jpg');

        $manager->persist($media4);

        $media5 = new Media();
        $media5->setTrick($trick1);
        $media5->setType('video');
        $media5->setMedia('<iframe width="560" height="315" src="https://www.youtube.com/embed/k6aOWf0LDcQ?si=pIkxVMDtCKfIVsQe" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>');

        $manager->persist($media5);

        $media6 = new Media();
        $media6->setTrick($trick2);
        $media6->setType('video');
        $media6->setMedia('<iframe width="560" height="315" src="https://www.youtube.com/embed/KEdFwJ4SWq4?si=CSMDJ9XsGYo-tjWR" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>');

        $manager->persist($media6);

        $media7 = new Media();
        $media7->setTrick($trick3);
        $media7->setType('video');
        $media7->setMedia('<iframe width="560" height="315" src="https://www.youtube.com/embed/4AlDWWsprZM?si=qRQZ8NoQsqUD3S9G" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>');

        $manager->persist($media7);

        $media8 = new Media();
        $media8->setTrick($trick4);
        $media8->setType('video');
        $media8->setMedia('<iframe width="560" height="315" src="https://www.youtube.com/embed/f9FjhCt_w2U?si=T0bd_iLh6G2xqmj7" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>');

        $manager->persist($media7);

        $manager->flush();

        // Let's add some comments
        $comment1 = new Comment();
        $comment1->setUser($user1);
        $comment1->setTrick($trick1);
        $comment1->setComment('Super cette figure !!');
        $comment1->setCreatedAt(new DateTime('now'));

        $manager->persist($comment1);

        $comment2 = new Comment();
        $comment2->setUser($user2);
        $comment2->setTrick($trick1);
        $comment2->setComment('En effet, c\'est vachement bien !!');
        $comment2->setCreatedAt(new DateTime('now'));

        $manager->persist($comment2);

        $comment3 = new Comment();
        $comment3->setUser($user3);
        $comment3->setTrick($trick1);
        $comment3->setComment('Ça a l\'air chaud quand même !');
        $comment3->setCreatedAt(new DateTime('now'));

        $manager->persist($comment3);

        $comment4 = new Comment();
        $comment4->setUser($user1);
        $comment4->setTrick($trick2);
        $comment4->setComment('Intéressant !');
        $comment4->setCreatedAt(new DateTime('now'));

        $manager->persist($comment4);

        $comment5 = new Comment();
        $comment5->setUser($user2);
        $comment5->setTrick($trick3);
        $comment5->setComment('Pas mal !');
        $comment5->setCreatedAt(new DateTime('now'));

        $manager->persist($comment5);
        
        $manager->flush();
    }
}
