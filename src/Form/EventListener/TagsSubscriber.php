<?php

namespace App\Form\EventListener;

use App\Entity\Tags;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TagsSubscriber implements EventSubscriberInterface
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
        $tags = $event->getData();
        $tags_ = [];

        foreach ($tags as $tag) {
            if (!is_numeric($tag)) {
                $tag_ = new Tags();
                $tag_->setName($tag);
                $this->manager->persist($tag_);
                $tags_[] = $tag_->getId().'';
            } else {
                $tags_[] = $tag;
            }
        }

        if (!empty($tags_)) {
            $this->manager->flush();
            $event->setData($tags_);
        }
    }
}
