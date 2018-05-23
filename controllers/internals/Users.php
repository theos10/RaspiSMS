<?php
namespace controllers\internals;
	/**
     * Controller interne des users
	 */
	class Users extends \InternalController
	{
		/**
         * Cette fonction retourne une liste des useres sous forme d'un tableau
         * @param mixed(int|bool) $nb_entry : Le nombre d'entrées à retourner par page
         * @param mixed(int|bool) $page : Le numéro de page en cours
         * @return array : La liste des useres
         */	
		public function get_list ($nb_entry = false, $page = false)
		{
			//Recupération des useres
            $modelUsers = new \models\Users($this->bdd);
            return $modelUsers->get_list($nb_entry, $nb_entry * $page);
        }
        
        /**
		 * Cette fonction va supprimer une liste de users
		 * @param array $ids : Les id des useres à supprimer
		 * @return int : Le nombre de useres supprimées;
		 */
		public function delete ($ids)
        {
            $modelUsers = new \models\Users($this->bdd);
            return $modelUsers->delete_by_ids($ids);
		}

		/**
         * Cette fonction vérifie s'il existe un utilisateur qui corresponde à ce couple login/password
         * @param string $login : L'eamil de l'utilisateur
         * @param string $password : Le mot de passe de l'utilisateur
         * @return mixed false | array : False si pas de user, le user correspondant sous forme d'array sinon
		 */
		public function check_credentials ($login, $password)
        {
            $modelUsers = new \models\Users($this->bdd);

            if (!$user = $modelUsers->get_by_email($login))
            {
                return false;
            }

            if (!password_verify($password, $user['password']))
            {
                return false;
            }

            return $user;
		}

		/**
         * Cette fonction change le mot de passe d'un utilisateur à partir de son email.
         * @param string $id : L'id de l'utilisateur
         * @param string $password : Le mot de passe de l'utilisateur
		 * @return boolean;
		 */
		public function update_password ($id, $password)
		{
            $modelUsers = new \models\Users($this->bdd);

            if (!$user = $modelUsers->get_by_id($id))
            {
                return false;
            }

            $user['password'] = password_hash($password, PASSWORD_DEFAULT);

            return (bool) $modelUsers->update($id, $user);
        }

        /**
         * Cette fonction retourne un utilisateur pour un mail donné
         * @param string $email : L'email de l'utilisateur
         * @return mixed boolean | array : false si pas de user pour le mail, le user sinon
         */
        public function get_by_email ($email)
        {
            $modelUsers = new \models\Users($this->bdd);
            return $modelUsers->get_by_email($email);
        }

		/**
         * Cette fonction met à jour une série de users
         * @return int : le nombre de ligne modifiées
		 */
		public function update ($users)
        {
            $modelUsers = new \models\Users($this->bdd);
            
            $nb_update = 0;
            foreach ($users as $user)
            {
                $result = $modelUsers->update($user['id'], $user);

                if ($result)
                {
                    $nb_update ++;
                }
            }
        
            return $nb_update;
        }
        
        /**
         * Cette fonction insert une nouvelle usere
         * @param array $user : Un tableau représentant la usere à insérer
         * @return mixed bool|int : false si echec, sinon l'id de la nouvelle usere insérée
		 */
        public function create ($user)
        {
            $user['password'] = password_hash($user['password'], PASSWORD_DEFAULT);
            $modelUsers = new \models\Users($this->bdd);
            return $modelUsers->insert($user);
		}
	}
