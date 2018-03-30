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
            ->add('qte')
            ->add('prixTotal')
            ->add('Produit', EntityType::class, array(
                'class' => 'AppBundle\Entity\Produit',
                'choice_label' => 'imei',
                'placeholder' => 'Please choose',
                'empty_data' => null,
                'required' => false
 
            )) 
            ->add('Commande', EntityType::class, array(
                'class' => 'AppBundle\Entity\Commande',
                'choice_label' => 'tva',
                'placeholder' => 'Please choose',
                'empty_data' => null,
                'required' => false
 
            )) 
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
