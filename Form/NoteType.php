<?php

//note on an encounter question

namespace Aristos\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NoteType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array (
				'label' => 'Note title',
				'attr' => array (
						'placeholder' => 'i.e: hotels in Salerno',
				) 
		) )
				
					
		->add ( 'description', 'textarea', array (
				'label' => 'Description',
				'attr' => array (
						'placeholder' => 'Description', 
						'cols' => '45', 'rows' => '5',
				) ,
			
				
		) );
		
		

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Aristos\CoreBundle\Entity\Note'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'core_note';
    }
}
