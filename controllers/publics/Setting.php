<?php
namespace controllers\publics;
	/**
	 * Page des settings
	 */
	class Setting extends \Controller
	{
		/**
		 * Cette fonction est appelée avant toute les autres : 
		 * Elle vérifie que l'utilisateur est bien connecté
		 * @return void;
		 */
		public function _before()
        {
            global $bdd;
            $this->bdd = $bdd;

            $this->internalSetting = new \controllers\internals\Setting($this->bdd);

			\controllers\internals\Tool::verify_connect();
        }

		/**
		 * Cette fonction retourne tous les settings, sous forme d'un tableau permettant l'administration de ces settings
		 */	
        public function show ()
        {
            return $this->render('setting/show');
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
                return header('Location: ' . $this->generateUrl('Setting', 'show'));
            }

            if (!$_SESSION['user']['admin'])
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Vous devez être administrateur pour pouvoir modifier un réglage.');
                return header('Location: ' . $this->generateUrl('Setting', 'show'));
            }

            $setting_value = isset($_POST['setting_value']) ? $_POST['setting_value'] : false;

            if ($setting_value === false)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Vous devez renseigner une valeure pour le réglage.');
                return header('Location: ' . $this->generateUrl('Setting', 'show'));
            }

            $update_setting_result = $this->internalSetting->update($setting_name, $setting_value);
            if ($update_setting_result === false)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Impossible de mettre à jour ce réglage.');
                return header('Location: ' . $this->generateUrl('Setting', 'show'));
            }

            \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'Le réglage a bien été mis à jour.');
            return header('Location: ' . $this->generateUrl('Setting', 'show'));
        }

	}
