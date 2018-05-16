<?php
	/**
	 * Page de connexion
	 */
	class Connect extends Controller
	{
		/**
		 * Cette fonction est appelée avant toute les autres : 
		 * Elle vérifie que l'utilisateur est bien connecté
		 * @return void;
		 */
		public function before()
        {
            global $bdd;
            global $model;
            $this->bdd = $bdd;
            $this->model = $model;

            $this->internalUsers = new \controllers\internals\Users($this->bdd);

			\controllers\internals\Tools::verify_connect();
        }

		/**
		 * Cette fonction retourne la fenetre de connexion
		 */	
		public function login ()
		{
			$this->render('connect/login');
		}
        
        /**
		 * Cette fonction connecte un utilisateur, et le redirige sur la page d'accueil
		 * @param string $_POST['mail'] : L'email de l'utilisateur
		 * @param string $_POST['password'] : Le mot de passe de l'utilisateur
		 * @return void
		 */
		public function connection()
		{
			$email = $_POST['mail'] ?? false;
            $password = $_POST['password'] ?? false;

			if (!$user = $this->internalUsers->check_credentials($email, $password))
			{
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Email ou mot de passe invalide.');
                header('Location: ' . $this->generateUrl('Connect', 'forget_password'));
                return false;
			}

			$_SESSION['connect'] = true;
			$_SESSION['admin'] = $user['admin'];
			$_SESSION['email'] = $user['email'];
			$_SESSION['transfer'] = $user['transfer'];
			$_SESSION['csrf'] = str_shuffle(uniqid().uniqid());
			header('Location: ' . $this->generateUrl(''));
			return true;
		}


		/**
		 * Cette fonction retourne la fenetre de changement de password
		 * @return void;
		 */
		public function forget_password()
		{
			$this->render('connect/forget-password');
        }

        /**
         * Cette fonction envoi un email contenant un lien pour re-générer un password oublié
         * @param string $csrf : jeton csrf
         * @param string $_POST['email'] : L'email pour lequel on veut envoyer un nouveau password
         */
        public function send_reset_password ($csrf)
        {
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Connect', 'forget_password'));
                return false;
            }

            $email = $_POST['email'] ?? false;

            if (!$email || !$user = this->internalUsers->get_by_email($email))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Aucun utilisateur n\'existe pour cette adresse mail.');
                header('Location: ' . $this->generateUrl('Connect', 'forget_password'));
                return false;
            }

            $Tokenista = new \Ingenerator\Tokenista(APP_SECRET);
            $token = $Tokenista->generate(3600, ['user_id' => $user['id']]);

            $reset_link = $this->generate_url('Connect', 'reset_password', ['user_id' => $user['id'], 'token' => $token]);

            \controllers\internals\Tools::send_email($email, EMAIL_RESET_PASSWORD, ['reset_link' => $reset_link]);

            return $this->render('connect/send-reset-password');
        }

        /**
         * Cette fonction permet à un utilisateur de re-définir son mot de passe
         * @param int $user_id : L'id du user dont on veut modifier le password
         * @param string $token : Le token permetttant de vérifier que l'opération est légitime
         * @param optionnal $_POST['password'] : Le nouveau password à utiliser
         */
        public function reset_password ($user_id, $token)
        {
            $password = $_POST['password'] ?? false;

            $Tokenista = new \Ingenerator\Tokenista(APP_SECRET);
            
            if (!$Tokenista->isValid($token, ['user_id' => $user_id]))
            {
                return $this->render('connect/reset-password-invalid');
            }

            if (!$password)
            {
                return $this->render('connect/reset-password');
            }

            $this->internalUsers->update_password($user_id, $password);
            return $this->render('connect/reset-password-done');
        }
	
		/**
		 * Cette fonction déconnecte un utilisateur et le renvoie sur la page d'accueil
		 * @return void
		 */
		public function logout()
		{
			session_unset();
			session_destroy();
			header('Location: ' . $this->generateUrl(''));
		}
	}
