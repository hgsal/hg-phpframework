<?php

/**
 * @author hg <hg>
 */

 namespace app\core;

// use app\core\Application as CoreApplication;
use app\core\Router;
use app\core\Request;
use app\core\Session;
use app\core\Response;
use app\core\UserModel;
use app\core\Controller;
use app\core\db\Database;

 class Application{
    
    public static string $ROOT_DIR;
    public string $userClass;
    public string $layout = 'main';
    public Request $request;
    public Router $router;
    public Response $response;
    public Session $session;
    public Database $db;
    public ?UserModel $user;
    public View $view;

    public static Application $app;
    public ?Controller $controller = null;

    public function __construct($rootPath, array $config)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();

        $this->db = new Database($config['db']);
        $this->userClass = $config['userClass'];
        $primaryValue = $this->session->get('user');
        if($primaryValue){
           $primaryKey = $this->userClass::primaryKey();
           $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        }else{
         $this->user = null;
        }
    }

    public function getController()
    {
      return $this->controller;
    }
      


    public function setController(\app\core\Controller $controller):void
    {
      $this->controller = $controller;
    }

    public function run(){

      try {
          echo $this->router->resolve();
      } catch (\Throwable $th) {
          $this->response->setStatusCode($th->getCode());
          echo $this->view->renderView('_error',['exception' => $th] );
      }
    }

    public function login(UserModel $user){
      $this->user = $user;
      $primaryKey = $user->primaryKey();
      $primaryValue = $user->{$primaryKey};
      $this->session->set('user', $primaryValue);
      return true;
    }

    public function logout()
    {
      $this->user=null;
      $this->session->remove('user');
    }

    public static function isGuest()
    {
      return !self::$app->user;
    }


 }