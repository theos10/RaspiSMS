<?php
namespace controllers\publics;
	/**
	 * Page des users
	 */
	class Users extends \Controller
	{
		/**
		 * Cette fonction est appelée avant toute les autres : 
		 * Elle vérifie que l'utilisateur est bien connecté
		 * @return void;
		 */
		public function _before()
        {
            global $bdd;
            global $model;
            $this->bdd = $bdd;
            $this->model = $model;

            $this->internalUsers = new \controllers\internals\Users($this->bdd);
            $this->internalEvents = new \controllers\internals\Events($this->bdd);

			\controllers\internals\Tools::verify_connect();
        }

		/**
		 * Cette fonction retourne tous les users, sous forme d'un tableau permettant l'administration de ces users
		 */	
        public function list ($page = 0)
        {
            $page = (int) $page;
            $users = $this->internalUsers->get_list(25, $page);
            $this->render('users/list', ['users' => $users]);
        }    
		
		/**
         * Cette fonction va supprimer une liste de users
         * @param array int $_GET['ids'] : Les id des useres à supprimer
         * @return boolean;
         */
        public function delete($csrf)
        {
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Users', 'list'));
                return false;
            }

            if (!$_SESSION['admin'])
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Vous devez être administrateur pour supprimer un utilisateur !');
                header('Location: ' . $this->generateUrl('Users', 'list'));
                return false;
            }

            $ids = $_GET['ids'] ?? [];
            $this->internalUsers->delete($ids);

            header('Location: ' . $this->generateUrl('Users', 'list'));
            return true;
        }

		/**
		 * Cette fonction retourne la page d'ajout d'un user
		 */
		public function add()
		{
			$this->render('users/add');
		}

		/**
		 * Cette fonction insert un nouveau user
		 * @param $csrf : Le jeton CSRF
         * @param string $_POST['email'] : L'email de l'utilisateur
         * @param string $_POST['email_confirm'] : Verif de l'email de l'utilisateur
         * @param optional string $_POST['password'] : Le mot de passe de l'utilisateur (si vide, généré automatiquement)
         * @param optional string $_POST['password_confirm'] : Confirmation du mot de passe de l'utilisateur
         * @param optional boolean $_POST['admin'] : Si vrai, l'utilisateur est admin, si vide non
		 */
		public function create($csrf)
		{
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Users', 'add'));
                return false;
            }
			
			$email = $_POST['email'] ?? false;
			$email_confirm = $_POST['email_confirm'] ?? false;
			$password = $_POST['password'] ?? false;
			$password_confirm = $_POST['password_confirm'] ?? false;
			$admin = $_POST['admin'] ?? false;

			if (!$email || !$email != $email_confirm))
			{
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Vous devez au moins fournir une adresse e-mail pour l\'utilisateur.');
                header('Location: ' . $this->generateUrl('Users', 'add'));
                return false;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'L\'adresse e-mail n\'est pas valide.');
                header('Location: ' . $this->generateUrl('Users', 'add'));
                return false;
            }

            if (!\controllers\internals\Tools::send_email($email, EMAIL_CREATE_USER, ['email' => $email, 'password' => $password]))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Impossible d\'envoyer l\'e-mail à l\'utilisateur, le compte n\'a donc pas été créé.');
                header('Location: ' . $this->generateUrl('Users', 'add'));
                return false;
            }

			if (!$this->internalUsers->create(['email' => $email, 'password' => $password, 'admin' => $admin]))
			{
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Impossible de créer ce user.');
				header('Location: ' . $this->generateUrl('Users', 'add'));
				return false;
			}

            $this->internalEvents->create(['type' => 'CONTACT_ADD', 'text' => 'Ajout de l\'utilisateur : ' . $email . '.'));

			\modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'L\'utilisateur a bien été créé.');
			header('Location: ' . $this->generateUrl('Users', 'list'));
			return true;
		}
	}
