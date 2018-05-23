<?php
namespace controllers\publics;
	/**
	 * Page des scheduleds
	 */
	class Scheduleds extends \Controller
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

            $this->internalScheduleds = new \controllers\internals\Scheduleds($this->bdd);
            $this->internalEvents = new \controllers\internals\Events($this->bdd);

			\controllers\internals\Tools::verify_connect();
        }

		/**
		 * Cette fonction retourne tous les scheduleds, sous forme d'un tableau permettant l'administration de ces scheduleds
		 */	
        public function list ($page = 0)
        {
            $page = (int) $page;
            $scheduleds = $this->internalScheduleds->get_list(25, $page);
            $this->render('scheduleds/list', ['scheduleds' => $scheduleds]);
        }    
		
		/**
         * Cette fonction va supprimer une liste de scheduleds
         * @param array int $_GET['ids'] : Les id des scheduledes à supprimer
         * @return boolean;
         */
        public function delete($csrf)
        {
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Scheduleds', 'list'));
                return false;
            }

            $ids = $_GET['ids'] ?? [];
            $this->internalScheduleds->delete($ids);

            header('Location: ' . $this->generateUrl('Scheduleds', 'list'));
            return true;
        }

		/**
		 * Cette fonction retourne la page d'ajout d'un scheduled
		 */
		public function add()
        {
            $now = new \DateTime();
            $less_one_minute = new \DateInterval('PT1M');
            $now->sub($less_one_minute);

            $this->render('scheduleds/add', [
                'now' => $now->format('Y-m-d H:i'),
            ]);
		}

		/**
		 * Cette fonction retourne la page d'édition des scheduleds
		 * @param int... $ids : Les id des scheduledes à supprimer
		 */
		public function edit()
        {
            $ids = $_GET['ids'] ?? [];

            $scheduleds = $this->internalScheduleds->get_by_ids($ids);

            //Pour chaque message on ajoute les numéros, les contacts & les groupes
            foreach ($scheduleds as $key => $scheduled)
            {
                $scheduleds[$key]['numbers'] = [];
                $scheduleds[$key]['contacts'] = [];
                $scheduleds[$key]['groups'] = [];


                $numbers = $this->internalScheduleds->get_numbers($scheduled['id']);
                foreach ($numbers as $number)
                {
                    $scheduleds[$key]['numbers'][] = $number['number'];
                }
                
                $contacts = $this->internalScheduleds->get_contacts($scheduled['id']);
                foreach ($contacts as $contact)
                {
                    $scheduleds[$key]['contacts'][] = (int) $contact['id'];
                }
                
                $groups = $this->internalScheduleds->get_groups($scheduled['id']);
                foreach ($groups as $group)
                {
                    $scheduleds[$key]['groups'][] = (int) $group['id'];
                }
            }


            $this->render('scheduleds/edit', array(
                'scheduleds' => $scheduleds,
            ));
        }

		/**
		 * Cette fonction insert un nouveau scheduled
		 * @param $csrf : Le jeton CSRF
		 * @param string $_POST['name'] : Le nom du scheduled
		 * @param string $_POST['date'] : La date d'envoie du scheduled
		 * @param string $_POST['numbers'] : Les numeros de téléphone du scheduled
		 * @param string $_POST['contacts'] : Les contacts du scheduled
		 * @param string $_POST['groups'] : Les groupes du scheduled
		 */
		public function create($csrf)
		{
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Scheduleds', 'add'));
                return false;
            }
			
			$date = $_POST['date'] ?? false;
            $content = $_POST['content'] ?? false;
            $numbers = $_POST['numbers'] ?? [];
            $contacts = $_POST['contacts'] ?? [];
            $groups = $_POST['groups'] ?? [];

            if (!$content)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Vous ne pouvez pas créer un SMS sans message.');
                header('Location: ' . $this->generateUrl('Scheduleds', 'add'));
                return false;
            }

            if (!\controllers\internals\Tools::validate_date($date, 'Y-m-d H:i:s') && !\controllers\internals\Tools::validate_date($date, 'Y-m-d H:i'))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Vous devez fournir une date valide.');
                header('Location: ' . $this->generateUrl('Scheduleds', 'add'));
                return false;
            }
            
            foreach ($numbers as $key => $number)
            {
                $number = \controllers\internals\Tools::parse_phone($number);

                if (!$number)
                {
                    unset($numbers[$key]);
                    continue;
                }

                $numbers[$key] = $number;   
            }

            if (!$numbers && !$contacts && !$groups)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Vous devez renseigner au moins un destinataire pour le SMS.');
                header('Location: ' . $this->generateUrl('Scheduleds', 'add'));
                return false;
            }

            $scheduled = [
                'at' => $date,
                'content' => $content,
                'flash' => false,
                'progress' => false,
            ];

            if (!$scheduled_id = $this->internalScheduleds->create($scheduled, $numbers, $contacts, $groups))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Impossible de créer le SMS.');
                header('Location: ' . $this->generateUrl('Scheduleds', 'add'));
                return false;
            }

			$this->internalEvents->create(['type' => 'SCHEDULED_ADD', 'text' => 'Ajout d\'un SMS pour le ' . $date . '.']);

			\modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'Le SMS a bien été créé pour le ' . $date . '.');
			header('Location: ' . $this->generateUrl('Scheduleds', 'list'));
			return true;
		}

		/**
         * Cette fonction met à jour une schedulede
         * @param $csrf : Le jeton CSRF
         * @param array $_POST['scheduleds'] : Un tableau des scheduledes avec leur nouvelle valeurs + les numbers, contacts et groups liées
         * @return boolean;
         */
        public function update($csrf)
        {
            if (!$this->verifyCSRF($csrf))
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Jeton CSRF invalid !');
                header('Location: ' . $this->generateUrl('Scheduleds', 'list'));
                return false;
            }
            
            $scheduleds = $_POST['scheduleds'] ?? [];

            $all_update_ok = true;

            foreach ($scheduleds as $id_scheduled => $scheduled)
            {

                $date = $scheduled['date'] ?? false;
                $content = $scheduled['content'] ?? false;
                $numbers = $scheduled['numbers'] ?? [];
                $contacts = $scheduled['contacts'] ?? [];
                $groups = $scheduled['groups'] ?? [];

                if (!$content)
                {
                    $all_update_ok = false;
                    continue;
                }

                if (!\controllers\internals\Tools::validate_date($date, 'Y-m-d H:i:s') && !\controllers\internals\Tools::validate_date($date, 'Y-m-d H:i'))
                {
                    $all_update_ok = false;
                    continue;
                }
                
                foreach ($numbers as $key => $number)
                {
                    $number = \controllers\internals\Tools::parse_phone($number);

                    if (!$number)
                    {
                        unset($numbers[$key]);
                        continue;
                    }

                    $numbers[$key] = $number;   
                }

                if (!$numbers && !$contacts && !$groups)
                {
                    $all_update_ok = false;
                    continue;
                }

                $scheduled = [
                    'scheduled' => [
                        'id' => $id_scheduled,
                        'at' => $date,
                        'content' => $content,
                        'flash' => false,
                        'progress' => false,
                    ],
                    'numbers' => $numbers,
                    'contacts_ids' => $contacts,
                    'groups_ids' => $groups,
                ];

                if (!$this->internalScheduleds->update([$scheduled]))
                {
                    $all_update_ok = false;
                    continue;
                }
            }

            if (!$all_update_ok)
            {
                \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('danger', 'Certains SMS n\'ont pas pu êtres mis à jour.');
                header('Location: ' . $this->generateUrl('Scheduleds', 'list'));
                return false;
            }

            \modules\DescartesSessionMessages\internals\DescartesSessionMessages::push('success', 'Tous les SMS ont été mis à jour.');
            header('Location: ' . $this->generateUrl('Scheduleds', 'list'));
            return false;
            return true;
        }
	}
