<?php
namespace UberScheduleBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RequestType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start_lat','text')
            ->add('start_lon','text')
            ->add('end_lat','text')
            ->add('end_lon','text')
            ->add('product_id', 'text')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UberScheduleBundle\Entity\Request'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'uberschedulebundle_request';
    }
}