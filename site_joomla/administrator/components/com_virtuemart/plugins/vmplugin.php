<?php

defined ('_JEXEC') or die('Restricted access');
/**
 * abstract class for payment plugins
 *
 * @package    VirtueMart
 * @subpackage Plugins
 * @author Valérie Isaksen, Max Milbers
 * @link https://virtuemart.net
 * @copyright Copyright (c) 2004 - 2023 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: vmplugin.php 4599 2011-11-02 18:29:04Z alatak $
 */


// Get the plugin library
jimport ('joomla.plugin.plugin');
#[\AllowDynamicProperties]
abstract class vmPlugin extends JPlugin {

	// var Must be overriden in every plugin file by adding this code to the constructor:
	// $this->_name = basename(__FILE, '.php');
	// just as note: protected can be accessed only within the class itself and by inherited and parent classes
	//This is normal name of the plugin family, custom, payment
	protected $_psType = 0;
	//Id of the joomla table where the plugins are registered
	protected $_jid = 0;
	protected $_vmpItable = 0;
	//the name of the table to store plugin internal data, like payment logs
	public $_tablename = 0;
	protected $_tableId = 'id';
	protected $_tableChecked = false;
	//Name of the primary key of this table, for exampel virtuemart_calc_id or virtuemart_order_id
	protected $_tablepkey = 0;
	protected $_vmpCtable = 0;
	//the name of the table which holds the configuration like paymentmethods, shipmentmethods, customs
	protected $_configTable = 0;
	protected $_configTableFileName = 0;
	protected $_configTableClassName = 0;
	protected $_xParams = 0;
	protected $_varsToPushParam = array();
	protected $_xmlFile = null;
	//id field of the config table
	protected $_idName = 0;
	//Name of the field in the configtable, which holds the parameters of the pluginmethod
	protected $_configTableFieldName = 0;
	protected $_debug = FALSE;
	protected $_loggable = FALSE;
	protected $_cryptedFields = false;
	protected $_toConvertInt = array();
	protected $_toConvertDec = array();
	protected $_dateFields = array();


	/**
	 * Constructor
	 *
	 * @param object $subject The object to observe
	 * @param array  $config  An array that holds the plugin configuration
	 * @since 1.5
	 */
	function __construct (& $subject, $config) {

		$this->autoloadLanguage = false;
		parent::__construct( $subject, $config );

		if(JVM_VERSION>3){
			if (class_exists('ReflectionClass') and (method_exists($this, 'registerLegacyListener'))) {
				$class = new ReflectionClass($this);
				$methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
				foreach ($methods as $m) {

					$methodName = $m->name;

					if (strpos($methodName, '_') !== 0 and strpos($methodName, 'on') !== 0) {
						$this->registerLegacyListener($methodName);
					}
				}
			}
		}
		//systemplugins must not load the language
		$wLang = ($this->_type != 'system');

		if (!class_exists( 'VmConfig' )) {
			require(JPATH_ROOT .'/administrator/components/com_virtuemart/helpers/config.php');
			VmConfig::loadConfig(FALSE, FALSE, $wLang);
		}


		$this->_psType = substr ($this->_type, 2);

		$filename = 'plg_' . $this->_type . '_' . $this->_name;

		if($wLang)$this->loadJLangThis($filename);

		$this->_tablename = '#__virtuemart_' . $this->_psType . '_plg_' . $this->_name;
		$this->_tableChecked = FALSE;
		$pRaw = VMPATH_ROOT .'/plugins/' . $this->_type .'/'.  $this->_name .'/'. $this->_name . '.xml';
		$this->_xmlFile	= vRequest::filterPath( $pRaw);
		if(!JFile::exists($this->_xmlFile)){
			$this->_xmlFile	= vRequest::filterPath( JPATH_ROOT .'/plugins/' . $this->_type .'/'.  $this->_name .'/'. $this->_name . '.xml');
		}
		if(!JFile::exists($this->_xmlFile)){
			vmError('Plugin xml not found at '.$this->_xmlFile.', but raw '.$pRaw,'Plugin xml not found');
		}
		//vmTrace('Plugin constructed '.$this->_type.' '.$this->_name);
	}

