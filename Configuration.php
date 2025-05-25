<?php
require_once("core/Database.php");
require_once("core/FilePresenter.php");
require_once("core/MustachePresenter.php");
require_once("core/Router.php");

require_once("controller/HomeController.php");
require_once ("controller/LoginController.php");
require_once ("controller/RegisterController.php");

require_once("model/LoginModel.php");
require_once("model/RegisterModel.php");

include_once('vendor/mustache/src/Mustache/Autoloader.php');

class Configuration
{
    public function getDatabase()
    {
        $config = $this->getIniConfig();

        return new Database(
            $config["database"]["server"],
            $config["database"]["user"],
            $config["database"]["dbname"],
            $config["database"]["pass"]
        );
    }

    public function getIniConfig()
    {
        return parse_ini_file("configuration/config.ini", true);
    }


    public function getHomeController()
    {
        return new HomeController($this->getViewer());
    }


    public function getGroupController()
    {
        return new GroupController(new GroupModel($this->getDatabase()), $this->getViewer());
    }

    public function getRouter()
    {
        return new Router("getHomeController", "show", $this);
    }

    public function getViewer()
    {
        //return new FileView();
        return new MustachePresenter("view");
    }


    public function getLoginController()
    {
        return new LoginController(
            $this->getViewer(),
            new LoginModel($this->getDatabase())
        );
    }

    public function getRegisterController()
    {
        return new RegisterController(
            $this->getViewer(),
            new RegisterModel($this->getDatabase())
        );
    }
}