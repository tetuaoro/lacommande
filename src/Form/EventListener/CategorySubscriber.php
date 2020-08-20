<?php

namespace App\Form\EventListener;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class CategorySubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSubmit(FormEvent $event)
    {
        $category = $event->getData();

        if (!is_numeric($category)) {
            $category_ = new Category();
            $category_->setName($category);
            $this->manager->persist($category_);
            $this->manager->flush();

            $event->setData($category_->getId().'');
        }
    }
}
