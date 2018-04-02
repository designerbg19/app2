<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;


class lineCmdFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', Filters\NumberFilterType::class)
                   
            ->add('Produit', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\Produit',
                    'choice_label' => 'imei',
            )) 
            ->add('Commande', Filters\EntityFilterType::class, array(
                    'class' => 'AppBundle\Entity\Commande',
                    'choice_label' => 'numCmd',
            )) 
            ->add('qte', Filters\NumberFilterType::class)
            ->add('prixTotal', Filters\TextFilterType::class)
        ;
        $builder->setMethod("GET");


    }

    public function getBlockPrefix()
    {
        return null;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
            'csrf_protection' => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
