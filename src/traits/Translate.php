<?php

namespace Oniti\Translation\Traits;

use Translation;
use Clicproxy\Translation\TranslationModel;

trait Translate
{
    public static  function boot(){
        parent::boot();
        /**
         * Supprime les clef de traduction de la future requete a la bdd pour ignorer les clefs de traduction
         * @var [type]
         */
        static::saving(function($model) {
            $model->attributes = array_diff_key($model->attributes, $model->translate);
        });
    }
    /**
    * Fonction appelée lors de la sérialisation en json de l'objet
    * @return [type] [description]
    */
    public function jsonSerialize()
    {
      return $this->addTranslateFieds(parent::jsonSerialize());
    }
    /**
     * Fonction appelée lors de la sérialisation de l'objet en array
     * @return [type] [description]
     */
    public function toArray(){
      return $this->addTranslateFieds(parent::toArray());
    }
    /**
     * Ajoute les champs de traduction lors de la sérialisation
     * @param array $data [description]
     */
    private function addTranslateFieds(array $data){
      if($this->translate && is_array($this->translate)){
          $allowedTranslations = config('translation.allowed_lang');
          $poidMaxLang = config('translation.poid_max');

          foreach ($this->translate as $champ) {
              $data[$champ] = $this->getTranslation($champ,$allowedTranslations,$poidMaxLang);
          }
      }
      return $data;
    }

    /**
     * Overwrite fill pour l'assignement en masse afin de pouvoir mettre a jour les traductions
     * @param  array  $attributes [description]
     * @return [type]             [description]
     */
    public function fill(array $attributes){
        $attributes = $this->setTranslationAttribute($attributes);
        parent::fill($attributes);
    }
    /**
     * Overwrite des tous les set afin de pouvoir mettre a jour les traductions
     * @param [type] $name  [description]
     * @param [type] $value [description]
     */
    public function __set($name,$value){
        $attributes = $this->setTranslationAttribute([$name => $value]);
        foreach ($attributes as $name => $value) {
            parent::__set($name,$value);
        }
    }
    /**
     * Fonction d'entrée pour la mise à jour des traductions
     * @param [type] $attributes [description]
     */
    private function setTranslationAttribute($attributes){
        if($this->translate && is_array($this->translate)){
            foreach ($attributes as $key => $value) {
                if(in_array($key, $this->translate)){
                    //C'est une clef identifié comme traduisable
                    if(!is_array($value)) throw new \Exception('Pour définir une nouvelle valeur pour la traduction d\'un champ merci de passer un tableau associatif [lang => valeur]');
                    //On est sur un update On connait déjà la clé primaire
                    if($this->{$this->getKeyName()}) $this->createOrUpdteTranslation($key,$value,$this);
                    else{
                        //Contexte de création on traitera la demande lors du callback
                        self::created(function($model)use($key,$value){
                            parent::created($model);
                            $this->createOrUpdteTranslation($key,$value,$model);
                        });
                    }
                }
            }

            //Une fois fini on supprime les clefs du tableau attribute afin de laisser laravel travailler sans interférances
            foreach ($this->translate as $key) {
                if(array_key_exists($key, $attributes)) unset($attributes[$key]);
            }
        }
        return $attributes;
    }
    /**
     * Fonction pour créer ou mettre à jour une traduction
     * @param  [type] $champ  clef du model traduit
     * @param  [type] $values array associatif [lang => traduction]
     * @param  [type] $model  $this ou mpdel on fonction du contexte
     * @return [type]         Void
     */
    private function createOrUpdteTranslation($champ, $values,$model){
        foreach ($values as $lang => $trad) {
            //Filtre sur les langue définie dans le fichier de configuration
            if(array_key_exists($lang, config('translation.allowed_lang'))){
                $translation = TranslationModel::getTraduction(get_class($this),$model->{$model->getKeyName()},$champ,$lang)->first();
                //On a déja la traduction en base on la met a jour
                if($translation){
                    $translation->traduction = $trad;
                    $translation->save();
                }else{
                    //Sinon on la créer
                    TranslationModel::create([
                        'class' => get_class($model),
                        'class_uuid' => $model->{$model->getKeyName()},
                        'champ' => $champ,
                        'lang' => $lang,
                        'traduction' => $trad
                    ]);
                }
            }
        }
    }
    /**
    * Pour intercepter les accesseur non géré par laravel et traduisible
    * @param  [type] $name [description]
    * @return [type]       [description]
    */
    public function __get($name){
        $parentGet = parent::__get($name);
        if(is_null($parentGet) && $this->translate && is_array($this->translate) && in_array($name, $this->translate)){
            $allowedTranslations = config('translation.allowed_lang');
            $poidMaxLang = config('translation.poid_max');

            $parentGet = $this->getTranslation($name, $allowedTranslations, $poidMaxLang);
        }
        return $parentGet;
    }

    /**
    * Fonction de traduction
    * @param  [type] $champ               [description]
    * @param  [type] $allowedTranslations [description]
    * @param  [type] $poidMaxLang         [description]
    * @return [type]                      [description]
    */
    private function getTranslation($champ,$allowedTranslations,$poidMaxLang){
        $dataTranslate = null;

        //Récupération de la traduction demandée
        $translation = TranslationModel::getTraduction(get_class($this),$this->{$this->getKeyName()},$champ,Translation::getLocal())->first();

        //On a pas trouvé la traduction dans la langue souhaité
        if(!$translation && array_key_exists(Translation::getLocal(), $allowedTranslations)){
            //On récupère le poids de la traduction demandée
            $poidLangue = $allowedTranslations[Translation::getLocal()];
            //On boucle sur les langues disponible ayant un poid suppérieur à la langue demandée
            while ($poidLangue <= $poidMaxLang && !$translation) {
                $poidLangue++;
                foreach ($allowedTranslations as $lang => $poid) {
                    if($poid == $poidLangue){
                        $translation = TranslationModel::getTraduction(get_class($this),$this->{$this->getKeyName()},$champ,$lang)->first();
                        if($translation) break;
                    }
                }
            }
        }

        if($translation){
            $dataTranslate = $translation->traduction;
        }
        return $dataTranslate;
    }
}
