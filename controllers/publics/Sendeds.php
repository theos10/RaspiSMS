<?php
	/**
	 * Page des sendeds
	 */
	class Sendeds extends Controller
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

            $this->internalSendeds = new \controllers\internals\Sendeds($this->bdd);

			\controllers\internals\Tools::verify_connect();
        }

		/**
		 * Cette fonction retourne tous les sendeds, sous forme d'un tableau permettant l'administration de ces sendeds
		 */	
        public function list ($page = 0)
        {
            $page = int($page);
            $sendeds = $this->internalSendeds->get_list(25, $page);
            $this->render('sendeds/list', ['sendeds' => $sendeds, 'page' => $page]);
        }    
		
		/**
         * Cette fonction va supprimer une liste de sendeds
         * @param array int $_GET['ids'] : Les id des sendedes à supprimer
         * @return boolean;
         */
        public function delete($csrf)
        {
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Sendeds', 'list'));
                return false;
            }

            $ids = $_GET['ids'] ?? [];
            $this->internalSendeds->delete($ids);

            header('Location: ' . $this->generateUrl('Sendeds', 'list'));
            return true;
        }
	}
