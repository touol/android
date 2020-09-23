<?php

class androidItemGetKeyProcessor extends modProcessor
{
    public $objectType = 'androidItem';
    public $classKey = 'androidItem';
    public $languageTopics = array('android:default');
    //public $permission = 'view';


    /**
     * We doing special check of permission
     * because of our objects is not an instances of modAccessibleObject
     *
     * @return mixed
     */
    public function process()
    {
        if (!$this->checkPermissions()) {
            return $this->failure($this->modx->lexicon('access_denied'));
        }
		$api_key = $this->generateCode();
        return $this->success('',array('api_key'=>$api_key));
    }
	
	public function generateCode($length = 50){
		$chars = 'abcdefghijklmnopqrstuvwxyz1234567890';
		$numChars = strlen($chars);
		$string = '';
		for ($i = 0; $i < $length; $i++) {
		$string .= substr($chars, rand(1, $numChars) - 1, 1);
		}
		return $string;
	}

}

return 'androidItemGetKeyProcessor';