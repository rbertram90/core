<?php
namespace rbwebdesigns\core;

/**
 * Class HTMLFormTools
 * Aim - to provide quick methods for creating complex form fields
 * 
 * Function List:
 *  - generateFromJSON($json, $data)
 *  - createTextField(name, caption, value, options)
 *  - createTextFieldFromArray(options)
 *  - createMemoField()
 *  - createMemoFieldFromArray(options)
 *  - createDropdown()
 *  - createDropdownFromArray()
 *  - createCheckbox()
 *  - createRadioGroup()
 *  - createYearSelector()
 *  - createYearSelectorFromArray()
 */
class HTMLFormTools
{
    private $securetools;
    
    public function __construct($appsecurityobject)
    {
        // Might be better to require these to be passed into the constructor!!!
        if(!defined('ICON_ROOT')) define('ICON_ROOT', '/resources/icons/64');
        
        // Check we've got a security object - create one if not
        if($appsecurityobject == null)
        {
            $this->securetools = new AppSecurity();
        }
        else
        {
            $this->securetools = $appsecurityobject;
        }
    }
    
    /**
     * strjson - json string (or array) which provides a definition
     * of the form
     * fields - form fields
     */
    public function generateFromJSON($strjson, $arraydata=null)
    {
        // Convert to array (if needed)
        if (strtolower(getType($strjson)) == 'array') {
            $arrayFormSpec = $strjson;
        }
        else {
            $arrayFormSpec = json_decode($strjson, true);
        }
        
        if (array_key_exists('addheader', $arrayFormSpec) && $arrayFormSpec['addheader'] == false) {
            // Do not add header
            $strformHTML = "";
        }
        else {
            // Generate Header
            $strformHTML = $this->generateFormHeader($arrayFormSpec);
        }
        
        foreach ($arrayFormSpec['fields'] as $field) {
            if (!array_key_exists('type', $field)) {
                $strformHTML.= 'TYPE key not supplied in form JSON';
                continue;
            }
            switch (strtolower($field['type'])) {
                case 'year':
                    $strformHTML.= $this->createYearSelectorFromArray($field);
                    break;
                case 'text':
                    $strformHTML.= $this->createTextFieldFromArray($field);
                    break;
                case 'memo':
                    $strformHTML.= $this->createMemoFieldFromArray($field);
                    break;
                case 'yesno':
                    $strformHTML.= $this->createYesNoFieldFromArray($field);
                    break;
                case 'dropdown':
                    $strformHTML.= $this->createDropdownFromArray($field);
                    break;
                case 'hidden':
                    $strformHTML.= $this->createHiddenFieldFromArray($field);
                    break;
                default:
                    $strformHTML.= 'TYPE "'.$field['type'].'" not recognised in form generator';
                    break;
            }
        }
        
        if (array_key_exists('addfooter', $arrayFormSpec) && $arrayFormSpec['addfooter'] == false) {
            // Do not add buttons
            $strformHTML.= "\n".'</form>';
        }
        else {
            // Add footer
            $strformHTML.= $this->generateFormFooter($arrayFormSpec);
        }
        
        // Convert data fields to text
        $this->parseFormFields($strformHTML, $arraydata);
        
        return $strformHTML;
    }
    
    private function generateFormHeader($config)
    {
        $html = "";
        
        // Form Icon
        $action = '';
        if(array_key_exists('action', $config)) $action = $config['action'];
        if(array_key_exists('icon', $config)) $html.= '<img src="'.ICON_ROOT.'/'.$config['icon'].'" />';
        
        // JavaScript for onsubmit
        $formOnSubmitAction = array_key_exists('onsubmitaction', $config) ? $config['onsubmitaction'] : 'return checkForm(this);';
        
        $formName = array_key_exists('formname', $config) ? $config['formname'] : 'frmMain';
        
        // Form Title
        if(array_key_exists('title', $config)) $html.= '<h1>'.$config['title'].'</h1>';
        
        $html.= '<style>.nobots{display:none;}</style>';
        $html.= "\n".'<form action="'.$action.'" class="ui form" method="POST" name="'.$formName.'" onsubmit="'.$formOnSubmitAction.'">';
        $html.= "\n".'	<input type="text" name="fld_generic" id="fld_generic" class="nobots" />';
        
        return $html;
    }
    
