<?php
require_once ("core/Database.php");
require_once ("core/MustachePresenter.php");
require_once ("core/Router.php");

require_once ("controller/HomeController.php");
require_once ("controller/LoginController.php");
require_once ("controller/RegisterController.php");
require_once ("controller/AdminController.php");
require_once ("controller/EditorController.php");
require_once ("controller/PlayerController.php");
require_once ("controller/PerfilUsuarioController.php");
require_once ("controller/QuizController.php");
require_once ("controller/RecordController.php");
require_once ("controller/RankingController.php");
require_once ("controller/PlayerProfileRankingController.php");
require_once ("controller/CrearPreguntaController.php");
require_once ("controller/ReportController.php");

require_once ("model/LoginModel.php");
require_once ("model/RegisterModel.php");
require_once ("model/AdminModel.php");
require_once ("model/EditorModel.php");
require_once ("model/PlayerModel.php");
require_once ("model/PerfilUsuarioModel.php");
require_once ("model/QuizModel.php");
require_once ("model/RecordModel.php");
require_once ("model/RankingModel.php");
require_once ("model/PlayerProfileRankingModel.php");
require_once ("model/CrearPreguntaModel.php");
require_once ("model/ReportModel.php");

include_once('vendor/autoload.php');
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

    public function getIniConfig(){
        return parse_ini_file("configuration/config.ini", true);
    }

    public function getRouter(){
        return new Router("getHomeController", "show", $this);
    }
    
    public function getViewer(){
        return new MustachePresenter("view");
    }
    
    
    public function getHomeController(){
        return new HomeController($this->getViewer());
    }

    public function getLoginController(){
        return new LoginController(
            $this->getViewer(),
            new LoginModel($this->getDatabase())
        );
    }

    public function getRegisterController(){
        return new RegisterController(
            $this->getViewer(),
            new RegisterModel($this->getDatabase())
        );
    }

    public function getPlayerController(){
        return new PlayerController(
            $this->getViewer(),
            new PlayerModel($this->getDatabase())

        );
    }

    public function getEditorController(){
        return new EditorController(
            $this->getViewer(),
            new EditorModel($this->getDatabase())

        );
    }

    public function getAdminController(){
        return new AdminController(
            $this->getViewer(),
            new AdminModel($this->getDatabase())

        );
    }

    public function getPerfilUsuarioController() {
        return new PerfilUsuarioController(
            $this->getViewer(),
            new PerfilUsuarioModel($this->getDatabase())
        );
    }

    public function getQuizController(){
        $model = new QuizModel($this->getDatabase());
        $view  = $this->getViewer();
        return new QuizController($model, $view);
    }

    public function getReportController(){
    $reportModel = new ReportModel($this->getDatabase());
    $quizModel = new QuizModel($this->getDatabase());
    $view  = $this->getViewer();
    return new ReportController($reportModel, $quizModel);
    }

    public function getRecordController(){
        $model = new RecordModel($this->getDatabase());
        $view = $this->getViewer();
        return new RecordController($view, $model);
    }

    public function getRankingController() {
        $model = new RankingModel($this->getDatabase());
        $view = $this->getViewer();
        return new RankingController($model, $view);
    }

    public function getPlayerProfileRankingController() {
        $model = new PlayerProfileRankingModel($this->getDatabase());
        $view = $this->getViewer();
        return new PlayerProfileRankingController($model, $view);
    }

    public function getCrearPreguntaController(){
        $model = new CrearPreguntaModel($this->getDatabase());
        $view = $this->getViewer();
        return new CrearPreguntaController($model, $view);
    }

}