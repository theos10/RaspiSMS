<?php
namespace controllers\publics;
	/**
	 * Page des sms_stops
	 */
	class SMSStops extends \Controller
	{
		/**
		 * Cette fonction est appelée avant toute les autres : 
		 * Elle vérifie que l'utilisateur est bien connecté
		 * @return void;
		 */
		public function _before()
        {
            global $bdd;
            global $model;
            $this->bdd = $bdd;
            $this->model = $model;

            $this->internalSMSStops = new \controllers\internals\SMSStops($this->bdd);

			\controllers\internals\Tools::verify_connect();
        }

		/**
		 * Cette fonction retourne tous les sms_stops, sous forme d'un tableau permettant l'administration de ces sms_stops
		 */	
        public function list ($page = 0)
        {
            $page = (int) $page;
            $sms_stops = $this->internalSMSStops->get_list(25, $page);
            $this->render('sms_stops/list', ['sms_stops' => $sms_stops]);
        }    
		
		/**
         * Cette fonction va supprimer une liste de sms_stops
         * @param array int $_GET['ids'] : Les id des sms_stopes à supprimer
         * @return boolean;
         */
        public function delete($csrf)
        {
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('SMSStops', 'list'));
                return false;
            }

            if (!$_SESSION['admin'])
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Vous devez être administrateur pour pouvoir supprimer un "STOP SMS" !');
                header('Location: ' . $this->generateUrl('SMSStops', 'list'));
                return false;
            }

            $ids = $_GET['ids'] ?? [];
            $this->internalSMSStops->delete($ids);

            header('Location: ' . $this->generateUrl('SMSStops', 'list'));
            return true;
        }

	}
