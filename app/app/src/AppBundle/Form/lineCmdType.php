<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class lineCmdType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
        ->add('Produit', EntityType::class, array(
            'class' => 'AppBundle\Entity\Produit',
            'choice_label' => 'refmodel',
            'placeholder' => 'Please choose',
            'empty_data' => null,
            'required' => false

        )) 
            ->add('Commande', EntityType::class, array(
                'class' => 'AppBundle\Entity\Commande',
                'choice_label' => 'numCmd',
                'placeholder' => 'Please choose',
                'empty_data' => null,
                'required' => false
 
            )) 
            ->add('qte')
            ->add('prixTotal')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\lineCmd'
        ));
    }
}
