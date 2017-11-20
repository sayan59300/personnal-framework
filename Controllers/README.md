# README #

### Contient les controllers nécessaires au fonctionnement de l'application ###

### Format ###
```PHP
<?php
    
    namespace Itval\Controllers;
    
    use Slim\Http\Request;
    use Slim\Http\Response;
    
    /**
     * Class NameController Description
     *
     * @author Nicolas Buffart <concepteur-developpeur@nicolas-buffart.fr>
     */
    class NameController extends Controller
    {
        /**
         * Rend la vue index
         *
         * @param Request $request
         * @param Response $response
         * @param $args
         * @return Response
         */
        public function index(Request $request, Response $response, $args): Response
        {
            return $this->render('index');
        }
    }
```