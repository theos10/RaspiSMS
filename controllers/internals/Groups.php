<?php
namespace controllers\internals;
	/**
	 * Classe des groupes
	 */
	class Groups extends \InternalController
	{

		/**
         * Cette fonction retourne une liste des groupes sous forme d'un tableau
         * @param mixed(int|bool) $nb_entry : Le nombre d'entrées à retourner par page
         * @param mixed(int|bool) $page : Le numéro de page en cours
         * @return array : La liste des groupes
         */	
		public function get_list ($nb_entry = false, $page = false)
		{
			//Recupération des groupes
            $modelGroups = new \models\Groups($this->bdd);
            return $modelGroups->get_list($nb_entry, $nb_entry * $page);
		}

		/**
         * Cette fonction retourne une liste des groupes sous forme d'un tableau
         * @param array int $ids : Les ids des entrées à retourner
         * @return array : La liste des groupes
         */	
		public function get_by_ids ($ids)
		{
			//Recupération des groupes
            $modelGroups = new \models\Groups($this->bdd);
            return $modelGroups->get_by_ids($ids);
        }
        
        /**
         * Cette fonction retourne un group par son name
         * @param string $name : Le name du group
         * @return array : Le group
         */	
        public function get_by_name ($name)
		{
			//Recupération des groupes
            $modelGroups = new \models\Groups($this->bdd);
            return $modelGroups->get_by_name($name);
        }

        /**
         * Cette fonction permet de compter le nombre de groups
         * @return int : Le nombre d'entrées dans la table
         */
        public function count ()
        {
            $modelGroups = new \models\Groups($this->bdd);
            return $modelGroups->count();
        }

		/**
		 * Cette fonction va supprimer une liste de groups
		 * @param array $ids : Les id des groupes à supprimer
		 * @return int : Le nombre de groupes supprimées;
		 */
		public function delete ($ids)
        {
            $modelGroups = new \models\Groups($this->bdd);
            return $modelGroups->delete_by_ids($ids);
		}

		/**
         * Cette fonction insert une nouvelle groupe
         * @param array $group : Un tableau représentant la groupe à insérer
         * @param array $groups_ids : Un tableau des ids des groups
         * @return mixed bool|int : false si echec, sinon l'id de la nouvelle groupe insérée
		 */
        public function create ($group, $groups_ids)
		{
            $modelGroups = new \models\Groups($this->bdd);
        
            if (!$id_group = $modelGroups->insert($group))
            {
                return false;
            }

            foreach ($groups_ids as $group_id)
            {
                $modelGroups->insert_groups_groups($id_group, $group_id);
            }

            return $id_group;
		}

		/**
         * Cette fonction met à jour une série de groupes
         * @param array $groups : Un tableau avec les groupes et pour chaque groupe une entrée 'groups' avec un tableau des id des groups à lier
         * @return int : le nombre de ligne modifiées
		 */
		public function update ($groups)
        {
            $modelGroups = new \models\Groups($this->bdd);
            
            $nb_update = 0;
            foreach ($groups as $group)
            {
                $result = $modelGroups->update($group['id'], $group);

                if (!$result)
                {
                    continue;
                }

                $modelGroups->delete_groups_groups($group['id']);

                foreach ($group['groups_ids'] as $group_id)
                {
                    $modelGroups->insert_groups_groups($group['id'], $group_id);
                }

                $nb_update ++;
            }
        
            return $nb_update;
        }
        
        /**
         * Cette fonction retourne les groups pour un group
         * @param string $id : L'id du group
         * @return array : Un tableau avec les groups
         */	
        public function get_groups ($id)
		{
			//Recupération des groupes
            $modelGroups = new \models\Groups($this->bdd);
            return $modelGroups->get_groups($id);
        }

	}
