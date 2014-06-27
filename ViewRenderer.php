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
use Jade\Jade;
//yii\web\View yii\base\View::render($view, $params = Array, $context = NULL)

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
     * @var \Jade\Jade
     */
    private $_jade = null;

    /**
     * @param string $file
     * @return array
     */
    private function _makeComplilePath($file)
    {
        $digest = md5($file);
        $dir = Yii::getAlias($this->_compilePath) . DIRECTORY_SEPARATOR . $digest[0] . DIRECTORY_SEPARATOR . $digest[1] . DIRECTORY_SEPARATOR . $digest[2];
        $name = substr($digest, 3) . '.php';
        return [
            'dir'  => $dir,
            'path' => $dir . DIRECTORY_SEPARATOR . $name
        ];
    }

    /**
     *
     */
    private  function _render()
    {
        extract(func_get_arg(1));
        ob_start();
        include func_get_arg(0);
        return ob_get_clean();
    }

    /**
     *
     */
    public function init()
    {
        parent::init();
        $this->_jade = new Jade();
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

        $params['app']  = Yii::$app;
        $result = $this->_makeComplilePath($file);

        if (@filemtime($file) > @filemtime($result['path'])) {
            $time = date('Y-m-d H:i:s');
            $tpl  = "<?php /* Compiled from $file at $time */ ?>\n" . $this->_jade->render($file, $params);
            if (!@file_put_contents($result['path'], $tpl)) {
                mkdir(Yii::getAlias($result['dir']), 0755, true);
                file_put_contents($result['path'], $tpl);
            };
        }

        return $view->renderPhpFile($result['path'], $params, $view->context);
    }
}