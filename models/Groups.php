<?php
	namespace models;
	/**
     * Cette classe gère les accès bdd pour les groupes
	 */
	class Groups extends \Model
    {
        /**
         * Retourne une entrée par son id
         * @param int $id : L'id de l'entrée
         * @return array : L'entrée
         */
        public function get_by_id ($id)
        {
            $groups = $this->getFromTableWhere('groups', ['id' => $id]);
            return isset($groups[0]) ? $groups[0] : false;
        }
        
        /**
         * Retourne une entrée par son numéro de tel
         * @param string $name : Le numéro de tél
         * @return array : L'entrée
         */
        public function get_by_name ($name)
        {
            $groups = $this->getFromTableWhere('groups', ['name' => $name]);
            return isset($groups[0]) ? $groups[0] : false;
        }

		/**
		 * Retourne une liste de groupes sous forme d'un tableau
         * @param int $limit : Nombre de résultat maximum à retourner
         * @param int $offset : Nombre de résultat à ingnorer
		 */
		public function get_list ($limit, $offset)
        {
            $groups = $this->getFromTableWhere('groups', [], '', false, $limit, $offset);

	    	return $groups;
		}
        
        /**
		 * Retourne une liste de groupes sous forme d'un tableau
         * @param array $ids : un ou plusieurs id d'entrées à récupérer
         * @return array : La liste des entrées
		 */
        public function get_by_ids ($ids)
        {
			$query = " 
                SELECT * FROM groups
                WHERE id ";
     
            //On génère la clause IN et les paramètres adaptés depuis le tableau des id 
            $generated_in = $this->generateInFromArray($ids);
            $query .= $generated_in['QUERY'];
            $params = $generated_in['PARAMS'];

            return $this->runQuery($query, $params);
        }

        /**
		 * Retourne une liste de groupes sous forme d'un tableau
         * @param array $ids : un ou plusieurs id d'entrées à supprimer
         * @return int : Le nombre de lignes supprimées
		 */
        public function delete_by_ids ($ids)
        {
			$query = " 
                DELETE FROM groups
                WHERE id ";
     
            //On génère la clause IN et les paramètres adaptés depuis le tableau des id 
            $generated_in = $this->generateInFromArray($ids);
            $query .= $generated_in['QUERY'];
            $params = $generated_in['PARAMS'];

            return $this->runQuery($query, $params, self::ROWCOUNT);
        }
        
        /**
         * Supprime les liens groups/contacts pour un groups précis
         * @param int $id_group : L'id du group pour lequel supprimer
         * @return int : Le nmbre d'entrées modifiées
         */
        public function delete_groups_contacts ($id_group)
        {
            return $this->deleteFromTableWhere('groups_contacts', ['id_group' => $id_group]);
        }

        /**
         * Insert une groupe
         * @param array $group : La groupe à insérer avec les champs name, script, admin & admin
         * @return mixed bool|int : false si echec, sinon l'id de la nouvelle lignée insérée
         */
        public function insert ($group)
        {
            $result = $this->insertIntoTable('groups', $groups);

            if (!$result)
            {
                return false;
            }

            return $this->lastId();
        }
        
        /**
         * Insert un lien group/contact
         * @param int $id_group : L'id du group à liéer
         * @param int $id_contact : L'id du contact à liéer
         * @return mixed bool|int : false si echec, sinon l'id de la nouvelle lignée insérée
         */
        public function insert_groups_contacts ($id_group, $id_contact)
        {
            $result = $this->insertIntoTable('groups_contacts', ['id_group' => $id_group, 'id_contact' => $id_contact]);

            if (!$result)
            {
                return false;
            }

            return $this->lastId();
        }

        /**
         * Met à jour une groupe par son id
         * @param int $id : L'id de la group à modifier
         * @param array $group : Les données à mettre à jour pour la groupe
         * @return int : le nombre de ligne modifiées
         */
        public function update ($id, $group)
        {
            return $this->updateTableWhere('groups', $group, ['id' => $id]);
        }
        
        /**
         * Compte le nombre d'entrées dans la table
         * @return int : Le nombre d'entrées
         */
        public function count ()
        {
            return $this->countTable('groups');
        }
        
        /**
         * Cette fonction retourne les contacts pour un group
         * @param string $id : L'id du group
         * @return array : Un tableau avec les contacts
         */	
        public static function get_contacts ($id)
        {
            $query = "
                SELECT * 
                FROM contacts
                WHERE id IN (SELECT id_contact FROM groups_contacts WHERE id_group = :id)
            ";

            $params = array(
                'id' => $id,
            );

            return $this->runQuery($query, $params);
        }
    }
