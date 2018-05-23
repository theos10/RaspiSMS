<?php
namespace controllers\internals;
	/**
	 * Classe des contactes
	 */
	class Contacts extends \InternalController
	{

		/**
         * Cette fonction retourne une liste des contactes sous forme d'un tableau
         * @param mixed(int|bool) $nb_entry : Le nombre d'entrées à retourner par page
         * @param mixed(int|bool) $page : Le numéro de page en cours
         * @return array : La liste des contactes
         */	
		public function get_list ($nb_entry = false, $page = false)
		{
			//Recupération des contactes
            $modelContacts = new \models\Contacts($this->bdd);
            return $modelContacts->get_list($nb_entry, $nb_entry * $page);
		}

		/**
         * Cette fonction retourne une liste des contactes sous forme d'un tableau
         * @param array int $ids : Les ids des entrées à retourner
         * @return array : La liste des contactes
         */	
		public function get_by_ids ($ids)
		{
			//Recupération des contactes
            $modelContacts = new \models\Contacts($this->bdd);
            return $modelContacts->get_by_ids($ids);
        }
        
        /**
         * Cette fonction retourne un contact par son numéro de tel
         * @param string $number : Le numéro du contact
         * @return array : Le contact
         */	
		public function get_by_number ($number)
		{
			//Recupération des contactes
            $modelContacts = new \models\Contacts($this->bdd);
            return $modelContacts->get_by_number($number);
        }
        
        /**
         * Cette fonction retourne un contact par son name
         * @param string $name : Le name du contact
         * @return array : Le contact
         */	
        public function get_by_name ($name)
		{
			//Recupération des contactes
            $modelContacts = new \models\Contacts($this->bdd);
            return $modelContacts->get_by_name($name);
        }
        

        /**
         * Cette fonction permet de compter le nombre de contacts
         * @return int : Le nombre d'entrées dans la table
         */
        public function count ()
        {
            $modelContacts = new \models\Contacts($this->bdd);
            return $modelContacts->count();
        }

		/**
		 * Cette fonction va supprimer une liste de contacts
		 * @param array $ids : Les id des contactes à supprimer
		 * @return int : Le nombre de contactes supprimées;
		 */
		public function delete ($ids)
        {
            $modelContacts = new \models\Contacts($this->bdd);
            return $modelContacts->delete_by_ids($ids);
		}

		/**
         * Cette fonction insert une nouvelle contacte
         * @param array $contact : Un tableau représentant la contacte à insérer
         * @return mixed bool|int : false si echec, sinon l'id de la nouvelle contacte insérée
		 */
        public function create ($contact)
		{
            $modelContacts = new \models\Contacts($this->bdd);
            return $modelContacts->insert($contact);
		}

		/**
         * Cette fonction met à jour une série de contactes
         * @return int : le nombre de ligne modifiées
		 */
		public function update ($contacts)
        {
            $modelContacts = new \models\Contacts($this->bdd);
            
            $nb_update = 0;
            foreach ($contacts as $contact)
            {
                $result = $modelContacts->update($contact['id'], $contact);

                if ($result)
                {
                    $nb_update ++;
                }
            }
        
            return $nb_update;
        }
	}
