<?php

namespace Dominos\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DominosUserBundle extends Bundle
{
	public function getParent()
    {
        return 'FOSUserBundle';
    }
}
