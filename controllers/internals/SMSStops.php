<?php
	/**
	 * Classe des sms_stopes
	 */
	class SMSStops extends InternalController
	{
		/**
         * Cette fonction retourne une liste des sms_stopes sous forme d'un tableau
         * @param mixed(int|bool) $nb_entry : Le nombre d'entrées à retourner par page
         * @param mixed(int|bool) $page : Le numéro de page en cours
         * @return array : La liste des sms_stopes
         */	
		public static function get_list ($nb_entry = false, $page = false)
		{
			//Recupération des sms_stopes
            $modelSMSStops = new \Models\SMSStops($this->bdd);
            return $modelSMSStops->get_list($nb_entry, $nb_entry * $page);
		}

		/**
         * Cette fonction retourne une liste des sms_stopes sous forme d'un tableau
         * @param array int $ids : Les ids des entrées à retourner
         * @return array : La liste des sms_stopes
         */	
		public static function get_by_ids ($ids)
		{
			//Recupération des sms_stopes
            $modelSMSStops = new \Models\SMSStops($this->bdd);
            return $modelSMSStops->get_by_ids($ids);
        }
        
        /**
         * Cette fonction retourne un sms_stop par son numéro de tel
         * @param string $number : Le numéro du sms_stop
         * @return array : Le sms_stop
         */	
		public static function get_by_number ($number)
		{
			//Recupération des sms_stopes
            $modelSMSStops = new \Models\SMSStops($this->bdd);
            return $modelSMSStops->get_by_number($number);
        }
        

        /**
         * Cette fonction permet de compter le nombre de sms_stops
         * @return int : Le nombre d'entrées dans la table
         */
        public function count ()
        {
            $modelSMSStops = new \Models\SMSStops($this->bdd);
            return $modelSMSStops->count();
        }

		/**
		 * Cette fonction va supprimer une liste de sms_stops
		 * @param array $ids : Les id des sms_stopes à supprimer
		 * @return int : Le nombre de sms_stopes supprimées;
		 */
		public static function delete ($ids)
        {
            $modelSMSStops = new \Models\SMSStops($this->bdd);
            return $modelSMSStops->delete_by_ids($ids);
		}

		/**
         * Cette fonction insert une nouvelle sms_stope
         * @param array $sms_stop : Un tableau représentant la sms_stope à insérer
         * @return mixed bool|int : false si echec, sinon l'id de la nouvelle sms_stope insérée
		 */
        public static function create ($sms_stop)
		{
            $modelSMSStops = new \Models\SMSStops($this->bdd);
            return $modelSMSStops->insert($sms_stop);
		}

		/**
         * Cette fonction met à jour une série de sms_stopes
         * @return int : le nombre de ligne modifiées
		 */
		public static function update ($sms_stops)
        {
            $modelSMSStops = new \Models\SMSStops($this->bdd);
            
            $nb_update = 0;
            foreach ($sms_stops as $sms_stop)
            {
                $result = $modelSMSStops->update($sms_stop['id'], $sms_stop);

                if ($result)
                {
                    $nb_update ++;
                }
            }
        
            return $nb_update;
        }
	}
