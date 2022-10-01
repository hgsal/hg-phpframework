<?php

namespace app\core\form;

use app\core\form\BaseField;

class TextAreaField extends BaseField
{

    public function renderInput():string
    {   
        return sprintf('<textarea name="%s" class="form-control%s">%s</textarea>',
                        $this->attribute,
                        $this->model->{$this->attribute},
                        $this->model->hasError($this->attribute) ? 'is-invalid' : ''
                );
    }
}
