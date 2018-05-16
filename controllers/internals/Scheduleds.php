<?php
	/**
	 * Classe des scheduledes
	 */
	class Scheduleds extends InternalController
	{
        
		/**
         * Cette fonction retourne une liste des scheduledes sous forme d'un tableau
         * @param mixed(int|bool) $nb_entry : Le nombre d'entrées à retourner par page
         * @param mixed(int|bool) $page : Le numéro de page en cours
         * @return array : La liste des scheduledes
         */	
		public static function get_list ($nb_entry = false, $page = false)
		{
			//Recupération des scheduledes
            $modelScheduleds = new \Models\Scheduleds($this->bdd);
            return $modelScheduleds->get_list($nb_entry, $nb_entry * $page);
		}

		/**
         * Cette fonction retourne une liste des scheduledes sous forme d'un tableau
         * @param array int $ids : Les ids des entrées à retourner
         * @return array : La liste des scheduledes
         */	
		public static function get_by_ids ($ids)
		{
			//Recupération des scheduledes
            $modelScheduleds = new \Models\Scheduleds($this->bdd);
            return $modelScheduleds->get_by_ids($ids);
        }

        /**
         * Cette fonction retourne les messages programmés avant une date et pour un numéro
         * @param DateTime $date : La date avant laquelle on veux le message
         * @param string $number : Le numéro
         * @return array : Les messages programmés avant la date
         */
        public function get_before_date_for_number ($date, $number)
        {
            $modelScheduleds = new \Models\Scheduleds($this->bdd);
            return $modelScheduleds->get_before_date_for_number($date, $number);
        }

        /**
         * Cette fonction permet de compter le nombre de scheduleds
         * @return int : Le nombre d'entrées dans la table
         */
        public function count ()
        {
            $modelScheduleds = new \Models\Scheduleds($this->bdd);
            return $modelScheduleds->count();
        }

		/**
		 * Cette fonction va supprimer une liste de scheduleds
		 * @param array $ids : Les id des scheduledes à supprimer
		 * @return int : Le nombre de scheduledes supprimées;
		 */
		public static function delete ($ids)
        {
            $modelScheduleds = new \Models\Scheduleds($this->bdd);
            return $modelScheduleds->delete_by_ids($ids);
		}

		/**
         * Cette fonction insert une nouvelle schedulede
         * @param array $scheduled : Le scheduled à créer avec at, content, flash, progress
         * @param array $numbers : Les numéros auxquels envoyer le scheduled
         * @param array $contacts_ids : Les ids des contacts auquels envoyer le scheduled
         * @param array $groups_ids : Les ids des groups auxquels envoyer le scheduled
         * @return mixed bool|int : false si echec, sinon l'id du nouveau scheduled inséré
		 */
        public static function create ($scheduled, $numbers = [], $contacts_ids = [], $groups_ids = [])
		{
            $modelScheduleds = new \Models\Scheduleds($this->bdd);
            
            if (!$id_scheduled = $modelScheduleds->insert($scheduled))
            {
                return false;
            }

            foreach ($numbers as $number)
            {
                $modelScheduleds->insert_scheduleds_number($id_scheduled, $number);
            }

            foreach ($contacts_ids as $contact_id)
            {
                $modelScheduleds->insert_scheduleds_contact($id_scheduled, $contact_id);
            }
            
            foreach ($groups_ids as $group_id)
            {
                $modelScheduleds->insert_scheduleds_groups($id_scheduled, $group_id);
            }

            return $id_scheduled;
		}

		/**
         * Cette fonction met à jour une série de scheduledes
         * @param array $scheduleds : Un tableau de scheduleds à modifier avec at, content, flash, progress + pour chaque scheduled numbers, contacts_ids, groups_ids
         * @param array $numbers : Les numéros auxquels envoyer le scheduled
         * @param array $contacts_ids : Les ids des contacts auquels envoyer le scheduled
         * @param array $groups_ids : Les ids des groups auxquels envoyer le scheduled
         * @return int : le nombre de ligne modifiées
		 */
        public static function update ($scheduleds)
        {
            $modelScheduleds = new \Models\Scheduleds($this->bdd);
            
            $nb_update = 0;
            foreach ($scheduleds as $scheduled)
            {
                $result = $modelScheduleds->update($scheduled['scheduled']['id'], $scheduled['scheduled']);

                if (!$result)
                {
                    continue;
                }

                $modelScheduleds->delete_scheduleds_numbers($scheduled['scheduled']['id']);
                $modelScheduleds->delete_scheduleds_contacts($scheduled['scheduled']['id']);
                $modelScheduleds->delete_scheduleds_groups($scheduled['scheduled']['id']);

                foreach ($scheduled['numbers'] as $number)
                {
                    $modelScheduleds->insert_scheduleds_number($id_scheduled, $number);
                }

                foreach ($scheduled['contacts_ids'] as $contact_id)
                {
                    $modelScheduleds->insert_scheduleds_contact($id_scheduled, $contact_id);
                }
                
                foreach ($scheduled['groups_ids'] as $group_id)
                {
                    $modelScheduleds->insert_scheduleds_groups($id_scheduled, $group_id);
                }
                

                $nb_update ++;
            }
        
            return $nb_update;
        }
        
        /**
         * Cette fonction retourne une liste de numéro pour un scheduleds
         * @param int $id_scheduled : L'id du scheduled pour lequel on veux le numéro
         * @return array : La liste des scheduledes
         */	
        public static function get_numbers ($id_scheduled)
		{
			//Recupération des scheduledes
            $modelScheduleds = new \Models\Scheduleds($this->bdd);
            return $modelScheduleds->get_numbers($id_scheduled);
		}

        /**
         * Cette fonction retourne une liste de contacts pour un scheduleds
         * @param int $id_scheduled : L'id du scheduled pour lequel on veux le numéro
         * @return array : La liste des contacts
         */	
        public static function get_contacts ($id_scheduled)
		{
			//Recupération des scheduledes
            $modelScheduleds = new \Models\Scheduleds($this->bdd);
            return $modelScheduleds->get_contacts($id_scheduled);
		}
        
        /**
         * Cette fonction retourne une liste de groups pour un scheduleds
         * @param int $id_scheduled : L'id du scheduled pour lequel on veux le numéro
         * @return array : La liste des groups
         */	
        public static function get_groups ($id_scheduled)
		{
			//Recupération des scheduledes
            $modelScheduleds = new \Models\Scheduleds($this->bdd);
            return $modelScheduleds->get_groups($id_scheduled);
		}
	}
