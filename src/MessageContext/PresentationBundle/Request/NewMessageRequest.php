<?php

namespace MessageContext\PresentationBundle\Request;

use Symfony\Component\OptionsResolver\OptionsResolver;

class NewMessageRequest
{
    private $requestParameters;

    public function __construct($options)
    {
        $resolver  = new OptionsResolver();
        $resolver->setRequired(['publisher_id', 'channel_id', 'message']);

        $resolver->setAllowedTypes(array(
            'publisher_id' => array('string'),
            'channel_id' => array('string'),
            'message' => array('string')
        ));

        $this->requestParameters = $resolver->resolve($options);
    }

    public function getRequestParameters()
    {
        return $this->requestParameters;
    }
}
