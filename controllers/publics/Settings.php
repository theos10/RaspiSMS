<?php
namespace controllers\publics;
	/**
	 * Page des settings
	 */
	class Settings extends \Controller
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

            $this->internalSettings = new \controllers\internals\Settings($this->bdd);

			\controllers\internals\Tools::verify_connect();
        }

		/**
		 * Cette fonction retourne tous les settings, sous forme d'un tableau permettant l'administration de ces settings
		 */	
        public function show ()
        {
            return $this->render('settings/show');
        }    
		
		/**
         * Cette fonction va permettre de mettre à jour un réglage
         * @param string $setting_name : Le nom du setting à modifier
         * @param $csrf : Le jeton csrf
         * @param string $_POST['setting_value'] : La nouvelle valeure du setting
         * @return boolean;
         */
        public function update ($setting_name, $csrf)
        {
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Settings', 'list'));
                return false;
            }

            if (!$_SESSION['admin'])
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Vous devez être administrateur pour pouvoir modifier un réglage.');
                header('Location: ' . $this->generateUrl('Settings', 'list'));
                return false;
            }

            $setting_value = isset($_POST['setting_value']) ? $_POST['setting_value'] : false;

            if ($setting_value === false)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Vous devez renseigner une valeure pour le réglage.');
                header('Location: ' . $this->generateUrl('Settings', 'list'));
                return false;
            }

            if (!$this->internalSettings->update($setting_name, $setting_value))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Impossible de mettre à jour ce réglage.');
                header('Location: ' . $this->generateUrl('Settings', 'list'));
                return false;
            }

            \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'Le réglage a bien été mis à jour.');
            header('Location: ' . $this->generateUrl('Settings', 'list'));
            return true;
        }

	}
