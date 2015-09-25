<?php

namespace PostContext\PresentationBundle\Request;

use Symfony\Component\OptionsResolver\OptionsResolver;

class DeleteMessageRequest
{
    private $requestParameters;

    public function __construct($options)
    {
        $resolver  = new OptionsResolver();
        $resolver->setRequired(['publisher_id', 'post_id']);

        $resolver->setAllowedTypes(array(
            'publisher_id' => array('string'),
            'post_id' => array('string')
        ));

        $this->requestParameters = $resolver->resolve($options);
    }

    public function getRequestParameters()
    {
        return $this->requestParameters;
    }
}
