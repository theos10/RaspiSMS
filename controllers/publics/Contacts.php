<?php
namespace controllers\publics;
	/**
	 * Page des contacts
	 */
	class Contacts extends \Controller
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

            $this->internalContacts = new \controllers\internals\Contacts($this->bdd);
            $this->internalEvents = new \controllers\internals\Events($this->bdd);

			\controllers\internals\Tools::verify_connect();
        }

		/**
		 * Cette fonction retourne tous les contacts, sous forme d'un tableau permettant l'administration de ces contacts
		 */	
        public function list ($page = 0)
        {
            $page = (int) $page;
            $contacts = $this->internalContacts->get_list(25, $page);
            $this->render('contacts/list', ['contacts' => $contacts]);
        }    
		
		/**
         * Cette fonction va supprimer une liste de contacts
         * @param array int $_GET['ids'] : Les id des contactes à supprimer
         * @return boolean;
         */
        public function delete($csrf)
        {
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Contacts', 'list'));
                return false;
            }

            $ids = $_GET['ids'] ?? [];
            $this->internalContacts->delete($ids);

            header('Location: ' . $this->generateUrl('Contacts', 'list'));
            return true;
        }

		/**
		 * Cette fonction retourne la page d'ajout d'un contact
		 */
		public function add()
		{
			$this->render('contacts/add');
		}

		/**
		 * Cette fonction retourne la page d'édition des contacts
		 * @param int... $ids : Les id des contactes à supprimer
		 */
		public function edit()
        {
            global $db;
            $ids = $_GET['ids'] ?? [];

            $contacts = $this->internalContacts->getByIds($ids);

            $this->render('contacts/edit', array(
                'ids' => $contacts,
            ));
        }

		/**
		 * Cette fonction insert un nouveau contact
		 * @param $csrf : Le jeton CSRF
		 * @param string $_POST['name'] : Le nom du contact
		 * @param string $_POST['phone'] : Le numero de téléphone du contact
		 */
		public function create($csrf)
		{
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Contacts', 'add'));
                return false;
            }
			
			$name = $_POST['name'] ?? false;
			$phone = $_POST['phone'] ?? false;

			if (!$name || !$phone)
			{
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Des champs sont manquants !');
                header('Location: ' . $this->generateUrl('Contacts', 'add'));
                return false;
			}

			if (!$phone = \controllers\internals\Tools::parse_phone($phone))
			{
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Numéro de téléphone incorrect.');
				header('Location: ' . $this->generateUrl('Contacts', 'add'));
				return false;
			}

			if (!$this->internalContacts->create(['number' => $phone, 'name' => $name]))
			{
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Impossible de créer ce contact.');
				header('Location: ' . $this->generateUrl('Contacts', 'add'));
				return false;
			}

			$this->internalEvents->create(['type' => 'CONTACT_ADD', 'text' => 'Ajout contact : ' . $name . ' (' . \controllers\internals\Tools::phone_add_space($phone) . ')']);

			\modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'Impossible de créer ce contact.');
			header('Location: ' . $this->generateUrl('Contacts', 'list'));
			return true;
		}

		/**
         * Cette fonction met à jour une contacte
         * @param $csrf : Le jeton CSRF
         * @param array $_POST['contacts'] : Un tableau des contactes avec leur nouvelle valeurs
         * @return boolean;
         */
        public function update($csrf)
        {
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Contacts', 'list'));
                return false;
            }

            $nb_contacts_update = $this->internalContacts->update($_POST['contacts']);

            if ($nb_contacts_update != count($_POST['contacts']))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Certais contacts n\'ont pas pu êtres mis à jour.');
                header('Location: ' . $this->generateUrl('Contacts', 'list'));
                return false;
            }

            \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'Tous les contacts ont été modifiés avec succès.');
            header('Location: ' . $this->generateUrl('Contacts', 'list'));
            return true;
        }

		/**
		 * Cette fonction retourne la liste des contacts sous forme JSON
		 */
		public function json_list()
        {
            header('Content-Type: application/json');
            echo json_encode($this->internalContacts->get_list());
		}
	}