    private function generateFormFooter($config) {
        
        $strfinishbtnlabel = array_key_exists('submitbuttonlabel', $config) ? $config['submitbuttonlabel'] : "Submit";
        $strcancelbtnlabel = array_key_exists('cancelbuttonlabel', $config) ? $config['cancelbuttonlabel'] : "Cancel";
        
        $strcancelbtnaction = array_key_exists('cancelbuttonaction', $config) ? $config['cancelbuttonaction'] : 'window.history.back()';
        
        // $crosssitescriptkey = $this->securetools->generateSecureKey();
        // $_SESSION['secure_form_key'] = $crosssitescriptkey;
        
        $strHTML = "\n".'<div class="push-right">';
        $strHTML.= "\n".'	<input type="submit" value="'.$strfinishbtnlabel.'" name="btn_submit" />';
        $strHTML.= "\n".'	<input type="button" onclick="'.$strcancelbtnaction.'" value="'.$strcancelbtnlabel.'" name="btn_cancel" />';
        // $strHTML.= "\n".'	<input type="hidden" value="'.$crosssitescriptkey.'" name="fld_secure_key" />';
        $strHTML.= "\n".'</div>';
        $strHTML.= "\n".'</form>';
        return $strHTML;
    }
    
    private function parseFormFields(&$strFormHTML, $data) {
    
        if (strpos($strFormHTML, '[!data.') !== false && $data != null) {
            // note would [!system. also be helpful?
            foreach ($data as $key => $value) {
                // echo '[!data.'.$key.'] -> '.$value;
                $strFormHTML = str_replace('[!data.'.$key.']', $value, $strFormHTML);
            }
        }
        // Catch any that were not found in data array
        $strFormHTML = preg_replace('/\[!data\.[A-Za-z0-9]+\]/', '', $strFormHTML);
    }
    
    public function addHelpTextIcon($strHelp) {
                
        return '<a href="/" onclick="alert(\''.filter_var($strHelp, FILTER_SANITIZE_ADD_SLASHES).'\'); return false;" class="help">[?]</a>';
    }
    
    /**
     * generateValidation - HTML attributes for validation
     * @param array $options
     *   Validation node from the form definition JSON
     *     json - JSON string to directly output to configure validate.js
     *     message - Message to show if validation fails
     */
    public function generateValidation($arrayoptions) {
        if(!array_key_exists('json', $arrayoptions)) return "Required key 'JSON' not found in validation object.";
        if(!array_key_exists('message', $arrayoptions)) return "Required key 'MESSAGE' not found in validation object.";
        return ' data-notValidText="'.$arrayoptions['message'].'" onkeyup="validate(this, '.$arrayoptions['json'].')"';
    }
    
    /**
     * createTextField - Generate a standard text field
     *
     * @param string $name
     *   (unique) Name & ID attributes for the field
     * @param string $caption
     *   Text to be applied to the field label
     * @param string $value
     *   Text to show in the field
     * @param array  $options
     *   Custom Options for the field - not yet used!
     */
    public function createTextField($strname, $strlabel, $strcurrentvalue="", $arrayoptions=null)
    {
        $strplaceholder = $strhelptext = $strvalidation = '';
        
        if($arrayoptions !== null) {
            if (array_key_exists('helptext', $arrayoptions)) $strhelptext = $this->addHelpTextIcon($arrayoptions['helptext']);
            if (array_key_exists('placeholder', $arrayoptions)) $strplaceholder = ' placeholder=\''.$arrayoptions['placeholder'].'\'';
            if (array_key_exists('validation', $arrayoptions)) $strvalidation = $this->generateValidation($arrayoptions['validation']);
        }
            
        $rtnHTML = "\n".'<div class="field">';
        $rtnHTML.= "\n".'	<label for="'.$strname.'">'.$strlabel.'</label>'.$strhelptext;		
        $rtnHTML.= "\n".'	<input type="text" value="'.$strcurrentvalue.'" name="'.$strname.'" id="'.$strname.'"'.$strplaceholder.$strvalidation.'>'."\n";
        $rtnHTML.= "\n".'</div>';
        
        return $rtnHTML;
    }
    public function createTextFieldFromArray($arrayconfig)
    {
        $arraydefaults = array(
            'name' 		=> 'fld_default',
            'label' 	=> 'default',
            'current' 	=> '',
            'options'	=> null
        );
        $arrayparams = array_merge($arraydefaults, $arrayconfig);
        
        return $this->createTextField($arrayparams['name'], $arrayparams['label'], $arrayparams['current'], $arrayparams['options']);
    }
    
    public function createHiddenField($strname, $strcurrentvalue="")
    {	
        $rtnHTML = "\n".'	<input type="hidden" value="'.$strcurrentvalue.'" name="'.$strname.'" id="'.$strname.'" />'."\n";
        return $rtnHTML;
    }
    public function createHiddenFieldFromArray($arrayconfig)
    {
        $arraydefaults = array(
            'name' 		=> 'fld_default',
            'current' 	=> ''
        );
        $arrayparams = array_merge($arraydefaults, $arrayconfig);
        
        return $this->createHiddenField($arrayparams['name'], $arrayparams['current']);
    }
    