	public function setConvertInt(array $toConvert){
		$this->_toConvertInt = array_merge($this->_toConvertInt, $toConvert);
	}

	public function setConvertDecimal(array $toConvert) {
		$this->_toConvertDec = array_merge($this->_toConvertDec, $toConvert);
	}

	public function setDateFields(array $dateFields){
		$this->_dateFields = array_merge($dateFields, $this->_dateFields);
	}

	public function loadJLangThis($fname,$type=0,$name=0){
		if(empty($type)) $type = $this->_type;
		if(empty($name)) $name = $this->_name;
		self::loadJLang($fname,$type,$name);
	}

	static public function loadJLang($fname,$type,$name){

		$tag = vmLanguage::$currLangTag;

		$cvalue = $fname.';'.$type;
		if(!isset(vmLanguage::$_loaded['plg'][$cvalue])){
			vmLanguage::$_loaded['plg'][$cvalue] = $name;
		}

		vmLanguage::getLanguage($tag);

		$path = $basePath = VMPATH_ROOT .'/plugins/' .$type.'/'.$name;

		if(VmConfig::get('enableEnglish', true) and $tag!='en-GB'){
			$testpath = $basePath .'/language/en-GB/en-GB.'.$fname.'.ini';
			if(!file_exists($testpath)){
				$epath = VMPATH_ADMINISTRATOR;
			} else {
				$epath = $path;
			}
			vmLanguage::$languages[$tag]->load($fname, $epath, 'en-GB', false, false);
		}

		$testpath = $basePath .'/language/'.$tag.'/'.$tag.'.'.$fname.'.ini';
		if(!file_exists($testpath)){
			$path = VMPATH_ADMINISTRATOR;
		}

		vmLanguage::$languages[$tag]->load($fname, $path,$tag, true, false);
	}

	function setPluginLoggable($set=TRUE){
		$this->_loggable = $set;
	}

	function setCryptedFields($fieldNames){
		$this->_cryptedFields = $fieldNames;
	}


	function getOwnUrl(){

		$url = '/plugins/'.$this->_type.'/'.$this->_name;
		return $url;
	}

	function display3rdInfo($intro,$developer,$contactlink,$manlink){
		$logolink = $this->getOwnUrl() ;
		return shopfunctions::display3rdInfo($this->_name,$intro,$developer,$logolink,$contactlink,$manlink);
	}

	/**
	 * This function gets the parameters of a plugin from the given JForm $form.
	 * This is used for the configuration GUI in the BE.
	 * Attention: the xml Params must be always a subset of the varsToPushParams declared in the constructor
	 * @param $form
	 * @return array
	 */
	static public function getVarsToPushFromForm ($form){
		$data = array();

		$fieldSets = $form->getFieldsets();
		foreach ($fieldSets as $name => $fieldSet) {
			foreach ($form->getFieldset($name) as $field) {

				$fieldname = (string)$field->fieldname;
				$private = false;

				if(strlen($fieldname)>1){
					if(substr($fieldname,0,2)=='__'){
						$private = true;
					}
				}

				if(!$private){
					$type='char';
					//vmdebug('getVarsToPushFromForm',$fieldname, $field->getAttribute('default'));
					$data[$fieldname] = array($field->getAttribute('default'),  $type);
				}

			}
		}

		return $data;
	}


	/**
	 * This function gets the parameters of a plugin by an xml file.
	 * This is used for the configuration GUI in the BE.
	 * Attention: the xml Params must be always a subset of the varsToPushParams declared in the constructor
	 * @param $xmlFile
	 * @param $name
	 * @return array
	 */
	static public function getVarsToPushByXML ($xmlFile,$name){
		try {
			$form = JForm::getInstance($name, $xmlFile, array(),false, '//vmconfig | //config[not(//vmconfig)]');
			return vmPlugin::getVarsToPushFromForm($form);
		} catch (Exception $e) {
			vmError('getVarsToPushByXML, Error parsing '.$xmlFile);
		}
	}

