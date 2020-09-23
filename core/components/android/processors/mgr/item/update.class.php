<?php

class androidItemUpdateProcessor extends modObjectUpdateProcessor
{
    public $objectType = 'androidItem';
    public $classKey = 'androidItem';
    public $languageTopics = array('android');
    //public $permission = 'save';


    /**
     * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject
     *
     * @return bool|string
     */
    public function beforeSave()
    {
        if (!$this->checkPermissions()) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $id = (int)$this->getProperty('id');
        if (empty($id)) {
            return $this->modx->lexicon('android_item_err_ns');
        }
		$user_id = trim($this->getProperty('user_id'));
		if (!$Orgs = $this->modx->getService('organizations', 'Organizations',$this->modx->getOption('organizations_core_path', null, $this->modx->getOption('core_path') . 'components/organizations/') . 'model/organizations/', array())) {
			return $this->failure('Could not load Organizations class!');
		}
        $defaultOrg = $Orgs->getDefaultOrg($userId);
		//получаем данные орг
		if($defaultOrg ==0){
			return $this->failure('Not found user Organization!');
		}
		$this->setProperty('org_id', $defaultOrg);

        return parent::beforeSet();
    }
}

return 'androidItemUpdateProcessor';