    public function createMemoField($strname, $strlabel, $strcurrentvalue="", $arrayoptions=null) {
    
        $strplaceholder = $strhelptext = '';
    
        if ($arrayoptions !== null) {
            if(array_key_exists('helptext', $arrayoptions)) $strhelptext = $this->addHelpTextIcon($arrayoptions['helptext']);
        }
        
        $rtnHTML = "\n".'	<label for="'.$strname.'">'.$strlabel.'</label>'.$strhelptext;		
        $rtnHTML.= "\n".'	<textarea name="'.$strname.'" id="'.$strname.'">'.$strcurrentvalue.'</textarea>'."\n";
        
        return $rtnHTML;
    }
    public function createMemoFieldFromArray($arrayconfig) {
        
        $arraydefaults = array(
            'name' 		=> 'fld_default',
            'label' 	=> 'default',
            'current' 	=> '',
            'options'	=> null
        );
        $arrayparams = array_merge($arraydefaults, $arrayconfig);
        
        return $this->createMemoField($arrayparams['name'], $arrayparams['label'], $arrayparams['current'], $arrayparams['options']);
    }
    
    public function createYesNoFieldFromArray($arrayconfig) {
    
        $arraydefaults = array(
            'name' 		=> 'fld_default',
            'label' 	=> 'default',
            'current' 	=> '',
            'values'    => array('Yes' => 1,'No' => 0),
            'options'	=> null
        );
        $arrayparams = array_merge($arraydefaults, $arrayconfig);
        
        return $this->createDropdown($arrayparams['name'], $arrayparams['label'], $arrayparams['values'], $arrayparams['current'], $arrayparams['options']);
    }
    
    /**
     * createTextField - Generate a standard text field
     *
     * @param string $name
     *   (unique) Name & ID attributes for the field
     * @param string $caption
     *   Text to be applied to the field label
     * @param array $values
     *   Key (Caption) => Value of each option
     * @param string $value
     *   Text to show in the field
     * @param array $options
     *   Custom Options for the field - not yet used!
     */
    public function createDropdown($strname, $strlabel, $arrayvalues, $strcurrentvalue="", $arrayoptions=null) {
        
        $rtnHTML = "\n".'	<label for="'.$strname.'">'.$strlabel.'</label>';
        if ($arrayoptions !== null && array_key_exists('helptext', $arrayoptions)) {
            $this->addHelpTextIcon($arrayoptions['helptext']);
        }
        $rtnHTML.= "\n".'	<select name="'.$strname.'" id="'.$strname.'">'."\n";
                
        foreach ($arrayvalues as $key => $value) {
            
            // $strcurrent = ($strcurrentvalue == $value) ? ' selected' : ''; // this doesn't work because currentvalue will more than likely be a [!data.something] field rather than the acutal value...
            $rtnHTML.= '		<option value="'.$value.'">'.$key.'</option>'."\n";
        }
        
        // this script doesn't first with ajax?
        $rtnHTML.= '</select><script type="text/javascript">console.log("running"); document.getElementById("'.$strname.'").value = "'.$strcurrentvalue.'"</script>'."\n";
        
        return $rtnHTML;
    }
    public function createDropdownFromArray($arrayconfig) {
        
        $arraydefaults = array(
            'name' 		=> 'fld_default',
            'label' 	=> 'default',
            'current' 	=> '',
            'values'	=> array(),
            'options'	=> null
        );
        $arrayparams = array_merge($arraydefaults, $arrayconfig);
        
        return $this->createDropdown($arrayparams['name'], $arrayparams['label'], $arrayparams['values'], $arrayparams['current'], $arrayparams['options']);
    }
    
    /**
     * Returns a select (drop down) list with options between $min and $max
     */
    public function createYearSelector($min=1900, $max=2200, $strname="fld_year", $strlabel="Year", $default=false) {

        if ($default == false) $default = date('Y');
        if ($min > $max) return 'Unable to generate year selector: provided minimum year is greater than maxium';
        
        $options = array();
        
        for ($i=$min; $i <= $max; $i++) $options[$i] = $i;
        
        return $this->createDropdown($strname, $strlabel, $options, $default);
    }
    public function createYearSelectorFromArray($arrayconfig) {
        
        $arraydefaults = array(
            'start' 	=> 1900,
            'end' 		=> 2200,
            'name' 		=> 'fld_year',
            'label' 	=> 'Year',
            'current' 	=> false
        );
        $arrayparams = array_merge($arraydefaults, $arrayconfig);
        
        return $this->createYearSelector($arrayparams['start'], $arrayparams['end'], $arrayparams['name'], $arrayparams['label'], $arrayparams['current']);
    }
    
    public function createCheckbox($strname, $boolischecked) {
        
    }
    
    public function createRadioGroup($strname, $arraycaptions, $arrayvalues, $strcurrentvalue) {
        
    }
}
