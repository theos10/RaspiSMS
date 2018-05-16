<?php
	/**
	 * Classe des settinges
	 */
	class Settings extends InternalController
	{

		/**
         * Cette fonction retourne une liste des settinges sous forme d'un tableau
         * @param mixed(int|bool) $nb_entry : Le nombre d'entrées à retourner par page
         * @param mixed(int|bool) $page : Le numéro de page en cours
         * @return array : La liste des settinges
         */	
		public static function get_list ($nb_entry = false, $page = false)
		{
			//Recupération des settinges
            $modelSettings = new \Models\Settings($this->bdd);
            return $modelSettings->get_list($nb_entry, $nb_entry * $page);
		}

        /**
         * Cette fonction retourne un setting par son nom
         * @param string $name : Le nom du setting
         * @return array : Le setting
         */	
		public static function get_by_name ($name)
		{
			//Recupération des settinges
            $modelSettings = new \Models\Settings($this->bdd);
            return $modelSettings->get_by_name($name);
        }
        
		/**
         * Cette fonction met à jour un setting
         * @param string $name : Le nom du setting
         * @param mixed $value : La nouvelle value du setting
         * @return bool : True si on a bien modifié, false sinon
		 */
		public static function update ($name, $value)
        {
            $modelSettings = new \Models\Settings($this->bdd);
            return (bool) $modelSettings->update($setting['id'], $setting);
        }
	}
