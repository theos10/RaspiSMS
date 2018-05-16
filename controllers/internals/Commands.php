<?php
	/**
	 * Classe des commandes
	 */
	class Commands extends InternalController
	{

		/**
         * Cette fonction retourne une liste des commandes sous forme d'un tableau
         * @param mixed(int|bool) $nb_entry : Le nombre d'entrées à retourner par page
         * @param mixed(int|bool) $page : Le numéro de page en cours
         * @return array : La liste des commandes
         */	
		public static function get_list ($nb_entry = false, $page = false)
		{
			//Recupération des commandes
            $modelCommands = new \Models\Commands($this->bdd);
            return $modelCommands->get_list($nb_entry, $nb_entry * $page);
		}

		/**
         * Cette fonction retourne une liste des commandes sous forme d'un tableau
         * @param array int $ids : Les ids des entrées à retourner
         * @return array : La liste des commandes
         */	
		public static function get_by_ids ($ids)
		{
			//Recupération des commandes
            $modelCommands = new \Models\Commands($this->bdd);
            return $modelCommands->get_by_ids($ids);
        }

        /**
         * Cette fonction permet de compter le nombre de scheduleds
         * @return int : Le nombre d'entrées dans la table
         */
        public function count ()
        {
            $modelCommands = new \Models\Commands($this->bdd);
            return $modelCommands->count();
        }

		/**
		 * Cette fonction va supprimer une liste de commands
		 * @param array $ids : Les id des commandes à supprimer
		 * @return int : Le nombre de commandes supprimées;
		 */
		public static function delete ($ids)
        {
            $modelCommands = new \Models\Commands($this->bdd);
            return $modelCommands->delete_by_ids($ids);
		}

		/**
         * Cette fonction insert une nouvelle commande
         * @param array $command : La commande à insérer
         * @return mixed bool|int : false si echec, sinon l'id de la nouvelle commande insérée
		 */
        public static function create ($command)
		{
            $modelCommands = new \Models\Commands($this->bdd);
            return $modelCommands->insert($command);
		}

		/**
         * Cette fonction met à jour une série de commandes
         * @param int $id : L'id de la command eà mettr eà jour
         * @param array $command : La commande à mettre à jour
         * @return int : le nombre de ligne modifiées
		 */
        public static function update ($id, $command)
        {
            $modelCommands = new \Models\Commands($this->bdd);
            $result = $modelCommands->update($command);
        
            return $nb_update;
        }
	}
