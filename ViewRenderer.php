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
use yii\base\Exception;
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

    private $_prettyPrint = YII_DEBUG;

    /**
     * @param string $file
     * @return array
     */
    private function _makeCompilePath($file)
    {
        $rootPath = Yii::getAlias('@app');

        if (mb_strpos($file, $rootPath) === false) {
            throw new \yii\base\Exception('Template file should be inside app root!');
        }

        $relativePath = str_replace($rootPath, '', $file);
        $targetFile = Yii::getAlias($this->_compilePath) . DIRECTORY_SEPARATOR . $relativePath . '.php';

        return $targetFile;
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
        $this->_jade = new Jade($this->_prettyPrint);
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
        $targetPath = $this->_makeCompilePath($file);

        if (@filemtime($file) > @filemtime($targetPath)) {
            $time = date('Y-m-d H:i:s');

            $tpl  = "<?php /* Compiled from $file at $time */ ?>\n" . $this->_jade->render($file, $params);

            if (!@file_put_contents($targetPath, $tpl)) {
                mkdir(Yii::getAlias(dirname($targetPath)), 0755, true);
                file_put_contents($targetPath, $tpl);
            };
        }

        return $view->renderPhpFile($targetPath, $params, $view->context);
    }
}