	/**
	 * Executes a function of a plugin directly, which is loaded via element
	 *
	 * @author Max Milbers
	 * @deprecated Use function in class vDispatcher instead
	 * @param $type type of the plugin, for example vmpayment
	 * @param $element the element of the plugin as written in the extensions table (usually lowercase)
	 * @param $trigger the function which was the trigger to execute
	 * @param $args the arguments (as before for the triggers)
	 * @return mixed
	 */
	static public function directTrigger($type,$element,$trigger, $args){
		return vDispatcher::directTrigger($type,$element,$trigger, $args);
	}

	/** Creates a plugin object. Used by the directTrigger and therefore loads also unpublished plugins.
	 * Otherwise, we would not be able to use the plug-in functions during the method saving process.
	 * @deprecated Use the class vDispatcher instead
	 * @param $type
	 * @param $element
	 * @return false|mixed
	 */
	static public function createPlugin($type, $element){

		return vDispatcher::createPlugin($type, $element);

	}
	/**
	 * Checks if this plugin should be active by the trigger
	 *
	 * @author Max Milbers
	 * @param string $psType shipment,payment,custom
	 * @param        string the name of the plugin for example textinput, paypal
	 * @param        int/array $jid the registered plugin id(s) of the joomla table
	 */
	protected function selectedThis ($psType, $name = 0, $jid = null) {

		if ($psType !== 0) {
			if ($psType != $this->_psType) {
				vmdebug ('selectedThis $psType does not fit');
				return FALSE;
			}
		}

		if ($name !== 0) {
			if ($name != $this->_name) {
				//vmdebug ('selectedThis $name ' . $name . ' does not fit pluginname ' . $this->_name);
				return FALSE;
			}
		}

		if ($jid === null) {
			return true;
		} else if($jid === 0){
			return FALSE;
		} else {
			if ($this->_jid === 0) {
				$this->getJoomlaPluginId ();
			}
			if (is_array ($jid)) {
				if (!in_array ($this->_jid, $jid)) {
					vmdebug ('selectedThis id ' . $jid . ' not in array does not fit ' . $this->_jid);
					return FALSE;
				}
			}
			else {
				if ($jid != $this->_jid) {
					vmdebug ('selectedThis $jid ' . $jid . ' does not fit ' . $this->_jid);
					return FALSE;
				}
			}
		}

		return true;
	}

	static $c = null;
	/**
	 * Checks if this plugin should be active by the trigger
	 *
	 * The function loads now all methods and caches them, so it is now cheap to use
	 *
	 * @author Max Milbers
	 * @author Valérie Isaksen
	 *
	 * @param int/array $id the registered plugin id(s) of the joomla table
	 */
	function selectedThisByMethodId ($id = 'type') {

		if ($id === 'type') {
			return TRUE;
		}
		else {

			if(empty(self::$c[$this->_psType])){
				$db = JFactory::getDBO ();

				$q = 'SELECT vm.* FROM `' . $this->_configTable . '` AS vm, #__extensions AS j 
					WHERE vm.' . $this->_psType . '_jplugin_id = j.extension_id ';
				if (VmConfig::isSite() ) {
					$q .= 'AND vm.published = 1 ';
				}
				//Todo test this against the one above, maybe we do not need the extension table?
				/*$q = 'SELECT vm.* FROM `' . $this->_configTable . '` AS vm
					WHERE vm.' . $this->_psType . '_jplugin_id > 0 ';
				if (VmConfig::isSite() ) {
					$q .= 'AND vm.published = 1 ';
				}*/

				$db->setQuery ($q);
				self::$c[$this->_psType] = $db->loadObjectList ($this->_idName);
				//vmdebug('selectedThisByMethodId loaded '.$this->_psType,self::$c);
			} else {
				//vmdebug('selectedThisByMethodId cached '.$this->_psType);
			}


			if(isset(self::$c[$this->_psType][$id]) and self::$c[$this->_psType][$id]->{$this->_psType.'_element'} == $this->_name){
				//vmdebug('selectedThisByMethodId return true');
				return self::$c[$this->_psType][$id];
			} else {
				return false;
			}

		}
	}

