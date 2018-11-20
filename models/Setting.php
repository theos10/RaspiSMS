<?php
	namespace models;
	/**
     * Cette classe gère les accès bdd pour les settinges
	 */
	class Setting extends \Model
    {
        /**
         * Retourne une entrée par son id
         * @param int $id : L'id de l'entrée
         * @return array : L'entrée
         */
        public function get_by_id ($id)
        {
            $settings = $this->getFromTableWhere('setting', ['id' => $id]);
            return isset($settings[0]) ? $settings[0] : false;
        }
        
        /**
         * Retourne une entrée par son nom
         * @param string $name : Le nom
         * @return array : L'entrée
         */
        public function get_by_name ($name)
        {
            $settings = $this->getFromTableWhere('setting', ['name' => $namer]);
            return isset($settings[0]) ? $settings[0] : false;
        }

		/**
		 * Retourne une liste de settinges sous forme d'un tableau
         * @param int $limit : Nombre de résultat maximum à retourner
         * @param int $offset : Nombre de résultat à ingnorer
		 */
		public function get_list ($limit, $offset)
        {
            $settings = $this->getFromTableWhere('setting', [], '', false, $limit, $offset);

	    	return $settings;
		}
        
        /**
         * Met à jour une settinge par son nom
         * @param string $name : Le nom du setting
         * @param mixed $value : La valeure du setting
         * @return int : le nombre de ligne modifiées
         */
        public function update ($name, $value)
        {
            return $this->updateTableWhere('setting', ['value' => $value], ['name' => $name]);
        }

    }
