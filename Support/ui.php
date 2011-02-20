<?php 

/**
* UI
*
* UI Layer to call up TM alerts, dialogs
* 
* NIB related code not exactly working - still needs more r&d, but the simpler
* UI dialogs, menus are working
*/
class UI {

    //Path to the dialog bin
    var $dialog = null;

    /**
     * @param string $dialog path to the dialog bin
     * @return void
     **/
    function __construct($dialog) {
        $this->dialog = $dialog;
    }
    
    /**
     * Print out help
     **/
    public function help($command = '') {
        $response = `{$this->dialog} help {$command}`;
    }
    
    /**
     * Throw up alert
     * @param string $msg Body Mesage of the alert. Use this for simplest use case
     * @param array $options Other more extensive options - button1, button2, title, 
     *      alertStyle:  'warning,' 'informational', 'critical'
     *      title: any string
     *      button1, button2
     * @return response
     **/
    public function alert($msg, $options = array()) {

        $optstring = " --body '{$msg}' ";
        foreach ($options as $key => $value) {
            $optstring .= "--{$key} '$value' ";
        }

        return `{$this->dialog} alert {$optstring}`;
    }


    /**
     * Generate a simple menu
     * like inline one generated by bundles where commands all map to same key.
     * 
     * @param array $items value of items
     * @return string
     **/
    public function menu($items) {
        $optstring = '('.implode(',', $items) .')';
        
        //({title = foo;}, {separator = 1;}, {header=1; title = bar;}, {title = baz;})
        $response = `{$this->dialog} menu --items '{$optstring}'`;
        return $response;
    }

    /**
     * Generate a popup menu 
     * Can specify what it inserts. Otherwise, not sure about the use here. 
     * Good for autocomplete
     *
     * @param array $opts expects $opts = array(array('display'=>'display name', 'insert'=>'to_insert)...)
     * @return null No response - it writes out to the document you are editing.
     **/
    public function popup($opts) {
        
        $opt = array();
        foreach ($opts as $option) {
            if(is_array($option)) {
                $item =  '{display = "'.$option['display'].'"; insert='.$option['insert'].';}';
            } else {
                $item =  '{display = "'.$option.'";}';
            }
            
            $opt[] = $item;
        }
        $optstring = '('.implode(',', $opt) .')';
        
        // $optstring = '( { display = law; }, { display = laws; insert = "(${1:hello}, ${2:again})"; } )';
        `{$this->dialog} popup --suggestions '{$optstring}'`;
    }


    public function popup_x($opts) {
        
        $opt = array();
        foreach ($opts as $option) {
            if(is_array($option)) {
                $item =  '{display = "'.$option['display'].'"; insert='.$option['insert'].';}';
            } else {
                $item =  '{display = "'.$option.'";}';
            }
            
            $opt[] = $item;
        }
        $optstring = '('.implode(',', $opt) .')';
        
        // $optstring = '( { display = law; }, { display = laws; insert = "(${1:hello}, ${2:again})"; } )';
        $response = `{$this->dialog} popup  --returnChoice --suggestions '{$optstring}'`;
        var_dump($response);
    }

    

    //used for deving the nibs - this flushes out all the old NIBs hanging around
    public function nib_list_clean() {
        $response = `{$this->dialog} nib --list`;

        var_dump($response);

        $nibs = explode("\n", $response);
        array_pop($nibs);
        array_shift($nibs);
        foreach ($nibs as $id) {
            $id = explode(' ', $id, 2);
            `{$this->dialog} nib --dispose {$id[0]}`;
        }
    }


    /**
     * Load a NIB
     *
     * @return void
     **/
    public function nib() {
        // $response = `{$this->dialog} nib --list`;
        // $nibpath = getenv('TM_SUPPORT_PATH') . DIRECTORY_SEPARATOR . 'nibs' . DIRECTORY_SEPARATOR . 'RequestItem.nib';
        // var_dump("{$this->dialog} nib --load '{$nibpath}'");

        $nibtoken = `{$this->dialog} nib --load "RequestItem" --model '{title = "Campaigns"; prompt = "Please select your campign:"; items=("foo","bar","baz"); }'`;
        // $response = `{$this->dialog} nib --update {$nibtoken} --model '{title = "Camp2"; prompt = "Please select your campign2:"; items=("foo","bar","baz"); }'`;
        // var_dump($response);

        //Ok, this sorta works. so now the question - how to pipe this to another php command,
        //which can 
        // a) get the response the user clicked
        // b) close the window
        $response = `{$this->dialog} nib --wait {$nibtoken} &> /Users/mitch/src/test.txt &`;
        var_dump($response);
        // var_dump($nibtoken);
        // $response = `{$this->dialog} nib --dispose {$nibtoken}`;
        // var_dump($response);
    }

    /**
     * undocumented function
     *
     * @return void
     **/
    public function requestItem() {

        $nibtoken = `{$this->dialog} nib --load "RequestItem" --model '{title = "Campaigns"; prompt = "Please select your campign:"; items=("foo","bar","baz"); }'`;

        $output = array();
        exec("{$this->dialog} nib --wait {$nibtoken} | wc &", $output);
        var_dump($output);
        // var_dump($nibtoken);
        // $response = `{$this->dialog} nib --dispose {$nibtoken}`;
        // var_dump($response);
     
        
    }


}
