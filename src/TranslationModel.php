<?php
namespace Oniti\Translation;

use Illuminate\Database\Eloquent\Model;
use Oniti\UuidForKey\UuidForKey;

class TranslationModel extends Model
{
    use UuidForKey;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    public $timestamps = false;
    public $table = "translations";

    protected $guarded = [];


    public function scopeGetTraduction($query, $class,$uuid,$champ,$lang){
        return $query->where('class',$class)->where('class_uuid',$uuid)->where('champ',$champ)->where('lang',$lang);
    }
}

?>