	/**
	 * Checks if this plugin should be active by the trigger
	 *
	 * @author Max Milbers
	 * @author Valérie Isaksen
	 * @param int/array $jplugin_id the registered plugin id(s) of the joomla table
	 */
	protected function selectedThisByJPluginId ($jplugin_id = 'type') {

		if ($jplugin_id === 'type') {
			return TRUE;
		}
		else {
			$db = JFactory::getDBO ();

			$q = 'SELECT vm.* FROM `' . $this->_configTable . '` AS vm,
						#__extensions AS j WHERE vm.`' . $this->_psType . '_jplugin_id`  = "' . $jplugin_id . '"
						AND vm.`' . $this->_psType . '_jplugin_id` = j.extension_id
						AND j.`element` = "' . $this->_name . '"';

			$db->setQuery ($q);
			if (!$res = $db->loadObject ()) {
				// 				vmError('selectedThisByMethodId '.$db->getQuery());
				return FALSE;
			}
			else {
				return $res;
			}
		}
	}

	/**
	 * Gets the id of the joomla table where the plugin is registered
	 *
	 * @author Max Milbers
	 */
	final protected function getJoomlaPluginId () {

		if (!empty($this->_jid)) {
			return $this->_jid;
		}
		$db = JFactory::getDBO ();

		$q = 'SELECT j.`extension_id` AS c FROM #__extensions AS j
					WHERE j.element = "' . $this->_name . '" AND j.`folder` = "' . $this->_type . '" and `enabled`= "1" and `state`="0" ';

		$db->setQuery ($q);
		try {
			$this->_jid = $db->loadResult ();
		} catch (Exception $e){
			vmError ('getJoomlaPluginId ' . $e->getMessage() );
			return FALSE;
		}
		return $this->_jid;
	}

	/**
	 * Create the table for this plugin if it does not yet exist.
	 * Or updates the table, if it exists. Please be aware that this function is slowing and is only called
	 * storing a method or installing/udpating a plugin. This trigger is called via directTrigger so we dont need to check, if the plugin is active
	 *
	 * @param string $psType shipment,payment,custom
	 * @author Valérie Isaksen
	 * @author Max Milbers
	 */
	public function onStoreInstallPluginTable () {
		vmdebug('onStoreInstallPluginTable, going to execute onStoreInstallPluginTable '.$this->_name);
		$SQLfields = $this->getTableSQLFields();
		if(empty($SQLfields)) return false;

		$loggablefields = $this->getTableSQLLoggablefields();
		$tablesFields = array_merge($SQLfields, $loggablefields);

		$keys = array('id'=>'PRIMARY KEY (`id`)');
		if(isset($tablesFields['virtuemart_order_id'])){
			$keys['virtuemart_order_id'] = 'KEY (`virtuemart_order_id`)';
		}
		$update[$this->_tablename] = array($tablesFields, $keys, array());
		$updater = new GenericTableUpdater();
		return $updater->updateMyVmTables($update);

	}

	/**
	 * adds loggable fields to the table
	 *
	 * @return array
	 */
	function getTableSQLLoggablefields () {
		return array(
			'created_on'  => 'datetime',
			'created_by'  => "int(11) NOT NULL DEFAULT '0'",
			'modified_on' => 'datetime',
			'modified_by' => "int(11) NOT NULL DEFAULT '0'",
			'locked_on'   => 'datetime',
			'locked_by'   => 'int(11) NOT NULL DEFAULT \'0\''
		);
	}

