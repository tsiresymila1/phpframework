<?php

namespace Core\Http\Security;

interface AuthenticatorModelInterface
{
    
    /**
     * getRoles
     *
     * @return array
     */
    public function getRoles();
}
