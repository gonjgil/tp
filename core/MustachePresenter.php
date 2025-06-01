<?php

class MustachePresenter{
    private $mustache;
    private $partialsPathLoader;

    public function __construct($partialsPathLoader){
        Mustache_Autoloader::register();
        $this->mustache = new Mustache_Engine(
            array(
            'partials_loader' => new Mustache_Loader_FilesystemLoader( $partialsPathLoader )
        ));
        $this->partialsPathLoader = $partialsPathLoader;
    }

    public function render($contentFile , $data = array() ){
        if (isset($_SESSION['user'])) {
            $data['user'] = $_SESSION['user'];

            switch ($_SESSION['user']['user_type']) {
                case 'jugador':
                    $data['panelUrl'] = '/tp/player/panel';
                    break;
                case 'editor':
                    $data['panelUrl'] = '/tp/editor/panel';
                    break;
                case 'administrador':
                    $data['panelUrl'] = '/tp/admin/panel';
                    break;
                default:
                    $data['panelUrl'] = '/tp/home/index';
                    break;
            }
        }
        $data['isLoginView'] = ($contentFile === 'login');
        $fullContentPath = $this->partialsPathLoader . '/' . $contentFile . "View.mustache";
        echo $this->generateHtml($fullContentPath, $data);
    }

    public function generateHtml($contentFile, $data = array()) {
        $contentAsString = '';
        $contentAsString = file_get_contents(  $this->partialsPathLoader .'/header.mustache');
        $contentAsString .= file_get_contents( $contentFile );
        $contentAsString .= file_get_contents($this->partialsPathLoader . '/footer.mustache');
        return $this->mustache->render($contentAsString, $data);
    }
}