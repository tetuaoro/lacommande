<?php

namespace App\Form\Type;

use App\Entity\Provider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class ProviderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('openHours', OpenHoursType::class)
            ->add('label', TextType::class, [
                'translation_domain' => 'form',
                'required' => false,
                'label' => 'Label',
            ])
            ->add('description', TextareaType::class, [
                'translation_domain' => 'form',
                'required' => false,
            ])
            ->add('linkinsta', UrlType::class, [
                'translation_domain' => 'form',
                'required' => false,
                'label' => 'Page Instagram',
                'attr' => [
                    'placeholder' => 'le lien vers votre instagram',
                ],
            ])
            ->add('linkfb', UrlType::class, [
                'translation_domain' => 'form',
                'required' => false,
                'label' => 'Page Facebook',
                'attr' => [
                    'placeholder' => 'le lien vers votre page facebook',
                ],
            ])
            ->add('linktwitter', UrlType::class, [
                'translation_domain' => 'form',
                'required' => false,
                'label' => 'Page Twitter',
                'attr' => [
                    'placeholder' => 'le lien vers votre twitter',
                ],
            ])
            ->add('image', FileType::class, [
                'translation_domain' => 'form',
                'required' => true,
                'mapped' => false,
                'data_class' => null,
                'attr' => [
                    'placeholder' => 'une image 1900x545',
                ],
                'label' => 'Bannière',
                'label_attr' => [
                    'data-browse' => 'Ajouter une image',
                ],
                'help' => 'image : exactement 1900x545',
                'help_attr' => [
                    'class' => 'pt-2',
                ],
                'constraints' => [
                    new Image([
                        'maxSize' => '5M',
                        'minWidth' => '1900',
                        'minHeight' => '545',
                        'maxWidth' => '1900',
                        'maxHeight' => '545',
                    ]),
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var \App\Entity\Provider $provider */
            $provider = $event->getData();

            if ($provider->getBgImg()) {
                $event->getForm()->remove('image')
                    ->add('image', FileType::class, [
                        'translation_domain' => 'form',
                        'required' => false,
                        'mapped' => false,
                        'data_class' => null,
                        'attr' => [
                            'placeholder' => $provider->getImgInfo()['metadata']['fullname'],
                        ],
                        'label' => 'Bannière',
                        'label_attr' => [
                            'data-browse' => 'Modifier l\'image',
                        ],
                        'help' => 'image : exactement 1900x545',
                        'help_attr' => [
                            'class' => 'pt-2',
                        ],
                        'constraints' => [
                            new Image([
                                'maxSize' => '5M',
                                'minWidth' => '1900',
                                'minHeight' => '545',
                                'maxWidth' => '1900',
                                'maxHeight' => '545',
                            ]),
                        ],
                    ])
                ;
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Provider::class,
        ]);
    }
}
