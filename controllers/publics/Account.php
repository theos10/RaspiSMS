<?php
    namespace controllers\publics;
	/**
	 * Page des account
	 */
	class Account extends \Controller
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

            $this->internalAccount = new \controllers\internals\Account($this->bdd);

			\controllers\internals\Tools::verify_connect();
        }

		/**
         * Cette fonction affiche la page du profile
		 */	
        public function show ()
        {
            $this->render('profile/default');
        }    

		/**
         * Cette fonction change le mot de passe de l'utilisateur
         * @param $csrf : Le jeton CSRF
         * @param string $_POST['password'] : Le nouveau mot de passe de l'utilisateur
         * @param string $_POST['verif_password'] : La vérification du nouveau mot de passe de l'utilisateur
         * @return void;
         */
		public function change_password($csrf)
        {
            //On vérifie que le jeton csrf est bon
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Account', 'show'));
                return false;
            }

			$password = $_POST['password'] ?? false;
			$verif_password = $_POST['verif_password'] ?? false;

            if (!$password || !$verif_password || $verif_password !== $password)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Les mots de passes ne correspondent pas.');
                header('Location: ' . $this->generateUrl('Account', 'show'));
                return false;
            }

            $user = $this->internalUsers->get_by_email($_SESSION['email']);

            if (!$this->internalUsers->update_password($user['id'], $password))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Impossible de mettre à jour le mot de passe.');
                header('Location: ' . $this->generateUrl('Account', 'show'));
                return false;
            }

            \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'Le mot de passe a bien été mis à jour.');
            header('Location: ' . $this->generateUrl('Account', 'show'));
            return false;
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
                header('Location: ' . $this->generateUrl('Account', 'show'));
                return false;
            }

            $transfer = $_POST['transfer'] ?? false;
            $user = $this->internalUsers->get_by_email($_SESSION['email']);
        
            $user['transfer'] = $transfer;

            if (!$this->internalUsers->update([$user]))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Impossible de mettre à jour.');
                header('Location: ' . $this->generateUrl('Account', 'show'));
                return false;
            }
            
            \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'Le transfert a bien été ' . ($transfer ? 'activé' : 'désactivé') . '.');
            header('Location: ' . $this->generateUrl('Account', 'show'));
            return false;
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
                header('Location: ' . $this->generateUrl('Account', 'show'));
                return false;
            }

            $email = $_POST['email'] ?? false;
            $verif_email = $_POST['verif_email'] ?? false;
            
            if (!$email || $email !== $verif_email)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Les emails ne correspondent pas !');
                header('Location: ' . $this->generateUrl('Account', 'show'));
                return false;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'L\'adresse e-mail n\'est pas une adresse valide.');
                header('Location: ' . $this->generateUrl('Account', 'show'));
                return false;
            }

            $user = $this->internalUsers->get_by_email($_SESSION['email']);
            $user['email'] = $email;

            if (!$this->internalUsers->update([$user]))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Impossible de mettre à jour.');
                header('Location: ' . $this->generateUrl('Account', 'show'));
                return false;
            }
            
            \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'L\'email a bien été mis à jour.');
            header('Location: ' . $this->generateUrl('Account', 'show'));
            return false;
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
                header('Location: ' . $this->generateUrl('Account', 'show'));
                return false;
            }

            $delete_account = $_POST['delete_account'] ?? false;

            if (!$delete_account)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Pour supprimer le compte, vous devez cocher la case correspondante.');
                header('Location: ' . $this->generateUrl('Account', 'show'));
                return false;
            }
            
            $user = $this->internalUsers->get_by_email($_SESSION['email']);

            if (!$this->internalUsers->delete([$user['id']]))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Impossible de supprimer le compte.');
                header('Location: ' . $this->generateUrl('Account', 'show'));
                return false;
            }

            $this->logout();
            return true;
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
            return true;
        }
	}
