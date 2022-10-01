<?php

namespace app\core\form;

use app\core\Model;


class Field extends BaseField
{
    public const TYPE_TEXT = 'text';
    public const TYPE_DATE = 'date';
    public const TYPE_EMAIL = 'email';
    public const TYPE_PASS = 'password';
    public const TYPE_NUMB = 'number';

    public string $type;
  

    public function __construct(Model $model, string $attribute)
    {       
        
        $this->type = self::TYPE_TEXT;
        parent::__construct($model, $attribute);
    }

    


    public function setType(string $type = 'text')
    {
        if($type === 'text') $this->type = self::TYPE_TEXT;
        if($type === 'email') $this->type = self::TYPE_EMAIL;
        if($type === 'number') $this->type = self::TYPE_NUMB;
        if($type === 'date') $this->type = self::TYPE_DATE;
        if($type === 'password') $this->type = self::TYPE_PASS;
        return $this;
    }

    public function passwordField()
    {
        $this->type = self::TYPE_PASS;
        return $this;
    }

    public function renderInput(): string
    {
        return sprintf('<input type="%s" name="%s"  value="%s" class="form-control %s">',
                        $this->type, 
                        $this->attribute,
                        $this->model->{$this->attribute},
                        $this->model->hasError($this->attribute) ? ' is-invalid' : '',  
                    );
    }

}