	/**
	 * @param $tableComment
	 * @return string
	 */
	protected function createTableSQL ($tableComment,$tablesFields=0) {

		$query = "CREATE TABLE IF NOT EXISTS `" . $this->_tablename . "` (";
		if(!empty($tablesFields)){
			foreach ($tablesFields as $fieldname => $fieldtype) {
				$query .= '`' . $fieldname . '` ' . $fieldtype . " , ";
			}
		} else {
			$SQLfields = $this->getTableSQLFields ();
			$loggablefields = $this->getTableSQLLoggablefields ();
			foreach ($SQLfields as $fieldname => $fieldtype) {
				$query .= '`' . $fieldname . '` ' . $fieldtype . " , ";
			}
			foreach ($loggablefields as $fieldname => $fieldtype) {
				$query .= '`' . $fieldname . '` ' . $fieldtype . ", ";
			}
		}

		$query .= "	      PRIMARY KEY (`id`)
	    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci COMMENT='" . $tableComment . "' AUTO_INCREMENT=1 ;";
		return $query;
	}

	/**
	 * @return array
	 */
	function getTableSQLFields () {

		return false;
	}

	/**
	 * Set with this function the provided plugin parameters
	 *
	 * @param string $paramsFieldName
	 * @param array  $varsToPushParam
	 */
	function setConfigParameterable ($paramsFieldName, $varsToPushParam) {
		$this->_xParams = $paramsFieldName;
		if(empty($varsToPushParam)) return false;
		$this->_varsToPushParam = array_merge($this->_varsToPushParam, $varsToPushParam);
	}

	/**
	 *
	 * @param $psType
	 * @param $name
	 * @param $id
	 * @param $xParams
	 * @param $varsToPush
	 * @return bool
	 */
	protected function getTablePluginParams ($psType,$name, $id, &$xParams, &$varsToPush, &$table=0) {

		if (!empty($this->_psType) and !$this->selectedThis ($psType, $name, $id)) {
			//vmdebug('getTablePluginParams return ',$psType, $this->_psType, $name, $this->_name, $id,$this->_jid);
			return FALSE;
		}

		$varsToPush = $this->_varsToPushParam;
		$xParams = $this->_xParams;
		if($table!=0) {
			$table->setConvertInt($this->_toConvertInt);
			$table->setConvertDecimal($this->_toConvertDec);
			$table->setDatefields($this->_dateFields);
		}
		//vmdebug('getTablePluginParams '.$name.' sets xParams '.$xParams.' vars',$varsToPush);
	}

	/**
	 * @param $name
	 * @param $id
	 * @param $table
	 * @return bool
	 */
	protected function setOnTablePluginParams ($name, $id, &$table) {

		//Todo I think a test on this is wrong here
		//Adjusted it like already done in declarePluginParams
		if (!empty($this->_psType) and !$this->selectedThis ($this->_psType, $name, $id)) {
			return FALSE;
		}
		else {
			if($this->_cryptedFields){
				$table->setCryptedFields($this->_cryptedFields);
			}

			$table->setParameterable ($this->_xParams, $this->_varsToPushParam);
			$table->setConvertDecimal($this->_toConvertDec);

			return TRUE;
		}

	}

	/**
	 * Does VmTable::bindParameterable and setCryptedFields $name, $id, $data
	 * @param $psType
	 * @param $data
	 * @return bool
	 */
	protected function declarePluginParams ($psType, &$data, $blind=0, $blind2=0) {

		if(!empty($this->_psType)){

		    if($this->_psType!=$psType){
                return FALSE;
            }

			$element = $this->_psType.'_element';
			$jplugin_id = $this->_psType.'_jplugin_id';
			if(empty($data->{$element})) $data->{$element} = 0;
			if(empty($data->{$jplugin_id})) $data->{$jplugin_id} = 0;

			if(!$this->selectedThis($psType,$data->{$element})){
				return FALSE;
			}

		}

		//New Vm3 way
		//Is only used for the config tables!
		//VmTable::bindParameterable ($data, $data->_xParams, $this->_varsToPushParam);
		if(isset($this->_varsToPushParam)){
			if(isset($data->_varsToPushParam)){
				$data->_varsToPushParam = array_merge((array)$data->_varsToPushParam, (array)$this->_varsToPushParam);
			} else {
				$data->_varsToPushParam = (array)$this->_varsToPushParam;
			}
		} else{
			vmdebug('no vars to push?',$this);
		}

		if($this->_cryptedFields){
			$data->setCryptedFields($this->_cryptedFields);
		}

		return TRUE;
	}

