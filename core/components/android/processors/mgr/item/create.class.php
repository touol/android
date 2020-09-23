<?php

class androidItemCreateProcessor extends modObjectCreateProcessor
{
    public $objectType = 'androidItem';
    public $classKey = 'androidItem';
    public $languageTopics = array('android');
    //public $permission = 'create';


    /**
     * @return bool
     */
    public function beforeSet()
    {
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

return 'androidItemCreateProcessor';