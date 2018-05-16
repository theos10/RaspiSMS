<?php
	namespace models;
	/**
     * Cette classe gère les accès bdd pour les users
	 */
	class Users extends \Model
    {
        /**
         * Retourne une entrée par son id
         * @param int $id : L'id de l'entrée
         * @return array : L'entrée
         */
        public function get_by_id ($id)
        {
            $users = $this->getFromTableWhere('users', ['id' => $id]);
            return isset($users[0]) ? $users[0] : false;
        }


        /**
         * Retourne un user par son email
         * @param string $email : L'email du user
         * @return mixed array | false : false si pas de user pour ce mail, sinon le user associé sous forme de tableau
         */
        public function get_by_email ($email)
        {
            $users = $this->getFromTableWhere('users', ['email' => $email]);
            return $users ? $users[0] : false;
        }

		/**
		 * Retourne une liste de useres sous forme d'un tableau
         * @param int $limit : Nombre de résultat maximum à retourner
         * @param int $offset : Nombre de résultat à ingnorer
		 */
		public function get_list ($limit, $offset)
        {
            $users = $this->getFromTableWhere('users', [], '', false, $limit, $offset);

	    	return $users;
		}
        
        /**
		 * Retourne une liste de useres sous forme d'un tableau
         * @param array $ids : un ou plusieurs id d'entrées à récupérer
         * @return array : La liste des entrées
		 */
        public function get_by_ids ($ids)
        {
			$query = " 
                SELECT * FROM users
                WHERE id ";
     
            //On génère la clause IN et les paramètres adaptés depuis le tableau des id 
            $generated_in = $this->generateInFromArray($ids);
            $query .= $generated_in['QUERY'];
            $params = $generated_in['PARAMS'];

            return $this->runQuery($query, $params);
        }
        /**
		 * Retourne une liste de useres sous forme d'un tableau
         * @param array $ids : un ou plusieurs id d'entrées à supprimer
         * @return int : Le nombre de lignes supprimées
		 */
        public function delete_by_ids ($ids)
        {
			$query = " 
                DELETE FROM users
                WHERE id ";
     
            //On génère la clause IN et les paramètres adaptés depuis le tableau des id 
            $generated_in = $this->generateInFromArray($ids);
            $query .= $generated_in['QUERY'];
            $params = $generated_in['PARAMS'];

            return $this->runQuery($query, $params, self::ROWCOUNT);
        }

        /**
         * Insert un user
         * @param array $user : La user à insérer avec les champs name, script, admin & admin
         * @return mixed bool|int : false si echec, sinon l'id de la nouvelle lignée insérée
         */
        public function insert ($user)
        {
            $result = $this->insertIntoTable('users', $users);

            if (!$result)
            {
                return false;
            }

            return $this->lastId();
        }

        /**
         * Met à jour un user par son id
         * @param int $id : L'id de la user à modifier
         * @param array $user : Les données à mettre à jour pour la user
         * @return int : le nombre de ligne modifiées
         */
        public function update ($id, $user)
        {
            return $this->updateTableWhere('users', $user, ['id' => $id]);
        }
    }
