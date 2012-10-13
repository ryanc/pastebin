<?php

namespace Paste\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints as Assert;

class Paste extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', 'textarea', array(
            'constraints' => new Assert\NotBlank,
        ));

        $builder->add('filename', 'text', array(
            'required' => false,
        ));

        $builder->add('convertTabs', 'checkbox', array(
            'required' => false,
        ));

        $builder->add('highlight', 'checkbox', array(
            'required' => false,
        ));
    }

    public function getName()
    {
        return 'paste';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Paste\Entity\Paste',
        ));
    }
}
