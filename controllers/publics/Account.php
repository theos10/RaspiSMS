<?php
    namespace controllers\publics;
	/**
	 * Page des account
	 */
	class Account extends \Controller
    {
        public $bdd;

		/**
		 * Cette fonction est appelée avant toute les autres : 
		 * Elle vérifie que l'utilisateur est bien connecté
		 * @return void;
		 */
		public function _before()
        {
            global $bdd;
            $this->bdd = $bdd;

            \controllers\internals\Tool::verify_connect();
            $this->internalUser = new \controllers\internals\User($this->bdd);
        }

		/**
         * Cette fonction affiche la page du profile
		 */	
        public function show ()
        {
            $this->render('account/show');
        }    

		/**
         * Cette fonction change le mot de passe de l'utilisateur
         * @param $csrf : Le jeton CSRF
         * @param string $_POST['password'] : Le nouveau mot de passe de l'utilisateur
         * @return void;
         */
		public function change_password ($csrf)
        {
            //On vérifie que le jeton csrf est bon
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                return header('Location: ' . $this->generateUrl('Account', 'show'));
            }

			$password = $_POST['password'] ?? false;

            if (!$password)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Vous devez renseigner un mot de passe.');
                return header('Location: ' . $this->generateUrl('Account', 'show'));
            }

            $update_password_result = $this->internalUser->update_password($_SESSION['user']['id'], $password);

            if (!$update_password_result)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Impossible de mettre à jour le mot de passe.');
                return header('Location: ' . $this->generateUrl('Account', 'show'));
            }

            \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'Le mot de passe a bien été mis à jour.');
            return header('Location: ' . $this->generateUrl('Account', 'show'));
        }

        /**
         * Cette fonction va changer la valeure du champs transfer de l'utilisateur
         * @param $csrf : Le jeton CSRF
         * @param string $_POST['transfer'] : Le nouveau transfer de l'utilisateur
         */
        public function change_transfer ($csrf)
        {
            //On vérifie que le jeton csrf est bon
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                return header('Location: ' . $this->generateUrl('Account', 'show'));
            }

            $transfer = $_POST['transfer'] ?? false;

            $transfer_update_result = $this->internalUser->update_transfer($_SESSION['user']['id'], $transfer);
            if (!$transfer_update_result)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Impossible de mettre à jour.');
                return header('Location: ' . $this->generateUrl('Account', 'show'));
            }

            $_SESSION['user']['transfer'] = $transfer;
            
            \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'Le transfert a bien été ' . ($transfer ? 'activé' : 'désactivé') . '.');
            return header('Location: ' . $this->generateUrl('Account', 'show'));
        }

        /**
         * Cette fonction va changer l'email de l'utilisateur
         * @param $csrf : Le jeton CSRF
         * @param string $_POST['email'] : Le nouveau mail de l'utilisateur
         * @param string $_POST['verif_email'] : Le nouveau mail de l'utilisateur
         */
        public function change_email ($csrf)
        {
            //On vérifie que le jeton csrf est bon
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                return header('Location: ' . $this->generateUrl('Account', 'show'));
            }

            $email = $_POST['email'] ?? false;
            
            if (!$email)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Vous devez fournir une adresse e-mail !');
                return header('Location: ' . $this->generateUrl('Account', 'show'));
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'L\'adresse e-mail n\'est pas une adresse valide.');
                return header('Location: ' . $this->generateUrl('Account', 'show'));
            }

            $update_email_result = $this->internalUser->update_email($_SESSION['user']['id'], $email);
            if (!$update_email_result)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Impossible de mettre à jour.');
                return header('Location: ' . $this->generateUrl('Account', 'show'));
            }
            
            $_SESSION['user']['email'] = $email;

            \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'L\'email a bien été mis à jour.');
            return header('Location: ' . $this->generateUrl('Account', 'show'));
        }

		/**
         * Cette fonction va supprimer l'utilisateur
         * @param string $_POST['delete_account'] : La vérif que l'on veux bien supprimer l'utilisateur
         * @return boolean;
         */
        public function delete ($csrf)
        {
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                return header('Location: ' . $this->generateUrl('Account', 'show'));
            }

            $delete_account = $_POST['delete_account'] ?? false;

            if (!$delete_account)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Pour supprimer le compte, vous devez cocher la case correspondante.');
                return header('Location: ' . $this->generateUrl('Account', 'show'));
            }
            
            $delete_account_result = $this->internalUser->delete($_SESSION['user']['id']);
            if (!$delete_account_result)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Impossible de supprimer le compte.');
                return header('Location: ' . $this->generateUrl('Account', 'show'));
            }

            return $this->logout();
        }

        /**
         * Cette fonction déconnecte un utilisateur et le renvoie sur la page d'accueil
         * @return void
         */
        public function logout()
        {
            session_unset();
            session_destroy();
            return header('Location: ' . $this->generateUrl('Connect', 'login'));
        }
	}
