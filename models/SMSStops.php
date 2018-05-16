<?php
	namespace models;
	/**
     * Cette classe gère les accès bdd pour les sms_stopes
	 */
	class SMSStops extends \Model
    {
        /**
         * Retourne une entrée par son id
         * @param int $id : L'id de l'entrée
         * @return array : L'entrée
         */
        public function get_by_id ($id)
        {
            $sms_stops = $this->getFromTableWhere('sms_stops', ['id' => $id]);
            return isset($sms_stops[0]) ? $sms_stops[0] : false;
        }
        
        /**
         * Retourne une entrée par son numéro de tel
         * @param string $number : Le numéro de tél
         * @return array : L'entrée
         */
        public function get_by_number ($number)
        {
            $sms_stops = $this->getFromTableWhere('sms_stops', ['number' => $number]);
            return isset($sms_stops[0]) ? $sms_stops[0] : false;
        }

		/**
		 * Retourne une liste de sms_stopes sous forme d'un tableau
         * @param int $limit : Nombre de résultat maximum à retourner
         * @param int $offset : Nombre de résultat à ingnorer
		 */
		public function get_list ($limit, $offset)
        {
            $sms_stops = $this->getFromTableWhere('sms_stops', [], '', false, $limit, $offset);

	    	return $sms_stops;
		}
        
        /**
		 * Retourne une liste de sms_stopes sous forme d'un tableau
         * @param array $ids : un ou plusieurs id d'entrées à récupérer
         * @return array : La liste des entrées
		 */
        public function get_by_ids ($ids)
        {
			$query = " 
                SELECT * FROM sms_stops
                WHERE id ";
     
            //On génère la clause IN et les paramètres adaptés depuis le tableau des id 
            $generated_in = $this->generateInFromArray($ids);
            $query .= $generated_in['QUERY'];
            $params = $generated_in['PARAMS'];

            return $this->runQuery($query, $params);
        }

        /**
		 * Retourne une liste de sms_stopes sous forme d'un tableau
         * @param array $ids : un ou plusieurs id d'entrées à supprimer
         * @return int : Le nombre de lignes supprimées
		 */
        public function delete_by_ids ($ids)
        {
			$query = " 
                DELETE FROM sms_stops
                WHERE id ";
     
            //On génère la clause IN et les paramètres adaptés depuis le tableau des id 
            $generated_in = $this->generateInFromArray($ids);
            $query .= $generated_in['QUERY'];
            $params = $generated_in['PARAMS'];

            return $this->runQuery($query, $params, self::ROWCOUNT);
        }

        /**
         * Insert une sms_stope
         * @param array $sms_stop : La sms_stope à insérer avec les champs name, script, admin & admin
         * @return mixed bool|int : false si echec, sinon l'id de la nouvelle lignée insérée
         */
        public function insert ($sms_stop)
        {
            $result = $this->insertIntoTable('sms_stops', $sms_stops);

            if (!$result)
            {
                return false;
            }

            return $this->lastId();
        }

        /**
         * Met à jour une sms_stope par son id
         * @param int $id : L'id de la sms_stop à modifier
         * @param array $sms_stop : Les données à mettre à jour pour la sms_stope
         * @return int : le nombre de ligne modifiées
         */
        public function update ($id, $sms_stop)
        {
            return $this->updateTableWhere('sms_stops', $sms_stop, ['id' => $id]);
        }

        /**
         * Compte le nombre d'entrées dans la table sms_stop
         * @return int : Le nombre de sms_stops
         */
        public function count ()
        {
            return $this->countTable('sms_stops');
        }
    }
