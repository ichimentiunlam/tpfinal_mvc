<?php
class Configurator {

    private $config;

    public function __construct()
    {
        $this->config = parse_ini_file("config/config.ini");
    }

    public function getGameController()
    {
        return new GameController($this->getGameModel(), $this->getRenderer(), new Request());
    }

    public function getUserController()
{
    return new UserController(
        $this->getGameModel(), $this->getRenderer(), new Request());
}

    private function getDatabase()
    {
        return new MyDatabase(
            $this->config['hostname'],
            $this->config['username'],
            $this->config['password'],
            $this->config['database'],
            $this->config['port']
        );
    }

    private function getRenderer()
    {
        return new MustacheRenderer(__DIR__ . '/../view');
    }

    private function getGameModel()
    {
        return new GameModel($this->getDatabase());
    }

    public function getRouter()
    {
        return new Router($this, 'game', 'home');
    }

    public function getOrDefault($controllerName, $defaultControllerName)
    {
        $getter = 'get' . ucfirst($controllerName) . 'Controller';
        if (method_exists($this, $getter)) {
            return $this->{$getter}();
        }
        $defaultGetter = 'get' . ucfirst($defaultControllerName) . 'Controller';
        return $this->{$defaultGetter}();
    }
}
