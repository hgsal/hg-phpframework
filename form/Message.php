<?php

namespace app\core\form;

class Message
{

    public static function divMessage( string $type, string $message) :string
    {
        return sprintf('
                <div class="alert alert-%s">
                    %s
                </div>
        ', $type,
           $message
        );
    }
}
