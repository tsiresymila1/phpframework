<?php

namespace Core\Database;

abstract class UserAuthenticatorModel extends BaseModel
{

    abstract public function getRoles();
}

