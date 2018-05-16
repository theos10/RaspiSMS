<?php
	/**
	 * Page des groups
	 */
	class Groups extends Controller
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

            $this->internalGroups = new \controllers\internals\Groups($this->bdd);
            $this->internalContacts = new \controllers\internals\Contacts($this->bdd);

			\controllers\internals\Tools::verify_connect();
        }

		/**
		 * Cette fonction retourne tous les groups, sous forme d'un tableau permettant l'administration de ces groups
		 */	
        public function list ($page = 0)
        {
            $page = int($page);
            $groups = $this->internalGroups->get_list(25, $page);
    
            foreach ($groups as $key => $group)
            {
                $contacts = $this->internalGroups->get_contacts($group['id']);
                $groups['nb_contacts'] = count($contacts);
            }

            $this->render('groups/list', ['groups' => $groups]);
        }    
		
		/**
         * Cette fonction va supprimer une liste de groups
         * @param array int $_GET['ids'] : Les id des groupes à supprimer
         * @return boolean;
         */
        public function delete($csrf)
        {
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Groups', 'list'));
                return false;
            }

            $ids = $_GET['ids'] ?? [];
            $this->internalGroups->delete($ids);

            header('Location: ' . $this->generateUrl('Groups', 'list'));
            return true;
        }

		/**
		 * Cette fonction retourne la page d'ajout d'un group
		 */
		public function add()
		{
			$this->render('groups/add');
		}

		/**
		 * Cette fonction retourne la page d'édition des groups
		 * @param int... $ids : Les id des groupes à supprimer
		 */
		public function edit()
        {
            $ids = $_GET['ids'] ?? [];

            $groups = $this->internalGroups->getByIds($ids);

            foreach ($groups as $key => $group)
            {
                $groups[$key]['contacts'] = $this->get_contacts($group['id']);
            }

            $this->render('groups/edit', array(
                'groups' => $groups,
            ));
        }

		/**
		 * Cette fonction insert un nouveau group
		 * @param $csrf : Le jeton CSRF
		 * @param string $_POST['name'] : Le nom du group
         * @param array $_POST['contacts'] : Les ids des contacts à mettre dans le group
		 */
		public function create($csrf)
		{
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Groups', 'add'));
                return false;
            }
			
			$name = $_POST['name'] ?? false;
			$contacts = $_POST['contacts'] ?? false;

			if (!$name || !$contacts))
			{
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Des champs sont manquants !');
                header('Location: ' . $this->generateUrl('Groups', 'add'));
                return false;
			}

			if (!$id_group = $this->internalGroups->create(['name' => $name], $contacts))
			{
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Impossible de créer ce groupe.');
				header('Location: ' . $this->generateUrl('Groups', 'add'));
				return false;
			}

            \controllers\internals\Events::create(['type' => 'GROUP_ADD', 'text' => 'Ajout groupe : ' . $name]);

            \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'Le groupe a bien été créé.');
			header('Location: ' . $this->generateUrl('Groups', 'list'));
			return true;
		}

		/**
         * Cette fonction met à jour une groupe
         * @param $csrf : Le jeton CSRF
         * @param array $_POST['groups'] : Un tableau des groupes avec leur nouvelle valeurs & une entrée 'contacts_id' avec les ids des contacts pour chaque group
         * @return boolean;
         */
        public function update($csrf)
        {
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Groups', 'list'));
                return false;
            }

            $nb_groups_update = $this->internalGroups->update($_POST['groups']);

            if ($nb_groups_update != count($_POST['groups']))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Certais groups n\'ont pas pu êtres mis à jour.');
                header('Location: ' . $this->generateUrl('Groups', 'list'));
                return false;
            }

            \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'Tous les groups ont été modifiés avec succès.');
            header('Location: ' . $this->generateUrl('Groups', 'list'));
            return true;
        }

		/**
		 * Cette fonction retourne la liste des groups sous forme JSON
		 */
		public function jsonGetGroups()
		{
            echo json_encode($this->internalGroups->get_list());
		}
	}
