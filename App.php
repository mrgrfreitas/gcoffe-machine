<?php
namespace app\Machine;

use app\Database\Database;
use app\Machine\Engine\Auth\Events\Verified;
use app\Machine\Engine\Helpers\Session;
use Exception;

class App
{
    // CREATE a VARIABLE
    static public string $ROOT_DIR;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public array $user = [];

    public static App $app;
    /**
     * @var mixed
     */
    public $controller = null;


    /**
     * App constructor.
     * @param $rootPath
     */
    public function __construct($rootPath)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app       = $this;

        /**
         * instances of classes
         */
        $this->request      = new Request();
        $this->response     = new Response();
        $this->router       = new Router($this->request, $this->response);
        $this->session      = new Session();
        $this->db           = new Database();

        $userPk = $this->session->get('user');
        if($userPk){
            $this->user = $this->session->findUser($userPk);
        }

    }

    public function login($findUser)
    {
        $pk = $findUser['id'];
        $pkValue = $pk;
        $this->session->set('user', $pkValue);
        return true;
    }

    public function logout()
    {
        $this->user = [];
        $this->session->destroy('user');
    }

    /**
     * @throws Exception
     */
    public function run()
    {
        try {
            echo $this->router->loading();
        }catch (Exception $e){
            logger($e);
            $this->response->setStatusCode($e->getCode());
            echo view('errors/'. $e->getCode());
        }
    }

}