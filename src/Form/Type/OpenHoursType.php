<?php

namespace App\Form\Type;

use App\Form\DataTransformer\OpenHoursTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpenHoursType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('monday')
            ->add('tuesday')
            ->add('wednesday')
            ->add('thursday')
            ->add('friday')
            ->add('saturday')
            ->add('sunday')

        ;

        $builder
            ->get('monday')
            ->addModelTransformer(new OpenHoursTransformer())
            ;
        $builder
            ->get('tuesday')
            ->addModelTransformer(new OpenHoursTransformer())
            ;
        $builder
            ->get('wednesday')
            ->addModelTransformer(new OpenHoursTransformer())
            ;
        $builder
            ->get('thursday')
            ->addModelTransformer(new OpenHoursTransformer())
            ;
        $builder
            ->get('friday')
            ->addModelTransformer(new OpenHoursTransformer())
            ;
        $builder
            ->get('saturday')
            ->addModelTransformer(new OpenHoursTransformer())
            ;
        $builder
            ->get('sunday')
            ->addModelTransformer(new OpenHoursTransformer())
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'required' => false,
        ]);
    }

    public function getParent(): string
    {
        return FormType::class;
    }
}
