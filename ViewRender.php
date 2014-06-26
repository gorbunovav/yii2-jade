<?php

/**
 * @link http://www.optimuspro.ru/
 * @copyright Copyright (c) 2014 Optimus LLC
 * @license MIT
 */

namespace optimus\jade;

use Yii;
use yii\base\View;
use yii\base\ViewRenderer as BaseViewRenderer;
//use Jade

class ViewRenderer extends BaseViewRenderer
{

    /**
     * @var string the directory or path alias pointing to where Jade cache will be stored.
     */
    private $_cachePath = '@runtime/optimus/jade/cache';

    /**
     * @var string the directory or path alias pointing to where Jade compiled templates will be stored.
     */
    private $_compilePath = '@runtime/optimus/jade/compile';

    /**
     *
     */
    public function init()
    {

    }


    /**
     * Renders a view file.
     *
     * This method is invoked by [[View]] whenever it tries to render a view.
     * Child classes must implement this method to render the given view file.
     *
     * @param View $view the view object used for rendering the file.
     * @param string $file the view file.
     * @param array $params the parameters to be passed to the view file.
     *
     * @return string the rendering result
     */
    public function render($view, $file, $params)
    {
        //Yii::getAlias($this->_cachePath)
        //Yii::getAlias($this->_compilePath)
    }

}