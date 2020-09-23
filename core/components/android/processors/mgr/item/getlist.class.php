<?php

class androidItemGetListProcessor extends modObjectGetListProcessor
{
    public $objectType = 'androidItem';
    public $classKey = 'androidItem';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    //public $permission = 'list';


    /**
     * We do a special check of permissions
     * because our objects is not an instances of modAccessibleObject
     *
     * @return boolean|string
     */
    public function beforeQuery()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        if (!$Orgs = $this->modx->getService('organizations', 'Organizations',$this->modx->getOption('organizations_core_path', null, $this->modx->getOption('core_path') . 'components/organizations/') . 'model/organizations/', array())) {
			return $this->failure('Could not load Organizations class!');
		}
		$query = trim($this->getProperty('query'));
        $c->leftJoin('modUser','modUser', '`'.$this->classKey.'`.`user_id` = `modUser`.`id`');
		$c->leftJoin('Orgs','Orgs', '`'.$this->classKey.'`.`org_id` = `Orgs`.`id`');
		$Columns = $this->modx->getSelectColumns($this->classKey, $this->classKey, '', array(), true);
		$c->select($Columns . ', `modUser`.`username`, `Orgs`.`shortname`');
		if ($query) {
            $c->where(array(
                '`modUser`.`username`:LIKE' => "%{$query}%",
                'OR:`Orgs`.`shortname`:LIKE' => "%{$query}%",
            ));
        }
		//$c->prepare(); echo $c->toSQL(); exit;
        return $c;
    }


    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray();
        $array['actions'] = array();

        // Edit
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('android_item_update'),
            //'multiple' => $this->modx->lexicon('android_items_update'),
            'action' => 'updateItem',
            'button' => true,
            'menu' => true,
        );

        if (!$array['active']) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-green',
                'title' => $this->modx->lexicon('android_item_enable'),
                'multiple' => $this->modx->lexicon('android_items_enable'),
                'action' => 'enableItem',
                'button' => true,
                'menu' => true,
            );
        } else {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-gray',
                'title' => $this->modx->lexicon('android_item_disable'),
                'multiple' => $this->modx->lexicon('android_items_disable'),
                'action' => 'disableItem',
                'button' => true,
                'menu' => true,
            );
        }

        // Remove
        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o action-red',
            'title' => $this->modx->lexicon('android_item_remove'),
            'multiple' => $this->modx->lexicon('android_items_remove'),
            'action' => 'removeItem',
            'button' => true,
            'menu' => true,
        );

        return $array;
    }

}

return 'androidItemGetListProcessor';