<?php
namespace controllers\internals;
	/**
     * Controller interne des users
	 */
	class User extends \InternalController
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
            $modelUser = new \models\User($this->bdd);
            return $modelUser->get_list($nb_entry, $nb_entry * $page);
        }
        
        /**
		 * Cette fonction va supprimer une liste de users
		 * @param array $ids : Les id des useres à supprimer
		 * @return int : Le nombre de useres supprimées;
		 */
		public function delete ($id)
        {
            $modelUser = new \models\User($this->bdd);
            return $modelUser->delete_by_id($id);
		}

		/**
         * Cette fonction vérifie s'il existe un utilisateur qui corresponde à ce couple login/password
         * @param string $login : L'eamil de l'utilisateur
         * @param string $password : Le mot de passe de l'utilisateur
         * @return mixed false | array : False si pas de user, le user correspondant sous forme d'array sinon
		 */
		public function check_credentials ($login, $password)
        {
            $modelUser = new \models\User($this->bdd);

            if (!$user = $modelUser->get_by_email($login))
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
            $modelUser = new \models\User($this->bdd);

            $user = ['password' => password_hash($password, PASSWORD_DEFAULT)];

            return (bool) $modelUser->update($id, $user);
        }
        
        /**
         * Cette fonction change le transfer d'un utilisateur
         * @param string $id : L'id de l'utilisateur
         * @param string $transfer : Le nouveau statut du champs transfer
		 * @return boolean;
		 */
		public function update_transfer ($id, $transfer)
		{
            $modelUser = new \models\User($this->bdd);

            $user = ['transfer' => $transfer];

            return (bool) $modelUser->update($id, $user);
        }
        
        /**
         * Cette fonction change l'email d'un utilisateur
         * @param string $id : L'id de l'utilisateur
         * @param string $email : Le nouvel email
		 * @return boolean;
		 */
		public function update_email ($id, $email)
		{
            $modelUser = new \models\User($this->bdd);

            $user = ['email' => $email];

            return (bool) $modelUser->update($id, $user);
        }

        /**
         * Cette fonction retourne un utilisateur pour un mail donné
         * @param string $email : L'email de l'utilisateur
         * @return mixed boolean | array : false si pas de user pour le mail, le user sinon
         */
        public function get_by_email ($email)
        {
            $modelUser = new \models\User($this->bdd);
            return $modelUser->get_by_email($email);
        }

		/**
         * Cette fonction met à jour une série de users
         * @return int : le nombre de ligne modifiées
		 */
        public function update ($id, $email, $password, $admin, $transfer)
        {
            $modelUser = new \models\User($this->bdd);
            
            $user = [
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'admin' => $admin,
                'transfer' => $transfer,
            ];

            return $modelUser->update($id, $user); 
        }
        
        /**
         * Cette fonction insert une nouvelle usere
         * @param array $user : Un tableau représentant la usere à insérer
         * @return mixed bool|int : false si echec, sinon l'id de la nouvelle usere insérée
		 */
        public function create ($email, $password, $admin, $transfer)
        {
            $user = [
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'admin' => $admin,
                'transfer' => $transfer,
            ];

            $modelUser = new \models\User($this->bdd);
            $result = $modelUser->insert($user);

            if (!$result)
            {
                return false;
            }

            $internalEvent = new \controllers\internals\Event($this->bdd);
            $internalEvent->create('CONTACT_ADD', 'Ajout de l\'utilisateur : ' . $email . '.');
            
            return $result;
		}
	}
