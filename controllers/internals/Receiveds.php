<?php
	/**
	 * Classe des receivedes
	 */
	class Receiveds extends InternalController
	{

		/**
         * Cette fonction retourne une liste des receivedes sous forme d'un tableau
         * @param mixed(int|bool) $nb_entry : Le nombre d'entrées à retourner par page
         * @param mixed(int|bool) $page : Le numéro de page en cours
         * @return array : La liste des receivedes
         */	
		public static function get_list ($nb_entry = false, $page = false)
		{
			//Recupération des receivedes
            $modelReceiveds = new \Models\Receiveds($this->bdd);
            return $modelReceiveds->get_list($nb_entry, $nb_entry * $page);
		}

		/**
         * Cette fonction retourne une liste des receivedes sous forme d'un tableau
         * @param array int $ids : Les ids des entrées à retourner
         * @return array : La liste des receivedes
         */	
		public static function get_by_ids ($ids)
		{
			//Recupération des receivedes
            $modelReceiveds = new \Models\Receiveds($this->bdd);
            return $modelReceiveds->get_by_ids($ids);
        }
        
        /**
         * Cette fonction retourne une liste des receivedes sous forme d'un tableau
         * @param string $number : Le numéro de à qui est envoyé le message
         * @return array : La liste des sendeds
         */	
		public static function get_by_send_by ($number)
		{
			//Recupération des sendeds
            $modelSendeds = new \Models\Sendeds($this->bdd);
            return $modelSendeds->get_by_send_by($number);
        }

        /**
         * Cette fonction retourne les X dernières entrées triées par date
         * @param mixed false|int $nb_entry : Nombre d'entrée à retourner ou faux pour tout
         * @return array : Les dernières entrées
         */
        public function get_lasts_by_date ($nb_entry = false)
        {
            $modelReceiveds = new \Models\Receiveds($this->bdd);
            return $modelReceiveds->get_lasts_by_date($nb_entry);
        }
        
        /**
         * Cette fonction retourne une liste des receiveds sous forme d'un tableau
         * @param string $send_by : Le numéro depuis lequel est envoyé le message
         * @return array : La liste des receiveds
         */	
		public static function get_by_send_by ($send_by)
		{
            $modelReceiveds = new \Models\Receiveds($this->bdd);
            return $modelReceiveds->get_by_send_by($send_by);
        }

		/**
         * Récupère les SMS reçus depuis une date
         * @param $date : La date depuis laquelle on veux les SMS (au format 2014-10-25 20:10:05)
         * @return array : Tableau avec tous les SMS depuis la date
         */
        public function get_since_by_date ($date)
        {
            $modelReceiveds = new \Models\Receiveds($this->bdd);
            return $modelReceiveds->get_since_by_date($date, $number);
        }

		/**
         * Récupère les SMS reçus depuis une date pour un numero
         * @param $date : La date depuis laquelle on veux les SMS (au format 2014-10-25 20:10:05)
         * @param $number : Le numéro
         * @return array : Tableau avec tous les SMS depuis la date
         */
        public function get_since_for_number_by_date ($date, $number)
        {
            $modelReceiveds = new \Models\Receiveds($this->bdd);
            return $modelReceiveds->get_since_for_number_by_date($date, $number);
        }

		/**
		 * Cette fonction va supprimer une liste de receiveds
		 * @param array $ids : Les id des receivedes à supprimer
		 * @return int : Le nombre de receivedes supprimées;
		 */
		public static function delete ($ids)
        {
            $modelReceiveds = new \Models\Receiveds($this->bdd);
            return $modelReceiveds->delete_by_ids($ids);
		}

		/**
         * Cette fonction insert une nouvelle receivede
         * @param array $received : Un tableau représentant la receivede à insérer
         * @return mixed bool|int : false si echec, sinon l'id de la nouvelle receivede insérée
		 */
        public static function create ($received)
		{
            $modelReceiveds = new \Models\Receiveds($this->bdd);
            return $modelReceiveds->create($received);
		}

		/**
         * Cette fonction met à jour une série de receivedes
         * @return int : le nombre de ligne modifiées
		 */
		public static function update ($receiveds)
        {
            $modelReceiveds = new \Models\Receiveds($this->bdd);
            
            $nb_update = 0;
            foreach ($receiveds as $received)
            {
                $result = $modelReceiveds->update($received['id'], $received);

                if ($result)
                {
                    $nb_update ++;
                }
            }
        
            return $nb_update;
        }

        /**
         * Cette fonction compte le nombre de receiveds par jour depuis une date
         * @return array : un tableau avec en clef la date et en valeure le nombre de sms envoyés
         */
        public function count_by_day_since ($date)
        {
            $modelReceiveds = new \Models\Receiveds($this->bdd);

            $counts_by_day = $modelReceiveds->count_by_day_since($date);
            $return = [];
            
            foreach ($counts_by_day as $count_by_day)
            {
                $return[$count_by_day['at_ymd']] = $count_by_day['nb'];
            }

            return $return;
        }
        
        /**
         * Cette fonction retourne les discussions avec un numéro
         * @return array : Un tableau avec la date de l'échange et le numéro de la personne
         */
        public function get_discussions ()
        {
            $modelReceiveds = new \Models\Receiveds($this->bdd);
            return $modelReceiveds->get_discussions();
        }
	}