	/**
	 * @param $int
	 * @return mixed
	 */
	public function getVmPluginMethod ($int, $cache = true) {

		if ($this->_vmpCtable === 0 || !$cache) {

			$db = JFactory::getDBO ();

			if (!class_exists ($this->_configTableClassName)) {
				require(VMPATH_ADMIN .'/tables/'. $this->_configTableFileName . '.php');
			}
			$this->_vmpCtable = new $this->_configTableClassName($db);
			if ($this->_xParams !== 0) {
				$this->_vmpCtable->setParameterable ($this->_configTableFieldName, $this->_varsToPushParam,true);
			}

			if($this->_cryptedFields){
				$this->_vmpCtable->setCryptedFields($this->_cryptedFields);
			}
		}

		return $this->_vmpCtable->load ($int);
	}

	/**
	 * This stores the data of the plugin, attention NOT the configuration of the pluginmethod,
	 * this function should never be triggered only called from triggered functions.
	 *
	 * @author Max Milbers
	 * @param array  $values array or object with the data to store
     * @param int|string $primaryKey
     * @param int|string $id
     * @param boolean $preload
     * @return array
	 */
	public function storePluginInternalData (&$values, $primaryKey = 0, $id = 0, $preload = FALSE) {

		if ($primaryKey === 0) {
			$primaryKey = $this->_tablepkey;
		}
		if ($this->_vmpItable === 0 or $id==0) {
			$this->_vmpItable = $this->createPluginTableObject ($this->_tablename, $this->tableFields, $primaryKey, $this->_tableId, $this->_loggable);
		}

		if($this->_toConvertDec){
			$this->_vmpItable->setConvertDecimal($this->_toConvertDec);
		}
		$this->_vmpItable->bindChecknStore ($values, $preload);

		return $values;
	}

	/**
	 * This loads the data stored by the plugin before, NOT the configuration of the method,
	 * this function should never be triggered only called from triggered functions.
	 *
	 * @param int    $id
	 * @param string $primaryKey
	 */
	protected function getPluginInternalData ($id, $primaryKey = 0) {

		if ($primaryKey === 0) {
			$primaryKey = $this->_tablepkey;
		}
		if ($this->_vmpItable === 0) {
			$this->_vmpItable = $this->createPluginTableObject ($this->_tablename, $this->tableFields, $primaryKey, $this->_tableId, $this->_loggable);
		}
		//vmdebug('getPluginInternalData $id '.$id.' and $primaryKey '.$primaryKey);
		return $this->_vmpItable->load ($id);
	}

	/**
	 * @param      $tableName
	 * @param      $tableFields
	 * @param      $primaryKey
	 * @param      $tableId
	 * @param bool $loggable
	 * @return VmTableData
	 */
	protected function createPluginTableObject ($tableName, $tableFields, $primaryKey, $tableId, $loggable = FALSE) {

		$db = JFactory::getDBO ();
		$table = new VmTable($tableName, $tableId, $db);
		foreach ($tableFields as $field) {
			$table->{$field} = 0;
		}

		if ($primaryKey !== 0) {
			$table->setPrimaryKey ($primaryKey);
		}
		if ($loggable) {
			$table->setLoggable ();
		}

		if($this->_cryptedFields){
			$table->setCryptedFields($this->_cryptedFields);
		}

		if($this->_toConvertDec and is_array($this->_toConvertDec) ){
			$table->setConvertDecimal($this->_toConvertDec);
		}

		/*if (!$this->_tableChecked) {
			$this->onStoreInstallPluginTable ($this->_psType);
			$this->_tableChecked = TRUE;
		}*/

		return $table;
	}

