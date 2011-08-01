<?php
/**
 * Description of Template
 *
 * @author Korri
 */
class Template {
    /**
     * Contains Twig_Environment instance to execure templates
     * @var Twig_Environment
     */
    private $twig;
    public function __construct()
    {
        $loader = new Twig_Loader_Filesystem(PAGES_DIR);
        $this->twig = new Twig_Environment($loader, array());
        $this->twig->addFilter('u', new Twig_Filter_Function('Template::u'));
    }
    public static function ucpage($page) {
        $page = str_replace('_', ' ', $page);
        $page = ucwords($page);
        $page = preg_replace('/\s+/', '_', $page);
        return $page;
    }
    public function renderPage()
    {
        try {
            $page = '';
            $action = 'Action_';

            if(isset($_GET['sub'])) {
                $page .= $_GET['sub'] . '/';
                $action .= self::ucpage($_GET['sub']).'_';
            }

            if(isset($_GET['p'])) {
                $page .= $_GET['p'] . '.html';
                $action .= self::ucpage($_GET['p']);
            }else {
                $page .= 'default.html';
                $action .= 'Default';
            }

            $this->loadSessionMessages();

            $template = $this->twig->loadTemplate($page);

            if(method_exists($this, $action)) {
                $this->$action();
            }

            $template->display(get_object_vars($this));
        }catch(Twig_Error_Loader $e)
        {
            header("HTTP/1.0 404 Not Found");
            $this->error = $e;
            $template = $this->twig->loadTemplate('404.html');
            $template->display(get_object_vars($this));
        }
    }
    private function loadSessionMessages()
    {
        foreach($_SESSION as $k => $v) {
            if(substr($k, 0, 4) == 'msg_') {
                $this->{substr($k, 4)} = $v;
                unset($_SESSION[$k]);
            }
        }
    }
    public function u($page, $params=false)
    {
        $url = BASE_FOLDER;

        if(isset($_GET['sub']))
        {
            $url .= $_GET['sub'] . '/';
        }
        if($page && $page != 'default' && $page != '/')
        {
            $url .= $page.'.html';
        }


        if($params) {
            $url .= '?'.http_build_query($params);
        }
        return $url;
    }
    public function redirectWithError($page, $error) {
        $_SESSION['msg_error'] = $error;
        header('Location: '.$this->u($page));
        exit;
    }
}