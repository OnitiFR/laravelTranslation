<?php
namespace Oniti\Translation;

class TranslationFacade extends \Illuminate\Support\Facades\Facade
{
  protected static function getFacadeAccessor()
  {
    return 'Translation';
  }
}