	/**
	 * @param     $id
	 * @param int $primaryKey
	 * @return mixed
	 */
	protected function removePluginInternalData ($id, $primaryKey = 0) {
		if ($primaryKey === 0) {
			$primaryKey = $this->_tablepkey;
		}
		if ($this->_vmpItable === 0) {
			$this->_vmpItable = $this->createPluginTableObject ($this->_tablename, $this->tableFields, $primaryKey, $this->_tableId, $this->_loggable);
		}
		vmdebug ('removePluginInternalData $id ' . $id . ' and $primaryKey ' . $primaryKey);
		return $this->_vmpItable->delete ($id);
	}

	/**
	 * @param string $layout
	 * @param null $viewData
	 * @param null $name
	 * @param null $psType
	 * @return string
	 * @author Patrick Kohl, Valérie Isaksen, Max Milbers
	 */
	public function renderByLayout ($layout = 'default', $viewData = NULL, $name = NULL, $psType = NULL) {
		if ($name === NULL) {
			$name = $this->_name;
		}

		if ($psType === NULL) {
			$psType = 'vm'.$this->_psType;
		}

		$layout = vmPlugin::_getLayoutPath ($name,  $psType, $layout);

		if($layout){
			ob_start ();
			include ($layout);
			return ob_get_clean ();
		} else {
			vmdebug('renderByLayout: layout '.$layout.'not found '.$psType. ' '.$name.' default path '.$layout);
		}

	}

	/**
	 *  Note: We have 2 subfolders for versions > J15 for 3rd parties developers, to avoid 2 installers
	 *	Note: from Version 2.12: it is possible to have the tmpl folder directly in $pluginName folder
	 * @author Max Milbers, Valérie Isaksen
	 */

	static public function _getLayoutPath ($pluginName, $group, $layout = 'default') {
		$layoutPath=$templatePathWithGroup=$defaultPathWithGroup='';
		jimport ('joomla.filesystem.file');
		// First search in the new system
		$vmStyle = VmTemplate::loadVmTemplateStyle();
		$template = $vmStyle['template'];
		$templatePath         = VMPATH_ROOT .'/templates/'. $template .'/html/'. $group . '/' . $pluginName . '/' . $layout . '.php';
		$defaultPath          = VMPATH_ROOT .'/plugins/'. $group . '/' . $pluginName .'/tmpl/'. $layout . '.php';
		$defaultPathWithGroup = VMPATH_ROOT .'/plugins/'. $group . '/' . $pluginName . '/' . $pluginName .'/tmpl/'. $layout . '.php';

		if (JFile::exists ($templatePath)) {
			$layoutPath= $templatePath;
		} elseif (JFile::exists ($defaultPath)) {
			$layoutPath= $defaultPath;
		} elseif (JFile::exists ($defaultPathWithGroup)) {
			$layoutPath = $defaultPathWithGroup;
		}
		if (empty($layoutPath)) {
			$warn='The layout: '. $layout. ' does not exist in:';
			$warn.='<br />'. $templatePath.'<br />'.$defaultPath;
			if (!empty($defaultPathWithGroup)) {
				$warn.='<br />'.$defaultPathWithGroup .'<br />';
			}
			vmWarn($warn);
			return false;
		}
		return $layoutPath;
	}
	/**
	 * @param        $pluginName
	 * @param        $group
	 * @param string $layout
	 * @return mixed
	 * @author Valérie Isaksen
	 */
	static public function getTemplatePath($pluginName, $group, $layout = 'default') {
		$layoutPath = vmPlugin::_getLayoutPath ($pluginName, 'vm' . $group, $layout);
		return str_replace('/' . $layout . '.php','',$layoutPath );
	}

	function plgVmOnDisplayEditCustoms($custom_id, &$html) {
		if ($this->_type == 'vmcustoms'){
			return $this->plgVmOnDisplayEdit($custom_id, $html);
		}

	}

	function plgVmOnDisplayEditCalc(&$calc, &$html) {
		if ($this->_type == 'vmcalculation'){
			return $this->plgVmOnDisplayEdit($calc, $html);
		}

	}

	/**
	 * @param $custom_id
	 * @param $html
	 * @deprecated Use plgVmOnDisplayEditCustoms or plgVmOnDisplayEditCalc instead
	 */
	function plgVmOnDisplayEdit($custom_id, &$html) {

	}
}
