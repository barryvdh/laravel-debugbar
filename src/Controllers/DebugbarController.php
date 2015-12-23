<?php namespace Barryvdh\Debugbar\Controllers;

use View, App, Controller, Config;

use Barryvdh\Debugbar\LaravelDebugbar;
use DebugBar\Storage\FileStorage;
use DebugBar\OpenHandler;
use Barryvdh\Debugbar\Facade as Debugbar;

class DebugbarController extends BaseController {
  public function getIndex() {
    $path = Config::get('debugbar.storage.path');

    if (!file_exists($path))
      return 'Debug directory does not exist: '.$path;

    $storage = new FileStorage($path);

    $files = $storage->find([], 1);

    $id = (empty($files)) ? null : $files[0]['id'];
    $data = (empty($files)) ? '' : $storage->get($id);

    Debugbar::setData($data);
    Debugbar::setCurrentRequestId($id);

    $renderer = Debugbar::getJavascriptRenderer();
    $renderer->setOpenHandlerUrl('debugbar/open-handler');
    return View::make('laravel-debugbar::debugbar', ['renderer' => $renderer]);
  }

  public function getOpenHandler() {
    Debugbar::disable();
    $debugbar = App::make('debugbar');
    $openHandler = new OpenHandler($debugbar);
    $openHandler->handle();
  }
}
