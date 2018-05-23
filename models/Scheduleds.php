<?php
	namespace models;
	/**
     * Cette classe gère les accès bdd pour les scheduledes
	 */
	class Scheduleds extends \Model
    {
        /**
         * Retourne une entrée par son id
         * @param int $id : L'id de l'entrée
         * @return array : L'entrée
         */
        public function get_by_id ($id)
        {
            $scheduleds = $this->getFromTableWhere('scheduleds', ['id' => $id]);
            return isset($scheduleds[0]) ? $scheduleds[0] : false;
        }

		/**
		 * Retourne une liste de scheduledes sous forme d'un tableau
         * @param int $limit : Nombre de résultat maximum à retourner
         * @param int $offset : Nombre de résultat à ingnorer
		 */
		public function get_list ($limit, $offset)
        {
            $scheduleds = $this->getFromTableWhere('scheduleds', [], '', false, $limit, $offset);

	    	return $scheduleds;
		}
        
        /**
		 * Retourne une liste de scheduledes sous forme d'un tableau
         * @param array $ids : un ou plusieurs id d'entrées à récupérer
         * @return array : La liste des entrées
		 */
        public function get_by_ids ($ids)
        {
			$query = " 
                SELECT * FROM scheduleds
                WHERE id ";
     
            //On génère la clause IN et les paramètres adaptés depuis le tableau des id 
            $generated_in = $this->generateInFromArray($ids);
            $query .= $generated_in['QUERY'];
            $params = $generated_in['PARAMS'];

            return $this->runQuery($query, $params);
        }

		/** 
         * Cette fonction retourne les messages programmés avant une date et pour un numéro
         * @param \DateTime $date : La date avant laquelle on veux le message
         * @param string $number : Le numéro
         * @return array : Les messages programmés avant la date
         */
        public function get_before_date_for_number ($date, $number)
        {   
			$query = " 
                SELECT *
                FROM scheduleds
                WHERE at <= :date
                AND (
                    id IN (
                        SELECT id_scheduled
                        FROM scheduleds_numbers
                        WHERE number = :number
                    )
                    OR id IN (
                        SELECT id_scheduled
                        FROM scheduleds_contacts
                        WHERE id_contact IN (
                            SELECT id
                            FROM contacts
                            WHERE number = :number
                        )
                    )
                    OR id IN (
                        SELECT id_scheduled
                        FROM scheduleds_groups
                        WHERE id_group IN (
                            SELECT id_group
                            FROM groups_contacts
                            WHERE id_contact IN (
                                SELECT id
                                FROM contacts
                                WHERE number = :number
                            )
                        )
                    )
                )
            ";
     
            $params = array(
                'date' => $date,
                'number' => $number,
            );

            return $this->runQuery($query, $params);
        }

        /**
		 * Retourne une liste de scheduledes sous forme d'un tableau
         * @param array $ids : un ou plusieurs id d'entrées à supprimer
         * @return int : Le nombre de lignes supprimées
		 */
        public function delete_by_ids ($ids)
        {
			$query = " 
                DELETE FROM scheduleds
                WHERE id ";
     
            //On génère la clause IN et les paramètres adaptés depuis le tableau des id 
            $generated_in = $this->generateInFromArray($ids);
            $query .= $generated_in['QUERY'];
            $params = $generated_in['PARAMS'];

            return $this->runQuery($query, $params, self::ROWCOUNT);
        }

        /**
         * Insert une schedulede
         * @param array $scheduled : La schedulede à insérer avec les champs name, script, admin & admin
         * @return mixed bool|int : false si echec, sinon l'id de la nouvelle lignée insérée
         */
        public function insert ($scheduled)
        {
            $result = $this->insertIntoTable('scheduleds', $scheduled);

            if (!$result)
            {
                return false;
            }

            return $this->lastId();
        }

        /**
         * Met à jour une schedulede par son id
         * @param int $id : L'id de la scheduled à modifier
         * @param array $scheduled : Les données à mettre à jour pour la schedulede
         * @return int : le nombre de ligne modifiées
         */
        public function update ($id, $scheduled)
        {
            return $this->updateTableWhere('scheduleds', $scheduled, ['id' => $id]);
        }
        
        /**
         * Compte le nombre d'entrées dans la table
         * @return int : Le nombre d'entrées
         */
        public function count ()
        {
            return $this->countTable('scheduleds');
        }
        
        /**
         * Cette fonction retourne une liste de numéro pour un scheduleds
         * @param int $id_scheduled : L'id du scheduled pour lequel on veux le numéro
         * @return array : Les numéros des scheduleds
         */
        public function get_numbers ($id_scheduled)
        {
            return $this->getFromTableWhere('scheduleds_numbers', ['id_scheduled' => $id_scheduled]);
        }
        
        /**
         * Cette fonction retourne une liste de contacts pour un scheduleds
         * @param int $id_scheduled : L'id du scheduled pour lequel on veux le numéro
         * @return array : Les contacts du scheduleds
         */
        public function get_contacts ($id_scheduled)
        {
            $query = 'SELECT * FROM contacts WHERE id IN (SELECT id_contact FROM scheduleds_contacts WHERE id_scheduled = :id_scheduled)';

            $params = ['id_scheduled' => $id_scheduled];

            return $this->runQuery($query, $params);
        }
        
        /**
         * Cette fonction retourne une liste de groupes pour un scheduleds
         * @param int $id_scheduled : L'id du scheduled pour lequel on veux le numéro
         * @return array : Les groupes du scheduleds
         */
        public function get_groups ($id_scheduled)
        {
            $query = 'SELECT * FROM groups WHERE id IN (SELECT id_group FROM scheduleds_groups WHERE id_scheduled = :id_scheduled)';

            $params = ['id_scheduled' => $id_scheduled];

            return $this->runQuery($query, $params);
        }
        
        /**
         * Insert un liens scheduled/number
         * @param int $id_scheduled : L'id du scheduled
         * @param string $number : Le numéro à lier
         * @return int : le nombre d'entrées
         */
        public function insert_scheduleds_number ($id_scheduled, $number)
        {
            $result = $this->insertIntoTable('scheduleds_numbers', ['id_scheduled' => $id_scheduled, 'number' => $number]);

            if (!$result)
            {
                return false;
            }

            return $this->lastId();
        }

        /**
         * Insert un liens scheduled/contact
         * @param int $id_scheduled : L'id du scheduled
         * @param int $id_contact : L'id du contact
         * @return int : le nombre d'entrées
         */
        public function insert_scheduleds_contacts ($id_scheduled, $id_contact)
        {
            $result = $this->insertIntoTable('scheduleds_contacts', ['id_scheduled' => $id_scheduled, 'id_contact' => $id_contact]);

            if (!$result)
            {
                return false;
            }

            return $this->lastId();
        }
        
        /**
         * Insert un liens scheduled/group
         * @param int $id_scheduled : L'id du scheduled
         * @param int $id_group : L'id du group
         * @return int : le nombre d'entrées
         */
        public function insert_scheduleds_groups ($id_scheduled, $id_group)
        {
            $result = $this->insertIntoTable('scheduleds_groups', ['id_scheduled' => $id_scheduled, 'id_group' => $id_group]);

            if (!$result)
            {
                return false;
            }

            return $this->lastId();
        }

        /**
         * Supprime les liens scheduleds/numbers pour un scheduleds précis
         * @param int $id_scheduled : L'id du scheduled pour lequel supprimer
         * @return int : Le nmbre d'entrées modifiées
         */
        public function delete_scheduleds_numbers ($id_scheduled)
        {
            return $this->deleteFromTableWhere('scheduleds_numbers', ['id_scheduled' => $id_scheduled]);
        }
        
        /**
         * Supprime les liens scheduleds/contacts pour un scheduleds précis
         * @param int $id_scheduled : L'id du scheduled pour lequel supprimer
         * @return int : Le nmbre d'entrées modifiées
         */
        public function delete_scheduleds_contacts ($id_scheduled)
        {
            return $this->deleteFromTableWhere('scheduleds_contacts', ['id_scheduled' => $id_scheduled]);
        }
        
        /**
         * Supprime les liens scheduleds/groups pour un scheduleds précis
         * @param int $id_scheduled : L'id du scheduled pour lequel supprimer
         * @return int : Le nmbre d'entrées modifiées
         */
        public function delete_scheduleds_groups ($id_scheduled)
        {
            return $this->deleteFromTableWhere('scheduleds_groups', ['id_scheduled' => $id_scheduled]);
        }
    }
