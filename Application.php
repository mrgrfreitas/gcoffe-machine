<?php
namespace app\Machine;

use app\Database\Database;
use app\Machine\Engine\Auth\Auth;
use app\Machine\Engine\Routing\Route;
use app\Machine\Engine\Support\Seo;
use Exception;

class Application
{
    // CREATE a VARIABLE
    static public string $ROOT_DIR;
    public Response $response;
    public Route $route;
    public Auth $auth;
    public Seo $seo;
    public Database $db;
    public array $user;
    public static Application $app;
    /**
     * @var mixed
     */
    public $controller = null;



    /**
     * Application constructor.
     * @param $rootDir
     */
    public function __construct($rootDir)
    {
        self::$ROOT_DIR = $rootDir;
        self::$app       = $this;

        /**
         * instances of classes
         */

        $this->response     = new Response();
        $this->seo          = new Seo();
        $this->db           = new Database();
        $this->route        = new Route();
        $this->auth         = new Auth();

        $this->user = Auth::$auth->userOnSession();

    }

    /**
     * @throws Exception
     */
    public function run()
    {
        try {
            echo Route::$route->Web()->loading();

        }catch (Exception $e){
            try {

                echo Auth::$auth->Route()->loading();

            }catch (Exception $e){
                logger($e);
                $this->response->setStatusCode($e->getCode());
                echo view('errors/'. $e->getCode());
            }

        }
    }

}