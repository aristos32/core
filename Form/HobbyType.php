<?php
namespace Aristos\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Intl\Intl;

class HobbyType extends AbstractType
{
	
   /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	   	
        $builder
        
                   
               
        ->add ( 'name', 'text', array (
				'label' => 'Hobbies, comma separated',
				'attr' => array (
						'placeholder' => 'i.e: hiking,city-tours,bar-hopping',
						'class' => 'input-medium search-query' 
				) 
		) )
		->add('Add hobbies', 'submit');
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Aristos\CoreBundle\Entity\Hobby'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'general_hobby';
    }
}
