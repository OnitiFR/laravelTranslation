<?php
namespace Oniti\Translation;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{

    private $currentLocal = null;

    public function __construct(){
        $this->currentLocal = config('translation.default_lang');
    }
    public function setLocal($local){
        if(array_key_exists($local, config('translation.allowed_lang')))
            $this->currentLocal = $local;
    }
    public function getLocal(){
        return $this->currentLocal;
    }
}

?>
