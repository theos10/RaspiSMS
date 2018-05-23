<?php
namespace controllers\publics;
	/**
	 * Page des commandes
	 */
	class Commands extends \Controller
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

            $this->internalCommands = new \controllers\internals\Commands($this->bdd);
            $this->internalEvents = new \controllers\internals\Events($this->bdd);

			\controllers\internals\Tools::verify_connect();
        }

        /**
		 * Cette fonction retourne toutes les commandes, sous forme d'un tableau permettant l'administration de ces commandess
         */
        public function list ($page = 0)
        {
            $page = (int) $page;
            $commands = $this->internalCommands->getList(25, $page);
            $this->render('commands/list', ['commands' => $commands]);
        }    

		/**
		 * Cette fonction va supprimer une liste de commands
		 * @param array int $_GET['ids'] : Les id des commandes à supprimer
		 * @return boolean;
		 */
		public function delete($csrf)
		{
			if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
				header('Location: ' . $this->generateUrl('Commands', 'list'));
				return false;
			}

            $ids = $_GET['ids'] ?? [];
            $this->internalCommands->delete($ids);
            
			header('Location: ' . $this->generateUrl('Commands', 'list'));		
			return true;
		}

		/**
		 * Cette fonction retourne la page d'ajout d'une commande
		 */
		public function add()
		{
			$this->render('commands/add');
		}

		/**
		 * Cette fonction retourne la page d'édition des commandes
		 * @param array int $_GET['ids'] : Les id des commandes à editer
		 */
		public function edit()
		{
			global $db;
            $ids = $_GET['ids'] ?? [];
            
            $commands = $this->internalCommands->getByIds($ids);

			$this->render('commands/edit', array(
				'ids' => $commands,
			));
		}

		/**
		 * Cette fonction insert une nouvelle commande
		 * @param $csrf : Le jeton CSRF
		 * @param string $_POST['name'] : Le nom de la commande
		 * @param string $_POST['script'] : Le script a appeler
		 * @param boolean $_POST['admin'] : Si la commande necessite les droits d'admin (par défaut non)
		 * @return boolean;
		 */
		public function create($csrf)
		{
			if (!$this->verifyCSRF($csrf))
			{
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
				header('Location: ' . $this->generateUrl('Commands', 'list'));
				return false;
			}

			$nom = $_POST['name'] ?? false;
			$script = $_POST['script'] ?? false;
			$admin = (isset($_POST['admin']) ? $_POST['admin'] : false);

            if (!$nom || !$script)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Renseignez au moins un nom et un script.');
				header('Location: ' . $this->generateUrl('Commands', 'list'));
                return false;
            }

            if (!$this->internalCommands->create(['name' => $name, 'script' => $script, 'admin' => $admin]))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Impossible créer cette commande.');
				header('Location: ' . $this->generateUrl('commands', 'add'));
				return false;
            }

			$this->internalEvents->create(['type' => 'COMMAND_ADD', 'text' => 'Ajout commande : ' . $nom . ' => ' . $script]);
			
            \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'La commande a bien été crée.');
			header('Location: ' . $this->generateUrl('Commands', 'list'));
			return true;

		}

		/**
		 * Cette fonction met à jour une commande
		 * @param $csrf : Le jeton CSRF
		 * @param array $_POST['commands'] : Un tableau des commandes avec leur nouvelle valeurs
		 * @return boolean;
		 */
		public function update($csrf)
		{
			if (!$this->verifyCSRF($csrf))
			{
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
				header('Location: ' . $this->generateUrl('Commands', 'list'));
				return false;
			}

            $nb_commands_update = $this->internalCommands->update($_POST['commands']);

            if ($nb_commands_update != count($_POST['commands']))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Certaines commandes n\'ont pas pu êtres mises à jour.');
				header('Location: ' . $this->generateUrl('Commands', 'list'));
				return false;
            }

            \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'Toutes les commandes ont été modifiées avec succès.');
            header('Location: ' . $this->generateUrl('Commands', 'list'));
            return true;
		}
	}
