<?php
	/**
	 * Page des receiveds
	 */
	class Receiveds extends Controller
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

            $this->internalReceiveds = new \controllers\internals\Receiveds($this->bdd);
            $this->internalContacts = new \controllers\internals\Contacts($this->bdd);

			\controllers\internals\Tools::verify_connect();
        }

		/**
		 * Cette fonction retourne tous les receiveds, sous forme d'un tableau permettant l'administration de ces receiveds
		 */	
        public function list ($page = 0)
        {
            $page = int($page);
            $receiveds = $this->internalReceiveds->get_list(25, $page);

            foreach ($receiveds as $key => $received)
            {
                if (!$contact = $this->internalContacts->get_by_number($received['send_by']))
                {
                    continue;
                }

                $receiveds[$key]['send_by'] = $contact['name'] . ' (' . $received['send_by'] . ')';
            }

            $this->render('receiveds/list', ['receiveds' => $receiveds, 'page' => $page, 'nb_results' => count($receiveds)]);
        }    

        /**
         * Cette fonction retourne tous les SMS reçus aujourd'hui pour la popup
         * @return json : Un tableau des SMS reçus
         */
        public function popup ()
        {
            $now = new \DateTime();
            $receiveds = $this->internalReceiveds->get_since_by_date($now->format('Y-m-d'));
        
            foreach ($receiveds as $key => $received)
            {
                if (!$contact = $this->internalContacts->get_by_number($received['send_by']))
                {
                    continue;
                }

                $receiveds[$key]['send_by'] = $contact['name'] . ' (' . $received['send_by'] . ')';
            }
        
            $nb_receiveds = count($receiveds);

            if (!isset($_SESSION['popup_nb_receiveds']) || $_SESSION['popup_nb_receiveds'] > $nb_receiveds)
            {
                $_SESSION['popup_nb_receiveds'] = $nb_receiveds;
            }

            $newly_receiveds = array_slice($receiveds, $_SESSION['popup_nb_receiveds']);
            
            $_SESSION['popup_nb_receiveds'] = $nb_receiveds;

            echo json_encode($newly_receiveds);
            return true;
        }

		/**
         * Cette fonction va supprimer une liste de receiveds
         * @param array int $_GET['ids'] : Les id des receivedes à supprimer
         * @return boolean;
         */
        public function delete($csrf)
        {
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Receiveds', 'list'));
                return false;
            }

            if (!$_SESSION['admin'])
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Vous devez être administrateur pour effectuer cette action.');
                header('Location: ' . $this->generateUrl('Receiveds', 'list'));
                return false;
            }

            $ids = $_GET['ids'] ?? [];
            $this->internalReceiveds->delete($ids);

            header('Location: ' . $this->generateUrl('Receiveds', 'list'));
            return true;
        }
	}
