<?php
/**
 * Dolibase
 * 
 * Open source framework for Dolibarr ERP/CRM
 *
 * Copyright (c) 2018 - 2019
 *
 *
 * @package     Dolibase
 * @author      AXeL
 * @copyright	Copyright (c) 2018 - 2019, AXeL-dev
 * @license
 * @link
 * 
 */

dolibase_include_once('/core/class/num_model.php');

/**
 * NumModelSaphir class
 *
 * Class to manage module numbering rules Saphir
 */

class NumModelSaphir extends NumModel
{
	public $version       = 'dolibarr'; // 'development', 'experimental', 'dolibarr'
	public $nom           = 'Saphir';
	protected $const_name = '';
	protected $table_name = '';
	protected $field_name = 'ref';

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		global $dolibase_config;

		// Generate constant name
		$this->const_name = get_rights_class(true) . '_SAPHIR_MASK';

		// Set parameters
		$this->table_name = $dolibase_config['numbering_model']['table'];
		$this->field_name = $dolibase_config['numbering_model']['field'];
	}

	/**
	 *  Return description of numbering model
	 *
	 *  @return     string      Text with description
	 */
	public function info()
	{
		global $db, $conf, $langs, $dolibase_config;

		$langs->load($dolibase_config['other']['lang_files'][0]);

		$module_name = $langs->transnoentities($dolibase_config['module']['name']);
		$const_name  = $this->const_name;

		$form = new Form($db);

		$text = $langs->trans('GenericNumRefModelDesc')."<br>\n";
		$text.= '<form action="'.$_SERVER["PHP_SELF"].'" method="POST">';
		$text.= '<input type="hidden" name="token" value="'.$_SESSION['newtoken'].'">';
		$text.= '<input type="hidden" name="action" value="updateMask">';
		$text.= '<input type="hidden" name="maskconst" value="'.$const_name.'">';
		$text.= '<table class="nobordernopadding" width="100%">';

		$tooltip = $langs->trans("GenericMaskCodes", $module_name, $module_name);
		$tooltip.= $langs->trans("GenericMaskCodes2");
		$tooltip.= $langs->trans("GenericMaskCodes3");
		$tooltip.= $langs->trans("GenericMaskCodes4a", $module_name, $module_name);
		$tooltip.= $langs->trans("GenericMaskCodes5");

		$text.= '<tr><td>'.$langs->trans("Mask").':</td>';
		$text.= '<td align="right">'.$form->textwithpicto('<input type="text" class="flat" size="24" name="mask" value="'.$conf->global->$const_name.'">',$tooltip,1,1).'</td>';

		$text.= '<td align="left" rowspan="2">&nbsp; <input type="submit" class="button" value="'.$langs->trans("Modify").'" name="Button"></td>';

		$text.= '</tr>';

		$text.= '</table>';
		$text.= '</form>';

		return $text;
	}

	/**
	 *  Return an example of numbering
	 *
	 *  @return string      Example
	 */
	public function getExample()
	{
		global $langs;

		$numExample = $this->getNextValue($this->table_name, $this->field_name);

		if (! $numExample)
		{
			$numExample = $langs->trans('NotConfigured');
		}
		return $numExample;
	}

	/**
	 * 	Return next free value
	 *
	 *  @param	Societe		$objsoc     Object thirdparty
	 *  @param  Object		$object		Object we need next value for
	 *  @return string      			Value if KO, <0 if KO
	 */
	public function getNextValue($objsoc = '', $object = '')
	{
		global $db, $conf, $langs;

		require_once DOL_DOCUMENT_ROOT .'/core/lib/functions2.lib.php';

		// We get cursor rule
		$const_name = $this->const_name;
		$mask = $conf->global->$const_name;

		if (! $mask)
		{
			$this->error = $langs->trans('ErrorModuleSetupNotComplete');
			return 0;
		}

		$date = time();
		$numFinal = get_next_value($db, $mask, $this->table_name, $this->field_name, '', $objsoc, $date, 'next', false);

		return  $numFinal;
	}
}