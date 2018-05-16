<?php
	/**
	 * Page des events
	 */
	class Events extends Controller
	{
		/**
		 * Cette fonction est appelée avant toute les autres : 
		 * Elle vérifie que l'utilisateur est bien connecté
		 * @return void;
		 */
		public function before()
        {
            global $bdd;
            global $model;
            $this->bdd = $bdd;
            $this->model = $model;

            $this->internalEvents = new \controllers\internals\Events($this->bdd);

			\controllers\internals\Tools::verify_connect();
        }

		/**
		 * Cette fonction retourne tous les events, sous forme d'un tableau permettant l'administration de ces events
		 */	
        public function list ($page = 0)
        {
            $page = int($page);
            $events = $this->internalEvents->getList(25, $page);
            $this->render('events/list', ['events' => $events]);
        }    
		
		/**
         * Cette fonction va supprimer une liste de events
         * @param array int $_GET['ids'] : Les id des eventes à supprimer
         * @return boolean;
         */
        public function delete($csrf)
        {
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Events', 'list'));
                return false;
            }
            
            if (!$_SESSION['admin'])
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Vous devez être admin pour pouvoir supprimer des events.');
                header('Location: ' . $this->generateUrl('Events', 'list'));
                return false;
            }

            $ids = $_GET['ids'] ?? [];
            $this->internalEvents->delete($ids);

            header('Location: ' . $this->generateUrl('Events', 'list'));
            return true;
        }
	}
