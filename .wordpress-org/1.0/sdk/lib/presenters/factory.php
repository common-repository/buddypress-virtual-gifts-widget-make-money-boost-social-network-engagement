<?php
/**
 * Feeligo
 *
 * @category   Feeligo
 * @package    API Connector SDK for PHP
 * @copyright  Copyright 2012 Feeligo
 * @license    
 * @author     Davide Bonapersona <tech@feeligo.com>
 */

/**
 * @category   Feeligo
 * @package    FeeligoPresenterFactory
 * @copyright  Copyright 2012 Feeligo
 * @license    
 */

require_once(str_replace('//','/',dirname(__FILE__).'/').'null.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'user.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'action.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'collection.php');

require_once(str_replace('//','/',dirname(__FILE__).'/').'../../interfaces/action_adapter.php');
require_once(str_replace('//','/',dirname(__FILE__).'/').'../../interfaces/user_adapter.php');

/**
 * factory for Presenter classes
 *
 * wraps a Model or Collection with the appropriate Presenter
 */
 
class FeeligoPresenterFactory {
  
  public static function present($item) {
    // null value: return a NullPresenter
    if ($item === null) return new FeeligoNullPresenter();
    
    // Models
    if (is_subclass_of($item, 'FeeligoUserAdapter') || $item instanceof FeeligoUserAdapter)
      return new FeeligoUserPresenter($item);
    if (is_subclass_of($item, 'FeeligoActionAdapter') || $item instanceof FeeligoActionAdapter)
      return new FeeligoActionPresenter($item);
    
    // Collections
    if (is_array($item)) return new FeeligoCollectionPresenter($item);

    // if no match, return a NullPresenter
    return new FeeligoNullPresenter($item);
  }
  
}